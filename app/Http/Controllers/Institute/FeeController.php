<?php

namespace App\Http\Controllers\Institute;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\{CourseBook, CourseDetail, FeeCollectDetail, Transaction, User, Wallet};
use App\Services\{InvoiceService, WalletService as StudentWalletService};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FeeController extends Controller
{
    private function institute() { return Auth::guard('institute')->user()->institute; }

    public function index()
    {
        $students = User::with(['studentProfile', 'wallet'])
            ->where('institute_id', $this->institute()->id)
            ->where('role', 'student')->latest()->paginate(20);
        return view('institute.fee.index', compact('students'));
    }

    public function collect(Request $request)
    {
        $data = $request->validate([
            'student_id'   => 'required|exists:users,id',
            'payment_mode' => 'required|in:CASH,UPI,NEFT,IMPS,CHEQUE',
            'utr'          => 'nullable|string|max:80',
            'amt'          => 'required|numeric|min:1',
            'date'         => 'required|date',
            'note'         => 'nullable|string|max:200',
        ]);

        $institute  = $this->institute();
        $student    = User::findOrFail($data['student_id']);
        abort_unless($student->institute_id === $institute->id, 403);

        $invoiceNo = (new InvoiceService)->generateFeeInvoice($institute->id);

        DB::transaction(function () use ($data, $student, $institute, $invoiceNo) {
            $wallet = Wallet::where('user_id', $student->id)->lockForUpdate()->firstOrFail();
            $opBal  = (float)$wallet->main_b;
            $clBal  = $opBal + (float)$data['amt'];

            Transaction::create([
                'user_id'      => $student->id,
                'institute_id' => $institute->id,
                'des'          => "{$data['payment_mode']}/Fee payment | {$data['note']}",
                'credit'       => $data['amt'],
                'debit'        => 0,
                'type'         => 1,
                'date'         => $data['date'],
                'c_date'       => now(),
                'op_bal'       => $opBal,
                'cl_bal'       => $clBal,
                'by_userid'    => Auth::guard('institute')->id(),
            ]);

            $wallet->update(['main_b' => $clBal]);

            FeeCollectDetail::create([
                'user_id'      => $student->id,
                'institute_id' => $institute->id,
                'invoice_no'   => $invoiceNo,
                'payment_mode' => $data['payment_mode'],
                'utr'          => $data['utr'] ?? null,
                'amt'          => $data['amt'],
                'date'         => $data['date'],
                'by_rcv'       => Auth::guard('institute')->id(),
            ]);
        });

        return back()->with('success', "Fee of ₹{$data['amt']} collected. Invoice: {$invoiceNo}");
    }

    public function history(User $student)
    {
        abort_unless($student->institute_id === $this->institute()->id, 403);
        $collections = FeeCollectDetail::where('user_id', $student->id)->latest()->get();
        return view('institute.fee.history', compact('student', 'collections'));
    }
}
