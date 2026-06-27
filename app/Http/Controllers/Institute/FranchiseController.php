<?php

namespace App\Http\Controllers\Institute;

use App\Http\Controllers\Controller;
use App\Mail\FranchiseWelcomeMail;
use App\Models\CourseDetail;
use App\Models\CourseType;
use App\Models\District;
use App\Models\Franchise;
use App\Models\State;
use App\Models\FranchiseCourseCharge;
use App\Models\FranchiseLevel;
use App\Models\FranchiseTransaction;
use App\Models\FranchiseWallet;
use App\Models\LevelCourseCharge;
use App\Services\FranchiseOnboardingService;
use App\Services\InvoiceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class FranchiseController extends Controller
{
    public function __construct(
        protected FranchiseOnboardingService $onboarding,
        protected InvoiceService $invoiceService,
    ) {}

    private function instituteId(): int
    {
        return Auth::guard('institute')->user()->institute_id;
    }

    // ─── Index ────────────────────────────────────────────────────────────────

    public function index()
    {
        $franchises = Franchise::with(['wallet', 'head', 'level'])
            ->where('institute_id', $this->instituteId())
            ->latest()
            ->get();

        return view('institute.franchises.index', compact('franchises'));
    }

    // ─── Create: Step 1 — Details Form ───────────────────────────────────────

    public function create()
    {
        $levels = FranchiseLevel::where('institute_id', $this->instituteId())
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        $courses = CourseDetail::where('institute_id', $this->instituteId())
            ->where('status', 'active')
            ->orderBy('name')
            ->get(['id', 'name', 'course_short_name', 'duration']);

        $states       = State::orderBy('name')->get(['id', 'name']);
        $districtsMap = District::all(['state_id', 'name'])
            ->groupBy('state_id')
            ->map(fn ($d) => $d->pluck('name'));

        // Restore session data into old() so all form fields repopulate when coming back from preview
        $prefill = session('franchise_create_data');
        if ($prefill) {
            session()->flashInput($prefill);
        }

        return view('institute.franchises.create', compact('levels', 'courses', 'prefill', 'states', 'districtsMap'));
    }

    // ─── Create: Step 1 POST — Validate & Branch ─────────────────────────────

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'               => 'required|string|max:150',
            'short_name'         => 'nullable|string|max:50',
            'email'              => 'required|email|unique:franchises,email|unique:users,email',
            'mobile'             => 'required|digits:10',
            'owner_name'         => 'required|string|max:100',
            'owner_mobile'       => 'required|digits:10',
            'franchise_level_id' => 'required|exists:franchise_levels,id',
            'commission_percent' => 'required|numeric|min:0|max:100',
            'has_sub_franchise'  => 'nullable|boolean',
            'low_wallet_alert'   => 'nullable|numeric|min:0',
            'address'            => 'nullable|string',
            'state'              => 'nullable|string|max:100',
            'district'           => 'nullable|string|max:100',
            'pin_code'           => 'nullable|string|max:10',
            'website'            => 'nullable|url|max:150',
            'opening_balance'    => 'nullable|numeric|min:0',
            'logo'               => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
        ]);

        // All franchises are wallet-based
        $data['management_type'] = 'wallet';
        $data['wallet_enabled']  = true;
        $data['has_sub_franchise'] = (bool) ($data['has_sub_franchise'] ?? false);

        $level = FranchiseLevel::where('id', $data['franchise_level_id'])
            ->where('institute_id', $this->instituteId())
            ->firstOrFail();

        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('logos/temp', 'public');
            $data['logo_temp'] = $logoPath;
            $data['logo'] = 'storage/' . $logoPath;
        }

        $data['_level'] = [
            'name'       => $level->name,
            'level_fee'  => (float) ($level->level_fee ?? 0),
            'commission' => (float) $level->commission_percent,
        ];

        session(['franchise_create_data' => $data]);

        return redirect()->route('institute.franchises.charges');
    }

    // ─── Create: Step 2 (wallet only) — Course Type Access ─────────────────

    public function chargesStep()
    {
        $data = session('franchise_create_data');

        if (! $data) {
            return redirect()->route('institute.franchises.create');
        }

        $iid     = $this->instituteId();
        $levelId = (int) ($data['franchise_level_id'] ?? 0);

        // All active course types with their active course count
        $courseTypes = CourseType::where('institute_id', $iid)
            ->where('status', 'active')
            ->withCount(['courses as active_courses' => fn ($q) => $q->where('status', 'active')->where('institute_id', $iid)])
            ->orderBy('name')
            ->get();

        // Level charge summary per course_type_id (for card pills)
        $levelChargesByType = [];
        // Per-course charges with level defaults (for the editable charge table)
        $coursesByType = [];

        if ($levelId) {
            $rows = LevelCourseCharge::where('franchise_level_id', $levelId)
                ->where('level_course_charges.status', 'active')
                ->join('course_details', 'course_details.id', '=', 'level_course_charges.course_id')
                ->selectRaw('course_details.course_type_id,
                             COUNT(*) as configured_count,
                             MIN(student_admission_charge) as min_adm,
                             MAX(student_admission_charge) as max_adm,
                             MIN(student_certificate_charge) as min_cert,
                             MAX(student_certificate_charge) as max_cert')
                ->groupBy('course_details.course_type_id')
                ->get();

            foreach ($rows as $row) {
                $levelChargesByType[$row->course_type_id] = $row;
            }

            // Per-course detail for charge editor
            $courseRows = LevelCourseCharge::where('franchise_level_id', $levelId)
                ->where('level_course_charges.status', 'active')
                ->join('course_details', 'course_details.id', '=', 'level_course_charges.course_id')
                ->select(
                    'course_details.id as course_id',
                    'course_details.name as course_name',
                    'course_details.duration',
                    'course_details.course_type_id',
                    'level_course_charges.student_admission_charge as default_adm',
                    'level_course_charges.student_certificate_charge as default_cert'
                )
                ->orderBy('course_details.name')
                ->get();

            foreach ($courseRows as $cr) {
                $coursesByType[$cr->course_type_id][] = $cr;
            }
        }

        $selected       = array_map('intval', $data['_course_type_access'] ?? []);
        $savedCharges   = $data['_course_charges'] ?? [];

        return view('institute.franchises.create-charges', compact(
            'data', 'courseTypes', 'levelChargesByType', 'coursesByType', 'selected', 'savedCharges'
        ));
    }

    public function storeCharges(Request $request)
    {
        $data = session('franchise_create_data');

        if (! $data) {
            return redirect()->route('institute.franchises.create');
        }

        $data['_course_type_access'] = array_map('intval', $request->input('course_type_ids', []));

        // Per-course charge overrides from the charge editor
        $rawCharges = $request->input('course_charges', []);
        $parsed = [];
        foreach ($rawCharges as $courseId => $charges) {
            $parsed[(int) $courseId] = [
                'admission'   => max(0, (float) ($charges['admission'] ?? 0)),
                'certificate' => max(0, (float) ($charges['certificate'] ?? 0)),
            ];
        }
        $data['_course_charges'] = $parsed;

        session(['franchise_create_data' => $data]);

        return redirect()->route('institute.franchises.preview');
    }

    // ─── Create: Step 3 — Preview ────────────────────────────────────────────

    public function preview()
    {
        $data = session('franchise_create_data');

        if (! $data) {
            return redirect()->route('institute.franchises.create')
                ->with('error', 'Session expired. Please fill in the details again.');
        }

        $levelId         = (int) ($data['franchise_level_id'] ?? 0);
        $selectedTypeIds = $data['_course_type_access'] ?? [];

        $courseTypes = CourseType::whereIn('id', $selectedTypeIds)
            ->orderBy('name')
            ->get(['id', 'name']);

        $customCharges      = $data['_course_charges'] ?? [];
        $levelChargesByType = [];

        if ($levelId && ! empty($selectedTypeIds)) {
            // Get per-course rows so we can apply custom charge overrides
            $courseRows = LevelCourseCharge::where('franchise_level_id', $levelId)
                ->where('level_course_charges.status', 'active')
                ->join('course_details', 'course_details.id', '=', 'level_course_charges.course_id')
                ->whereIn('course_details.course_type_id', $selectedTypeIds)
                ->select(
                    'course_details.course_type_id',
                    'course_details.id as course_id',
                    'level_course_charges.student_admission_charge as def_adm',
                    'level_course_charges.student_certificate_charge as def_cert'
                )
                ->get();

            foreach ($courseRows->groupBy('course_type_id') as $typeId => $courses) {
                $adms  = $courses->map(fn ($c) => (float) ($customCharges[$c->course_id]['admission']   ?? $c->def_adm));
                $certs = $courses->map(fn ($c) => (float) ($customCharges[$c->course_id]['certificate'] ?? $c->def_cert));
                $levelChargesByType[$typeId] = (object) [
                    'course_count' => $courses->count(),
                    'min_adm'      => $adms->min(),
                    'max_adm'      => $adms->max(),
                    'min_cert'     => $certs->min(),
                    'max_cert'     => $certs->max(),
                ];
            }
        }

        return view('institute.franchises.create-preview', compact('data', 'courseTypes', 'levelChargesByType'));
    }

    // ─── Create: Step 3 POST — Confirm & Create ──────────────────────────────

    public function confirmCreate()
    {
        $data = session('franchise_create_data');

        if (! $data) {
            return redirect()->route('institute.franchises.create')
                ->with('error', 'Session expired. Please fill in the details again.');
        }

        // Move temp logo to permanent path
        if (! empty($data['logo_temp']) && Storage::disk('public')->exists($data['logo_temp'])) {
            $permanent = str_replace('logos/temp/', 'logos/', $data['logo_temp']);
            Storage::disk('public')->move($data['logo_temp'], $permanent);
            $data['logo'] = 'storage/' . $permanent;
        }

        $levelFee = (float) ($data['_level']['level_fee'] ?? 0);

        try {
            $franchise = $this->onboarding->create(
                $data,
                $this->instituteId(),
                Auth::guard('institute')->id(),
                $levelFee
            );

            session()->forget('franchise_create_data');

            $levelFeeMsg = $levelFee > 0
                ? " Joining fee of ₹" . number_format($levelFee, 2) . " is pending collection."
                : '';

            $walletMsg = ($franchise->management_type === 'wallet' && ($franchise->wallet?->balance ?? 0) > 0)
                ? " Opening balance of ₹" . number_format($franchise->wallet->balance, 2) . " credited to operational wallet."
                : '';

            // Both modes go to joining fee collection page
            return redirect()
                ->route('institute.franchises.fee.index', $franchise)
                ->with('success', "Franchise '{$franchise->name}' created. Login credentials sent to {$franchise->email}.{$walletMsg}{$levelFeeMsg}");
        } catch (\Throwable $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    // ─── Show ─────────────────────────────────────────────────────────────────

    public function show(Franchise $franchise)
    {
        $this->authorizeFranchise($franchise);
        $franchise->load(['wallet', 'head.profile', 'transactions', 'level']);

        $iid = $franchise->institute_id;

        // All course types for course-access tab
        $allCourseTypes = CourseType::where('institute_id', $iid)
            ->where('status', 'active')
            ->withCount(['courses as active_courses' => fn ($q) => $q->where('status', 'active')->where('institute_id', $iid)])
            ->orderBy('name')
            ->get();

        // Which type IDs have at least one FranchiseCourseCharge record
        $grantedTypeIds = FranchiseCourseCharge::where('franchise_id', $franchise->id)
            ->whereNotNull('course_type_id')
            ->pluck('course_type_id')
            ->unique()
            ->values();

        // Charges grouped by course_type_id for the table
        $courseChargesByType = FranchiseCourseCharge::where('franchise_id', $franchise->id)
            ->orderBy('course_type_id')
            ->orderBy('course_name')
            ->get()
            ->groupBy('course_type_id');

        return view('institute.franchises.show', compact(
            'franchise', 'allCourseTypes', 'grantedTypeIds', 'courseChargesByType'
        ));
    }

    // ─── Transactions / Ledger ────────────────────────────────────────────────

    public function transactions(Franchise $franchise)
    {
        $this->authorizeFranchise($franchise);
        $transactions = $franchise->transactions()->paginate(20);

        return view('institute.franchises.transactions', compact('franchise', 'transactions'));
    }

    // ─── Edit ─────────────────────────────────────────────────────────────────

    public function edit(Franchise $franchise)
    {
        $this->authorizeFranchise($franchise);
        $levels = FranchiseLevel::where('institute_id', $this->instituteId())
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        $courses = CourseDetail::where('institute_id', $this->instituteId())
            ->where('status', 'active')
            ->orderBy('name')
            ->get(['id', 'name', 'course_short_name', 'duration']);

        $states       = State::orderBy('name')->get(['id', 'name']);
        $districtsMap = District::all(['state_id', 'name'])
            ->groupBy('state_id')
            ->map(fn ($d) => $d->pluck('name'));

        return view('institute.franchises.edit', compact('franchise', 'levels', 'courses', 'states', 'districtsMap'));
    }

    public function update(Request $request, Franchise $franchise)
    {
        $this->authorizeFranchise($franchise);

        $head = $franchise->head;

        $data = $request->validate([
            'name'               => 'required|string|max:150',
            'short_name'         => 'nullable|string|max:50',
            'email'              => 'required|email|unique:franchises,email,' . $franchise->id . '|unique:users,email,' . ($head?->id ?? 'NULL'),
            'mobile'             => 'required|digits:10',
            'owner_name'         => 'required|string|max:100',
            'owner_mobile'       => 'required|digits:10',
            'franchise_level_id' => 'required|exists:franchise_levels,id',
            'commission_percent' => 'required|numeric|min:0|max:100',
            'has_sub_franchise'  => 'nullable|boolean',
            'low_wallet_alert'   => 'nullable|numeric|min:0',
            'address'            => 'nullable|string',
            'state'              => 'nullable|string|max:100',
            'district'           => 'nullable|string|max:100',
            'pin_code'           => 'nullable|string|max:10',
            'website'            => 'nullable|url|max:150',
            'logo'               => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
        ]);

        abort_unless(
            FranchiseLevel::where('id', $data['franchise_level_id'])->where('institute_id', $this->instituteId())->exists(),
            422
        );

        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('logos', 'public');
            $data['logo'] = 'storage/' . $logoPath;
        }

        DB::transaction(function () use ($data, $franchise, $head) {
            $franchise->update($data);

            if ($head) {
                $head->update([
                    'email'  => $data['email'],
                    'mobile' => $data['owner_mobile'],
                ]);

                if ($head->profile) {
                    $head->profile->update([
                        'name'     => $data['owner_name'],
                        'address'  => $data['address'] ?? null,
                        'state'    => $data['state'] ?? null,
                        'pin_code' => $data['pin_code'] ?? null,
                    ]);
                }
            }
        });

        return redirect()->route('institute.franchises.show', $franchise)->with('success', 'Franchise updated successfully.');
    }

    // ─── Wallet Overview ──────────────────────────────────────────────────────

    public function walletIndex()
    {
        $franchises = Franchise::with(['wallet', 'level'])
            ->where('institute_id', $this->instituteId())
            ->latest()
            ->get();

        $lowWalletFranchises = $franchises->filter(function (Franchise $franchise) {
            if (! $franchise->wallet_enabled) {
                return false;
            }

            return ($franchise->wallet?->balance ?? 0) <= $franchise->low_wallet_alert;
        });

        return view('institute.franchises.wallets', compact('franchises', 'lowWalletFranchises'));
    }

    // ─── Toggle Status ────────────────────────────────────────────────────────

    public function toggle(Franchise $franchise)
    {
        $this->authorizeFranchise($franchise);

        $newStatus = $franchise->status === 'active' ? 'inactive' : 'active';

        DB::transaction(function () use ($franchise, $newStatus) {
            $franchise->update(['status' => $newStatus]);
            $franchise->head?->update(['status' => $newStatus]);
        });

        return back()->with('success', "Franchise status changed to {$newStatus}.");
    }

    // ─── Wallet Recharge ──────────────────────────────────────────────────────

    public function recharge(Request $request, Franchise $franchise)
    {
        $this->authorizeFranchise($franchise);

        $data = $request->validate([
            'payment_mode' => 'required|in:CASH,UPI,NEFT,IMPS,CHEQUE',
            'utr'          => 'nullable|string|max:80',
            'amount'       => 'required|numeric|min:1',
            'date'         => 'required|date',
            'note'         => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($franchise, $data) {
            /** @var FranchiseWallet $wallet */
            $wallet = FranchiseWallet::where('franchise_id', $franchise->id)
                ->lockForUpdate()
                ->firstOrFail();

            $opBal = (float) $wallet->balance;
            $clBal = $opBal + (float) $data['amount'];
            $txnNo = $this->invoiceService->generateFranchiseTxnNo($franchise->institute_id, $franchise->id);

            FranchiseTransaction::create([
                'franchise_id' => $franchise->id,
                'institute_id' => $franchise->institute_id,
                'txn_no'       => $txnNo,
                'description'  => "{$data['payment_mode']} wallet recharge" . (! empty($data['note']) ? " | {$data['note']}" : ''),
                'credit'       => $data['amount'],
                'debit'        => 0,
                'type'         => 2,
                'payment_mode' => $data['payment_mode'],
                'utr'          => $data['utr'] ?? null,
                'op_bal'       => $opBal,
                'cl_bal'       => $clBal,
                'date'         => $data['date'],
                'c_date'       => now(),
                'by_userid'    => Auth::guard('institute')->id(),
            ]);

            $wallet->update(['balance' => $clBal]);
        });

        return back()->with('success', "Wallet recharged by ₹{$data['amount']}.");
    }

    // ─── Wallet Recharge with Bonus (Festival Offer) ──────────────────────────

    public function rechargeBonus(Request $request, Franchise $franchise)
    {
        $this->authorizeFranchise($franchise);

        $data = $request->validate([
            'payment_mode'    => 'required|in:CASH,UPI,NEFT,IMPS,CHEQUE',
            'utr'             => 'nullable|string|max:80',
            'paid_amount'     => 'required|numeric|min:1',
            'recharge_amount' => 'required|numeric|min:1',
            'date'            => 'required|date',
            'note'            => 'nullable|string|max:255',
        ]);

        // recharge_amount is the total to credit (may include bonus over paid_amount)
        $total     = (float) $data['recharge_amount'];
        $bonusDiff = max(0, $total - (float) $data['paid_amount']);

        DB::transaction(function () use ($franchise, $data, $total, $bonusDiff) {
            /** @var FranchiseWallet $wallet */
            $wallet = FranchiseWallet::where('franchise_id', $franchise->id)
                ->lockForUpdate()
                ->firstOrFail();

            $opBal = (float) $wallet->balance;
            $clBal = $opBal + $total;
            $desc  = "{$data['payment_mode']} recharge ₹{$data['paid_amount']}" .
                ($bonusDiff > 0 ? " + ₹{$bonusDiff} bonus" : '') .
                (! empty($data['note']) ? " | {$data['note']}" : '');

            FranchiseTransaction::create([
                'franchise_id' => $franchise->id,
                'institute_id' => $franchise->institute_id,
                'txn_no'       => $this->invoiceService->generateFranchiseTxnNo($franchise->institute_id, $franchise->id),
                'description'  => $desc,
                'credit'       => $total,
                'debit'        => 0,
                'type'         => $bonusDiff > 0 ? 3 : 2,
                'payment_mode' => $data['payment_mode'],
                'utr'          => $data['utr'] ?? null,
                'op_bal'       => $opBal,
                'cl_bal'       => $clBal,
                'date'         => $data['date'],
                'c_date'       => now(),
                'by_userid'    => Auth::guard('institute')->id(),
            ]);

            $wallet->update(['balance' => $clBal]);
        });

        $msg = $bonusDiff > 0
            ? "Wallet recharged: ₹{$data['paid_amount']} received + ₹{$bonusDiff} bonus = ₹{$total} credited."
            : "Wallet recharged: ₹{$total} credited.";

        return back()->with('success', $msg);
    }

    // ─── Certificate ──────────────────────────────────────────────────────────

    public function certificate(Franchise $franchise)
    {
        $this->authorizeFranchise($franchise);
        $franchise->load(['institute', 'level']);

        return view('institute.franchises.certificate', compact('franchise'));
    }

    // ─── Course Type Access Management ───────────────────────────────────────

    public function grantCourseType(Request $request, Franchise $franchise, CourseType $courseType)
    {
        $this->authorizeFranchise($franchise);
        abort_if($courseType->institute_id !== $franchise->institute_id, 403);

        $iid     = $franchise->institute_id;
        $levelId = $franchise->franchise_level_id;

        $courses = CourseDetail::where('institute_id', $iid)
            ->where('course_type_id', $courseType->id)
            ->where('status', 'active')
            ->get(['id', 'name', 'duration']);

        DB::transaction(function () use ($franchise, $courseType, $courses, $levelId, $iid) {
            foreach ($courses as $course) {
                $levelCharge = $levelId
                    ? LevelCourseCharge::where('franchise_level_id', $levelId)
                        ->where('course_id', $course->id)
                        ->where('status', 'active')
                        ->first()
                    : null;

                FranchiseCourseCharge::updateOrCreate(
                    ['franchise_id' => $franchise->id, 'course_id' => $course->id],
                    [
                        'institute_id'       => $iid,
                        'course_type_id'     => $courseType->id,
                        'course_name'        => $course->name,
                        'duration'           => $course->duration ?? 0,
                        'admission_charge'   => $levelCharge?->student_admission_charge ?? 0,
                        'certificate_charge' => $levelCharge?->student_certificate_charge ?? 0,
                        'student_fee'        => null,
                        'enabled'            => true,
                    ]
                );
            }
        });

        return response()->json([
            'success' => true,
            'count'   => $courses->count(),
            'message' => $courseType->name . ' access granted (' . $courses->count() . ' courses).',
        ]);
    }

    public function revokeCourseType(Franchise $franchise, CourseType $courseType)
    {
        $this->authorizeFranchise($franchise);

        $deleted = FranchiseCourseCharge::where('franchise_id', $franchise->id)
            ->where('course_type_id', $courseType->id)
            ->delete();

        return response()->json([
            'success' => true,
            'message' => $courseType->name . ' access revoked (' . $deleted . ' courses removed).',
        ]);
    }

    public function updateCourseCharge(Request $request, Franchise $franchise, FranchiseCourseCharge $charge)
    {
        $this->authorizeFranchise($franchise);
        abort_if($charge->franchise_id !== $franchise->id, 403);

        $data = $request->validate([
            'admission_charge'   => 'required|numeric|min:0',
            'certificate_charge' => 'required|numeric|min:0',
        ]);

        $charge->update($data);

        return response()->json(['success' => true]);
    }

    // ─── Course Charges JSON (for franchise list modal) ───────────────────────

    public function courseCharges(Franchise $franchise)
    {
        $this->authorizeFranchise($franchise);

        $charges = FranchiseCourseCharge::where('franchise_id', $franchise->id)
            ->orderBy('duration')
            ->orderBy('course_name')
            ->get(['course_name', 'duration', 'admission_charge', 'certificate_charge']);

        return response()->json($charges);
    }

    // ─── Resend Credentials ───────────────────────────────────────────────────

    public function resendCredentials(Franchise $franchise)
    {
        $this->authorizeFranchise($franchise);

        $head = $franchise->head;
        abort_if(! $head, 404, 'Franchise login user not found.');

        $plainPassword = $this->onboarding->generatePassword();
        $head->update(['password' => $plainPassword]);

        try {
            Mail::to($franchise->email)->send(
                new FranchiseWelcomeMail($franchise, $head, $plainPassword)
            );
        } catch (\Throwable $e) {
            logger()->error("Franchise resend credentials mail failed for franchise {$franchise->id}: " . $e->getMessage());
            return back()->with('error', 'Failed to send credentials email. Please try again.');
        }

        return back()->with('success', "Login credentials resent to {$franchise->email}.");
    }

    // ─── Auth Helper ──────────────────────────────────────────────────────────

    private function authorizeFranchise(Franchise $franchise): void
    {
        abort_if($franchise->institute_id !== $this->instituteId(), 403);
    }
}
