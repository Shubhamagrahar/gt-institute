<?php

namespace App\Http\Controllers\Franchise;

use App\Http\Controllers\Controller;
use App\Models\{
    AdmissionFormField, BatchDetail, CourseBook, CourseDetail,
    EnrollmentFeeSnapshot, EnrollmentPaymentPlan, FeeCollectDetail,
    InstituteEnrollmentCounter, InstituteSession, InstituteStudentTransaction,
    InstituteStudentWallet, PaymentPlanType, State, StudentTransaction,
    StudentWallet, User, UserProfile
};
use App\Services\InvoiceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, DB, Hash, Log, Schema};
use Illuminate\Support\Str;

class EnrollmentController extends Controller
{
    public function __construct(protected InvoiceService $invoiceService) {}

    // ── Helpers ──────────────────────────────────────────────────────────────

    private function franchiseUser()
    {
        return Auth::guard('institute')->user();
    }

    private function franchiseId(): int
    {
        return $this->franchiseUser()->franchise_id;
    }

    private function instituteId(): int
    {
        return $this->franchiseUser()->institute_id;
    }

    private function activeSessionId(): int
    {
        return InstituteSession::where('institute_id', $this->instituteId())
            ->where('is_active', true)
            ->value('id') ?? abort(422, 'No active session.');
    }

    private function authorizeCourseBook(CourseBook $courseBook): void
    {
        abort_if(
            $courseBook->institute_id !== $this->instituteId() ||
            $courseBook->franchise_id !== $this->franchiseId(),
            403
        );
    }

    // ── Pending Admissions ───────────────────────────────────────────────────

    public function pending()
    {
        $fid = $this->franchiseId();

        $allBooks = CourseBook::with(['student.profile', 'course', 'paymentPlan'])
            ->where('franchise_id', $fid)
            ->whereIn('status', ['OPEN', 'RUN'])
            ->latest()
            ->get()
            ->map(function (CourseBook $cb) {
                $paidAmount = (float) FeeCollectDetail::where('course_book_id', $cb->id)
                    ->whereNull('cancelled_at')->sum(FeeCollectDetail::amountColumn());
                $plan = $cb->paymentPlan;
                $requiredAmount = $plan
                    ? ($plan->plan_type === 'OTP' ? (float) $plan->total_fee : (float) ($plan->required_fee ?: 0))
                    : (float) $cb->final_fee;

                $cb->setAttribute('paid_amount', $paidAmount);
                $cb->setAttribute('required_amount', $requiredAmount);
                $cb->setAttribute('details_complete', (bool) $cb->profile_completed_at);
                $cb->setAttribute('admission_ready', $cb->profile_completed_at && $paidAmount + 0.01 >= $requiredAmount);
                $cb->setAttribute('plan_code', $plan?->plan_type);
                return $cb;
            });

        $openBooks     = $allBooks->where('status', 'OPEN')->values();
        $admittedBooks = $allBooks->where('status', 'RUN')->values();

        return view('franchise.enrollment.pending', compact('openBooks', 'admittedBooks'));
    }

    // ── New Admission ────────────────────────────────────────────────────────

    public function newStudent()
    {
        $iid     = $this->instituteId();
        $courses = CourseDetail::with(['courseType', 'feeStructures.feeType'])
            ->where('institute_id', $iid)->where('status', 'active')->orderBy('name')->get();
        $batches = BatchDetail::where('institute_id', $iid)->where('status', 'active')->orderBy('name')->get();
        $states  = State::orderBy('name')->pluck('name');

        $districtsByState = Schema::hasTable('districts')
            ? \App\Models\District::select('districts.name as district_name', 'states.name as state_name')
                ->join('states', 'states.id', '=', 'districts.state_id')
                ->orderBy('states.name')->orderBy('districts.name')->get()
                ->groupBy('state_name')
                ->map(fn ($rows) => $rows->pluck('district_name')->values())->toArray()
            : [];

        $courseCatalog = $courses->map(fn ($c) => [
            'id'         => $c->id,
            'name'       => $c->name,
            'total_fee'  => round((float) $c->fee + $c->feeStructures->sum('amount'), 2),
            'fee_items'  => collect([[
                'fee_type_id'   => null,
                'fee_type_name' => 'Course Fee',
                'amount'        => round((float) $c->fee, 2),
                'is_mandatory'  => true,
            ]])->merge($c->feeStructures->map(fn ($fs) => [
                'fee_type_id'   => $fs->fee_type_id,
                'fee_type_name' => $fs->fee_type_name,
                'amount'        => round((float) $fs->amount, 2),
                'is_mandatory'  => (bool) $fs->feeType?->is_mandatory,
            ]))->values()->all(),
        ])->values();

        return view('franchise.enrollment.new', compact(
            'courses', 'batches', 'states', 'districtsByState', 'courseCatalog'
        ));
    }

