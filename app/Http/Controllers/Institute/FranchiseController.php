<?php

namespace App\Http\Controllers\Institute;

use App\Http\Controllers\Controller;
use App\Models\Franchise;
use App\Models\FranchiseLevel;
use App\Models\FranchiseTransaction;
use App\Models\FranchiseWallet;
use App\Services\FranchiseOnboardingService;
use App\Services\InvoiceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

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

    public function index()
    {
        $franchises = Franchise::with(['wallet', 'head', 'level'])
            ->where('institute_id', $this->instituteId())
            ->latest()
            ->get();

        return view('institute.franchises.index', compact('franchises'));
    }

    public function create()
    {
        $levels = FranchiseLevel::where('institute_id', $this->instituteId())
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('institute.franchises.create', compact('levels'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'short_name' => 'nullable|string|max:50',
            'email' => 'required|email|unique:franchises,email|unique:users,email',
            'mobile' => 'required|string|max:15',
            'owner_name' => 'required|string|max:100',
            'owner_mobile' => 'required|string|max:15',
            'franchise_level_id' => 'required|exists:franchise_levels,id',
            'commission_percent' => 'required|numeric|min:0|max:100',
            'wallet_enabled' => 'required|boolean',
            'low_wallet_alert' => 'nullable|numeric|min:0',
            'has_sub_franchise' => 'required|boolean',
            'address' => 'nullable|string',
            'state' => 'nullable|string|max:60',
            'pin_code' => 'nullable|string|max:10',
            'website' => 'nullable|url|max:150',
            'opening_balance' => 'nullable|numeric|min:0',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
        ]);

        abort_unless(
            FranchiseLevel::where('id', $data['franchise_level_id'])->where('institute_id', $this->instituteId())->exists(),
            422
        );

        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('logos', 'public');
            $data['logo'] = 'storage/' . $logoPath;
        }

        try {
            $franchise = $this->onboarding->create(
                $data,
                $this->instituteId(),
                Auth::guard('institute')->id()
            );

            return redirect()
                ->route('institute.franchises.show', $franchise)
                ->with('success', "Franchise '{$franchise->name}' created and credentials sent to {$franchise->email}.");
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function show(Franchise $franchise)
    {
        $this->authorizeFranchise($franchise);
        $franchise->load(['wallet', 'head.profile', 'transactions', 'level']);

        return view('institute.franchises.show', compact('franchise'));
    }

    public function transactions(Franchise $franchise)
    {
        $this->authorizeFranchise($franchise);
        $transactions = $franchise->transactions()->paginate(20);

        return view('institute.franchises.transactions', compact('franchise', 'transactions'));
    }

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
            'name' => 'required|string|max:150',
            'short_name' => 'nullable|string|max:50',
            'email' => 'required|email|unique:franchises,email,' . $franchise->id . '|unique:users,email,' . ($head?->id ?? 'NULL'),
            'mobile' => 'required|string|max:15',
            'owner_name' => 'required|string|max:100',
            'owner_mobile' => 'required|string|max:15',
            'franchise_level_id' => 'required|exists:franchise_levels,id',
            'commission_percent' => 'required|numeric|min:0|max:100',
            'wallet_enabled' => 'required|boolean',
            'low_wallet_alert' => 'nullable|numeric|min:0',
            'has_sub_franchise' => 'required|boolean',
            'address' => 'nullable|string',
            'state' => 'nullable|string|max:60',
            'pin_code' => 'nullable|string|max:10',
            'website' => 'nullable|url|max:150',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
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
                    'email' => $data['email'],
                    'mobile' => $data['owner_mobile'],
                ]);

                if ($head->profile) {
                    $head->profile->update([
                        'name' => $data['owner_name'],
                        'address' => $data['address'] ?? null,
                        'state' => $data['state'] ?? null,
                        'pin_code' => $data['pin_code'] ?? null,
                    ]);
                }
            }
        });

        return redirect()->route('institute.franchises.show', $franchise)->with('success', 'Franchise updated successfully.');
    }

    public function walletIndex()
    {
        $franchises = Franchise::with(['wallet', 'level'])
            ->where('institute_id', $this->instituteId())
            ->latest()
            ->get();

        $lowWalletFranchises = $franchises->filter(function (Franchise $franchise) {
            if (!$franchise->wallet_enabled) {
                return false;
            }

            return ($franchise->wallet?->balance ?? 0) <= $franchise->low_wallet_alert;
        });

        return view('institute.franchises.wallets', compact('franchises', 'lowWalletFranchises'));
    }

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

    public function recharge(Request $request, Franchise $franchise)
    {
        $this->authorizeFranchise($franchise);

        $data = $request->validate([
            'payment_mode' => 'required|in:CASH,UPI,NEFT,IMPS,CHEQUE',
            'utr' => 'nullable|string|max:80',
            'amount' => 'required|numeric|min:1',
            'date' => 'required|date',
            'note' => 'nullable|string|max:255',
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
                'txn_no' => $txnNo,
                'description' => "{$data['payment_mode']} wallet recharge" . (!empty($data['note']) ? " | {$data['note']}" : ''),
                'credit' => $data['amount'],
                'debit' => 0,
                'type' => 2,
                'payment_mode' => $data['payment_mode'],
                'utr' => $data['utr'] ?? null,
                'op_bal' => $opBal,
                'cl_bal' => $clBal,
                'date' => $data['date'],
                'c_date' => now(),
                'by_userid' => Auth::guard('institute')->id(),
            ]);

            $wallet->update(['balance' => $clBal]);
        });

        return back()->with('success', "Wallet recharged by ₹{$data['amount']}.");
    }

    private function authorizeFranchise(Franchise $franchise): void
    {
        abort_if($franchise->institute_id !== $this->instituteId(), 403);
    }
}
