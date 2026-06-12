<?php

namespace App\Http\Controllers\Institute;

use App\Http\Controllers\Controller;
use App\Models\Franchise;
use App\Models\FranchiseFeeCollection;
use App\Models\FranchiseLevel;
use App\Models\FranchiseTransaction;
use App\Models\FranchiseWallet;
use App\Services\FranchiseOnboardingService;
use App\Services\InvoiceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
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

        // Restore session data into old() so all form fields repopulate when coming back from preview
        $prefill = session('franchise_create_data');
        if ($prefill) {
            session()->flashInput($prefill);
        }

        return view('institute.franchises.create', compact('levels', 'prefill'));
    }

    // ─── Create: Step 1 POST — Validate & Branch ─────────────────────────────

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'               => 'required|string|max:150',
            'short_name'         => 'nullable|string|max:50',
            'email'              => 'required|email|unique:franchises,email|unique:users,email',
            'mobile'             => 'required|string|max:15',
            'owner_name'         => 'required|string|max:100',
            'owner_mobile'       => 'required|string|max:15',
            'franchise_level_id' => 'required|exists:franchise_levels,id',
            'commission_percent' => 'required|numeric|min:0|max:100',
            'management_type'    => 'required|in:independent,wallet',
            'wallet_enabled'     => 'required|boolean',
            'low_wallet_alert'   => 'nullable|numeric|min:0',
            'admission_charge'   => 'nullable|numeric|min:0',
            'certificate_charge' => 'nullable|numeric|min:0',
            'onboarding_fee'     => 'nullable|numeric|min:0',
            'has_sub_franchise'  => 'required|boolean',
            'address'            => 'nullable|string',
            'state'              => 'nullable|string|max:60',
            'pin_code'           => 'nullable|string|max:10',
            'website'            => 'nullable|url|max:150',
            'opening_balance'    => 'nullable|numeric|min:0',
            'logo'               => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
        ]);

        $level = FranchiseLevel::where('id', $data['franchise_level_id'])
            ->where('institute_id', $this->instituteId())
            ->firstOrFail();

        // Store logo to a temp path so it survives the session redirect
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('logos/temp', 'public');
            $data['logo_temp'] = $logoPath;
            $data['logo'] = 'storage/' . $logoPath;
        }

        // Both modes go through preview (step 2)
        $data['_level'] = [
            'name'       => $level->name,
            'level_fee'  => (float) ($level->level_fee ?? 0),
            'commission' => (float) $level->commission_percent,
        ];

        session(['franchise_create_data' => $data]);

        return redirect()->route('institute.franchises.preview');
    }

    // ─── Create: Step 2 — Preview ────────────────────────────────────────────

    public function preview()
    {
        $data = session('franchise_create_data');

        if (! $data) {
            return redirect()->route('institute.franchises.create')
                ->with('error', 'Session expired. Please fill in the details again.');
        }

        $levels = FranchiseLevel::where('institute_id', $this->instituteId())
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('institute.franchises.create-preview', compact('data', 'levels'));
    }

    // ─── Create: Step 2 POST — Confirm & Create ──────────────────────────────

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

            if (($franchise->management_type ?? 'wallet') === 'independent') {
                return redirect()
                    ->route('institute.franchises.fee.index', $franchise)
                    ->with('success', "Franchise '{$franchise->name}' created successfully. Please collect the onboarding fee.");
            }

            // Wallet mode: go to recharge/ledger page to record opening balance
            return redirect()
                ->route('institute.franchises.transactions', $franchise)
                ->with('success', "Franchise '{$franchise->name}' created. Login credentials sent to {$franchise->email}." . (($franchise->wallet?->balance ?? 0) > 0 ? " Opening balance of ₹" . number_format($franchise->wallet->balance, 2) . " has been credited." : " Please recharge the wallet to get started."));
        } catch (\Throwable $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    // ─── Show ─────────────────────────────────────────────────────────────────

    public function show(Franchise $franchise)
    {
        $this->authorizeFranchise($franchise);
        $franchise->load(['wallet', 'head.profile', 'transactions', 'level']);

        return view('institute.franchises.show', compact('franchise'));
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

        return view('institute.franchises.edit', compact('franchise', 'levels'));
    }

    public function update(Request $request, Franchise $franchise)
    {
        $this->authorizeFranchise($franchise);

        $head = $franchise->head;

        $data = $request->validate([
            'name'               => 'required|string|max:150',
            'short_name'         => 'nullable|string|max:50',
            'email'              => 'required|email|unique:franchises,email,' . $franchise->id . '|unique:users,email,' . ($head?->id ?? 'NULL'),
            'mobile'             => 'required|string|max:15',
            'owner_name'         => 'required|string|max:100',
            'owner_mobile'       => 'required|string|max:15',
            'franchise_level_id' => 'required|exists:franchise_levels,id',
            'commission_percent' => 'required|numeric|min:0|max:100',
            'management_type'    => 'required|in:independent,wallet',
            'wallet_enabled'     => 'required|boolean',
            'low_wallet_alert'   => 'nullable|numeric|min:0',
            'admission_charge'   => 'nullable|numeric|min:0',
            'certificate_charge' => 'nullable|numeric|min:0',
            'onboarding_fee'     => 'nullable|numeric|min:0',
            'has_sub_franchise'  => 'required|boolean',
            'address'            => 'nullable|string',
            'state'              => 'nullable|string|max:60',
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

        DB::transaction(function () use ($franchise, $data, $total) {
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

    // ─── Auth Helper ──────────────────────────────────────────────────────────

    private function authorizeFranchise(Franchise $franchise): void
    {
        abort_if($franchise->institute_id !== $this->instituteId(), 403);
    }
}
