<?php

namespace App\Http\Controllers\Institute;

use App\Http\Controllers\Controller;
use App\Models\Franchise;
use App\Models\FranchiseFeeCollection;
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
        $franchise->load(['level', 'head']);

        $collections = FranchiseFeeCollection::where('franchise_id', $franchise->id)
            ->orderByDesc('id')
            ->get();

        $totalPaid       = $collections->whereNull('cancelled_at')->sum('amount');
        $totalCancelled  = $collections->whereNotNull('cancelled_at')->sum('amount');
        $outstanding     = max(0, (float) $franchise->fee_total - $totalPaid);

        return view('institute.franchises.fee-collections', compact(
            'franchise', 'collections', 'totalPaid', 'totalCancelled', 'outstanding'
        ));
    }

    // ─── Collect Payment ─────────────────────────────────────────────────────

    public function collect(Request $request, Franchise $franchise)
    {
        $this->authorizeFranchise($franchise);

        $outstanding = max(0, (float) $franchise->fee_total - $franchise->feePaid());

        $data = $request->validate([
            'payment_mode' => 'required|in:CASH,UPI,NEFT,IMPS,CHEQUE',
            'utr'          => 'nullable|string|max:80',
            'amount'       => "required|numeric|min:0.01|max:{$outstanding}",
            'date'         => 'required|date',
            'note'         => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($franchise, $data) {
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
        });

        return back()->with('success', "₹" . number_format($data['amount'], 2) . " collected successfully.");
    }

    // ─── Receipt ─────────────────────────────────────────────────────────────

    public function receipt(Franchise $franchise, FranchiseFeeCollection $collection)
    {
        $this->authorizeFranchise($franchise);
        abort_if($collection->franchise_id !== $franchise->id, 403);

        $franchise->load(['level', 'institute']);
        $totalPaid   = FranchiseFeeCollection::where('franchise_id', $franchise->id)
            ->whereNull('cancelled_at')
            ->sum('amount');
        $outstanding = max(0, (float) $franchise->fee_total - $totalPaid);

        return view('institute.franchises.fee-receipt', compact('franchise', 'collection', 'totalPaid', 'outstanding'));
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

        $collection->update([
            'cancelled_at'  => now(),
            'cancel_reason' => $data['cancel_reason'],
            'cancelled_by'  => Auth::guard('institute')->id(),
        ]);

        return back()->with('success', 'Collection cancelled successfully.');
    }

    // ─── Auth Helper ─────────────────────────────────────────────────────────

    private function authorizeFranchise(Franchise $franchise): void
    {
        abort_if($franchise->institute_id !== $this->instituteId(), 403);
    }
}