    public function storeNew(Request $request)
    {
        $iid       = $this->instituteId();
        $fid       = $this->franchiseId();
        $sessionId = $this->activeSessionId();

        $data = $request->validate([
            'name'       => 'required|string|max:100',
            'mobile'     => 'required|string|max:15|unique:users,mobile',
            'email'      => 'nullable|email|max:100|unique:users,email',
            'course_id'  => ['required', \Illuminate\Validation\Rule::exists('course_details', 'id')
                ->where(fn ($q) => $q->where('institute_id', $iid))],
            'batch_id'   => ['nullable', \Illuminate\Validation\Rule::exists('batch_details', 'id')
                ->where(fn ($q) => $q->where('institute_id', $iid))],
            'dob'        => 'nullable|date',
            'gender'     => 'nullable|in:Male,Female,Other',
            'father_name'=> 'nullable|string|max:100',
            'address'    => 'nullable|string',
            'state'      => 'nullable|string|max:100',
            'district'   => 'nullable|string|max:100',
            'city'       => 'nullable|string|max:100',
            'pin_code'   => 'nullable|string|max:10',
        ]);

        DB::transaction(function () use ($data, $iid, $fid, $sessionId) {
            $userId = 'S' . $iid . now()->format('ymdHis') . strtoupper(Str::random(4));
            $user = User::create([
                'institute_id'  => $iid,
                'franchise_id'  => $fid,
                'user_id'       => $userId,
                'mobile'        => $data['mobile'],
                'email'         => $data['email'] ?? null,
                'password'      => Hash::make(Str::random(10)),
                'role'          => 'student',
                'user_type'     => 'student',
                'owner_type'    => 'franchise',
                'status'        => 'active',
            ]);

            UserProfile::create([
                'user_id'     => $user->id,
                'institute_id'=> $iid,
                'name'        => $data['name'],
                'photo'       => 'images/user.svg',
                'father_name' => $data['father_name'] ?? null,
                'dob'         => $data['dob'] ?? null,
                'gender'      => $data['gender'] ?? null,
                'address'     => $data['address'] ?? null,
                'state'       => $data['state'] ?? null,
                'district'    => $data['district'] ?? null,
                'city'        => $data['city'] ?? null,
                'pin_code'    => $data['pin_code'] ?? null,
                'r_date'      => now()->toDateString(),
                'issue_date'  => now()->toDateString(),
            ]);

            $courseBook = CourseBook::create([
                'institute_id'        => $iid,
                'franchise_id'        => $fid,
                'session_id'          => $sessionId,
                'user_id'             => $user->id,
                'course_id'           => $data['course_id'],
                'batch_id'            => $data['batch_id'] ?? null,
                'enrollment_no'       => null,
                'fee'                 => 0,
                'final_fee'           => 0,
                'book_date'           => now()->toDateString(),
                'status'              => 'OPEN',
                'booking_mode'        => 'full',
                'profile_completed_at'=> now(),
                'admission_by'        => Auth::guard('institute')->id(),
            ]);

            StudentWallet::firstOrCreate(
                ['user_id' => $user->id],
                ['institute_id' => $iid, 'franchise_id' => $fid, 'owner_type' => 'franchise', 'balance' => 0]
            );

            return $courseBook->fresh(['student.profile', 'course']);
        });

        return redirect()->route('franchise.enrollment.pending')
            ->with('success', 'Seat booked successfully for ' . $data['name'] . '. Complete profile and payment to finalize.');
    }

    // ── Profile ──────────────────────────────────────────────────────────────

