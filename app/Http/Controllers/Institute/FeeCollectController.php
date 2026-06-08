<?php

namespace App\Http\Controllers\Institute;

use App\Http\Controllers\Controller;
use App\Models\{FeeCollectDetail, InstituteStudentWallet,
                 InstituteEnrollmentCounter,
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

        $pendingEnrollments = $enrollments->where('status', 'OPEN')->values()->map(function ($courseBook) {
            $paidAmount = (float) FeeCollectDetail::where('course_book_id', $courseBook->id)
                ->sum(FeeCollectDetail::amountColumn());
            $requiredAmount = $this->requiredAdmissionAmount($courseBook);
            $courseBook->setAttribute('paid_amount', $paidAmount);
            $courseBook->setAttribute('required_amount', $requiredAmount);
            $courseBook->setAttribute('details_complete', (bool) $courseBook->profile_completed_at);

            return $courseBook;
        });

        return view('institute.fee-collect.show',
            compact('user', 'wallet', 'transactions', 'receipts', 'enrollments', 'pendingEnrollments'));
    }

    // Collect fee
    public function collect(Request $request, User $user)
    {
        if ($user->institute_id !== $this->instituteId()) abort(403);

        $data = $request->validate([
            'course_book_id' => 'nullable|exists:course_books,id',
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
            $courseBook = null;

            if (! empty($data['course_book_id'])) {
                $courseBook = $user->enrollments()
                    ->where('institute_id', $iid)
                    ->where('id', $data['course_book_id'])
                    ->with('paymentPlan', 'student.profile')
                    ->firstOrFail();

                if ($courseBook->status === 'OPEN' && ! $courseBook->paymentPlan && (float) $courseBook->final_fee <= 0) {
                    abort(422, 'Fee setup is required before collecting admission payment.');
                }
            }

            // 1. fee_collect_details
            $feePayload = [
                'institute_id' => $iid,
                'franchise_id' => $user->franchise_id,
                'user_id'      => $user->id,
                'course_book_id' => $courseBook?->id,
                'invoice_no'   => $invoice,
                'payment_mode' => $data['payment_mode'],
                'utr'          => $data['utr'] ?? null,
                'date'         => $data['date'],
                'note'         => $data['note'] ?? null,
                'received_by'  => $byUser,
                'by_rcv'       => $byUser,
            ];
            $feePayload['amount'] = $amount;
            $feePayload['amt'] = $amount;

            $fee = FeeCollectDetail::create($feePayload);

            if (! $courseBook || $courseBook->status === 'RUN') {
                // Existing admitted student payment immediately affects student due ledger.
                $sw    = StudentWallet::firstOrCreate(
                    ['user_id' => $user->id],
                    [
                        'institute_id' => $iid,
                        'franchise_id' => $user->franchise_id,
                        'owner_type' => $user->franchise_id ? 'franchise' : 'institute',
                        'balance' => 0,
                    ]
                );
                $opBal = $sw->balance;
                $clBal = $opBal + $amount;
                $sw->update(['balance' => $clBal]);

                StudentTransaction::create([
                    'user_id'      => $user->id,
                    'institute_id' => $iid,
                    'franchise_id' => $user->franchise_id,
                    'owner_type'   => $user->franchise_id ? 'franchise' : 'institute',
                    'description'  => $data['payment_mode'] . ' payment received. Invoice: ' . $invoice,
                    'credit'       => $amount,
                    'debit'        => 0,
                    'type'         => 2,
                    'ref_type'     => 'fee_collect_detail',
                    'ref_id'       => null,
                    'date'         => $data['date'],
                    'c_date'       => $now,
                    'op_bal'       => $opBal,
                    'cl_bal'       => $clBal,
                    'by_user_id'   => $byUser,
                ]);
            }

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
                'franchise_id' => $user->franchise_id,
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

            if (! $courseBook || $courseBook->status === 'RUN') {
                StudentTransaction::where('user_id', $user->id)
                    ->where('ref_type', 'fee_collect_detail')
                    ->whereNull('ref_id')
                    ->latest('id')
                    ->limit(1)
                    ->update(['ref_id' => $fee->id]);
            }

            if ($courseBook) {
                $this->finalizeAdmissionIfEligible($courseBook->fresh(['student.profile', 'paymentPlan']));
            }
        });

        return back()->with('success', 'Fee collected successfully. Pending admission will activate once payment and details are complete.');
    }

    // Receipt
    public function receipt(User $user, FeeCollectDetail $fee)
    {
        if ($fee->institute_id !== $this->instituteId()) abort(403);
        $institute = $user->institute;
        return view('institute.fee-collect.receipt', compact('user', 'fee', 'institute'));
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
                // Another request may create this row first.
            }

            $counter = InstituteEnrollmentCounter::where('institute_id', $iid)->lockForUpdate()->firstOrFail();
        }

        $counter->last_enrollment_no++;
        $counter->save();

        $code = Auth::guard('institute')->user()->institute?->unique_id ?? 'INST';

        return $code . '/ENR/' . str_pad((string) $counter->last_enrollment_no, 4, '0', STR_PAD_LEFT);
    }

    private function requiredAdmissionAmount($courseBook): float
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

    private function finalizeAdmissionIfEligible($courseBook): void
    {
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

        if (! $courseBook->enrollment_no) {
            $enrollmentNo = $this->generateEnrollmentNo($courseBook->institute_id);
            $courseBook->update(['enrollment_no' => $enrollmentNo]);
        }

        $courseBook->update([
            'status' => 'RUN',
            'start_date' => $courseBook->start_date ?? now()->toDateString(),
        ]);
    }
}
