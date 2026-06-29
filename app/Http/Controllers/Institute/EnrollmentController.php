<?php

namespace App\Http\Controllers\Institute;

use App\Http\Controllers\Controller;
use App\Models\{
    AdmissionFormField,
    BatchDetail,
    ChannelPartner,
    CourseBook,
    CourseDetail,
    District,
    EnrollmentFeeSnapshot,
    EnrollmentPaymentPlan,
    FeeCollectDetail,
    Franchise,
    FranchiseCourseCharge,
    FranchiseTransaction,
    FranchiseWallet,
    InstituteEnrollmentCounter,
    InstituteSession,
    InstituteStudentTransaction,
    InstituteStudentWallet,
    PaymentPlanType,
    State,
    StudentTransaction,
    StudentWallet,
    User,
    UserEducation,
    UserProfile
};
use App\Services\InvoiceService;
use App\Mail\SeatBookingConfirmationMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, DB, Hash, Log, Mail, Schema};
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class EnrollmentController extends Controller
{
    public function __construct(protected InvoiceService $invoiceService) {}

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

    private function defaultPhotoPath(): string
    {
        return 'images/user.svg';
    }

    public function choose()
    {
        return view('institute.enrollment.choose');
    }

    public function pending()
    {
        $iid = $this->instituteId();

        $openBooks = CourseBook::with(['student.profile', 'course', 'batch'])
            ->where('institute_id', $iid)
            ->where('status', 'OPEN')
            ->latest()
            ->get()
            ->map(function (CourseBook $courseBook) {
                $detailsDone = (bool) $courseBook->profile_completed_at;
                // Stage: 1=booked only, 2=details filled (waiting payment)
                $stage = $detailsDone ? 2 : 1;
                $courseBook->setAttribute('details_complete', $detailsDone);
                $courseBook->setAttribute('stage', $stage);
                return $courseBook;
            });

        $countTotal          = $openBooks->count();
        $countDetailsPending = $openBooks->where('details_complete', false)->count();
        $countPaymentPending = $openBooks->where('details_complete', true)->count();

        $expiredBooks = CourseBook::with(['student.profile', 'course'])
            ->where('institute_id', $iid)
            ->where('status', 'EXPIRED')
            ->latest('book_date')
            ->limit(50)
            ->get();

        return view('institute.enrollment.pending', compact(
            'openBooks', 'countTotal', 'countDetailsPending', 'countPaymentPending',
            'expiredBooks'
        ));
    }

    public function newStudent(Request $request)
    {
        $enquiryPrefill = $this->resolveEnquiryPrefill($request->get('enquiry_id'));
        return view('institute.enrollment.new', $this->admissionFormData() + ['enquiryPrefill' => $enquiryPrefill]);
    }

    public function quickStudent(Request $request)
    {
        $enquiryPrefill = $this->resolveEnquiryPrefill($request->get('enquiry_id'));
        return view('institute.enrollment.quick', $this->admissionFormData() + ['enquiryPrefill' => $enquiryPrefill]);
    }

    private function resolveEnquiryPrefill(?string $enquiryId): ?array
    {
        if (!$enquiryId) return null;
        $enquiry = \App\Models\Enquiry::with('course')
            ->where('institute_id', $this->instituteId())
            ->where('status', 'OPEN')
            ->find((int) $enquiryId);
        if (!$enquiry) return null;
        return [
            'enquiry_id' => $enquiry->id,
            'name'       => $enquiry->name,
            'mobile'     => $enquiry->mobile,
            'email'      => $enquiry->email ?? '',
            'course_id'  => $enquiry->course_id,
            'course_name' => $enquiry->course?->name ?? '',
        ];
    }

    public function findStudent(Request $request)
    {
        if ($request->isMethod('GET')) {
            return redirect()->route('institute.enrollment.choose');
        }
        $request->validate(['search' => 'required|string']);
        $search = trim($request->search);
        $iid = $this->instituteId();

        $user = User::where('institute_id', $iid)
            ->where('role', 'student')
            ->where(function ($query) use ($search, $iid) {
                $query->where('mobile', $search)
                    ->orWhere('user_id', $search)
                    ->orWhereHas('enrollments', function ($enrollments) use ($search, $iid) {
                        $enrollments->where('institute_id', $iid)
                            ->where('enrollment_no', $search);
                    });
            })
            ->with('profile')
            ->first();

        if (! $user) {
            return back()->withErrors(['search' => 'Student not found.'])->withInput();
        }

        $courses = CourseDetail::where('institute_id', $iid)->where('status', 'active')->get();
        $batches = BatchDetail::where('institute_id', $iid)->where('status', 'active')->get();
        $sessionId = $this->activeSessionId();

        return view('institute.enrollment.existing', compact('user', 'courses', 'batches', 'sessionId'));
    }

    public function validateField(Request $request)
    {
        $data = $request->validate([
            'field' => 'required|in:mobile,email',
            'value' => 'required|string|max:100',
        ]);

        $query = User::where($data['field'], trim($data['value']));

        return response()->json([
            'exists' => $query->exists(),
        ]);
    }

    public function editBooking(User $student, CourseBook $courseBook)
    {
        $this->authorizeStudentCourseBook($student, $courseBook);

        $iid = $this->instituteId();
        $courses = CourseDetail::where('institute_id', $iid)->where('status', 'active')->orderBy('name')->get();
        $batches = BatchDetail::where('institute_id', $iid)->where('status', 'active')->orderBy('name')->get();
        $plans = $this->activePaymentPlans($iid);
        $currentPlanId = $courseBook->paymentPlan?->payment_plan_type_id;

        return view('institute.students.enrollment-edit', compact('student', 'courseBook', 'courses', 'batches', 'plans', 'currentPlanId'));
    }

    public function updateBooking(Request $request, User $student, CourseBook $courseBook)
    {
        $this->authorizeStudentCourseBook($student, $courseBook);
        $iid = $this->instituteId();

        $data = $request->validate([
            'course_id' => [
                'required',
                Rule::exists('course_details', 'id')->where(fn ($query) => $query->where('institute_id', $iid)),
            ],
            'batch_id' => [
                'nullable',
                Rule::exists('batch_details', 'id')->where(fn ($query) => $query->where('institute_id', $iid)),
            ],
            'payment_plan_type_id' => [
                'nullable',
                Rule::exists('payment_plan_types', 'id')->where(fn ($query) => $query->where('institute_id', $iid)->where('is_active', true)),
            ],
        ]);

        $course = CourseDetail::with(['feeStructures.feeType'])
            ->where('institute_id', $iid)
            ->findOrFail($data['course_id']);
        $plan = PaymentPlanType::where('institute_id', $iid)->findOrFail($data['payment_plan_type_id']);

        $this->assertCourseBookingAllowed(
            $student->id,
            $course->id,
            $data['batch_id'] ?? null,
            $courseBook->id
        );

        $courseSummary = $this->courseCatalogItem($course);
        $oldFee = (float) $courseBook->final_fee;
        $newFee = (float) $courseSummary['total_fee'];
        $feeDiff = round($newFee - $oldFee, 2);
        $existingPlan = $courseBook->paymentPlan;
        $paidAmount = (float) FeeCollectDetail::where('course_book_id', $courseBook->id)->sum(FeeCollectDetail::amountColumn());
        $monthlyAmount = $plan->type === 'MONTHLY'
            ? round($newFee / max(1, (int) ($course->duration ?: 1)), 2)
            : null;
        $requiredFee = $plan->type === 'OTP' ? $newFee : round((float) $courseSummary['required_fee'], 2);
        $remainingFee = max(round($newFee - $paidAmount, 2), 0);

        DB::transaction(function () use ($courseBook, $data, $course, $courseSummary, $plan, $existingPlan, $feeDiff, $newFee, $monthlyAmount, $requiredFee, $remainingFee) {
            $courseBook->update([
                'course_id' => $course->id,
                'batch_id' => $data['batch_id'] ?? null,
                'fee' => $newFee,
                'final_fee' => $newFee,
            ]);

            $courseBook->feeSnapshots()->delete();
            foreach ($courseSummary['fee_items'] as $item) {
                EnrollmentFeeSnapshot::create([
                    'institute_id' => $courseBook->institute_id,
                    'course_book_id' => $courseBook->id,
                    'fee_type_id' => $item['fee_type_id'],
                    'fee_type_name' => $item['fee_type_name'],
                    'original_amount' => $item['amount'],
                    'discount_percent' => 0,
                    'discount_amount' => 0,
                    'final_amount' => $item['amount'],
                ]);
            }

            $courseBook->paymentPlan()->delete();
            EnrollmentPaymentPlan::create([
                'institute_id' => $courseBook->institute_id,
                'course_book_id' => $courseBook->id,
                'payment_plan_type_id' => $plan->id,
                'plan_type' => $plan->type,
                'total_fee' => $newFee,
                'required_fee' => $requiredFee,
                'first_payment_amount' => min((float) ($existingPlan?->first_payment_amount ?? 0), $newFee),
                'remaining_fee' => $remainingFee,
                'monthly_amount' => $monthlyAmount,
                'grace_days' => $plan->grace_days,
                'late_fee_per_day' => $plan->late_fee_per_day,
                'next_due_date' => $plan->type === 'MONTHLY' ? now()->addMonth()->toDateString() : null,
            ]);

            if (abs($feeDiff) >= 0.01) {
                $this->applyCourseChangeAdjustment($courseBook, $feeDiff, $course->name);
            }
        });

        $courseBook = $courseBook->fresh(['student.profile', 'paymentPlan', 'course']);
        $this->syncAdmissionStatus($courseBook);

        return redirect()->route('institute.students.show', $student)
            ->with('success', 'Student course booking updated successfully.');
    }

    public function storeNew(Request $request)
    {
        if ($request->filled('user_id')) {
            return $this->storeExistingStudent($request);
        }

        $iid = $this->instituteId();
        $sessionId = $this->activeSessionId();
        $fields = AdmissionFormField::where('institute_id', $iid)->get()->keyBy('field_key');
        $rules = $this->wizardRules($iid, $fields);
        $data = $request->validate($rules);

        $course = CourseDetail::with(['feeStructures.feeType'])
            ->where('institute_id', $iid)
            ->where('id', $data['course_id'])
            ->firstOrFail();
        $courseShortCode = $this->toCourseCode($request->input('course_short_code', ''), $course->name);
        $plainPassword = Str::random(10);
        $result = DB::transaction(function () use (
            $data,
            $iid,
            $sessionId,
            $course,
            $courseShortCode,
            $plainPassword
        ) {
            $userId = $this->generateUserId($iid);
            $user = User::create([
                'institute_id' => $iid,
                'user_id' => $userId,
                'mobile' => $data['mobile'],
                'email' => $data['email'] ?? null,
                'password' => Hash::make($plainPassword),
                'role' => 'student',
                'user_type' => 'student',
                'franchise_id' => null,
                'channel_partner_id' => $data['channel_partner_id'] ?? null,
                'owner_type' => 'institute',
                'status' => 'active',
            ]);

            $profile = new UserProfile([
                'user_id' => $user->id,
                'institute_id' => $iid,
                'name' => $data['name'],
                'photo' => $this->defaultPhotoPath(),
                'father_name' => $data['father_name'] ?? null,
                'mother_name' => $data['mother_name'] ?? null,
                'guardian_name' => $data['guardian_name'] ?? null,
                'guardian_relation' => $data['guardian_relation'] ?? null,
                'guardian_mobile' => $data['guardian_mobile'] ?? null,
                'guardian_occupation' => $data['guardian_occupation'] ?? null,
                'dob' => $data['dob'] ?? null,
                'gender' => $data['gender'] ?? null,
                'category' => $data['category'] ?? null,
                'religion' => $data['religion'] ?? null,
                'nationality' => $data['nationality'] ?? null,
                'whatsapp_no' => $data['whatsapp_no'] ?? null,
                'alternate_mobile' => $data['alternate_mobile'] ?? null,
                'aadhar_no' => $data['aadhar_no'] ?? null,
                'pan_no' => $data['pan_no'] ?? null,
                'blood_group' => $data['blood_group'] ?? null,
                'employment_status' => $data['employment_status'] ?? null,
                'computer_literacy' => $data['computer_literacy'] ?? null,
                'qualification' => $data['qualification'] ?? null,
                'address' => $data['address'] ?? null,
                'permanent_address' => $data['permanent_address'] ?? null,
                'state' => $data['state'] ?? null,
                'district' => $data['district'] ?? null,
                'city' => $data['city'] ?? null,
                'pin_code' => $data['pin_code'] ?? null,
                'permanent_state' => $data['permanent_state'] ?? null,
                'permanent_district' => $data['permanent_district'] ?? null,
                'permanent_city' => $data['permanent_city'] ?? null,
                'permanent_pin_code' => $data['permanent_pin_code'] ?? null,
                'fee_collect_type' => null,
                'monthly_fee' => 0,
                'daily_late_fee' => 0,
                'late_fee_count_after' => 0,
                'next_fee_date' => null,
                'issue_date' => now()->toDateString(),
                'r_date' => now()->toDateString(),
            ]);
            $profile->save();

            foreach ($data['education'] ?? [] as $education) {
                if (blank($education['examination'] ?? null)) {
                    continue;
                }

                UserEducation::create([
                    'user_id' => $user->id,
                    'institute_id' => $iid,
                    'franchise_id' => null,
                    'examination' => $education['examination'],
                    'institute_name' => $education['institute_name'] ?? null,
                    'board_university' => $education['board_university'] ?? null,
                    'passing_year' => $education['passing_year'] ?? null,
                    'division' => $education['division'] ?? null,
                    'marks_percentage' => $education['marks_percentage'] ?? null,
                ]);
            }

            $courseSummary = $this->courseCatalogItem($course);
            $totalFee = (float) $courseSummary['total_fee'];

            $courseBook = CourseBook::create([
                'institute_id' => $iid,
                'franchise_id' => null,
                'channel_partner_id' => $data['channel_partner_id'] ?? null,
                'session_id' => $sessionId,
                'user_id' => $user->id,
                'course_id' => $course->id,
                'course_code' => $courseShortCode,
                'batch_id' => $data['batch_id'] ?? null,
                'enrollment_no' => null,
                'fee' => $totalFee,
                'final_fee' => $totalFee,
                'book_date' => now()->toDateString(),
                'start_date' => null,
                'status' => 'OPEN',
                'booking_mode' => 'full',
                'profile_completed_at' => now(),
                'admission_by' => Auth::guard('institute')->id(),
            ]);

            foreach ($courseSummary['fee_items'] as $item) {
                EnrollmentFeeSnapshot::create([
                    'institute_id'    => $iid,
                    'course_book_id'  => $courseBook->id,
                    'fee_type_id'     => $item['fee_type_id'],
                    'fee_type_name'   => $item['fee_type_name'],
                    'original_amount' => $item['amount'],
                    'discount_percent'=> 0,
                    'discount_amount' => 0,
                    'final_amount'    => $item['amount'],
                ]);
            }

            $wallet = $this->ensureStudentWallet($user);
            if ($totalFee > 0) {
                $opBal = (float) $wallet->balance;
                $clBal = $opBal - $totalFee;
                $wallet->update(['balance' => $clBal]);
                StudentTransaction::create([
                    'user_id'      => $user->id,
                    'institute_id' => $iid,
                    'franchise_id' => null,
                    'owner_type'   => 'institute',
                    'description'  => 'Course Booked: ' . $course->name,
                    'credit'       => 0,
                    'debit'        => $totalFee,
                    'type'         => 1,
                    'ref_type'     => 'course_book',
                    'ref_id'       => $courseBook->id,
                    'date'         => now()->toDateString(),
                    'c_date'       => now(),
                    'op_bal'       => $opBal,
                    'cl_bal'       => $clBal,
                    'by_user_id'   => Auth::guard('institute')->id(),
                ]);
            }

            return [
                'courseBook' => $courseBook->fresh(['student.profile', 'course']),
                'user' => $user->fresh(['profile']),
            ];
        });

        $this->sendSeatBookingEmail($result['user'], $result['courseBook'], $plainPassword);
        $this->markEnquiryConverted($request->input('enquiry_id'), $result['courseBook']->id);

        return redirect()->route('institute.enrollment.pending')
            ->with('success', 'Seat booked successfully. Admission will complete after profile and payment are finished.');
    }

    public function storeQuick(Request $request)
    {
        $iid = $this->instituteId();
        $sessionId = $this->activeSessionId();
        $fields = AdmissionFormField::where('institute_id', $iid)->get()->keyBy('field_key');
        $rules = $this->quickWizardRules($iid, $fields);
        $data = $request->validate($rules);

        $course = CourseDetail::with(['feeStructures.feeType'])
            ->where('institute_id', $iid)
            ->where('id', $data['course_id'])
            ->firstOrFail();
        $courseSummary = $this->courseCatalogItem($course);

        $quickCourseCode = $this->toCourseCode($request->input('course_short_code', ''), $course->name);
        $quickPlainPassword = Str::random(10);
        $result = DB::transaction(function () use ($data, $iid, $sessionId, $course, $courseSummary, $fields, $quickCourseCode, $quickPlainPassword) {
            $userId = $this->generateUserId($iid);
            $user = User::create([
                'institute_id' => $iid,
                'user_id' => $userId,
                'mobile' => $data['mobile'],
                'email' => $data['email'] ?? null,
                'password' => Hash::make($quickPlainPassword),
                'role' => 'student',
                'user_type' => 'student',
                'franchise_id' => null,
                'channel_partner_id' => $data['channel_partner_id'] ?? null,
                'owner_type' => 'institute',
                'status' => 'active',
            ]);

            $wallet = $this->ensureStudentWallet($user);

            $profileData = [
                'user_id' => $user->id,
                'institute_id' => $iid,
                'name' => $data['name'],
                'photo' => $this->defaultPhotoPath(),
                'r_date' => now()->toDateString(),
            ];

            foreach ($fields as $field) {
                if (! $field->quick_is_active || in_array($field->field_key, ['name', 'mobile', 'education_details'], true)) {
                    continue;
                }

                if ($field->field_key === 'email') {
                    continue;
                }

                $profileData[$field->field_key] = $data[$field->field_key] ?? null;
            }

            UserProfile::create($profileData);

            $courseBook = CourseBook::create([
                'institute_id' => $iid,
                'franchise_id' => null,
                'channel_partner_id' => $data['channel_partner_id'] ?? null,
                'session_id' => $sessionId,
                'user_id' => $user->id,
                'course_id' => $course->id,
                'course_code' => $quickCourseCode,
                'batch_id' => $data['batch_id'] ?? null,
                'enrollment_no' => null,
                'fee' => $courseSummary['total_fee'],
                'final_fee' => $courseSummary['total_fee'],
                'book_date' => now()->toDateString(),
                'status' => 'OPEN',
                'booking_mode' => 'quick',
                'profile_completed_at' => null,
                'admission_by' => Auth::guard('institute')->id(),
            ]);

            foreach ($courseSummary['fee_items'] as $item) {
                EnrollmentFeeSnapshot::create([
                    'institute_id'    => $iid,
                    'course_book_id'  => $courseBook->id,
                    'fee_type_id'     => $item['fee_type_id'],
                    'fee_type_name'   => $item['fee_type_name'],
                    'original_amount' => $item['amount'],
                    'discount_percent'=> 0,
                    'discount_amount' => 0,
                    'final_amount'    => $item['amount'],
                ]);
            }

            $quickTotalFee = (float) $courseSummary['total_fee'];
            if ($quickTotalFee > 0) {
                $opBal = (float) $wallet->balance;
                $clBal = $opBal - $quickTotalFee;
                $wallet->update(['balance' => $clBal]);
                StudentTransaction::create([
                    'user_id'      => $user->id,
                    'institute_id' => $iid,
                    'franchise_id' => null,
                    'owner_type'   => 'institute',
                    'description'  => 'Course Booked: ' . $course->name,
                    'credit'       => 0,
                    'debit'        => $quickTotalFee,
                    'type'         => 1,
                    'ref_type'     => 'course_book',
                    'ref_id'       => $courseBook->id,
                    'date'         => now()->toDateString(),
                    'c_date'       => now(),
                    'op_bal'       => $opBal,
                    'cl_bal'       => $clBal,
                    'by_user_id'   => Auth::guard('institute')->id(),
                ]);
            }

            $user = $user->fresh(['profile']);

            return [
                'courseBook' => $courseBook->fresh(['student.profile', 'course']),
                'user' => $user,
            ];
        });

        $this->sendSeatBookingEmail($result['user'], $result['courseBook'], $quickPlainPassword);
        $this->markEnquiryConverted($request->input('enquiry_id'), $result['courseBook']->id);

        return redirect()->route('institute.enrollment.pending')
            ->with('success', 'Quick seat booking saved. Complete the remaining details before final admission.');
    }

    public function profileForm(CourseBook $courseBook)
    {
        $this->authorizeCourseBook($courseBook);
        $iid     = $this->instituteId();
        $profile = $courseBook->student->profile;
        $education = $courseBook->student->education;
        $states = State::orderBy('name')->pluck('name');

        $savedFields = AdmissionFormField::where('institute_id', $iid)->get()->keyBy('field_key');

        $districtsByState = Schema::hasTable('districts')
            ? District::query()
                ->select('districts.name as district_name', 'states.name as state_name')
                ->join('states', 'states.id', '=', 'districts.state_id')
                ->orderBy('states.name')->orderBy('districts.name')
                ->get()
                ->groupBy('state_name')
                ->map(fn ($rows) => $rows->pluck('district_name')->values())
                ->toArray()
            : [];

        $educationField   = $savedFields->get('education_details');
        $educationEnabled = ! $educationField || $educationField->is_active;
        $educationRequired = (bool) ($educationField?->is_required);
        $institute        = auth('institute')->user()->institute;

        return view('institute.enrollment.profile', compact(
            'courseBook', 'savedFields', 'profile', 'education',
            'states', 'districtsByState', 'educationEnabled', 'educationRequired', 'institute'
        ) + ['defaultPhotoPath' => $this->defaultPhotoPath()]);
    }

    public function saveProfile(Request $request, CourseBook $courseBook)
    {
        $this->authorizeCourseBook($courseBook);
        $iid     = $this->instituteId();
        $section = $request->input('_section', 'personal');
        $allFields = AdmissionFormField::where('institute_id', $iid)->where('is_active', true)->get();
        $user    = $courseBook->student;

        $personalKeys = ['photo', 'mobile', 'email', 'dob', 'gender', 'category', 'religion',
                         'nationality', 'whatsapp_no', 'alternate_mobile', 'aadhar_no', 'pan_no',
                         'blood_group', 'employment_status', 'computer_literacy', 'qualification',
                         'address', 'permanent_address', 'state', 'district', 'city', 'pin_code',
                         'permanent_state', 'permanent_district', 'permanent_city', 'permanent_pin_code'];
        $guardianKeys = ['father_name', 'mother_name', 'guardian_name', 'guardian_relation',
                         'guardian_mobile', 'guardian_occupation'];

        $activeKeys = match ($section) {
            'guardian' => $guardianKeys,
            'all'      => array_merge($personalKeys, $guardianKeys),
            default    => $personalKeys,
        };

        $nonProfileKeys = AdmissionFormField::nonProfileKeys();
        $fields = $allFields->filter(fn ($f) => in_array($f->field_key, $activeKeys, true));

        $rules = [];
        foreach ($fields as $field) {
            if (in_array($field->field_key, $nonProfileKeys, true)) {
                continue;
            }
            $rules[$field->field_key] = $field->is_required ? 'required' : 'nullable';
            if ($field->field_type === 'file') {
                $rules[$field->field_key] .= '|image|mimes:jpg,jpeg,png,webp|max:2048';
            }
            if ($field->field_key === 'mobile') {
                $rules[$field->field_key] = ($field->is_required ? 'required' : 'nullable') . '|digits:10|unique:users,mobile,' . $user->id;
            }
            if ($field->field_key === 'email') {
                $rules[$field->field_key] = ($field->is_required ? 'required' : 'nullable') . '|email|max:100|unique:users,email,' . $user->id;
            }
            if (in_array($field->field_key, ['state', 'permanent_state'], true)) {
                $rules[$field->field_key] = [
                    $field->is_required ? 'required' : 'nullable',
                    'string', 'max:100',
                    Rule::exists('states', 'name'),
                ];
            }
            // Specific format validation for sensitive fields
            if ($field->field_key === 'aadhar_no') {
                $rules[$field->field_key] = ($field->is_required ? 'required' : 'nullable') . '|digits:12';
            }
            if ($field->field_key === 'pan_no') {
                $rules[$field->field_key] = [$field->is_required ? 'required' : 'nullable', 'regex:/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/'];
            }
            if ($field->field_key === 'blood_group') {
                $rules[$field->field_key] = ($field->is_required ? 'required' : 'nullable') . '|in:A+,A-,B+,B-,AB+,AB-,O+,O-';
            }
            if ($field->field_key === 'dob') {
                $rules[$field->field_key] = ($field->is_required ? 'required' : 'nullable') . '|date|before:today';
            }
            if (in_array($field->field_key, ['whatsapp_no', 'alternate_mobile', 'guardian_mobile'], true)) {
                $rules[$field->field_key] = ($field->is_required ? 'required' : 'nullable') . '|digits:10';
            }
            if (in_array($field->field_key, ['pin_code', 'permanent_pin_code'], true)) {
                $rules[$field->field_key] = ($field->is_required ? 'required' : 'nullable') . '|digits:6';
            }
        }

        if (in_array($section, ['personal', 'all'])) {
            $rules['name'] = 'required|string|max:100';
        }

        $validated = $request->validate($rules);
        $profile   = $user->profile ?? new UserProfile(['user_id' => $courseBook->user_id]);

        if (in_array($section, ['personal', 'all'])) {
            $profile->name = $validated['name'];
        }

        foreach ($fields as $field) {
            if (in_array($field->field_key, $nonProfileKeys, true)) {
                continue;
            }
            if ($field->field_type === 'file') {
                if ($request->hasFile($field->field_key)) {
                    $path = $request->file($field->field_key)->store('student-photos', 'public');
                    $profile->{$field->field_key} = 'storage/' . $path;
                } elseif ($field->field_key === 'photo' && empty($profile->{$field->field_key})) {
                    $profile->{$field->field_key} = $this->defaultPhotoPath();
                }
            } elseif (in_array($field->field_key, ['mobile', 'email'], true)) {
                $user->{$field->field_key} = $validated[$field->field_key] ?? null;
            } else {
                $profile->{$field->field_key} = $validated[$field->field_key] ?? null;
            }
        }

        $user->save();
        $profile->user_id = $courseBook->user_id;
        if (empty($profile->photo)) {
            $profile->photo = $this->defaultPhotoPath();
        }
        $profile->save();

        $courseBook->update([
            'profile_completed_at' => now(),
            'booking_mode'         => $courseBook->booking_mode === 'quick' ? 'quick' : 'full',
        ]);

        if ($section === 'all') {
            // Save education records
            if ($request->has('education')) {
                $user->education()->where('institute_id', $courseBook->institute_id)->delete();
                foreach ($request->input('education', []) as $edu) {
                    if (blank($edu['examination'] ?? null)) continue;
                    \App\Models\UserEducation::create([
                        'user_id'          => $user->id,
                        'institute_id'     => $courseBook->institute_id,
                        'franchise_id'     => null,
                        'examination'      => $edu['examination'],
                        'institute_name'   => $edu['institute_name']   ?? null,
                        'board_university' => $edu['board_university'] ?? null,
                        'passing_year'     => $edu['passing_year']     ?? null,
                        'division'         => $edu['division']         ?? null,
                        'marks_percentage' => $edu['marks_percentage'] ?? null,
                    ]);
                }
            }

            return redirect()->route('institute.enrollment.preview', $courseBook)
                ->with('success', 'Details saved successfully. Please review and proceed to payment.');
        }

        $label = $section === 'guardian' ? 'Guardian' : 'Personal';

        return back()->with('success_' . $section, $label . ' details saved successfully.');
    }

    public function feeForm(CourseBook $courseBook)
    {
        $this->authorizeCourseBook($courseBook);

        // Payment plan already configured — skip setup, go directly to fee collection
        if ($courseBook->paymentPlan) {
            return redirect()->route('institute.enrollment.payment-complete', $courseBook);
        }

        $iid = $this->instituteId();
        $courseBook->loadMissing(['course.feeStructures.feeType']);
        $courseSummary = $this->courseCatalogItem($courseBook->course);
        $feeStructure = collect($courseSummary['fee_items'])->map(fn ($item) => (object) [
            'fee_type_id' => $item['fee_type_id'],
            'fee_type_name' => $item['fee_type_name'],
            'amount' => $item['amount'],
            'is_mandatory' => $item['is_mandatory'],
        ]);

        // Zero-fee course — skip payment setup, go directly to preview/confirm
        $totalFee = $feeStructure->sum('amount');
        if ($totalFee <= 0) {
            $courseBook->update(['fee' => 0, 'final_fee' => 0]);
            return redirect()->route('institute.enrollment.preview', $courseBook);
        }

        $plans = $this->activePaymentPlans($iid);

        return view('institute.enrollment.fee', compact('courseBook', 'feeStructure', 'plans'));
    }

    public function saveFee(Request $request, CourseBook $courseBook)
    {
        $this->authorizeCourseBook($courseBook);
        $request->validate([
            'payment_plan_type_id' => [
                'required',
                Rule::exists('payment_plan_types', 'id')
                    ->where(fn ($query) => $query->where('institute_id', $this->instituteId())->where('is_active', true)),
            ],
            'fee_items' => 'required|array',
            'fee_items.*.discount_percent' => 'nullable|numeric|min:0|max:100',
            'first_payment_amount' => 'required|numeric|min:0.01',
            'payment_mode' => 'required|in:CASH,UPI,NEFT,IMPS,CHEQUE',
            'payment_date' => 'required|date',
            'utr' => 'nullable|string|max:80',
            'payment_note' => 'nullable|string|max:255',
        ]);

        $plan = PaymentPlanType::where('institute_id', $this->instituteId())->findOrFail($request->payment_plan_type_id);
        $courseBook->loadMissing(['course.feeStructures.feeType', 'paymentPlan']);
        $feeQuote = $this->buildFeeQuote($courseBook, $request->input('fee_items', []));
        $terms = $this->paymentTerms($plan, $courseBook, $feeQuote, $courseBook->paymentPlan);

        $firstPayment = (float) $request->first_payment_amount;
        if (! $this->isPaymentSelectionValid($plan->type, $firstPayment, $terms['required_fee'], $feeQuote['total_fee'])) {
            $msg = match ($plan->type) {
                'OTP'     => 'OTP plan mein poori fee (₹' . number_format($feeQuote['total_fee'], 2) . ') ek saath pay karni hogi.',
                'MONTHLY' => 'Monthly plan mein kam se kam pehli installment (₹' . number_format($terms['required_fee'], 2) . ') deni padegi.',
                default   => 'Koi bhi positive amount enter karo (minimum ₹0.01).',
            };

            return back()->withErrors(['first_payment_amount' => $msg])->withInput();
        }

        DB::transaction(function () use ($courseBook, $plan, $feeQuote, $terms) {
            $courseBook->feeSnapshots()->delete();
            $courseBook->paymentPlan()->delete();

            foreach ($feeQuote['items'] as $item) {
                EnrollmentFeeSnapshot::create([
                    'institute_id' => $courseBook->institute_id,
                    'course_book_id' => $courseBook->id,
                    'fee_type_id' => $item['fee_type_id'] ?? null,
                    'fee_type_name' => $item['fee_type_name'],
                    'original_amount' => $item['original_amount'],
                    'discount_percent' => $item['discount_percent'] ?? 0,
                    'discount_amount' => $item['discount_amount'] ?? 0,
                    'final_amount' => $item['final_amount'],
                ]);
            }

            EnrollmentPaymentPlan::create([
                'institute_id' => $courseBook->institute_id,
                'course_book_id' => $courseBook->id,
                'payment_plan_type_id' => $plan->id,
                'plan_type' => $plan->type,
                'total_fee' => $feeQuote['total_fee'],
                'required_fee' => $terms['required_fee'],
                'first_payment_amount' => $terms['first_payment_amount'],
                'remaining_fee' => $terms['remaining_fee'],
                'monthly_amount' => $terms['monthly_amount'],
                'grace_days' => $plan->grace_days,
                'late_fee_per_day' => $plan->late_fee_per_day,
                'next_due_date' => $terms['next_due_date'],
            ]);

            $courseBook->update([
                'fee' => $feeQuote['total_fee'],
                'final_fee' => $feeQuote['total_fee'],
                'profile_completed_at' => $courseBook->profile_completed_at ?? now(),
            ]);
        });

        $firstPayment = (float) $request->first_payment_amount;
        if ($firstPayment > 0) {
            $courseBook->loadMissing('student');
            $this->recordPayment(
                $courseBook->student,
                $courseBook,
                $firstPayment,
                $request->payment_mode,
                $request->payment_date,
                $request->utr ?? null,
                $request->payment_note ?? null
            );
            $this->finalizeAdmissionIfEligible($courseBook->fresh(['student.profile', 'paymentPlan', 'course']));
        }

        return redirect()->route('institute.enrollment.payment-complete', $courseBook);
    }

    public function paymentComplete(CourseBook $courseBook)
    {
        $this->authorizeCourseBook($courseBook);
        $courseBook->loadMissing(['student.profile', 'course', 'batch', 'paymentPlan', 'feeSnapshots']);
        $amountColumn = FeeCollectDetail::amountColumn();
        $paidTotal = (float) FeeCollectDetail::where('course_book_id', $courseBook->id)
            ->whereNull('cancelled_at')
            ->sum($amountColumn);
        $latestFee = FeeCollectDetail::where('course_book_id', $courseBook->id)
            ->whereNull('cancelled_at')
            ->latest('id')
            ->first();
        $fees = FeeCollectDetail::where('course_book_id', $courseBook->id)->orderByDesc('id')->paginate(15);
        $transactions = StudentTransaction::where('user_id', $courseBook->user_id)
            ->where('institute_id', $courseBook->institute_id)
            ->orderByDesc('id')
            ->get();

        $plan    = $courseBook->paymentPlan;
        $due     = max(round((float) $courseBook->final_fee - $paidTotal, 2), 0);
        $lateFee = $plan && $plan->plan_type === 'MONTHLY' ? $this->calculateLateFee($plan) : 0;

        $modalDefaultAmount = match ($plan?->plan_type) {
            'MONTHLY' => round(($plan->monthly_amount ?? 0) + $lateFee, 2),
            'OTP'     => $due,
            default   => null,
        };

        return view('institute.enrollment.payment-complete', compact(
            'courseBook', 'fees', 'latestFee', 'paidTotal', 'transactions',
            'due', 'lateFee', 'modalDefaultAmount'
        ));
    }

    public function addPayment(Request $request, CourseBook $courseBook)
    {
        $this->authorizeCourseBook($courseBook);
        $courseBook->loadMissing(['student', 'paymentPlan', 'course']);

        if (! $courseBook->paymentPlan) {
            return back()->withErrors(['amount' => 'Payment plan not configured yet.']);
        }

        $utrRule = in_array($request->payment_mode, ['UPI', 'NEFT', 'IMPS', 'CHEQUE'])
            ? 'required|string|max:80'
            : 'nullable|string|max:80';

        $request->validate([
            'amount'       => 'required|numeric|min:0.01|max:500000',
            'payment_mode' => 'required|in:CASH,UPI,NEFT,IMPS,CHEQUE',
            'payment_date' => 'required|date',
            'utr'          => $utrRule,
            'payment_note' => 'nullable|string|max:255',
        ]);

        $amount = (float) $request->amount;

        DB::transaction(function () use ($request, $courseBook, $amount) {
            $this->recordPayment(
                $courseBook->student,
                $courseBook,
                $amount,
                $request->payment_mode,
                $request->payment_date,
                $request->utr ?? null,
                $request->payment_note ?? null
            );

            $fresh = $courseBook->fresh(['student.profile', 'paymentPlan', 'course']);
            $this->finalizeAdmissionIfEligible($fresh);

            // Advance next_due_date for MONTHLY plan after sufficient payment
            $fresh = $fresh->fresh(['paymentPlan']);
            if ($fresh->paymentPlan && $fresh->paymentPlan->plan_type === 'MONTHLY') {
                $plan     = $fresh->paymentPlan;
                $lateFee  = $this->calculateLateFee($plan);
                $totalDue = round(($plan->monthly_amount ?? 0) + $lateFee, 2);
                if ($amount >= $totalDue - 0.01 && $plan->next_due_date) {
                    $plan->update([
                        'next_due_date' => \Carbon\Carbon::parse($plan->next_due_date)->addMonth()->toDateString(),
                    ]);
                }
            }
        });

        return redirect()->route('institute.enrollment.payment-complete', $courseBook)
            ->with('success', 'Payment of ₹' . number_format($amount, 2) . ' recorded successfully.');
    }

    public function monthlyFees()
    {
        $iid   = $this->instituteId();
        $today = now();

        $enrollments = CourseBook::with(['student.profile', 'course', 'paymentPlan'])
            ->where('institute_id', $iid)
            ->where('status', 'RUN')
            ->whereHas('paymentPlan', fn ($q) => $q->where('plan_type', 'MONTHLY'))
            ->latest()
            ->get()
            ->map(function (CourseBook $book) use ($today) {
                $plan      = $book->paymentPlan;
                $paidTotal = (float) FeeCollectDetail::where('course_book_id', $book->id)
                    ->whereNull('cancelled_at')
                    ->sum(FeeCollectDetail::amountColumn());

                $nextDue     = $plan->next_due_date ? \Carbon\Carbon::parse($plan->next_due_date) : null;
                $graceEnd    = $nextDue ? $nextDue->copy()->addDays((int) ($plan->grace_days ?? 0)) : null;
                $overdueDays = ($graceEnd && $today->gt($graceEnd)) ? (int) $today->diffInDays($graceEnd) : 0;
                $lateFee     = round($overdueDays * (float) ($plan->late_fee_per_day ?? 0), 2);
                $monthlyAmt  = (float) ($plan->monthly_amount ?? 0);
                $isDue       = $nextDue && $nextDue->format('Y-m') <= $today->format('Y-m');

                // Accumulated months: if May unpaid and June starts, both are due
                $monthsUnpaid = ($isDue && $nextDue)
                    ? max(1, (int) $nextDue->copy()->startOfMonth()->diffInMonths($today->copy()->startOfMonth()) + 1)
                    : 0;
                $totalDue = round($monthsUnpaid * $monthlyAmt + $lateFee, 2);

                $book->setAttribute('paid_total', $paidTotal);
                $book->setAttribute('next_due', $nextDue);
                $book->setAttribute('grace_end', $graceEnd);
                $book->setAttribute('overdue_days', $overdueDays);
                $book->setAttribute('late_fee_amt', $lateFee);
                $book->setAttribute('months_unpaid', $monthsUnpaid);
                $book->setAttribute('total_due_now', $totalDue);
                $book->setAttribute('monthly_amount', $monthlyAmt);
                $book->setAttribute('is_overdue', $overdueDays > 0);
                $book->setAttribute('is_due', $isDue);

                return $book;
            });

        $overdueList  = $enrollments->filter(fn ($b) => $b->is_overdue)->values();
        $dueList      = $enrollments->filter(fn ($b) => $b->is_due && ! $b->is_overdue)->values();
        $upcomingList = $enrollments->filter(fn ($b) => ! $b->is_due)->values();

        return view('institute.enrollment.monthly-fees', compact(
            'enrollments', 'overdueList', 'dueList', 'upcomingList', 'today'
        ));
    }

    public function renewBooking(CourseBook $courseBook)
    {
        $this->authorizeCourseBook($courseBook);

        abort_unless($courseBook->status === 'EXPIRED', 422, 'Only expired bookings can be renewed.');

        $courseBook->update([
            'status'    => 'OPEN',
            'book_date' => now()->toDateString(),
        ]);

        $name = $courseBook->student?->profile?->name ?? $courseBook->student?->user_id ?? 'Student';

        return redirect()->route('institute.enrollment.pending')
            ->with('success', "Booking renewed for {$name}. Complete details or payment from the list below.");
    }

    public function cancelBooking(CourseBook $courseBook)
    {
        $this->authorizeCourseBook($courseBook);
        abort_unless(in_array($courseBook->status, ['OPEN', 'EXPIRED']), 422, 'Only OPEN or EXPIRED bookings can be cancelled.');

        $courseBook->update(['status' => 'CANCEL']);

        $name = $courseBook->student?->profile?->name ?? $courseBook->student?->user_id ?? 'Student';
        return redirect()->back()->with('success', "Booking cancelled for {$name}.");
    }

    public function cancelFee(Request $request, CourseBook $courseBook, FeeCollectDetail $fee)
    {
        $this->authorizeCourseBook($courseBook);
        abort_unless($fee->user_id === $courseBook->user_id, 403);
        abort_if($fee->cancelled_at !== null, 422, 'This payment is already cancelled.');

        $request->validate([
            'reason'  => 'required|string|max:255',
            'confirm' => ['required', 'in:CANCEL'],
        ], [
            'confirm.in' => 'Please type CANCEL exactly to confirm.',
        ]);

        $amountColumn = FeeCollectDetail::amountColumn();
        $amount = (float) $fee->{$amountColumn};
        $iid = $this->instituteId();

        DB::transaction(function () use ($fee, $courseBook, $request, $amount, $iid, $amountColumn) {
            $fee->update([
                'cancelled_at' => now(),
                'cancel_reason' => $request->reason,
                'cancelled_by' => Auth::guard('institute')->id(),
            ]);

            $instituteWallet = InstituteStudentWallet::firstOrCreate(
                ['institute_id' => $iid],
                ['balance' => 0]
            );
            $iOpBal = (float) $instituteWallet->balance;
            $iClBal = round($iOpBal - $amount, 2);
            $instituteWallet->update(['balance' => $iClBal]);

            InstituteStudentTransaction::create([
                'institute_id'  => $iid,
                'franchise_id'  => $fee->franchise_id,
                'ref_user_id'   => $fee->user_id,
                'description'   => 'Payment cancelled | Invoice: ' . $fee->invoice_no . ' | ' . $request->reason,
                'debit'         => $amount,
                'credit'        => 0,
                'type'          => 5,
                'date'          => now()->toDateString(),
                'c_date'        => now(),
                'op_bal'        => $iOpBal,
                'cl_bal'        => $iClBal,
                'by_user_id'    => Auth::guard('institute')->id(),
            ]);

            $studentTxn = StudentTransaction::where('ref_type', 'fee_collect_detail')
                ->where('ref_id', $fee->id)
                ->first();

            if ($studentTxn) {
                $wallet = StudentWallet::firstOrCreate(
                    ['user_id' => $fee->user_id],
                    ['institute_id' => $iid, 'franchise_id' => $fee->franchise_id, 'owner_type' => 'institute', 'balance' => 0]
                );
                $opBal = (float) $wallet->balance;
                $clBal = round($opBal - $amount, 2);
                $wallet->update(['balance' => $clBal]);

                StudentTransaction::create([
                    'user_id'      => $fee->user_id,
                    'institute_id' => $iid,
                    'franchise_id' => $fee->franchise_id,
                    'owner_type'   => $fee->franchise_id ? 'franchise' : 'institute',
                    'description'  => 'Payment reversal | Invoice: ' . $fee->invoice_no . ' | ' . $request->reason,
                    'debit'        => $amount,
                    'credit'       => 0,
                    'type'         => 5,
                    'ref_type'     => 'fee_collect_detail_cancel',
                    'ref_id'       => $fee->id,
                    'date'         => now()->toDateString(),
                    'c_date'       => now(),
                    'op_bal'       => $opBal,
                    'cl_bal'       => $clBal,
                    'by_user_id'   => Auth::guard('institute')->id(),
                ]);
            }
        });

        return redirect()->route('institute.enrollment.payment-complete', $courseBook)
            ->with('success', 'Payment cancelled and ledger updated.');
    }

    public function receiptA4(CourseBook $courseBook, FeeCollectDetail $fee)
    {
        $this->authorizeCourseBook($courseBook);
        abort_unless($fee->user_id === $courseBook->user_id, 403);
        $courseBook->loadMissing(['student.profile', 'course', 'batch']);
        $institute = Auth::guard('institute')->user()->institute;

        return view('institute.enrollment.receipt-a4', compact('courseBook', 'fee', 'institute'));
    }

    public function receiptThermal(CourseBook $courseBook, FeeCollectDetail $fee)
    {
        $this->authorizeCourseBook($courseBook);
        abort_unless($fee->user_id === $courseBook->user_id, 403);
        $courseBook->loadMissing(['student.profile', 'course', 'batch']);
        $institute = Auth::guard('institute')->user()->institute;

        return view('institute.enrollment.receipt-thermal', compact('courseBook', 'fee', 'institute'));
    }

    public function preview(CourseBook $courseBook)
    {
        $this->authorizeCourseBook($courseBook);
        $iid = $this->instituteId();
        $fields = AdmissionFormField::where('institute_id', $iid)->where('is_active', true)->orderBy('sort_order')->get();
        $profile = $courseBook->student->profile;
        $education = $courseBook->student->education;
        $snapshots = $courseBook->feeSnapshots;
        if (! $snapshots->contains(fn ($snapshot) => $snapshot->fee_type_id === null && $snapshot->fee_type_name === 'Course Fee')) {
            $courseFeeSnapshot = EnrollmentFeeSnapshot::create([
                'institute_id' => $courseBook->institute_id,
                'course_book_id' => $courseBook->id,
                'fee_type_id' => null,
                'fee_type_name' => 'Course Fee',
                'original_amount' => $courseBook->course?->fee ?? 0,
                'discount_percent' => 0,
                'discount_amount' => 0,
                'final_amount' => $courseBook->course?->fee ?? 0,
            ]);
            $snapshots = collect([$courseFeeSnapshot])->merge($snapshots);
        }
        $displayTotalFee = round((float) $snapshots->sum('final_amount'), 2);
        if (abs((float) $courseBook->final_fee - $displayTotalFee) >= 0.01) {
            $paidAmount = (float) FeeCollectDetail::where('course_book_id', $courseBook->id)
                ->sum(FeeCollectDetail::amountColumn());
            $courseBook->update([
                'fee' => $displayTotalFee,
                'final_fee' => $displayTotalFee,
            ]);
            if ($courseBook->paymentPlan) {
                $courseBook->paymentPlan->update([
                    'total_fee' => $displayTotalFee,
                    'remaining_fee' => max(round($displayTotalFee - $paidAmount, 2), 0),
                    'monthly_amount' => $courseBook->paymentPlan->plan_type === 'MONTHLY'
                        ? round($displayTotalFee / max(1, (int) ($courseBook->course->duration ?: 1)), 2)
                        : $courseBook->paymentPlan->monthly_amount,
                ]);
            }
        }
        $plan = $courseBook->paymentPlan;
        $educationField = AdmissionFormField::where('institute_id', $iid)
            ->where('field_key', 'education_details')
            ->first();
        $educationEnabled = ! $educationField || $educationField->is_active;

        $paidTotal = round((float) \App\Models\FeeCollectDetail::where('course_book_id', $courseBook->id)
            ->whereNull('cancelled_at')
            ->sum(\App\Models\FeeCollectDetail::amountColumn()), 2);

        $savedFields = AdmissionFormField::where('institute_id', $iid)->get()->keyBy('field_key');
        $institute   = auth('institute')->user()->institute;

        return view('institute.enrollment.preview', compact('courseBook', 'fields', 'savedFields', 'profile', 'education', 'snapshots', 'plan', 'educationEnabled', 'displayTotalFee', 'paidTotal', 'institute'));
    }

    public function confirm(Request $request, CourseBook $courseBook)
    {
        $this->authorizeCourseBook($courseBook);

        $courseBook->update([
            'profile_completed_at' => $courseBook->profile_completed_at ?? now(),
        ]);

        $fresh = $courseBook->fresh(['student.profile', 'paymentPlan', 'course']);
        $plan  = $fresh->paymentPlan;

        // For OTP/MONTHLY plans that require upfront payment, block confirm if nothing paid yet.
        if ($plan && in_array($plan->plan_type, ['OTP', 'MONTHLY'])) {
            $paid = (float) FeeCollectDetail::where('course_book_id', $courseBook->id)
                ->whereNull('cancelled_at')
                ->sum(FeeCollectDetail::amountColumn());

            if ($paid <= 0) {
                return redirect()->route('institute.enrollment.fee', $courseBook)
                    ->with('error', 'Fee collection required before confirming admission. Please collect at least the first payment.');
            }
        }

        $this->forceFinalizeAdmission($fresh);

        $this->sendAdmissionConfirmationEmail($courseBook->fresh(['course', 'batch', 'student.profile']));

        return redirect()->route('institute.enrollment.pending', ['filter' => 'all'])
            ->with('success', 'Admission completed successfully. You can review all booked and admitted students here.');
    }

    public function addEducation(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'examination' => 'required|string|max:80',
            'institute_name' => 'nullable|string|max:150',
            'board_university' => 'nullable|string|max:150',
            'passing_year' => 'nullable|integer|min:1900|max:' . date('Y'),
            'division' => 'nullable|string|max:50',
            'marks_percentage' => 'nullable|numeric|min:0|max:100',
        ]);
        $user = User::findOrFail($data['user_id']);
        abort_unless($user->institute_id === $this->instituteId(), 403);
        $data['institute_id'] = $user->institute_id;
        $data['franchise_id'] = $user->franchise_id;
        $edu = UserEducation::create($data);

        return response()->json(['success' => true, 'id' => $edu->id]);
    }

    public function removeEducation(UserEducation $education)
    {
        abort_unless($education->institute_id === $this->instituteId(), 403);
        $education->delete();

        return response()->json(['success' => true]);
    }

    private function storeExistingStudent(Request $request)
    {
        $iid = $this->instituteId();
        $sessionId = $this->activeSessionId();
        $data = $request->validate([
            'course_id'         => 'required|exists:course_details,id',
            'batch_id'          => 'nullable|exists:batch_details,id',
            'user_id'           => 'required|exists:users,id',
            'course_short_code' => 'nullable|string|max:10',
        ]);

        $user = User::findOrFail($data['user_id']);
        $this->assertCourseBookingAllowed($user->id, (int) $data['course_id'], $data['batch_id'] ?? null);

        $course = CourseDetail::with(['feeStructures.feeType'])
            ->where('institute_id', $iid)
            ->findOrFail($data['course_id']);

        $existingSummary = $this->courseCatalogItem($course);
        $existingTotalFee = (float) $existingSummary['total_fee'];

        $courseBook = DB::transaction(function () use (
            $iid, $sessionId, $data, $course, $existingSummary, $existingTotalFee, $user, $request
        ) {
            $courseBook = CourseBook::create([
                'institute_id'         => $iid,
                'user_id'              => $user->id,
                'course_id'            => $course->id,
                'session_id'           => $sessionId,
                'batch_id'             => $data['batch_id'] ?? null,
                'course_code'          => $this->toCourseCode($request->input('course_short_code', ''), $course->name),
                'enrollment_no'        => null,
                'fee'                  => $existingTotalFee,
                'final_fee'            => $existingTotalFee,
                'status'               => 'OPEN',
                'booking_mode'         => 'existing',
                'book_date'            => now()->toDateString(),
                'profile_completed_at' => $user->profile ? now() : null,
                'admission_by'         => Auth::guard('institute')->id(),
            ]);

            foreach ($existingSummary['fee_items'] as $item) {
                EnrollmentFeeSnapshot::create([
                    'institute_id'    => $iid,
                    'course_book_id'  => $courseBook->id,
                    'fee_type_id'     => $item['fee_type_id'],
                    'fee_type_name'   => $item['fee_type_name'],
                    'original_amount' => $item['amount'],
                    'discount_percent'=> 0,
                    'discount_amount' => 0,
                    'final_amount'    => $item['amount'],
                ]);
            }

            $wallet = $this->ensureStudentWallet($user);
            if ($existingTotalFee > 0) {
                $opBal = (float) $wallet->balance;
                $clBal = $opBal - $existingTotalFee;
                $wallet->update(['balance' => $clBal]);
                StudentTransaction::create([
                    'user_id'      => $user->id,
                    'institute_id' => $iid,
                    'franchise_id' => null,
                    'owner_type'   => 'institute',
                    'description'  => 'Course Booked: ' . $course->name,
                    'credit'       => 0,
                    'debit'        => $existingTotalFee,
                    'type'         => 1,
                    'ref_type'     => 'course_book',
                    'ref_id'       => $courseBook->id,
                    'date'         => now()->toDateString(),
                    'c_date'       => now(),
                    'op_bal'       => $opBal,
                    'cl_bal'       => $clBal,
                    'by_user_id'   => Auth::guard('institute')->id(),
                ]);
            }

            return $courseBook;
        });

        $this->sendSeatBookingEmail($user, $courseBook->fresh(['student.profile', 'course', 'batch']), null);

        return redirect()->route('institute.enrollment.profile', $courseBook);
    }

    private function markEnquiryConverted(?string $enquiryId, int $courseBookId): void
    {
        if (!$enquiryId) return;
        \App\Models\Enquiry::where('institute_id', $this->instituteId())
            ->where('status', 'OPEN')
            ->where('id', (int) $enquiryId)
            ->update([
                'status'                      => 'CONVERTED',
                'converted_to_course_book_id' => $courseBookId,
            ]);
    }

    private function admissionFormData(): array
    {
        $iid = $this->instituteId();
        $courses = CourseDetail::with(['courseType', 'feeStructures.feeType'])
            ->where('institute_id', $iid)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();
        $courseTypes = $courses->pluck('courseType')
            ->filter()
            ->unique('id')
            ->sortBy('name')
            ->values();
        $batches = BatchDetail::where('institute_id', $iid)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();
        $channelPartners = ChannelPartner::where('institute_id', $iid)
            ->where('status', 'active')
            ->orderBy('name')
            ->get(['id', 'name', 'mobile']);
        $savedFields = AdmissionFormField::where('institute_id', $iid)
            ->get()
            ->keyBy('field_key');
        $plans = $this->activePaymentPlans($iid);
        $states = State::orderBy('name')->pluck('name');
        $districtsByState = Schema::hasTable('districts')
            ? District::query()
                ->select('districts.name as district_name', 'states.name as state_name')
                ->join('states', 'states.id', '=', 'districts.state_id')
                ->orderBy('states.name')
                ->orderBy('districts.name')
                ->get()
                ->groupBy('state_name')
                ->map(fn ($rows) => $rows->pluck('district_name')->values())
                ->toArray()
            : [];
        $courseCatalog = $courses->map(fn (CourseDetail $course) => $this->courseCatalogItem($course))->values();

        return compact('courses', 'courseTypes', 'batches', 'channelPartners', 'savedFields', 'plans', 'states', 'districtsByState', 'courseCatalog')
            + ['defaultPhotoPath' => $this->defaultPhotoPath()];
    }

    private function activePaymentPlans(int $iid)
    {
        return PaymentPlanType::where('institute_id', $iid)
            ->where('is_active', true)
            ->orderByRaw("CASE type WHEN 'OTP' THEN 1 WHEN 'PART' THEN 2 WHEN 'MONTHLY' THEN 3 ELSE 4 END")
            ->orderBy('name')
            ->get();
    }

    private function wizardRules(int $iid, $fields): array
    {
        $required = fn (string $key, string $fallback = 'nullable') => ($fields[$key]?->is_required ?? false) ? 'required' : $fallback;

        return [
            'course_id' => [
                'required',
                Rule::exists('course_details', 'id')->where(fn ($query) => $query->where('institute_id', $iid)),
            ],
            'batch_id' => [
                'nullable',
                Rule::exists('batch_details', 'id')->where(fn ($query) => $query->where('institute_id', $iid)),
            ],
            'admission_source' => 'required|in:direct,channel_partner',
            'channel_partner_id' => [
                'nullable',
                'required_if:admission_source,channel_partner',
                Rule::exists('channel_partners', 'id')->where(fn ($query) => $query->where('institute_id', $iid)->where('status', 'active')),
            ],
            'payment_plan_type_id' => [
                'nullable',
                Rule::exists('payment_plan_types', 'id')->where(fn ($query) => $query->where('institute_id', $iid)->where('is_active', true)),
            ],
            'name' => 'required|string|max:100',
            'mobile' => 'required|digits:10|unique:users,mobile',
            'email' => 'nullable|email|max:100|unique:users,email',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'father_name' => $required('father_name', 'nullable|string|max:100'),
            'mother_name' => $required('mother_name', 'nullable|string|max:100'),
            'guardian_name' => $required('guardian_name', 'nullable|string|max:100'),
            'guardian_relation' => $required('guardian_relation', 'nullable|string|max:50'),
            'guardian_mobile' => $required('guardian_mobile', 'nullable|digits:10'),
            'guardian_occupation' => $required('guardian_occupation', 'nullable|string|max:80'),
            'dob' => 'nullable|date|before:today',
            'gender' => 'nullable|in:Male,Female,Other',
            'category' => 'nullable|string|max:20',
            'religion' => 'nullable|string|max:50',
            'nationality' => 'nullable|string|max:50',
            'whatsapp_no' => 'nullable|digits:10',
            'alternate_mobile' => 'nullable|digits:10',
            'aadhar_no' => 'nullable|digits:12',
            'pan_no' => ['nullable', 'regex:/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/'],
            'blood_group' => 'nullable|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'employment_status' => 'nullable|in:Employed,Unemployed',
            'computer_literacy' => 'nullable|in:Yes,No',
            'qualification' => 'nullable|string|max:80',
            'address' => 'nullable|string',
            'permanent_address' => 'nullable|string',
            'state' => ['nullable', 'string', 'max:100', Rule::exists('states', 'name')],
            'district' => array_values(array_filter([
                'nullable',
                'string',
                'max:100',
                Schema::hasTable('districts') ? Rule::exists('districts', 'name') : null,
            ])),
            'city' => 'nullable|string|max:100',
            'pin_code' => 'nullable|digits:6',
            'permanent_state' => ['nullable', 'string', 'max:100', Rule::exists('states', 'name')],
            'permanent_district' => array_values(array_filter([
                'nullable',
                'string',
                'max:100',
                Schema::hasTable('districts') ? Rule::exists('districts', 'name') : null,
            ])),
            'permanent_city' => 'nullable|string|max:100',
            'permanent_pin_code' => 'nullable|digits:6',
            'education' => 'nullable|array',
            'education.*.examination' => 'nullable|string|max:80',
            'education.*.institute_name' => 'nullable|string|max:150',
            'education.*.board_university' => 'nullable|string|max:150',
            'education.*.passing_year' => 'nullable|integer|min:1900|max:' . date('Y'),
            'education.*.division' => 'nullable|string|max:50',
            'education.*.marks_percentage' => 'nullable|numeric|min:0|max:100',
            'first_payment_amount' => 'nullable|numeric|min:0',
            'payment_mode' => 'nullable|in:CASH,UPI,NEFT,IMPS,CHEQUE',
            'payment_date' => 'nullable|date',
            'utr' => 'nullable|string|max:80',
            'payment_note' => 'nullable|string|max:255',
        ];
    }

    private function quickWizardRules(int $iid, $fields): array
    {
        $rules = [
            'course_id' => [
                'required',
                Rule::exists('course_details', 'id')->where(fn ($query) => $query->where('institute_id', $iid)),
            ],
            'batch_id' => [
                'nullable',
                Rule::exists('batch_details', 'id')->where(fn ($query) => $query->where('institute_id', $iid)),
            ],
            'admission_source' => 'required|in:direct,channel_partner',
            'channel_partner_id' => [
                'nullable',
                'required_if:admission_source,channel_partner',
                Rule::exists('channel_partners', 'id')->where(fn ($query) => $query->where('institute_id', $iid)->where('status', 'active')),
            ],
            'payment_plan_type_id' => [
                'nullable',
                Rule::exists('payment_plan_types', 'id')->where(fn ($query) => $query->where('institute_id', $iid)->where('is_active', true)),
            ],
            'name' => 'required|string|max:100',
            'mobile' => 'required|digits:10|unique:users,mobile',
            'email' => 'required|email|max:100|unique:users,email',
        ];

        foreach ($fields as $field) {
            if (! $field->quick_is_active || in_array($field->field_key, ['name', 'mobile', 'email', 'education_details'], true)) {
                continue;
            }

            $base = $field->quick_is_required ? 'required' : 'nullable';
            $rules[$field->field_key] = match ($field->field_type) {
                'email' => $base . '|email|max:100',
                'date' => $base . '|date',
                'number' => $base . '|string|max:20',
                'textarea' => $base . '|string',
                default => $base . '|string|max:255',
            };

            if ($field->field_key === 'state') {
                $rules[$field->field_key] = [
                    $field->quick_is_required ? 'required' : 'nullable',
                    'string',
                    'max:100',
                    Rule::exists('states', 'name'),
                ];
            } elseif ($field->field_key === 'district') {
                $rules[$field->field_key] = array_values(array_filter([
                    $field->quick_is_required ? 'required' : 'nullable',
                    'string',
                    'max:100',
                    Schema::hasTable('districts') ? Rule::exists('districts', 'name') : null,
                ]));
            }
        }

        return $rules;
    }

    private function courseCatalogItem(CourseDetail $course): array
    {
        $baseFee = round((float) $course->fee, 2);
        $boundItems = $course->feeStructures
            ->map(function ($row) {
                return [
                    'fee_type_id' => $row->fee_type_id,
                    'fee_type_name' => $row->fee_type_name,
                    'amount' => round((float) $row->amount, 2),
                    'is_mandatory' => (bool) ($row->feeType?->is_mandatory),
                ];
            })
            ->values();

        $requiredFee = round($baseFee + $boundItems->where('is_mandatory', true)->sum('amount'), 2);
        $boundFeeTotal = round($boundItems->sum('amount'), 2);
        $items = collect([[
            'fee_type_id' => null,
            'fee_type_name' => 'Course Fee',
            'amount' => $baseFee,
            'is_mandatory' => true,
        ]])->merge($boundItems)->values()->all();

        return [
            'id' => $course->id,
            'name' => $course->name,
            'course_type_id' => $course->course_type_id,
            'course_type_name' => $course->courseType?->name,
            'duration' => (int) ($course->duration ?: 1),
            'course_fee' => $baseFee,
            'bound_fee_total' => $boundFeeTotal,
            'required_fee' => $requiredFee,
            'total_fee' => round($baseFee + $boundFeeTotal, 2),
            'fee_items' => $items,
        ];
    }

    private function buildFeeQuote(CourseBook $courseBook, array $submittedItems = []): array
    {
        $courseBook->loadMissing(['course.feeStructures.feeType', 'feeSnapshots']);

        // Franchise bookings already have fee snapshots with franchise-specific pricing.
        // Use those as the base to avoid overriding with institute catalog amounts.
        if ($courseBook->feeSnapshots->isNotEmpty()) {
            $catalog = $courseBook->feeSnapshots->map(fn ($s) => [
                'fee_type_id'   => $s->fee_type_id,
                'fee_type_name' => $s->fee_type_name,
                'amount'        => round((float) $s->original_amount, 2),
                'is_mandatory'  => $s->fee_type_id === null,
            ])->values();
        } else {
            $catalog = collect($this->courseCatalogItem($courseBook->course)['fee_items'])->values();
        }

        $submittedItems = collect($submittedItems)->values();

        $items = $catalog->map(function (array $catalogItem, int $index) use ($submittedItems) {
            $submitted = $submittedItems->get($index, []);
            $originalAmount = round((float) $catalogItem['amount'], 2);
            $discountPercent = round((float) ($submitted['discount_percent'] ?? 0), 2);
            $discountPercent = min(max($discountPercent, 0), 100);
            $discountAmount = round(($originalAmount * $discountPercent) / 100, 2);
            $finalAmount = max(round($originalAmount - $discountAmount, 2), 0);

            return [
                'fee_type_id' => $catalogItem['fee_type_id'],
                'fee_type_name' => $catalogItem['fee_type_name'],
                'original_amount' => $originalAmount,
                'discount_percent' => $discountPercent,
                'discount_amount' => $discountAmount,
                'final_amount' => $finalAmount,
                'is_mandatory' => (bool) ($catalogItem['is_mandatory'] ?? false),
            ];
        })->values();

        return [
            'items' => $items->all(),
            'total_fee' => round($items->sum('final_amount'), 2),
            'total_discount' => round($items->sum('discount_amount'), 2),
            'required_fee' => round($items->where('is_mandatory', true)->sum('final_amount'), 2),
        ];
    }

    private function paymentTerms(
        PaymentPlanType $plan,
        CourseBook $courseBook,
        array $feeQuote,
        ?EnrollmentPaymentPlan $existingPlan = null
    ): array {
        $totalFee    = round((float) $feeQuote['total_fee'], 2);
        $duration    = max(1, (int) ($courseBook->course->duration ?: 1));
        $monthlyAmt  = $plan->type === 'MONTHLY' ? round($totalFee / $duration, 2) : null;

        // required_fee = minimum first payment needed to proceed:
        //   OTP    → full amount
        //   MONTHLY → at least one installment
        //   PART   → 0 (admin decides any positive amount)
        $requiredFee = match ($plan->type) {
            'OTP'     => $totalFee,
            'MONTHLY' => $monthlyAmt,
            default   => 0,
        };

        $firstPaymentAmount = match ($plan->type) {
            'OTP'     => $totalFee,
            'MONTHLY' => $monthlyAmt,
            default   => round((float) ($existingPlan?->first_payment_amount ?? 0), 2),
        };

        return [
            'required_fee'         => $requiredFee,
            'first_payment_amount' => $firstPaymentAmount,
            'remaining_fee'        => $totalFee,
            'monthly_amount'       => $monthlyAmt,
            'next_due_date'        => $plan->type === 'MONTHLY' ? now()->addMonth()->toDateString() : null,
        ];
    }

    private function recordPayment(
        User $user,
        CourseBook $courseBook,
        float $amount,
        string $paymentMode,
        string $paymentDate,
        ?string $utr,
        ?string $note
    ): void {
        $iid = $this->instituteId();
        $invoiceNo = 'INV' . now()->format('Ymd') . strtoupper(substr(uniqid(), -5));

        $byUser = Auth::guard('institute')->id();
        $feePayload = [
            'institute_id' => $iid,
            'franchise_id' => $user->franchise_id,
            'user_id' => $user->id,
            'course_book_id' => $courseBook->id,
            'invoice_no' => $invoiceNo,
            'payment_mode' => $paymentMode,
            'utr' => $utr,
            'date' => $paymentDate,
            'note' => $note,
            'received_by' => $byUser,
            'by_rcv' => $byUser,
        ];
        $feePayload['amount'] = $amount;
        $feePayload['amt'] = $amount;
        $fee = FeeCollectDetail::create($feePayload);

        $instituteWallet = InstituteStudentWallet::firstOrCreate(
            ['institute_id' => $iid],
            ['balance' => 0]
        );
        $iOpBal = (float) $instituteWallet->balance;
        $iClBal = $iOpBal + $amount;
        $instituteWallet->update(['balance' => $iClBal]);

        InstituteStudentTransaction::create([
            'institute_id' => $iid,
            'franchise_id' => $user->franchise_id,
            'ref_user_id' => $user->id,
            'description' => 'Student fee received | Invoice: ' . $invoiceNo,
            'credit' => $amount,
            'debit' => 0,
            'type' => 1,
            'date' => $paymentDate,
            'c_date' => now(),
            'op_bal' => $iOpBal,
            'cl_bal' => $iClBal,
            'by_user_id' => Auth::guard('institute')->id(),
        ]);

        // Student due ledger starts only after final admission, not on seat booking.
        if ($courseBook->status === 'RUN' || StudentTransaction::where('ref_type', 'course_book')->where('ref_id', $courseBook->id)->exists()) {
            $this->syncStudentPaymentTransaction($user, $fee);
        }
    }

    private function requiredAdmissionAmount(CourseBook $courseBook): float
    {
        $plan = $courseBook->paymentPlan;
        if (! $plan) {
            return (float) $courseBook->final_fee;
        }

        if ($plan->plan_type === 'OTP') {
            return round((float) $plan->total_fee, 2);
        }

        return round((float) ($plan->required_fee ?: 0), 2);
    }

    private function finalizeAdmissionIfEligible(CourseBook $courseBook): void
    {
        $courseBook->loadMissing(['student.profile', 'paymentPlan']);

        if ($courseBook->status !== 'OPEN') {
            return;
        }

        $paidAmount = (float) FeeCollectDetail::where('course_book_id', $courseBook->id)
            ->whereNull('cancelled_at')
            ->sum(FeeCollectDetail::amountColumn());

        if ($paidAmount <= 0) {
            return;
        }

        $this->createStudentAdmissionLedger($courseBook);
        $this->syncStudentPaymentTransactionsForCourseBook($courseBook);
        $this->deductFranchiseAdmissionCharge($courseBook);

        if (! $courseBook->enrollment_no) {
            $courseBook->update(['enrollment_no' => $this->generateEnrollmentNo($courseBook->loadMissing('course'))]);
        }

        $this->deductFranchiseAdmissionCharge($courseBook);

        $courseBook->update([
            'status' => 'RUN',
            'start_date' => $courseBook->start_date ?? now()->toDateString(),
        ]);
    }

    private function forceFinalizeAdmission(CourseBook $courseBook): void
    {
        $courseBook->loadMissing(['student.profile', 'paymentPlan', 'course']);

        if ($courseBook->status === 'RUN') {
            return;
        }

        $this->ensureBookingDebit($courseBook);
        $this->deductFranchiseAdmissionCharge($courseBook);

        if (! $courseBook->enrollment_no) {
            $courseBook->update(['enrollment_no' => $this->generateEnrollmentNo($courseBook)]);
        }

        $courseBook->update([
            'status' => 'RUN',
            'start_date' => $courseBook->start_date ?? now()->toDateString(),
        ]);
    }

    private function deductFranchiseAdmissionCharge(CourseBook $courseBook): void
    {
        if (! $courseBook->franchise_id) {
            return;
        }

        $franchise = Franchise::find($courseBook->franchise_id);
        if (! $franchise || $franchise->management_type !== 'wallet' || ! $franchise->wallet_enabled) {
            return;
        }

        $courseCharge = FranchiseCourseCharge::where('franchise_id', $franchise->id)
            ->where('course_id', $courseBook->course_id)
            ->first();

        $charge = $courseCharge ? (float) $courseCharge->admission_charge : 0;
        if ($charge <= 0) {
            return;
        }

        // Idempotency: don't deduct twice for the same course_book
        $alreadyDeducted = FranchiseTransaction::where('franchise_id', $franchise->id)
            ->where('type', 4)
            ->where('description', 'LIKE', '%course_book#' . $courseBook->id . '%')
            ->exists();

        if ($alreadyDeducted) {
            return;
        }

        DB::transaction(function () use ($franchise, $courseBook, $charge) {
            $wallet = FranchiseWallet::where('franchise_id', $franchise->id)->lockForUpdate()->first();
            if (! $wallet) {
                return;
            }

            $opBal = (float) $wallet->balance;
            $clBal = max(0, $opBal - $charge);

            FranchiseTransaction::create([
                'franchise_id' => $franchise->id,
                'institute_id' => $franchise->institute_id,
                'txn_no' => $this->invoiceService->generateFranchiseTxnNo($franchise->institute_id, $franchise->id),
                'description' => "Admission charge for {$courseBook->student?->user_id} | course_book#{$courseBook->id}",
                'credit' => 0,
                'debit' => $charge,
                'type' => 4,
                'op_bal' => $opBal,
                'cl_bal' => $clBal,
                'date' => now()->toDateString(),
                'c_date' => now(),
                'by_userid' => null,
            ]);

            $wallet->update(['balance' => $clBal]);
        });
    }

    private function syncAdmissionStatus(CourseBook $courseBook): void
    {
        $courseBook->loadMissing(['student.profile', 'paymentPlan']);
        if ($courseBook->status === 'RUN') {
            return;
        }

        $paidAmount = (float) FeeCollectDetail::where('course_book_id', $courseBook->id)
            ->sum(FeeCollectDetail::amountColumn());
        $requiredAmount = $this->requiredAdmissionAmount($courseBook);

        if ($courseBook->profile_completed_at && $paidAmount + 0.01 >= $requiredAmount) {
            $this->finalizeAdmissionIfEligible($courseBook);
            return;
        }

        $courseBook->update([
            'status' => 'OPEN',
            'start_date' => null,
        ]);
    }

    private function ensureBookingDebit(CourseBook $courseBook): void
    {
        $this->createStudentAdmissionLedger($courseBook);
    }

    private function applyCourseChangeAdjustment(CourseBook $courseBook, float $feeDiff, string $courseName): void
    {
        $hasDebitLedger = StudentTransaction::where('ref_type', 'course_book')
            ->where('ref_id', $courseBook->id)
            ->where('debit', '>', 0)
            ->exists();

        if (! $hasDebitLedger) {
            return;
        }

        $wallet = StudentWallet::firstOrCreate(
            ['user_id' => $courseBook->user_id],
            [
                'institute_id' => $courseBook->institute_id,
                'franchise_id' => $courseBook->franchise_id,
                'owner_type' => $courseBook->franchise_id ? 'franchise' : 'institute',
                'balance' => 0,
            ]
        );

        $opBal = (float) $wallet->balance;
        $clBal = $opBal - $feeDiff;
        $wallet->update(['balance' => $clBal]);

        StudentTransaction::create([
            'user_id' => $courseBook->user_id,
            'institute_id' => $courseBook->institute_id,
            'franchise_id' => $courseBook->franchise_id,
            'owner_type' => $courseBook->franchise_id ? 'franchise' : 'institute',
            'description' => 'Course change adjustment: ' . $courseName,
            'credit' => $feeDiff < 0 ? abs($feeDiff) : 0,
            'debit' => $feeDiff > 0 ? $feeDiff : 0,
            'type' => 4,
            'ref_type' => 'course_book_adjustment',
            'ref_id' => $courseBook->id,
            'date' => now()->toDateString(),
            'c_date' => now(),
            'op_bal' => $opBal,
            'cl_bal' => $clBal,
            'by_user_id' => Auth::guard('institute')->id(),
        ]);
    }

    private function createStudentAdmissionLedger(CourseBook $courseBook): void
    {
        $existingTxn = StudentTransaction::where('ref_type', 'course_book')
            ->where('ref_id', $courseBook->id)
            ->where('debit', '>', 0)
            ->exists();

        if ($existingTxn) {
            return;
        }

        $wallet = StudentWallet::firstOrCreate(
            ['user_id' => $courseBook->user_id],
            [
                'institute_id' => $courseBook->institute_id,
                'franchise_id' => $courseBook->franchise_id,
                'owner_type' => $courseBook->franchise_id ? 'franchise' : 'institute',
                'balance' => 0,
            ]
        );

        $opBal = (float) $wallet->balance;
        $clBal = $opBal - (float) $courseBook->final_fee;
        $wallet->update(['balance' => $clBal]);

        StudentTransaction::create([
            'user_id' => $courseBook->user_id,
            'institute_id' => $courseBook->institute_id,
            'franchise_id' => $courseBook->franchise_id,
            'owner_type' => $courseBook->franchise_id ? 'franchise' : 'institute',
            'description' => 'Course admission debit: ' . $courseBook->course->name,
            'credit' => 0,
            'debit' => $courseBook->final_fee,
            'type' => 1,
            'ref_type' => 'course_book',
            'ref_id' => $courseBook->id,
            'date' => now()->toDateString(),
            'c_date' => now(),
            'op_bal' => $opBal,
            'cl_bal' => $clBal,
            'by_user_id' => Auth::guard('institute')->id(),
        ]);
    }

    private function syncStudentPaymentTransactionsForCourseBook(CourseBook $courseBook): void
    {
        $courseBook->loadMissing('student');

        $receipts = FeeCollectDetail::where('course_book_id', $courseBook->id)
            ->orderBy('date')
            ->orderBy('id')
            ->get();

        foreach ($receipts as $receipt) {
            $this->syncStudentPaymentTransaction($courseBook->student, $receipt);
        }
    }

    private function syncStudentPaymentTransaction(User $user, FeeCollectDetail $fee): void
    {
        $existing = StudentTransaction::where('ref_type', 'fee_collect_detail')
            ->where('ref_id', $fee->id)
            ->exists();

        if ($existing) {
            return;
        }

        $wallet = StudentWallet::firstOrCreate(
            ['user_id' => $user->id],
            [
                'institute_id' => $fee->institute_id,
                'franchise_id' => $fee->franchise_id,
                'owner_type' => $user->franchise_id ? 'franchise' : 'institute',
                'balance' => 0,
            ]
        );
        $amountColumn = FeeCollectDetail::amountColumn();
        $amount = (float) $fee->{$amountColumn};
        $opBal = (float) $wallet->balance;
        $clBal = $opBal + $amount;
        $wallet->update(['balance' => $clBal]);

        StudentTransaction::create([
            'user_id' => $user->id,
            'institute_id' => $fee->institute_id,
            'franchise_id' => $fee->franchise_id,
            'owner_type' => $user->franchise_id ? 'franchise' : 'institute',
            'description' => $fee->payment_mode . ' payment received | Invoice: ' . $fee->invoice_no,
            'credit' => $amount,
            'debit' => 0,
            'type' => 2,
            'ref_type' => 'fee_collect_detail',
            'ref_id' => $fee->id,
            'date' => $fee->date,
            'c_date' => now(),
            'op_bal' => $opBal,
            'cl_bal' => $clBal,
            'by_user_id' => $fee->received_by,
        ]);
    }

    private function assertCourseBookingAllowed(int $userId, int $courseId, ?int $batchId, ?int $ignoreCourseBookId = null): void
    {
        $sessionId = $this->activeSessionId();

        $activeEnrollments = CourseBook::where('user_id', $userId)
            ->when(Schema::hasColumn('course_books', 'session_id'), fn ($query) => $query->where('session_id', $sessionId))
            ->whereIn('status', ['OPEN', 'RUN'])
            ->when($ignoreCourseBookId, fn ($query) => $query->where('id', '!=', $ignoreCourseBookId))
            ->get(['id', 'course_id', 'batch_id', 'status']);

        // Block only exact duplicate: same course + same batch
        $duplicate = $activeEnrollments->first(function ($enrollment) use ($courseId, $batchId) {
            if ((int) $enrollment->course_id !== $courseId) {
                return false;
            }
            // Both have same batch (including both null = no batch assigned)
            return (int) ($enrollment->batch_id ?? 0) === (int) ($batchId ?? 0);
        });

        if ($duplicate) {
            $msg = $batchId
                ? 'Is student ka yeh course + batch combination already active/booked hai. Koi aur batch ya course select karo.'
                : 'Is student ka yeh course already bina batch ke active/booked hai. Alag batch select karo ya naya course choose karo.';

            throw \Illuminate\Validation\ValidationException::withMessages([
                'course_id' => $msg,
            ]);
        }
    }

    private function authorizeStudentCourseBook(User $student, CourseBook $courseBook): void
    {
        abort_if($student->institute_id !== $this->instituteId(), 403);
        abort_if($courseBook->institute_id !== $this->instituteId() || $courseBook->user_id !== $student->id, 403);
    }

    private function isPaymentSelectionValid(string $planType, float $firstPayment, float $requiredFee, float $totalFee): bool
    {
        if ($firstPayment < 0 || $firstPayment > $totalFee) {
            return false;
        }

        return match ($planType) {
            'OTP'     => abs($firstPayment - $totalFee) < 0.01,
            'PART'    => $firstPayment >= 0.01,
            'MONTHLY' => $firstPayment + 0.01 >= $requiredFee,
            default   => $firstPayment >= 0.01,
        };
    }

    private function toCourseCode(string $input, string $courseName): string
    {
        $code = strtoupper(preg_replace('/[^A-Z0-9]/i', '', trim($input)));
        if ($code === '') {
            $code = implode('', array_map(
                fn ($w) => strtoupper($w[0] ?? ''),
                array_filter(explode(' ', $courseName), fn ($w) => strlen($w) > 0)
            ));
        }
        return substr($code, 0, 10) ?: 'CRS';
    }

    private function generateUserId(int $iid): string
    {
        $institute = \App\Models\Owner\Institute::find($iid);
        $short = strtoupper(preg_replace('/[^A-Z0-9]/i', '', $institute?->short_name ?? $institute?->name ?? 'INS'));
        $short = substr($short, 0, 8) ?: 'INS';
        $year = now()->year;

        $counter = InstituteEnrollmentCounter::where('institute_id', $iid)->lockForUpdate()->first();
        if (! $counter) {
            try {
                InstituteEnrollmentCounter::create(['institute_id' => $iid, 'last_enrollment_no' => 0, 'last_student_no' => 0]);
            } catch (\Throwable) {}
            $counter = InstituteEnrollmentCounter::where('institute_id', $iid)->lockForUpdate()->firstOrFail();
        }

        $counter->increment('last_student_no');

        return $short . '/STU/' . $year . '/' . str_pad((string) ($counter->last_student_no), 4, '0', STR_PAD_LEFT);
    }

    private function sendSeatBookingEmail(?User $user, CourseBook $courseBook, ?string $plainPassword = null): void
    {
        if (! $user?->email) {
            return;
        }

        $institute    = \App\Models\Owner\Institute::find($courseBook->institute_id);
        $validityDays = $institute?->seat_booking_validity_days ?? 30;
        $instituteName = $institute?->name ?? '';

        try {
            Mail::to($user->email)->send(new SeatBookingConfirmationMail(
                $user->fresh(['profile']),
                $courseBook->fresh(['course', 'batch', 'student.profile']),
                $validityDays,
                $instituteName,
            ));
        } catch (\Throwable $e) {
            Log::warning('Seat booking email failed: ' . $e->getMessage(), [
                'user_id' => $user->id ?? null,
                'course_book_id' => $courseBook->id ?? null,
            ]);
        }
    }

    private function sendAdmissionConfirmationEmail(CourseBook $courseBook): void
    {
        $user = $courseBook->student;

        if (! $user?->email) {
            return;
        }

        // Only send credentials if this is the student's first confirmed admission
        $confirmedCount = CourseBook::where('student_id', $user->id)
            ->where('status', 'RUN')
            ->count();

        if ($confirmedCount > 1) {
            return;
        }

        $plainPassword = \Illuminate\Support\Str::random(10);
        $user->password       = \Illuminate\Support\Facades\Hash::make($plainPassword);
        $user->remember_token = \Illuminate\Support\Str::random(60);
        $user->save();

        $instituteName = \App\Models\Owner\Institute::find($courseBook->institute_id)?->name ?? '';

        try {
            Mail::to($user->email)->send(new \App\Mail\AdmissionConfirmationMail(
                $user->fresh(['profile']),
                $courseBook->fresh(['course', 'batch', 'student.profile']),
                $plainPassword,
                $instituteName,
            ));
        } catch (\Throwable $e) {
            Log::warning('Admission confirmation email failed: ' . $e->getMessage(), [
                'user_id' => $user->id ?? null,
                'course_book_id' => $courseBook->id ?? null,
            ]);
        }
    }

    private function generateEnrollmentNo(CourseBook $courseBook): string
    {
        $iid = $courseBook->institute_id;
        $institute = Auth::guard('institute')->user()->institute
            ?? \App\Models\Owner\Institute::find($iid);
        $short = strtoupper(preg_replace('/[^A-Z0-9]/i', '', $institute?->short_name ?? $institute?->name ?? 'INS'));
        $short = substr($short, 0, 8) ?: 'INS';

        $courseCode = strtoupper(trim($courseBook->course_code ?? ''));
        if (! $courseCode) {
            $courseName = $courseBook->course?->name ?? 'COURSE';
            $courseCode = implode('', array_map(
                fn ($w) => strtoupper($w[0] ?? ''),
                array_filter(explode(' ', $courseName), fn ($w) => strlen($w) > 0)
            ));
        }
        $courseCode = substr($courseCode, 0, 10) ?: 'CRS';

        $year = now()->year;

        $counter = InstituteEnrollmentCounter::where('institute_id', $iid)->lockForUpdate()->first();
        if (! $counter) {
            try {
                InstituteEnrollmentCounter::create(['institute_id' => $iid, 'last_enrollment_no' => 0, 'last_student_no' => 0]);
            } catch (\Throwable) {}
            $counter = InstituteEnrollmentCounter::where('institute_id', $iid)->lockForUpdate()->firstOrFail();
        }

        $counter->increment('last_enrollment_no');

        return $short . '/' . $courseCode . '/' . $year . '/' . str_pad((string) $counter->last_enrollment_no, 4, '0', STR_PAD_LEFT);
    }

    private function calculateLateFee(EnrollmentPaymentPlan $plan): float
    {
        if (! $plan->next_due_date || ! $plan->late_fee_per_day) {
            return 0;
        }

        $graceEnd = \Carbon\Carbon::parse($plan->next_due_date)->addDays((int) ($plan->grace_days ?? 0));

        if (now()->lte($graceEnd)) {
            return 0;
        }

        return round(now()->diffInDays($graceEnd) * (float) $plan->late_fee_per_day, 2);
    }

    private function ensureStudentWallet(User $user): StudentWallet
    {
        return StudentWallet::firstOrCreate(
            ['user_id' => $user->id],
            [
                'institute_id' => $user->institute_id,
                'franchise_id' => $user->franchise_id,
                'owner_type' => $user->franchise_id ? 'franchise' : 'institute',
                'balance' => 0,
            ]
        );
    }

    private function authorizeCourseBook(CourseBook $courseBook): void
    {
        $user = Auth::guard('institute')->user();

        if ($courseBook->institute_id !== $this->instituteId()) {
            abort(403);
        }

        // Staff (not institute_head) cannot access franchise-owned enrollments
        if ($user->role === 'staff' && $courseBook->franchise_id !== null) {
            abort(403);
        }
    }
}
