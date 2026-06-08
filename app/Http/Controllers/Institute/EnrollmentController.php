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
use App\Mail\SeatBookingConfirmationMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, DB, Hash, Log, Mail, Schema};
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

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

    private function defaultPhotoPath(): string
    {
        return 'images/user.png';
    }

    public function choose()
    {
        return view('institute.enrollment.choose');
    }

    public function pending()
    {
        $iid = $this->instituteId();
        $filter = request('filter', 'all');
        $pendingBooks = CourseBook::with(['student.profile', 'course', 'paymentPlan'])
            ->where('institute_id', $iid)
            ->whereIn('status', ['OPEN', 'RUN'])
            ->latest()
            ->get()
            ->map(function (CourseBook $courseBook) {
                $plan = $courseBook->paymentPlan;
                $requiredAmount = $this->requiredAdmissionAmount($courseBook);
                $paidAmount = (float) FeeCollectDetail::where('course_book_id', $courseBook->id)
                    ->sum(FeeCollectDetail::amountColumn());

                $courseBook->setAttribute('paid_amount', $paidAmount);
                $courseBook->setAttribute('required_amount', $requiredAmount);
                $courseBook->setAttribute('details_complete', (bool) $courseBook->profile_completed_at);
                $courseBook->setAttribute('admission_ready', $courseBook->profile_completed_at && $paidAmount + 0.01 >= $requiredAmount);
                $courseBook->setAttribute('plan_code', $plan?->plan_type);

                return $courseBook;
            });

        $pendingBooks = $pendingBooks->filter(function (CourseBook $courseBook) use ($filter) {
            return match ($filter) {
                'details_pending' => ! $courseBook->details_complete,
                'payment_pending' => $courseBook->paid_amount + 0.01 < $courseBook->required_amount,
                'ready' => $courseBook->details_complete && $courseBook->paid_amount + 0.01 >= $courseBook->required_amount,
                'admitted' => $courseBook->status === 'RUN',
                'booked' => $courseBook->status === 'OPEN',
                'quick' => $courseBook->booking_mode === 'quick',
                'full' => $courseBook->booking_mode === 'full',
                default => true,
            };
        })->values();

        return view('institute.enrollment.pending', compact('pendingBooks', 'filter'));
    }

    public function newStudent()
    {
        return view('institute.enrollment.new', $this->admissionFormData());
    }

    public function quickStudent()
    {
        return view('institute.enrollment.quick', $this->admissionFormData());
    }

    public function findStudent(Request $request)
    {
        $request->validate(['search' => 'required|string']);
        $search = trim($request->search);
        $iid = $this->instituteId();

        $user = User::where('institute_id', $iid)
            ->where('role', 'student')
            ->where(function ($query) use ($search) {
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
        $result = DB::transaction(function () use (
            $data,
            $iid,
            $sessionId,
            $course
        ) {
            $userId = $this->generateUserId($iid);
            $user = User::create([
                'institute_id' => $iid,
                'user_id' => $userId,
                'mobile' => $data['mobile'],
                'email' => $data['email'] ?? null,
                'password' => Hash::make(Str::random(10)),
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

            $courseBook = CourseBook::create([
                'institute_id' => $iid,
                'franchise_id' => null,
                'channel_partner_id' => $data['channel_partner_id'] ?? null,
                'session_id' => $sessionId,
                'user_id' => $user->id,
                'course_id' => $course->id,
                'batch_id' => $data['batch_id'] ?? null,
                'enrollment_no' => null,
                'fee' => 0,
                'final_fee' => 0,
                'book_date' => now()->toDateString(),
                'start_date' => null,
                'status' => 'OPEN',
                'booking_mode' => 'full',
                'profile_completed_at' => now(),
                'admission_by' => Auth::guard('institute')->id(),
            ]);

            $this->ensureStudentWallet($user);

            return [
                'courseBook' => $courseBook->fresh(['student.profile', 'course']),
                'user' => $user->fresh(['profile']),
            ];
        });

        $this->sendSeatBookingEmail($result['user'], $result['courseBook']);

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

        $result = DB::transaction(function () use ($data, $iid, $sessionId, $course, $courseSummary, $fields) {
            $userId = $this->generateUserId($iid);
            $user = User::create([
                'institute_id' => $iid,
                'user_id' => $userId,
                'mobile' => $data['mobile'],
                'email' => $data['email'] ?? null,
                'password' => Hash::make(Str::random(10)),
                'role' => 'student',
                'user_type' => 'student',
                'franchise_id' => null,
                'channel_partner_id' => $data['channel_partner_id'] ?? null,
                'owner_type' => 'institute',
                'status' => 'active',
            ]);

            $this->ensureStudentWallet($user);

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

            $user = $user->fresh(['profile']);

            return [
                'courseBook' => $courseBook->fresh(['student.profile', 'course']),
                'user' => $user,
            ];
        });

        $this->sendSeatBookingEmail($result['user'], $result['courseBook']);

        return redirect()->route('institute.enrollment.pending')
            ->with('success', 'Quick seat booking saved. Complete the remaining details before final admission.');
    }

    public function profileForm(CourseBook $courseBook)
    {
        $this->authorizeCourseBook($courseBook);
        $fields = AdmissionFormField::where('institute_id', $this->instituteId())
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();
        $profile = $courseBook->student->profile;
        $education = $courseBook->student->education;
        $states = State::orderBy('name')->pluck('name');
        $educationField = AdmissionFormField::where('institute_id', $this->instituteId())
            ->where('field_key', 'education_details')
            ->first();
        $educationEnabled = ! $educationField || $educationField->is_active;
        $educationRequired = (bool) ($educationField?->is_required);

        return view('institute.enrollment.profile', compact('courseBook', 'fields', 'profile', 'education', 'states', 'educationEnabled', 'educationRequired'));
    }

    public function saveProfile(Request $request, CourseBook $courseBook)
    {
        $this->authorizeCourseBook($courseBook);
        $iid = $this->instituteId();
        $fields = AdmissionFormField::where('institute_id', $iid)->where('is_active', true)->get();
        $user = $courseBook->student;

        $rules = [];
        $nonProfileKeys = AdmissionFormField::nonProfileKeys();
        foreach ($fields as $field) {
            if (in_array($field->field_key, $nonProfileKeys, true)) {
                continue;
            }
            $rules[$field->field_key] = $field->is_required ? 'required' : 'nullable';
            if ($field->field_type === 'file') {
                $rules[$field->field_key] .= '|image|max:2048';
            }
            if ($field->field_key === 'mobile') {
                $rules[$field->field_key] = ($field->is_required ? 'required' : 'nullable') . '|string|max:15|unique:users,mobile,' . $user->id;
            }
            if ($field->field_key === 'email') {
                $rules[$field->field_key] = ($field->is_required ? 'required' : 'nullable') . '|email|max:100|unique:users,email,' . $user->id;
            }
            if ($field->field_key === 'state') {
                $rules[$field->field_key] = [
                    $field->is_required ? 'required' : 'nullable',
                    'string',
                    'max:100',
                    Rule::exists('states', 'name'),
                ];
            }
        }

        $validated = $request->validate($rules);
        $profile = $user->profile ?? UserProfile::make(['user_id' => $courseBook->user_id, 'name' => '']);

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

        $educationField = $fields->firstWhere('field_key', 'education_details');
        if ($educationField?->is_required && ! $user->education()->exists()) {
            return back()->withErrors(['education_details' => 'At least one education record is required.'])->withInput();
        }

        $courseBook->update([
            'profile_completed_at' => now(),
            'booking_mode' => $courseBook->booking_mode === 'quick' ? 'quick' : 'full',
        ]);

        return redirect()->route('institute.enrollment.fee', $courseBook);
    }

    public function feeForm(CourseBook $courseBook)
    {
        $this->authorizeCourseBook($courseBook);
        $iid = $this->instituteId();
        $courseBook->loadMissing(['course.feeStructures.feeType']);
        $courseSummary = $this->courseCatalogItem($courseBook->course);
        $feeStructure = collect($courseSummary['fee_items'])->map(fn ($item) => (object) [
            'fee_type_id' => $item['fee_type_id'],
            'fee_type_name' => $item['fee_type_name'],
            'amount' => $item['amount'],
            'is_mandatory' => $item['is_mandatory'],
        ]);
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
            'first_payment_amount' => 'required|numeric|min:0',
            'payment_mode' => 'required|in:CASH,UPI,NEFT,IMPS,CHEQUE',
            'payment_date' => 'required|date',
            'utr' => 'nullable|string|max:80',
            'payment_note' => 'nullable|string|max:255',
        ]);

        $plan = PaymentPlanType::where('institute_id', $this->instituteId())->findOrFail($request->payment_plan_type_id);
        $courseBook->loadMissing(['course.feeStructures.feeType', 'paymentPlan']);
        $feeQuote = $this->buildFeeQuote($courseBook, $request->input('fee_items', []));
        $terms = $this->paymentTerms($plan, $courseBook, $feeQuote, $courseBook->paymentPlan);

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
        $paidTotal = (float) FeeCollectDetail::where('course_book_id', $courseBook->id)->sum($amountColumn);
        $latestFee = FeeCollectDetail::where('course_book_id', $courseBook->id)->latest('id')->first();
        $fees = FeeCollectDetail::where('course_book_id', $courseBook->id)->orderByDesc('id')->paginate(10);

        return view('institute.enrollment.payment-complete', compact('courseBook', 'fees', 'latestFee', 'paidTotal'));
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

        return view('institute.enrollment.preview', compact('courseBook', 'fields', 'profile', 'education', 'snapshots', 'plan', 'educationEnabled', 'displayTotalFee'));
    }

    public function confirm(Request $request, CourseBook $courseBook)
    {
        $this->authorizeCourseBook($courseBook);

        $courseBook->update([
            'profile_completed_at' => $courseBook->profile_completed_at ?? now(),
        ]);

        $this->forceFinalizeAdmission($courseBook->fresh(['student.profile', 'paymentPlan', 'course']));

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
            'passing_year' => 'nullable|string|max:10',
            'division' => 'nullable|string|max:50',
            'marks_percentage' => 'nullable|string|max:10',
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
            'course_id' => 'required|exists:course_details,id',
            'batch_id' => 'nullable|exists:batch_details,id',
            'user_id' => 'required|exists:users,id',
        ]);

        $user = User::findOrFail($data['user_id']);
        $this->assertCourseBookingAllowed($user->id, (int) $data['course_id'], $data['batch_id'] ?? null);

        $courseBookData = [
            'institute_id' => $iid,
            'user_id' => $user->id,
            'course_id' => $data['course_id'],
            'batch_id' => $data['batch_id'] ?? null,
            'enrollment_no' => null,
            'status' => 'OPEN',
            'booking_mode' => 'existing',
            'book_date' => now()->toDateString(),
            'profile_completed_at' => $user->profile ? now() : null,
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
        if (Schema::hasColumn('course_books', 'admission_by')) {
            $courseBookData['admission_by'] = Auth::guard('institute')->id();
        }

        $courseBook = CourseBook::create($courseBookData);
        $this->ensureStudentWallet($user);
        $this->sendSeatBookingEmail($user, $courseBook->fresh(['student.profile', 'course', 'batch']));

        return redirect()->route('institute.enrollment.profile', $courseBook);
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
        $defaults = [
            'OTP' => 'One Time Payment',
            'PART' => 'Partial Payment',
            'MONTHLY' => 'Monthly Payment',
        ];

        foreach ($defaults as $type => $name) {
            PaymentPlanType::firstOrCreate(
                ['institute_id' => $iid, 'type' => $type],
                [
                    'name' => $name,
                    'grace_days' => 0,
                    'late_fee_per_day' => 0,
                    'is_active' => true,
                ]
            );
        }

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
            'mobile' => 'required|string|max:15|unique:users,mobile',
            'email' => 'nullable|email|max:100|unique:users,email',
            'photo' => 'nullable|image|max:2048',
            'father_name' => $required('father_name', 'nullable|string|max:100'),
            'mother_name' => $required('mother_name', 'nullable|string|max:100'),
            'guardian_name' => $required('guardian_name', 'nullable|string|max:100'),
            'guardian_relation' => $required('guardian_relation', 'nullable|string|max:50'),
            'guardian_mobile' => $required('guardian_mobile', 'nullable|string|max:15'),
            'guardian_occupation' => $required('guardian_occupation', 'nullable|string|max:80'),
            'dob' => 'nullable|date',
            'gender' => 'nullable|in:Male,Female,Other',
            'category' => 'nullable|string|max:20',
            'religion' => 'nullable|string|max:50',
            'nationality' => 'nullable|string|max:50',
            'whatsapp_no' => 'nullable|string|max:15',
            'alternate_mobile' => 'nullable|string|max:15',
            'aadhar_no' => 'nullable|string|max:16',
            'pan_no' => 'nullable|string|max:10',
            'blood_group' => 'nullable|string|max:5',
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
            'pin_code' => 'nullable|string|max:10',
            'permanent_state' => ['nullable', 'string', 'max:100', Rule::exists('states', 'name')],
            'permanent_district' => array_values(array_filter([
                'nullable',
                'string',
                'max:100',
                Schema::hasTable('districts') ? Rule::exists('districts', 'name') : null,
            ])),
            'permanent_city' => 'nullable|string|max:100',
            'permanent_pin_code' => 'nullable|string|max:10',
            'education' => 'nullable|array',
            'education.*.examination' => 'nullable|string|max:80',
            'education.*.institute_name' => 'nullable|string|max:150',
            'education.*.board_university' => 'nullable|string|max:150',
            'education.*.passing_year' => 'nullable|string|max:10',
            'education.*.division' => 'nullable|string|max:50',
            'education.*.marks_percentage' => 'nullable|string|max:10',
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
            'mobile' => 'required|string|max:15|unique:users,mobile',
            'email' => 'nullable|email|max:100|unique:users,email',
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
        $courseBook->loadMissing(['course.feeStructures.feeType']);
        $catalog = collect($this->courseCatalogItem($courseBook->course)['fee_items'])->values();
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
        $totalFee = round((float) $feeQuote['total_fee'], 2);
        $requiredFee = match ($plan->type) {
            'OTP' => $totalFee,
            default => min($totalFee, round((float) ($feeQuote['required_fee'] ?: $totalFee), 2)),
        };

        $firstPaymentAmount = match ($plan->type) {
            'OTP' => $totalFee,
            default => min($totalFee, max($requiredFee, round((float) ($existingPlan?->first_payment_amount ?? 0), 2))),
        };

        $duration = max(1, (int) ($courseBook->course->duration ?: 1));

        return [
            'required_fee' => $requiredFee,
            'first_payment_amount' => $firstPaymentAmount,
            'remaining_fee' => $totalFee,
            'monthly_amount' => $plan->type === 'MONTHLY' ? round($totalFee / $duration, 2) : null,
            'next_due_date' => $plan->type === 'MONTHLY' ? now()->addMonth()->toDateString() : null,
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

        if ($courseBook->status !== 'OPEN' || ! $courseBook->profile_completed_at) {
            return;
        }

        if (! $courseBook->paymentPlan && (float) $courseBook->final_fee <= 0) {
            return;
        }

        $paidAmount = (float) FeeCollectDetail::where('course_book_id', $courseBook->id)
            ->sum(FeeCollectDetail::amountColumn());
        $requiredAmount = $this->requiredAdmissionAmount($courseBook);

        if ($paidAmount + 0.01 < $requiredAmount) {
            return;
        }

        $this->createStudentAdmissionLedger($courseBook);
        $this->syncStudentPaymentTransactionsForCourseBook($courseBook);

        if (! $courseBook->enrollment_no) {
            $enrollmentNo = $this->generateEnrollmentNo($courseBook->institute_id);
            $courseBook->update(['enrollment_no' => $enrollmentNo]);
        }

        $courseBook->update([
            'status' => 'RUN',
            'start_date' => $courseBook->start_date ?? now()->toDateString(),
        ]);
    }

    private function forceFinalizeAdmission(CourseBook $courseBook): void
    {
        $courseBook->loadMissing(['student.profile', 'paymentPlan', 'course']);

        if ($courseBook->status === 'RUN' || ! $courseBook->profile_completed_at) {
            return;
        }

        $this->ensureBookingDebit($courseBook);

        if (! $courseBook->enrollment_no) {
            $enrollmentNo = $this->generateEnrollmentNo($courseBook->institute_id);
            $courseBook->update(['enrollment_no' => $enrollmentNo]);
        }

        $courseBook->update([
            'status' => 'RUN',
            'start_date' => $courseBook->start_date ?? now()->toDateString(),
        ]);
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

        if ($activeEnrollments->contains(fn ($enrollment) => (int) $enrollment->course_id === $courseId)) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'course_id' => 'Same student ka yeh course already active/seat-booked hai current session me.',
            ]);
        }

        if ($batchId && $activeEnrollments->contains(fn ($enrollment) => (int) $enrollment->batch_id === $batchId)) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'batch_id' => 'Student ka koi aur active/seat-booked course already isi batch me hai. Same batch duplicate allowed nahi hai.',
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

        if ($planType === 'OTP') {
            return abs($firstPayment - $totalFee) < 0.01;
        }

        return $firstPayment + 0.01 >= $requiredFee;
    }

    private function generateUserId(int $iid): string
    {
        for ($attempt = 0; $attempt < 10; $attempt++) {
            $candidate = 'S' . $iid . now()->format('ymdHis') . strtoupper(Str::random(4));
            if (! User::where('user_id', $candidate)->exists()) {
                return $candidate;
            }
        }

        return 'S' . $iid . now()->format('ymdHis') . strtoupper(Str::random(2));
    }

    private function sendSeatBookingEmail(?User $user, CourseBook $courseBook): void
    {
        if (! $user?->email) {
            return;
        }

        try {
            Mail::to($user->email)->send(new SeatBookingConfirmationMail(
                $user->fresh(['profile']),
                $courseBook->fresh(['course', 'batch', 'student.profile'])
            ));
        } catch (\Throwable $e) {
            Log::warning('Seat booking email failed: ' . $e->getMessage(), [
                'user_id' => $user->id ?? null,
                'course_book_id' => $courseBook->id ?? null,
            ]);
        }
    }

    private function generateEnrollmentNo(int $iid): string
    {
        $counter = InstituteEnrollmentCounter::where('institute_id', $iid)->lockForUpdate()->first();
        if (! $counter) {
            try {
                InstituteEnrollmentCounter::create([
                    'institute_id' => $iid,
                    'last_enrollment_no' => 0,
                ]);
            } catch (\Throwable $e) {
                // Concurrent request may create the row first.
            }

            $counter = InstituteEnrollmentCounter::where('institute_id', $iid)->lockForUpdate()->firstOrFail();
        }

        $counter->last_enrollment_no++;
        $counter->save();

        $code = Auth::guard('institute')->user()->institute?->unique_id ?? 'INST';

        return $code . '/ENR/' . str_pad((string) $counter->last_enrollment_no, 4, '0', STR_PAD_LEFT);
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
        if ($courseBook->institute_id !== $this->instituteId()) {
            abort(403);
        }
    }
}
