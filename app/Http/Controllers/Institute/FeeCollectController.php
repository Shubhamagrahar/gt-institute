<?php

namespace App\Http\Controllers\Institute;

use App\Http\Controllers\Controller;
use App\Models\{FeeCollectDetail, InstituteStudentWallet,
                 InstituteStudentTransaction, StudentTransaction,
                 StudentWallet, User};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, DB};

class FeeCollectController extends Controller
{
    private function instituteId(): int
    {
        return Auth::guard('institute')->user()->institute_id;
    }

    // List students with dues
    public function index()
    {
        $iid = $this->instituteId();
        $students = User::where('institute_id', $iid)
            ->where('role', 'student')
            ->with(['profile', 'studentWallet'])
            ->get()
            ->filter(fn($u) => $u->studentWallet?->balance < 0);

        return view('institute.fee-collect.index', compact('students'));
    }

    // Student fee detail page
    public function show(User $user)
    {
        if ($user->institute_id !== $this->instituteId()) abort(403);

        $wallet       = $user->studentWallet;
        $transactions = StudentTransaction::where('user_id', $user->id)
                            ->orderByDesc('id')->get();
        $receipts     = FeeCollectDetail::where('user_id', $user->id)
                            ->orderByDesc('date')->get();
        $enrollments  = $user->enrollments()->with(['course', 'feeSnapshots', 'paymentPlan'])->get();

        return view('institute.fee-collect.show',
            compact('user', 'wallet', 'transactions', 'receipts', 'enrollments'));
    }

    // Collect fee
    public function collect(Request $request, User $user)
    {
        if ($user->institute_id !== $this->instituteId()) abort(403);

        $data = $request->validate([
            'amount'       => 'required|numeric|min:1',
            'payment_mode' => 'required|in:CASH,UPI,NEFT,IMPS,CHEQUE',
            'utr'          => 'nullable|string|max:80',
            'date'         => 'required|date',
            'note'         => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($data, $user) {
            $iid    = $this->instituteId();
            $byUser = Auth::guard('institute')->id();
            $amount = (float) $data['amount'];
            $now    = now();
            $invoice = 'INV' . date('Ymd') . strtoupper(substr(uniqid(), -5));
            $feeAmountColumn = FeeCollectDetail::amountColumn();

            // 1. fee_collect_details
            $feePayload = [
                'institute_id' => $iid,
                'user_id'      => $user->id,
                'invoice_no'   => $invoice,
                'payment_mode' => $data['payment_mode'],
                'utr'          => $data['utr'] ?? null,
                'date'         => $data['date'],
                'note'         => $data['note'] ?? null,
                'received_by'  => $byUser,
            ];
            $feePayload[$feeAmountColumn] = $amount;

            FeeCollectDetail::create($feePayload);

            // 2. Student wallet credit
            $sw    = StudentWallet::where('user_id', $user->id)->firstOrFail();
            $opBal = $sw->balance;
            $clBal = $opBal + $amount;
            $sw->update(['balance' => $clBal]);

            // 3. Student transaction credit
            StudentTransaction::create([
                'user_id'      => $user->id,
                'institute_id' => $iid,
                'description'  => $data['payment_mode'] . ' payment received. Invoice: ' . $invoice,
                'credit'       => $amount,
                'debit'        => 0,
                'type'         => 2,
                'date'         => $data['date'],
                'c_date'       => $now,
                'op_bal'       => $opBal,
                'cl_bal'       => $clBal,
                'by_user_id'   => $byUser,
            ]);

            // 4. Institute student wallet credit
            $iw = InstituteStudentWallet::firstOrCreate(
                ['institute_id' => $iid],
                ['balance' => 0]
            );
            $iOpBal = $iw->balance;
            $iClBal = $iOpBal + $amount;
            $iw->update(['balance' => $iClBal]);

            // 5. Institute transaction
            InstituteStudentTransaction::create([
                'institute_id' => $iid,
                'ref_user_id'  => $user->id,
                'description'  => 'Fee received from ' . ($user->profile?->name ?? $user->user_id) . ' | Invoice: ' . $invoice,
                'credit'       => $amount,
                'debit'        => 0,
                'type'         => 1,
                'date'         => $data['date'],
                'c_date'       => $now,
                'op_bal'       => $iOpBal,
                'cl_bal'       => $iClBal,
                'by_user_id'   => $byUser,
            ]);
        });

        return back()->with('success', 'Fee collected successfully.');
    }

    // Receipt
    public function receipt(User $user, FeeCollectDetail $fee)
    {
        if ($fee->institute_id !== $this->instituteId()) abort(403);
        $institute = $user->institute;
        return view('institute.fee-collect.receipt', compact('user', 'fee', 'institute'));
    }
}
