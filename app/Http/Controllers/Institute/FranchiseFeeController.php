<?php

namespace App\Http\Controllers\Institute;

use App\Http\Controllers\Controller;
use App\Models\Franchise;
use App\Models\FranchiseFeeCollection;
use App\Models\FranchiseJoiningWallet;
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

    // ─── Fee Collection List & Stats ─────────────────────────────────────────

    public function index(Franchise $franchise)
    {
        $this->authorizeFranchise($franchise);
        $franchise->load(['level', 'head', 'joiningWallet']);

        $collections = FranchiseFeeCollection::where('franchise_id', $franchise->id)
            ->orderByDesc('id')
            ->get();

        $wallet      = $franchise->joiningWallet;
        $totalDue    = $wallet ? (float) $wallet->total_due   : (float) $franchise->fee_total;
        $totalPaid   = $wallet ? (float) $wallet->total_paid  : $collections->whereNull('cancelled_at')->sum('amount');
        $outstanding = $wallet ? (float) $wallet->balance     : max(0, $totalDue - $totalPaid);
        $cancelled   = $collections->whereNotNull('cancelled_at')->sum('amount');

        return view('institute.franchises.fee-collections', compact(
            'franchise', 'collections',
            'totalDue', 'totalPaid', 'outstanding', 'cancelled'
        ));
    }

    // ─── Collect Payment ─────────────────────────────────────────────────────

    public function collect(Request $request, Franchise $franchise)
    {
        $this->authorizeFranchise($franchise);

        $wallet      = FranchiseJoiningWallet::where('franchise_id', $franchise->id)->first();
        $outstanding = $wallet ? (float) $wallet->balance
                               : max(0, (float) $franchise->fee_total - $franchise->feePaid());

        $data = $request->validate([
            'payment_mode' => 'required|in:CASH,UPI,NEFT,IMPS,CHEQUE',
            'utr'          => 'nullable|string|max:80',
            'amount'       => "required|numeric|min:0.01|max:{$outstanding}",
            'date'         => 'required|date',
            'note'         => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($franchise, $data, $wallet) {
            FranchiseFeeCollection::create([
                'franchise_id' => $franchise->id,
                'institute_id' => $franchise->institute_id,
                'invoice_no'   => $this->invoiceService->generateFranchiseFeeInvoice(
                    $franchise->institute_id, $franchise->id
                ),
                'payment_mode' => $data['payment_mode'],
                'utr'          => $data['utr'] ?? null,
                'amount'       => $data['amount'],
                'date'         => $data['date'],
                'note'         => $data['note'] ?? null,
                'collected_by' => Auth::guard('institute')->id(),
            ]);

            // Keep joining wallet in sync
            if ($wallet) {
                $wallet->recalculate();
            }
        });

        return back()->with('success', "₹" . number_format($data['amount'], 2) . " collected successfully.");
    }

    // ─── Receipt ─────────────────────────────────────────────────────────────

    public function receipt(Franchise $franchise, FranchiseFeeCollection $collection)
    {
        $this->authorizeFranchise($franchise);
        abort_if($collection->franchise_id !== $franchise->id, 403);

        $franchise->load(['level', 'institute', 'joiningWallet']);

        $wallet      = $franchise->joiningWallet;
        $totalPaid   = $wallet ? (float) $wallet->total_paid
                               : FranchiseFeeCollection::where('franchise_id', $franchise->id)->whereNull('cancelled_at')->sum('amount');
        $outstanding = $wallet ? (float) $wallet->balance
                               : max(0, (float) $franchise->fee_total - $totalPaid);

        return view('institute.franchises.fee-receipt', compact(
            'franchise', 'collection', 'totalPaid', 'outstanding'
        ));
    }

    // ─── Cancel Collection ────────────────────────────────────────────────────

    public function cancel(Request $request, Franchise $franchise, FranchiseFeeCollection $collection)
    {
        $this->authorizeFranchise($franchise);
        abort_if($collection->franchise_id !== $franchise->id, 403);
        abort_if($collection->cancelled_at !== null, 422, 'Already cancelled.');

        $data = $request->validate([
            'cancel_reason' => 'required|string|max:255',
        ]);

        DB::transaction(function () use ($franchise, $collection, $data) {
            $collection->update([
                'cancelled_at'  => now(),
                'cancel_reason' => $data['cancel_reason'],
                'cancelled_by'  => Auth::guard('institute')->id(),
            ]);

            $wallet = FranchiseJoiningWallet::where('franchise_id', $franchise->id)->first();
            $wallet?->recalculate();
        });

        return back()->with('success', 'Collection cancelled. Wallet balance updated.');
    }

    // ─── Auth Helper ─────────────────────────────────────────────────────────

    private function authorizeFranchise(Franchise $franchise): void
    {
        abort_if($franchise->institute_id !== $this->instituteId(), 403);
    }
}