    public function profileForm(CourseBook $courseBook)
    {
        $this->authorizeCourseBook($courseBook);
        $iid       = $this->instituteId();
        $fields    = AdmissionFormField::where('institute_id', $iid)->where('is_active', true)->orderBy('sort_order')->get();
        $profile   = $courseBook->student->profile;
        $education = $courseBook->student->education;
        $states    = State::orderBy('name')->pluck('name');
        $districtsByState = Schema::hasTable('districts')
            ? \App\Models\District::select('districts.name as district_name', 'states.name as state_name')
                ->join('states', 'states.id', '=', 'districts.state_id')
                ->orderBy('states.name')->orderBy('districts.name')->get()
                ->groupBy('state_name')
                ->map(fn ($rows) => $rows->pluck('district_name')->values())->toArray()
            : [];
        $educationField   = AdmissionFormField::where('institute_id', $iid)->where('field_key', 'education_details')->first();
        $educationEnabled = !$educationField || $educationField->is_active;
        $educationRequired = (bool) ($educationField?->is_required);

        return view('franchise.enrollment.profile', compact(
            'courseBook', 'fields', 'profile', 'education', 'states', 'districtsByState', 'educationEnabled', 'educationRequired'
        ));
    }

    public function saveProfile(Request $request, CourseBook $courseBook)
    {
        $this->authorizeCourseBook($courseBook);
        $controller = app(\App\Http\Controllers\Institute\EnrollmentController::class);
        $result = $controller->saveProfile($request, $courseBook);
        if ($result instanceof \Illuminate\Http\RedirectResponse) {
            return redirect()->route('franchise.enrollment.fee', $courseBook)
                ->with('success', 'Profile saved successfully.');
        }
        return $result;
    }

    // ── Fee ──────────────────────────────────────────────────────────────────

    public function feeForm(CourseBook $courseBook)
    {
        $this->authorizeCourseBook($courseBook);

        if ($courseBook->paymentPlan) {
            return redirect()->route('franchise.enrollment.payment-complete', $courseBook);
        }

        $iid  = $this->instituteId();
        $courseBook->loadMissing(['course.feeStructures.feeType']);
        $feeStructure = collect($courseBook->course->feeStructures)->map(fn ($fs) => (object)[
            'fee_type_id'   => $fs->fee_type_id,
            'fee_type_name' => $fs->fee_type_name,
            'amount'        => (float) $fs->amount,
            'is_mandatory'  => (bool) $fs->feeType?->is_mandatory,
        ]);

        // Add base course fee
        $baseFee = (object)['fee_type_id' => null, 'fee_type_name' => 'Course Fee', 'amount' => (float) $courseBook->course->fee, 'is_mandatory' => true];
        $feeStructure = collect([$baseFee])->merge($feeStructure)->values();

        $totalFee = $feeStructure->sum('amount');
        if ($totalFee <= 0) {
            $courseBook->update(['fee' => 0, 'final_fee' => 0]);
            return redirect()->route('franchise.enrollment.preview', $courseBook);
        }

        $plans = $this->activePlans($iid);

        return view('franchise.enrollment.fee', compact('courseBook', 'feeStructure', 'plans'));
    }

    public function saveFee(Request $request, CourseBook $courseBook)
    {
        $this->authorizeCourseBook($courseBook);
        $controller = app(\App\Http\Controllers\Institute\EnrollmentController::class);
        $result = $controller->saveFee($request, $courseBook);
        if ($result instanceof \Illuminate\Http\RedirectResponse) {
            return redirect()->route('franchise.enrollment.payment-complete', $courseBook);
        }
        return $result;
    }

    // ── Preview / Confirm ────────────────────────────────────────────────────

    public function preview(CourseBook $courseBook)
    {
        $this->authorizeCourseBook($courseBook);
        $controller = app(\App\Http\Controllers\Institute\EnrollmentController::class);
        // Pass franchise layout info via view share
        \Illuminate\Support\Facades\View::share('franchiseMode', true);
        return $controller->preview($courseBook);
    }

    public function confirm(Request $request, CourseBook $courseBook)
    {
        $this->authorizeCourseBook($courseBook);
        $controller = app(\App\Http\Controllers\Institute\EnrollmentController::class);
        // Override redirect after confirm
        $courseBook->update(['profile_completed_at' => $courseBook->profile_completed_at ?? now()]);

        // Force finalize
        if ($courseBook->status !== 'RUN') {
            if (! $courseBook->enrollment_no) {
                $counter = InstituteEnrollmentCounter::where('institute_id', $courseBook->institute_id)->lockForUpdate()->first();
                if (! $counter) {
                    InstituteEnrollmentCounter::create(['institute_id' => $courseBook->institute_id, 'last_enrollment_no' => 0]);
                    $counter = InstituteEnrollmentCounter::where('institute_id', $courseBook->institute_id)->lockForUpdate()->firstOrFail();
                }
                $counter->last_enrollment_no++;
                $counter->save();
                $code = Auth::guard('institute')->user()->institute?->unique_id ?? 'INST';
                $enrollmentNo = $code . '/ENR/' . str_pad((string) $counter->last_enrollment_no, 4, '0', STR_PAD_LEFT);
                $courseBook->update(['enrollment_no' => $enrollmentNo]);
            }
            $courseBook->update(['status' => 'RUN', 'start_date' => $courseBook->start_date ?? now()->toDateString()]);
        }

        return redirect()->route('franchise.enrollment.pending')
            ->with('success', 'Admission confirmed! Enrollment No: ' . $courseBook->fresh()->enrollment_no);
    }

