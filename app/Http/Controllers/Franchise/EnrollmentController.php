<?php

namespace App\Http\Controllers\Franchise;

use App\Http\Controllers\Controller;
use App\Models\{
    AdmissionFormField, BatchDetail, ChannelPartner, CourseBook, CourseDetail,
    EnrollmentFeeSnapshot, EnrollmentPaymentPlan, FeeCollectDetail, FeeType,
    FranchiseCourseCharge, FranchiseFeeStructure, FranchiseTransaction, FranchiseWallet,
    InstituteEnrollmentCounter, InstituteSession, InstituteStudentTransaction,
    InstituteStudentWallet, PaymentPlanType, State, StudentTransaction,
    StudentWallet, User, UserEducation, UserProfile
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

    // ── Choose ───────────────────────────────────────────────────────────────

    public function choose()
    {
        return view('franchise.enrollment.choose');
    }

    // ── New Admission ────────────────────────────────────────────────────────

    private function buildCatalog(int $iid, int $fid): \Illuminate\Support\Collection
    {
        $courses = CourseDetail::with(['courseType', 'feeStructures.feeType'])
            ->where('institute_id', $iid)->where('status', 'active')->orderBy('name')->get();

        $franchiseCharges = FranchiseCourseCharge::where('franchise_id', $fid)
            ->where('enabled', true)->get()->keyBy('course_id');
        $franchiseFees = FranchiseFeeStructure::where('franchise_id', $fid)
            ->where('enabled', true)->get()->groupBy('course_id');

        return $courses->filter(fn ($c) => isset($franchiseCharges[$c->id]))->map(function ($c) use ($franchiseCharges, $franchiseFees) {
            $charge  = $franchiseCharges[$c->id];
            $baseFee = $charge->student_fee > 0
                ? round((float) $charge->student_fee, 2)
                : round((float) $c->fee, 2);

            $extraFees = ($franchiseFees[$c->id] ?? collect())->map(fn ($fs) => [
                'fee_type_id'   => $fs->fee_type_id,
                'fee_type_name' => $fs->fee_type_name,
                'amount'        => round((float) $fs->amount, 2),
                'is_mandatory'  => false,
            ]);

            $feeItems = collect([[
                'fee_type_id'   => null,
                'fee_type_name' => 'Course Fee',
                'amount'        => $baseFee,
                'is_mandatory'  => true,
            ]])->merge($extraFees)->values()->all();

            $totalFee    = collect($feeItems)->sum('amount');
            $requiredFee = collect($feeItems)->where('is_mandatory', true)->sum('amount');

            return [
                'id'              => $c->id,
                'name'            => $c->name,
                'duration'        => (int) ($c->duration_months ?? $c->duration ?? 1),
                'course_type_id'  => $c->course_type_id,
                'course_type_name'=> $c->courseType?->name,
                'total_fee'       => $totalFee,
                'required_fee'    => $requiredFee,
                'fee_items'       => $feeItems,
            ];
        })->values();
    }

    private function sharedNewData(int $iid, \Illuminate\Support\Collection $catalog): array
    {
        $batches = BatchDetail::forFranchise($this->franchiseId())->where('status', 'active')->orderBy('name')->get();
        $states  = State::orderBy('name')->pluck('name');
        $districtsByState = Schema::hasTable('districts')
            ? \App\Models\District::select('districts.name as district_name', 'states.name as state_name')
                ->join('states', 'states.id', '=', 'districts.state_id')
                ->orderBy('states.name')->orderBy('districts.name')->get()
                ->groupBy('state_name')
                ->map(fn ($r) => $r->pluck('district_name')->values())->toArray()
            : [];

        $assignedTypeIds = $catalog->pluck('course_type_id')->unique()->filter()->values();
        $courseTypes = \App\Models\CourseType::whereIn('id', $assignedTypeIds)->orderBy('name')->get();

        $savedFields = AdmissionFormField::where('institute_id', $iid)->get()->keyBy('field_key');

        $channelPartners = ChannelPartner::where('institute_id', $iid)
            ->where('status', 'active')->orderBy('name')->get(['id', 'name', 'mobile']);

        return compact('batches', 'states', 'districtsByState', 'courseTypes', 'savedFields', 'channelPartners')
            + ['defaultPhotoPath' => 'images/user.svg'];
    }

    public function newStudent()
    {
        $iid = $this->instituteId();
        $fid = $this->franchiseId();
        $courseCatalog = $this->buildCatalog($iid, $fid);
        return view('franchise.enrollment.new',
            array_merge($this->sharedNewData($iid, $courseCatalog), compact('courseCatalog')));
    }

    public function quick()
    {
        $iid = $this->instituteId();
        $fid = $this->franchiseId();
        $courseCatalog = $this->buildCatalog($iid, $fid);
        return view('franchise.enrollment.quick',
            array_merge($this->sharedNewData($iid, $courseCatalog), compact('courseCatalog')));
    }

    public function storeQuick(Request $request)
    {
        // Delegate to storeNew — quick booking uses same backend,
        // booking_mode distinguishes it
        return $this->storeNewInternal($request, 'quick');
    }

    public function storeNew(Request $request)
    {
        return $this->storeNewInternal($request, 'full');
    }

    private function storeNewInternal(Request $request, string $bookingMode)
    {
        $iid       = $this->instituteId();
        $fid       = $this->franchiseId();
        $sessionId = $this->activeSessionId();

        $data = $request->validate([
            'name'                => 'required|string|max:100',
            'mobile'              => 'required|string|max:15|unique:users,mobile',
            'email'               => 'nullable|email|max:100|unique:users,email',
            'photo'               => 'nullable|image|max:2048',
            'course_id'           => ['required', \Illuminate\Validation\Rule::exists('course_details', 'id')
                ->where(fn ($q) => $q->where('institute_id', $iid))],
            'batch_id'            => ['nullable', \Illuminate\Validation\Rule::exists('batch_details', 'id')
                ->where(fn ($q) => $q->where('franchise_id', $this->franchiseId()))],
            'admission_source'    => 'nullable|in:direct,channel_partner',
            'dob'                 => 'nullable|date',
            'gender'              => 'nullable|in:Male,Female,Other',
            'category'            => 'nullable|string|max:20',
            'religion'            => 'nullable|string|max:50',
            'nationality'         => 'nullable|string|max:50',
            'whatsapp_no'         => 'nullable|string|max:15',
            'alternate_mobile'    => 'nullable|string|max:15',
            'aadhar_no'           => 'nullable|string|max:16',
            'pan_no'              => 'nullable|string|max:10',
            'blood_group'         => 'nullable|string|max:5',
            'employment_status'   => 'nullable|in:Employed,Unemployed',
            'computer_literacy'   => 'nullable|in:Yes,No',
            'qualification'       => 'nullable|string|max:80',
            'father_name'         => 'nullable|string|max:100',
            'mother_name'         => 'nullable|string|max:100',
            'guardian_name'       => 'nullable|string|max:100',
            'guardian_relation'   => 'nullable|string|max:50',
            'guardian_mobile'     => 'nullable|string|max:15',
            'guardian_occupation' => 'nullable|string|max:80',
            'address'             => 'nullable|string',
            'permanent_address'   => 'nullable|string',
            'state'               => 'nullable|string|max:100',
            'district'            => 'nullable|string|max:100',
            'city'                => 'nullable|string|max:100',
            'pin_code'            => 'nullable|string|max:10',
            'permanent_state'     => 'nullable|string|max:100',
            'permanent_district'  => 'nullable|string|max:100',
            'permanent_city'      => 'nullable|string|max:100',
            'permanent_pin_code'  => 'nullable|string|max:10',
            'education'                      => 'nullable|array',
            'education.*.examination'        => 'nullable|string|max:80',
            'education.*.institute_name'     => 'nullable|string|max:150',
            'education.*.board_university'   => 'nullable|string|max:150',
            'education.*.passing_year'       => 'nullable|string|max:10',
            'education.*.division'           => 'nullable|string|max:50',
            'education.*.marks_percentage'   => 'nullable|string|max:10',
        ]);

        // Build fee catalog entry for this course (franchise-specific pricing)
        $catalog     = $this->buildCatalog($iid, $fid);
        $catalogItem = $catalog->firstWhere('id', (int) $data['course_id']);
        $totalFee    = $catalogItem ? (float) $catalogItem['total_fee'] : 0.0;
        $feeItems    = $catalogItem ? $catalogItem['fee_items'] : [];

        DB::transaction(function () use ($data, $request, $iid, $fid, $sessionId, $bookingMode, $totalFee, $feeItems) {
            // Handle photo upload
            $photoPath = 'images/user.svg';
            if ($request->hasFile('photo') && $request->file('photo')->isValid()) {
                $photoPath = $request->file('photo')->store('student-photos', 'public');
                $photoPath = 'storage/' . $photoPath;
            }

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
                'user_id'             => $user->id,
                'institute_id'        => $iid,
                'name'                => $data['name'],
                'photo'               => $photoPath,
                'father_name'         => $data['father_name'] ?? null,
                'mother_name'         => $data['mother_name'] ?? null,
                'guardian_name'       => $data['guardian_name'] ?? null,
                'guardian_relation'   => $data['guardian_relation'] ?? null,
                'guardian_mobile'     => $data['guardian_mobile'] ?? null,
                'guardian_occupation' => $data['guardian_occupation'] ?? null,
                'dob'                 => $data['dob'] ?? null,
                'gender'              => $data['gender'] ?? null,
                'category'            => $data['category'] ?? null,
                'religion'            => $data['religion'] ?? null,
                'nationality'         => $data['nationality'] ?? null,
                'whatsapp_no'         => $data['whatsapp_no'] ?? null,
                'alternate_mobile'    => $data['alternate_mobile'] ?? null,
                'aadhar_no'           => $data['aadhar_no'] ?? null,
                'pan_no'              => $data['pan_no'] ?? null,
                'blood_group'         => $data['blood_group'] ?? null,
                'employment_status'   => $data['employment_status'] ?? null,
                'computer_literacy'   => $data['computer_literacy'] ?? null,
                'qualification'       => $data['qualification'] ?? null,
                'address'             => $data['address'] ?? null,
                'permanent_address'   => $data['permanent_address'] ?? null,
                'state'               => $data['state'] ?? null,
                'district'            => $data['district'] ?? null,
                'city'                => $data['city'] ?? null,
                'pin_code'            => $data['pin_code'] ?? null,
                'permanent_state'     => $data['permanent_state'] ?? null,
                'permanent_district'  => $data['permanent_district'] ?? null,
                'permanent_city'      => $data['permanent_city'] ?? null,
                'permanent_pin_code'  => $data['permanent_pin_code'] ?? null,
                'r_date'              => now()->toDateString(),
                'issue_date'          => now()->toDateString(),
            ]);

            foreach ($data['education'] ?? [] as $edu) {
                if (blank($edu['examination'] ?? null)) continue;
                UserEducation::create([
                    'user_id'          => $user->id,
                    'institute_id'     => $iid,
                    'franchise_id'     => $fid,
                    'examination'      => $edu['examination'],
                    'institute_name'   => $edu['institute_name'] ?? null,
                    'board_university' => $edu['board_university'] ?? null,
                    'passing_year'     => $edu['passing_year'] ?? null,
                    'division'         => $edu['division'] ?? null,
                    'marks_percentage' => $edu['marks_percentage'] ?? null,
                ]);
            }

            $courseBook = CourseBook::create([
                'institute_id'        => $iid,
                'franchise_id'        => $fid,
                'session_id'          => $sessionId,
                'user_id'             => $user->id,
                'course_id'           => $data['course_id'],
                'batch_id'            => $data['batch_id'] ?? null,
                'enrollment_no'       => null,
                'fee'                 => $totalFee,
                'final_fee'           => $totalFee,
                'book_date'           => now()->toDateString(),
                'status'              => 'OPEN',
                'booking_mode'        => $bookingMode,
                'profile_completed_at'=> now(),
                'admission_by'        => Auth::guard('institute')->id(),
            ]);

            // Snapshot of franchise fee structure at booking time
            foreach ($feeItems as $item) {
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

            StudentWallet::firstOrCreate(
                ['user_id' => $user->id],
                ['institute_id' => $iid, 'franchise_id' => $fid, 'owner_type' => 'franchise', 'balance' => 0]
            );
        });

        return redirect()->route('franchise.enrollment.pending')
            ->with('success', 'Seat booked for ' . $data['name'] . '. Complete fee setup to finalize admission.');
    }

    public function validateField(\Illuminate\Http\Request $request)
    {
        $data = $request->validate([
            'field' => 'required|in:mobile,email',
            'value' => 'required|string|max:100',
        ]);
        return response()->json(['exists' => User::where($data['field'], trim($data['value']))->exists()]);
    }

    // ── Find Existing Student ────────────────────────────────────────────────

    public function findStudent(Request $request)
    {
        if ($request->isMethod('GET')) {
            return redirect()->route('franchise.enrollment.choose');
        }
        $search = trim($request->input('search', ''));
        $iid    = $this->instituteId();

        $student = User::where('institute_id', $iid)
            ->where('role', 'student')
            ->where(fn ($q) => $q->where('mobile', $search)->orWhere('user_id', $search))
            ->with('profile')
            ->first();

        if (!$student) {
            return redirect()->route('franchise.enrollment.choose')
                ->withInput()->with('error', 'No student found with mobile/ID "' . $search . '".');
        }

        $iid  = $this->instituteId();
        $fid  = $this->franchiseId();
        $courseCatalog = $this->buildCatalog($iid, $fid);
        return view('franchise.enrollment.existing',
            array_merge($this->sharedNewData($iid, $courseCatalog), compact('student', 'courseCatalog')));
    }

    // ── Enroll Existing Student ───────────────────────────────────────────────

    public function storeExisting(Request $request)
    {
        $iid       = $this->instituteId();
        $fid       = $this->franchiseId();
        $sessionId = $this->activeSessionId();

        $data = $request->validate([
            'student_id' => 'required|exists:users,id',
            'course_id'  => ['required', \Illuminate\Validation\Rule::exists('course_details', 'id')
                ->where(fn ($q) => $q->where('institute_id', $iid))],
            'batch_id'   => ['nullable', \Illuminate\Validation\Rule::exists('batch_details', 'id')
                ->where(fn ($q) => $q->where('institute_id', $iid))],
        ]);

        $student = User::where('id', $data['student_id'])->where('institute_id', $iid)
            ->where('role', 'student')->firstOrFail();

        $alreadyEnrolled = CourseBook::where('user_id', $student->id)
            ->where('course_id', $data['course_id'])
            ->whereIn('status', ['OPEN', 'RUN'])->exists();
        if ($alreadyEnrolled) {
            return back()->with('error', 'Student is already enrolled in this course.');
        }

        CourseBook::create([
            'institute_id'        => $iid,
            'franchise_id'        => $fid,
            'session_id'          => $sessionId,
            'user_id'             => $student->id,
            'course_id'           => $data['course_id'],
            'batch_id'            => $data['batch_id'] ?? null,
            'enrollment_no'       => null,
            'fee'                 => 0,
            'final_fee'           => 0,
            'book_date'           => now()->toDateString(),
            'status'              => 'OPEN',
            'booking_mode'        => 'existing',
            'profile_completed_at'=> now(),
            'admission_by'        => Auth::guard('institute')->id(),
        ]);

        return redirect()->route('franchise.enrollment.pending')
            ->with('success', 'New course enrolled for ' . ($student->profile?->name ?? $student->user_id) . '.');
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

        $iid = $this->instituteId();

        $courseBook->loadMissing(['feeSnapshots']);
        $snapshots = $courseBook->feeSnapshots;

        // If no snapshot exists yet (edge case: booked before this fix), build from catalog
        if ($snapshots->isEmpty()) {
            $catalog     = $this->buildCatalog($iid, $this->franchiseId());
            $catalogItem = $catalog->firstWhere('id', $courseBook->course_id);
            $feeStructure = collect($catalogItem ? $catalogItem['fee_items'] : [])
                ->map(fn ($item) => (object) [
                    'fee_type_id'   => $item['fee_type_id'],
                    'fee_type_name' => $item['fee_type_name'],
                    'amount'        => (float) $item['amount'],
                    'is_mandatory'  => (bool) $item['is_mandatory'],
                ])->values();
        } else {
            $feeStructure = $snapshots->map(fn ($s) => (object) [
                'fee_type_id'   => $s->fee_type_id,
                'fee_type_name' => $s->fee_type_name,
                'amount'        => (float) $s->final_amount,
                'is_mandatory'  => $s->fee_type_id === null, // Course Fee row = mandatory
            ])->values();
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

        $fid = $this->franchiseId();
        $iid = $this->instituteId();

        // Check wallet balance before committing status change
        $charge = FranchiseCourseCharge::where('franchise_id', $fid)
            ->where('course_id', $courseBook->course_id)
            ->where('enabled', true)
            ->first();

        $admissionCost = $charge ? (float) $charge->admission_charge : 0;

        if ($admissionCost > 0) {
            $wallet = FranchiseWallet::where('franchise_id', $fid)->first();
            if (!$wallet || (float) $wallet->balance < $admissionCost) {
                $available = $wallet ? number_format($wallet->balance, 2) : '0.00';
                return redirect()->route('franchise.wallet')
                    ->with('error', "Insufficient wallet balance. Required: ₹{$admissionCost}, Available: ₹{$available}. Please recharge your wallet and try again.");
            }
        }

        DB::transaction(function () use ($courseBook, $fid, $iid, $admissionCost, $charge) {
            $courseBook->update(['profile_completed_at' => $courseBook->profile_completed_at ?? now()]);

            if ($courseBook->status !== 'RUN') {
                if (! $courseBook->enrollment_no) {
                    $counter = InstituteEnrollmentCounter::where('institute_id', $courseBook->institute_id)->lockForUpdate()->first();
                    if (! $counter) {
                        InstituteEnrollmentCounter::create(['institute_id' => $courseBook->institute_id, 'last_enrollment_no' => 0]);
                        $counter = InstituteEnrollmentCounter::where('institute_id', $courseBook->institute_id)->lockForUpdate()->firstOrFail();
                    }
                    $counter->last_enrollment_no++;
                    $counter->save();
                    $code         = Auth::guard('institute')->user()->institute?->unique_id ?? 'INST';
                    $enrollmentNo = $code . '/ENR/' . str_pad((string) $counter->last_enrollment_no, 4, '0', STR_PAD_LEFT);
                    $courseBook->update(['enrollment_no' => $enrollmentNo]);
                }
                $courseBook->update(['status' => 'RUN', 'start_date' => $courseBook->start_date ?? now()->toDateString()]);
            }

            // Deduct admission charge from franchise wallet
            if ($admissionCost > 0) {
                $wallet = FranchiseWallet::where('franchise_id', $fid)->lockForUpdate()->first();
                $opBal  = (float) $wallet->balance;
                $clBal  = $opBal - $admissionCost;
                $wallet->decrement('balance', $admissionCost);

                $fresh       = $courseBook->fresh(['student.profile', 'course']);
                $studentName = $fresh->student?->profile?->name ?? 'Student';
                $courseName  = $fresh->course?->name ?? 'Course';

                FranchiseTransaction::create([
                    'franchise_id' => $fid,
                    'institute_id' => $iid,
                    'txn_no'       => $this->invoiceService->generateFranchiseTxnNo($iid, $fid),
                    'description'  => "Admission: {$studentName} | Course: {$courseName} | Enroll: " . $fresh->enrollment_no,
                    'type'         => 4, // 4 = admission deduction
                    'credit'       => 0,
                    'debit'        => $admissionCost,
                    'op_bal'       => $opBal,
                    'cl_bal'       => $clBal,
                    'date'         => now()->toDateString(),
                    'c_date'       => now(),
                    'by_userid'    => Auth::guard('institute')->id(),
                ]);
            }
        });

        return redirect()->route('franchise.enrollment.pending')
            ->with('success', 'Admission confirmed! Enrollment No: ' . $courseBook->fresh()->enrollment_no
                . ($admissionCost > 0 ? " | ₹{$admissionCost} deducted from wallet." : ''));
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

        // Wallet info for confirm admission panel
        $fid             = $this->franchiseId();
        $walletBalance   = (float) (FranchiseWallet::where('franchise_id', $fid)->value('balance') ?? 0);
        $admissionCharge = (float) (FranchiseCourseCharge::where('franchise_id', $fid)
            ->where('course_id', $courseBook->course_id)->where('enabled', true)->value('admission_charge') ?? 0);

        return view('franchise.enrollment.payment-complete', compact(
            'courseBook', 'fees', 'paidTotal', 'transactions', 'due', 'lateFee', 'modalDefaultAmount',
            'walletBalance', 'admissionCharge'
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
