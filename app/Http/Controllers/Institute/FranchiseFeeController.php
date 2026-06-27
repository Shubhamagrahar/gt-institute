<?php

namespace App\Http\Controllers\Institute;

use App\Http\Controllers\Controller;
use App\Models\Franchise;
use App\Models\FranchiseInstituteTransaction;
use App\Models\FranchiseInstituteWallet;
use App\Models\FranchisePayDetail;
use App\Models\InstituteStudentTransaction;
use App\Models\InstituteStudentWallet;
use App\Services\InvoiceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FranchiseFeeController extends Controller
{
    public function __construct(protected InvoiceService $invoiceService) {}

    private function instituteId(): int
    {
        return Auth::guard('institute')->user()->institute_id;
    }

    // ─── Fee Collection Ledger ────────────────────────────────────────────────

    public function index(Franchise $franchise)
    {
        $this->authorizeFranchise($franchise);
        $franchise->load(['level', 'head']);

        $fiWallet     = FranchiseInstituteWallet::where('franchise_id', $franchise->id)->first();
        $balance      = $fiWallet ? (float) $fiWallet->balance : -(float) $franchise->fee_total;
        $onboardingFee = (float) $franchise->fee_total;
        $outstanding  = $balance < 0 ? abs($balance) : 0.0;
        $totalPaid    = max(0.0, $onboardingFee + $balance);

        $transactions = FranchiseInstituteTransaction::where('franchise_id', $franchise->id)
            ->orderBy('id')
            ->get();

        $payments = FranchisePayDetail::where('franchise_id', $franchise->id)
            ->orderByDesc('id')
            ->get();

        $cancelledAmount = $payments->whereNotNull('cancelled_at')->sum('amount');

        return view('institute.franchises.fee-collections', compact(
            'franchise', 'fiWallet', 'balance', 'onboardingFee',
            'outstanding', 'totalPaid', 'cancelledAmount',
            'transactions', 'payments'
        ));
    }

    // ─── Collect Payment ──────────────────────────────────────────────────────

    public function collect(Request $request, Franchise $franchise)
    {
        $this->authorizeFranchise($franchise);

        $fiWallet    = FranchiseInstituteWallet::where('franchise_id', $franchise->id)->first();
        $balance     = $fiWallet ? (float) $fiWallet->balance : -(float) $franchise->fee_total;
        $outstanding = $balance < 0 ? abs($balance) : 0.0;

        if ($outstanding <= 0) {
            return back()->with('error', 'No outstanding balance. Payment not required.');
        }

        $utrRule = in_array($request->payment_mode, ['UPI', 'NEFT', 'IMPS', 'CHEQUE'])
            ? 'required|string|max:80'
            : 'nullable|string|max:80';

        $data = $request->validate([
            'payment_mode' => 'required|in:CASH,UPI,NEFT,IMPS,CHEQUE',
            'utr'          => $utrRule,
            'amount'       => "required|numeric|min:0.01|max:{$outstanding}",
            'date'         => 'required|date',
            'note'         => 'nullable|string|max:255',
        ]);

        $amount = (float) $data['amount'];
        $iid    = $franchise->institute_id;
        $fid    = $franchise->id;
        $by     = Auth::guard('institute')->id();

        DB::transaction(function () use ($franchise, $data, $amount, $iid, $fid, $by, $fiWallet) {
            // 1. Create payment record
            $invoiceNo = $this->invoiceService->generateFranchisePayInvoice($iid);
            FranchisePayDetail::create([
                'franchise_id' => $fid,
                'institute_id' => $iid,
                'invoice_no'   => $invoiceNo,
                'payment_mode' => $data['payment_mode'],
                'utr'          => $data['utr'] ?? null,
                'amount'       => $amount,
                'date'         => $data['date'],
                'note'         => $data['note'] ?? null,
                'collected_by' => $by,
            ]);

            // 2. Franchise institute transaction ledger entry (credit reduces debt)
            $currentBalance = $fiWallet ? (float) $fiWallet->balance : -(float) $franchise->fee_total;
            $newBalance     = $currentBalance + $amount;

            FranchiseInstituteTransaction::create([
                'franchise_id' => $fid,
                'institute_id' => $iid,
                'txn_no'       => $this->invoiceService->generateFranchiseInstTxnNo($iid, $fid),
                'type'         => 2, // payment_received
                'description'  => "Payment received via {$data['payment_mode']}" . ($data['note'] ? " — {$data['note']}" : ''),
                'credit'       => $amount,
                'debit'        => 0,
                'payment_mode' => $data['payment_mode'],
                'utr'          => $data['utr'] ?? null,
                'invoice_no'   => $invoiceNo,
                'op_bal'       => $currentBalance,
                'cl_bal'       => $newBalance,
                'date'         => $data['date'],
                'c_date'       => now(),
                'by_userid'    => $by,
            ]);

            // 3. Update franchise institute wallet balance
            if ($fiWallet) {
                $fiWallet->update(['balance' => $newBalance]);
            } else {
                FranchiseInstituteWallet::create([
                    'franchise_id' => $fid,
                    'institute_id' => $iid,
                    'balance'      => $newBalance,
                ]);
            }

            // 4. Credit institute student wallet (institute's earnings)
            $isWallet = InstituteStudentWallet::where('institute_id', $iid)->lockForUpdate()->first();
            if ($isWallet) {
                $isOpBal = (float) $isWallet->balance;
                $isClBal = $isOpBal + $amount;

                InstituteStudentTransaction::create([
                    'institute_id' => $iid,
                    'franchise_id' => $fid,
                    'description'  => "Franchise payment: {$franchise->name} | {$invoiceNo}",
                    'credit'       => $amount,
                    'debit'        => 0,
                    'type'         => 1, // fee_received
                    'date'         => $data['date'],
                    'c_date'       => now(),
                    'op_bal'       => $isOpBal,
                    'cl_bal'       => $isClBal,
                    'by_user_id'   => $by,
                ]);

                $isWallet->update(['balance' => $isClBal]);
            }
        });

        return back()->with('success', '₹' . number_format($amount, 2) . ' collected successfully.');
    }

    // ─── Receipt ─────────────────────────────────────────────────────────────

    public function receipt(Franchise $franchise, FranchisePayDetail $payment)
    {
        $this->authorizeFranchise($franchise);
        abort_if($payment->franchise_id !== $franchise->id, 403);

        $franchise->load(['level', 'institute']);

        $fiWallet    = FranchiseInstituteWallet::where('franchise_id', $franchise->id)->first();
        $balance     = $fiWallet ? (float) $fiWallet->balance : -(float) $franchise->fee_total;
        $outstanding = $balance < 0 ? abs($balance) : 0.0;
        $totalPaid   = max(0.0, (float) $franchise->fee_total + $balance);

        return view('institute.franchises.fee-receipt', compact(
            'franchise', 'payment', 'totalPaid', 'outstanding'
        ));
    }

    // ─── Cancel Payment ───────────────────────────────────────────────────────

    public function cancel(Request $request, Franchise $franchise, FranchisePayDetail $payment)
    {
        $this->authorizeFranchise($franchise);
        abort_if($payment->franchise_id !== $franchise->id, 403);
        abort_if($payment->cancelled_at !== null, 422, 'Already cancelled.');

        $data = $request->validate([
            'cancel_reason' => 'required|string|max:255',
        ]);

        $amount = (float) $payment->amount;
        $iid    = $franchise->institute_id;
        $fid    = $franchise->id;
        $by     = Auth::guard('institute')->id();

        DB::transaction(function () use ($franchise, $payment, $data, $amount, $iid, $fid, $by) {
            // 1. Mark payment cancelled
            $payment->update([
                'cancelled_at'  => now(),
                'cancel_reason' => $data['cancel_reason'],
                'cancelled_by'  => $by,
            ]);

            // 2. Reversal ledger entry
            $fiWallet       = FranchiseInstituteWallet::where('franchise_id', $fid)->lockForUpdate()->first();
            $currentBalance = $fiWallet ? (float) $fiWallet->balance : -(float) $franchise->fee_total;
            $newBalance     = $currentBalance - $amount;

            FranchiseInstituteTransaction::create([
                'franchise_id' => $fid,
                'institute_id' => $iid,
                'txn_no'       => $this->invoiceService->generateFranchiseInstTxnNo($iid, $fid),
                'type'         => 3, // payment_cancelled
                'description'  => "Payment cancelled: {$payment->invoice_no} — {$data['cancel_reason']}",
                'credit'       => 0,
                'debit'        => $amount,
                'invoice_no'   => $payment->invoice_no,
                'op_bal'       => $currentBalance,
                'cl_bal'       => $newBalance,
                'date'         => now()->toDateString(),
                'c_date'       => now(),
                'by_userid'    => $by,
            ]);

            // 3. Restore wallet balance
            $fiWallet?->update(['balance' => $newBalance]);

            // 4. Reverse institute student wallet entry
            $isWallet = InstituteStudentWallet::where('institute_id', $iid)->lockForUpdate()->first();
            if ($isWallet) {
                $isOpBal = (float) $isWallet->balance;
                $isClBal = $isOpBal - $amount;

                InstituteStudentTransaction::create([
                    'institute_id' => $iid,
                    'franchise_id' => $fid,
                    'description'  => "Franchise payment reversal: {$payment->invoice_no} — {$data['cancel_reason']}",
                    'credit'       => 0,
                    'debit'        => $amount,
                    'type'         => 2, // refund
                    'date'         => now()->toDateString(),
                    'c_date'       => now(),
                    'op_bal'       => $isOpBal,
                    'cl_bal'       => $isClBal,
                    'by_user_id'   => $by,
                ]);

                $isWallet->update(['balance' => $isClBal]);
            }
        });

        return back()->with('success', 'Payment cancelled and wallet balance reversed.');
    }

    // ─── Auth Helper ─────────────────────────────────────────────────────────

    private function authorizeFranchise(Franchise $franchise): void
    {
        abort_if($franchise->institute_id !== $this->instituteId(), 403);
    }
}