    // ── Payment Complete ─────────────────────────────────────────────────────

    public function paymentComplete(CourseBook $courseBook)
    {
        $this->authorizeCourseBook($courseBook);
        $courseBook->loadMissing(['student.profile', 'course', 'batch', 'paymentPlan', 'feeSnapshots']);
        $amountColumn = FeeCollectDetail::amountColumn();
        $paidTotal    = (float) FeeCollectDetail::where('course_book_id', $courseBook->id)->whereNull('cancelled_at')->sum($amountColumn);
        $fees         = FeeCollectDetail::where('course_book_id', $courseBook->id)->orderByDesc('id')->paginate(15);
        $transactions = StudentTransaction::where('user_id', $courseBook->user_id)->where('institute_id', $courseBook->institute_id)->orderBy('id')->get();
        $plan         = $courseBook->paymentPlan;
        $due          = max(round((float) $courseBook->final_fee - $paidTotal, 2), 0);
        $lateFee      = 0;
        $modalDefaultAmount = match ($plan?->plan_type) {
            'MONTHLY' => round((float) ($plan->monthly_amount ?? 0), 2),
            'OTP'     => $due,
            default   => null,
        };

        return view('franchise.enrollment.payment-complete', compact(
            'courseBook', 'fees', 'paidTotal', 'transactions', 'due', 'lateFee', 'modalDefaultAmount'
        ));
    }

    public function addPayment(Request $request, CourseBook $courseBook)
    {
        $this->authorizeCourseBook($courseBook);
        $controller = app(\App\Http\Controllers\Institute\EnrollmentController::class);
        $result = $controller->addPayment($request, $courseBook);
        // Override redirect target; keep any flash set by institute controller
        if ($result instanceof \Illuminate\Http\RedirectResponse) {
            return redirect()->route('franchise.enrollment.payment-complete', $courseBook);
        }
        return $result;
    }

    public function receiptA4(CourseBook $courseBook, FeeCollectDetail $fee)
    {
        $this->authorizeCourseBook($courseBook);
        $courseBook->loadMissing(['student.profile', 'course', 'batch']);
        $institute = Auth::guard('institute')->user()->institute;
        return view('institute.enrollment.receipt-a4', compact('courseBook', 'fee', 'institute'));
    }

    public function receiptThermal(CourseBook $courseBook, FeeCollectDetail $fee)
    {
        $this->authorizeCourseBook($courseBook);
        $courseBook->loadMissing(['student.profile', 'course', 'batch']);
        $institute = Auth::guard('institute')->user()->institute;
        return view('institute.enrollment.receipt-thermal', compact('courseBook', 'fee', 'institute'));
    }

    public function cancelFee(Request $request, CourseBook $courseBook, FeeCollectDetail $fee)
    {
        $this->authorizeCourseBook($courseBook);
        $controller = app(\App\Http\Controllers\Institute\EnrollmentController::class);
        return $controller->cancelFee($request, $courseBook, $fee);
    }

    // ── Private helpers ──────────────────────────────────────────────────────

    private function activePlans(int $iid)
    {
        $defaults = ['OTP' => 'One Time Payment', 'PART' => 'Partial Payment', 'MONTHLY' => 'Monthly Payment'];
        foreach ($defaults as $type => $name) {
            PaymentPlanType::firstOrCreate(
                ['institute_id' => $iid, 'type' => $type],
                ['name' => $name, 'grace_days' => 0, 'late_fee_per_day' => 0, 'is_active' => true]
            );
        }
        return PaymentPlanType::where('institute_id', $iid)->where('is_active', true)
            ->orderByRaw("CASE type WHEN 'OTP' THEN 1 WHEN 'PART' THEN 2 WHEN 'MONTHLY' THEN 3 ELSE 4 END")->get();
    }
}
