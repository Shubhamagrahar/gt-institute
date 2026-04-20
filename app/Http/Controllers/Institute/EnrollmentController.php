<?php

namespace App\Http\Controllers\Institute;

use App\Http\Controllers\Controller;
use App\Models\{AdmissionFormField, BatchDetail, CourseBook, CourseDetail,
                 EnrollmentFeeSnapshot, EnrollmentPaymentPlan,
                 InstituteSession, PaymentPlanType, StudentWallet,
                 State, StudentTransaction, User, UserEducation, UserProfile};
use App\Models\Owner\Institute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, DB, Hash};
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class EnrollmentController extends Controller
{
    private function instituteId(): int
    {
        return Auth::guard('institute')->user()->institute_id;
    }

    private function activeSessionId(): int
    {
        $inst = $this->instituteId();
        return InstituteSession::where('institute_id', $inst)
                   ->where('is_active', true)
                   ->value('id') ?? abort(422, 'No active session.');
    }

    // Step 1 — Choose new or existing
    public function choose()
    {
        return view('institute.enrollment.choose');
    }

    public function newStudent()
    {
        $iid = $this->instituteId();
        $courses = CourseDetail::where('institute_id', $iid)
            ->where('status', 'active')
            ->get();
        $batches = BatchDetail::where('institute_id', $iid)
            ->where('status', 'active')
            ->get();
        $savedFields = AdmissionFormField::where('institute_id', $iid)
            ->get()
            ->keyBy('field_key');

        return view('institute.enrollment.new', compact('courses', 'batches', 'savedFields'));
    }

    // Step 1b — Find existing student
    public function findStudent(Request $request)
    {
        $request->validate(['search' => 'required|string']);
        $search = trim($request->search);
        $iid    = $this->instituteId();

        $user = User::where('institute_id', $iid)
            ->where('role', 'student')
            ->where(function ($q) use ($search) {
                $q->where('mobile', $search)
                  ->orWhere('user_id', $search);
            })
            ->with('profile')
            ->first();

        if (!$user) {
            return back()->withErrors(['search' => 'Student not found.'])->withInput();
        }

        // Check if already enrolled in selected course this session
        $courses  = CourseDetail::where('institute_id', $iid)->where('status', 'active')->get();
        $batches  = BatchDetail::where('institute_id', $iid)->where('status', 'active')->get();
        $sessionId = $this->activeSessionId();

        return view('institute.enrollment.existing', compact('user', 'courses', 'batches', 'sessionId'));
    }

    // Step 1b — Existing student: create course_book
    public function storeNew(Request $request)
    {
        $iid       = $this->instituteId();
        $sessionId = $this->activeSessionId();

        $data = $request->validate([
            'course_id'  => 'required|exists:course_details,id',
            'batch_id'   => 'nullable|exists:batch_details,id',
            'user_id'    => 'nullable|exists:users,id',  // existing student
            // New student fields
            'name'       => 'required_without:user_id|string|max:100',
            'mobile'     => 'required_without:user_id|string|max:15',
        ]);

        // New student
        if (!$request->user_id) {
            // Duplicate check
            $exists = User::where('institute_id', $iid)
                          ->where('mobile', $data['mobile'])
                          ->exists();
            if ($exists) {
                return back()->withErrors(['mobile' => 'A student with this mobile already exists.'])->withInput();
            }

            $userId = $this->generateUserId($iid);
            $user = User::create([
                'institute_id' => $iid,
                'user_id'      => $userId,
                'mobile'       => $data['mobile'],
                'password'     => Hash::make(Str::random(10)),
                'role'         => 'student',
                'status'       => 'active',
            ]);
            UserProfile::create(['user_id' => $user->id, 'name' => $data['name']]);
            StudentWallet::create(['user_id' => $user->id, 'institute_id' => $iid, 'balance' => 0]);
        } else {
            $user = User::findOrFail($data['user_id']);
        }

        // Duplicate enrollment check
        $alreadyEnrolled = CourseBook::where('user_id', $user->id)
            ->where('course_id', $data['course_id'])
            ->when(Schema::hasColumn('course_books', 'session_id'), fn ($query) => $query->where('session_id', $sessionId))
            ->whereIn('status', ['OPEN','RUN'])
            ->exists();

        if ($alreadyEnrolled) {
            return back()->withErrors(['course_id' => 'Student is already enrolled in this course for the current session.'])->withInput();
        }

        // Create course_book OPEN
        $courseBookData = [
            'institute_id'  => $iid,
            'user_id'       => $user->id,
            'course_id'     => $data['course_id'],
            'batch_id'      => $data['batch_id'] ?? null,
            'enrollment_no' => $this->generateEnrollmentNo($iid),
            'status'        => 'OPEN',
        ];

        if (Schema::hasColumn('course_books', 'session_id')) {
            $courseBookData['session_id'] = $sessionId;
        }
        if (Schema::hasColumn('course_books', 'final_fee')) {
            $courseBookData['final_fee'] = 0;
        }
        if (Schema::hasColumn('course_books', 'fee')) {
            $courseBookData['fee'] = 0;
        }
        if (Schema::hasColumn('course_books', 'book_date')) {
            $courseBookData['book_date'] = now()->toDateString();
        }
        if (Schema::hasColumn('course_books', 'admission_by')) {
            $courseBookData['admission_by'] = Auth::guard('institute')->id();
        }

        $courseBook = CourseBook::create($courseBookData);

        return redirect()->route('institute.enrollment.profile', $courseBook);
    }

    // Step 2 — Profile form
    public function profileForm(CourseBook $courseBook)
    {
        $this->authorizeCourseBook($courseBook);
        $fields  = AdmissionFormField::where('institute_id', $this->instituteId())
                       ->where('is_active', true)
                       ->orderBy('sort_order')
                       ->get();
        $profile = $courseBook->student->profile;
        $education = $courseBook->student->education;
        $states = State::orderBy('name')->pluck('name');
        $educationField = AdmissionFormField::where('institute_id', $this->instituteId())
            ->where('field_key', 'education_details')
            ->first();
        $educationEnabled = !$educationField || $educationField->is_active;
        $educationRequired = (bool) ($educationField?->is_required);

        return view('institute.enrollment.profile', compact('courseBook', 'fields', 'profile', 'education', 'states', 'educationEnabled', 'educationRequired'));
    }

    // Step 2 save
    public function saveProfile(Request $request, CourseBook $courseBook)
    {
        $this->authorizeCourseBook($courseBook);
        $iid     = $this->instituteId();
        $fields  = AdmissionFormField::where('institute_id', $iid)
                       ->where('is_active', true)->get();
        $user = $courseBook->student;

        $rules = [];
        $nonProfileKeys = AdmissionFormField::nonProfileKeys();
        foreach ($fields as $f) {
            if (in_array($f->field_key, $nonProfileKeys, true)) {
                continue;
            }
            $rules[$f->field_key] = $f->is_required ? 'required' : 'nullable';
            if ($f->field_type === 'file') $rules[$f->field_key] .= '|image|max:2048';
            if ($f->field_key === 'mobile') {
                $rules[$f->field_key] = ($f->is_required ? 'required' : 'nullable') . '|string|max:15|unique:users,mobile,' . $user->id;
            }
            if ($f->field_key === 'email') {
                $rules[$f->field_key] = ($f->is_required ? 'required' : 'nullable') . '|email|max:100|unique:users,email,' . $user->id;
            }
            if ($f->field_key === 'state') {
                $rules[$f->field_key] = [
                    $f->is_required ? 'required' : 'nullable',
                    'string',
                    'max:100',
                    Rule::exists('states', 'name'),
                ];
            }
        }

        $validated = $request->validate($rules);

        $profile = $user->profile
                ?? UserProfile::make(['user_id' => $courseBook->user_id, 'name' => '']);

        foreach ($fields as $f) {
            if (in_array($f->field_key, $nonProfileKeys, true)) {
                continue;
            }
            if ($f->field_type === 'file') {
                if ($request->hasFile($f->field_key)) {
                    $path = $request->file($f->field_key)->store('student-photos', 'public');
                    $profile->{$f->field_key} = 'storage/' . $path;
                } elseif ($f->field_key === 'photo' && empty($profile->{$f->field_key})) {
                    $profile->{$f->field_key} = 'images/user.png';
                }
            } elseif (in_array($f->field_key, ['mobile', 'email'], true)) {
                $user->{$f->field_key} = $validated[$f->field_key] ?? null;
            } else {
                $profile->{$f->field_key} = $validated[$f->field_key] ?? null;
            }
        }
        $user->save();
        $profile->user_id = $courseBook->user_id;
        if (empty($profile->photo)) {
            $profile->photo = 'images/user.png';
        }
        $profile->save();

        $educationField = $fields->firstWhere('field_key', 'education_details');
        if ($educationField?->is_required && !$user->education()->exists()) {
            return back()->withErrors(['education_details' => 'At least one education record is required.'])->withInput();
        }

        return redirect()->route('institute.enrollment.fee', $courseBook);
    }

    // Step 3 — Fee form
    public function feeForm(CourseBook $courseBook)
    {
        $this->authorizeCourseBook($courseBook);
        $iid          = $this->instituteId();
        $feeStructure = $courseBook->course->feeStructures ?? collect();
        $plans        = PaymentPlanType::where('institute_id', $iid)->where('is_active', true)->get();

        return view('institute.enrollment.fee', compact('courseBook', 'feeStructure', 'plans'));
    }

    // Step 3 save
    public function saveFee(Request $request, CourseBook $courseBook)
    {
        $this->authorizeCourseBook($courseBook);
        $request->validate([
            'payment_plan_type_id' => 'required|exists:payment_plan_types,id',
            'fee_items'            => 'required|array',
            'fee_items.*.fee_type_id'   => 'nullable',
            'fee_items.*.fee_type_name' => 'required|string',
            'fee_items.*.original_amount' => 'required|numeric|min:0',
            'fee_items.*.discount_percent' => 'nullable|numeric|min:0|max:100',
            'fee_items.*.discount_amount'  => 'nullable|numeric|min:0',
            'fee_items.*.final_amount'     => 'required|numeric|min:0',
        ]);

        $plan    = PaymentPlanType::findOrFail($request->payment_plan_type_id);
        $totalFee = collect($request->fee_items)->sum('final_amount');

        DB::transaction(function () use ($request, $courseBook, $plan, $totalFee) {
            // Delete old snapshots
            $courseBook->feeSnapshots()->delete();
            $courseBook->paymentPlan()->delete();

            // Save fee snapshots
            foreach ($request->fee_items as $item) {
                EnrollmentFeeSnapshot::create([
                    'institute_id'    => $courseBook->institute_id,
                    'course_book_id'  => $courseBook->id,
                    'fee_type_id'     => $item['fee_type_id'] ?? null,
                    'fee_type_name'   => $item['fee_type_name'],
                    'original_amount' => $item['original_amount'],
                    'discount_percent'=> $item['discount_percent'] ?? 0,
                    'discount_amount' => $item['discount_amount'] ?? 0,
                    'final_amount'    => $item['final_amount'],
                ]);
            }

            // Save payment plan
            $monthly   = $plan->type === 'MONTHLY'
                ? round($totalFee / ($courseBook->course->duration_months ?: 1), 2)
                : null;
            $nextDue   = $plan->type === 'MONTHLY' ? now()->addMonth()->toDateString() : null;

            EnrollmentPaymentPlan::create([
                'institute_id'         => $courseBook->institute_id,
                'course_book_id'       => $courseBook->id,
                'payment_plan_type_id' => $plan->id,
                'plan_type'            => $plan->type,
                'monthly_amount'       => $monthly,
                'grace_days'           => $plan->grace_days,
                'late_fee_per_day'     => $plan->late_fee_per_day,
                'next_due_date'        => $nextDue,
            ]);

            // Update course_book final fee
            $courseBook->update(['final_fee' => $totalFee]);
        });

        return redirect()->route('institute.enrollment.preview', $courseBook);
    }

    // Step 4 — Preview
    public function preview(CourseBook $courseBook)
    {
        $this->authorizeCourseBook($courseBook);
        $iid     = $this->instituteId();
        $fields  = AdmissionFormField::where('institute_id', $iid)
                       ->where('is_active', true)->orderBy('sort_order')->get();
        $profile  = $courseBook->student->profile;
        $education = $courseBook->student->education;
        $snapshots = $courseBook->feeSnapshots;
        $plan      = $courseBook->paymentPlan;
        $educationField = AdmissionFormField::where('institute_id', $iid)
            ->where('field_key', 'education_details')
            ->first();
        $educationEnabled = !$educationField || $educationField->is_active;

        return view('institute.enrollment.preview',
            compact('courseBook', 'fields', 'profile', 'education', 'snapshots', 'plan', 'educationEnabled'));
    }

    // Step 5 — Confirm & finalize
    public function confirm(Request $request, CourseBook $courseBook)
    {
        $this->authorizeCourseBook($courseBook);

        DB::transaction(function () use ($courseBook) {
            $iid  = $courseBook->institute_id;
            $uid  = $courseBook->user_id;
            $fee  = $courseBook->final_fee;
            $now  = now();

            // course_book → RUN
            $courseBook->update(['status' => 'RUN', 'start_date' => $now->toDateString()]);

            // Student wallet debit (due set)
            $wallet = StudentWallet::where('user_id', $uid)->first();
            $opBal  = $wallet->balance;
            $clBal  = $opBal - $fee;
            $wallet->update(['balance' => $clBal]);

            // Student transaction
            StudentTransaction::create([
                'user_id'     => $uid,
                'institute_id'=> $iid,
                'description' => 'Course Enrollment: ' . $courseBook->course->name,
                'credit'      => 0,
                'debit'       => $fee,
                'type'        => 1,
                'date'        => $now->toDateString(),
                'c_date'      => $now,
                'op_bal'      => $opBal,
                'cl_bal'      => $clBal,
                'by_user_id'  => Auth::guard('institute')->id(),
            ]);
        });

        return redirect()->route('institute.fee-collect.show', $courseBook->user_id)
            ->with('success', 'Enrollment confirmed! Proceed with fee collection.');
    }

    // Education AJAX
    public function addEducation(Request $request)
    {
        $data = $request->validate([
            'user_id'          => 'required|exists:users,id',
            'examination'      => 'required|string|max:80',
            'board_university' => 'nullable|string|max:150',
            'passing_year'     => 'nullable|string|max:10',
            'marks_percentage' => 'nullable|string|max:10',
        ]);
        $edu = UserEducation::create($data);
        return response()->json(['success' => true, 'id' => $edu->id]);
    }

    public function removeEducation(UserEducation $education)
    {
        $education->delete();
        return response()->json(['success' => true]);
    }

    // Helpers
    private function generateUserId(int $iid): string
    {
        $count = User::where('institute_id', $iid)->where('role', 'student')->count() + 1;
        $inst  = \App\Models\Owner\Institute::find($iid);
        $code  = $inst?->unique_id ?? 'INST';
        return $code . '/STU/' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }

    private function generateEnrollmentNo(int $iid): string
    {
        $count = CourseBook::where('institute_id', $iid)->count() + 1;
        return 'ENR' . date('Y') . str_pad($count, 4, '0', STR_PAD_LEFT);
    }

    private function authorizeCourseBook(CourseBook $cb): void
    {
        if ($cb->institute_id !== $this->instituteId()) abort(403);
    }
}
