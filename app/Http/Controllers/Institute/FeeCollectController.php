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

    // Quick Pay: search page
    public function quickPay()
    {
        return view('institute.fee-collect.quick-pay');
    }

    // Quick Pay: AJAX student search
    public function quickPaySearch(Request $request)
    {
        $q = trim($request->get('q', ''));
        if (mb_strlen($q) < 2) {
            return response()->json([]);
        }

        $iid = $this->instituteId();

        $students = User::where('institute_id', $iid)
            ->where('role', 'student')
            ->with(['profile', 'enrollments' => fn($eq) => $eq->orderByDesc('id')->limit(5)])
            ->where(function ($query) use ($q) {
                $query->where('user_id', 'like', "%{$q}%")
                      ->orWhere('mobile', 'like', "%{$q}%")
                      ->orWhere('email', 'like', "%{$q}%")
                      ->orWhereHas('profile', fn($pq) => $pq->where('name', 'like', "%{$q}%"))
                      ->orWhereHas('enrollments', fn($eq) => $eq->where('enrollment_no', 'like', "%{$q}%"));
            })
            ->limit(15)
            ->get();

        return response()->json($students->map(function ($student) {
            $enrollment = $student->enrollments->whereNotNull('enrollment_no')->first();
            return [
                'id'            => $student->id,
                'user_id'       => $student->user_id,
                'name'          => $student->profile?->name ?? $student->user_id,
                'mobile'        => $student->mobile ?? null,
                'email'         => $student->email ?? null,
                'enrollment_no' => $enrollment?->enrollment_no ?? null,
                'url'           => route('institute.fee-collect.show', $student->id),
            ];
        }));
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
            } else {
                // Auto-link only when the student has exactly ONE active RUN enrollment.
                $runBooks = $user->enrollments()
                    ->where('institute_id', $iid)
                    ->where('status', 'RUN')
                    ->get();
                if ($runBooks->count() === 1) {
                    $courseBook = $runBooks->first();
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
                $this->finalizeAdmissionIfEligible($courseBook->fresh(['course']));
            }
        });

        return back()->with('success', 'Fee collected successfully. Admission activated automatically.');
    }

    // Receipt
    public function receipt(User $user, FeeCollectDetail $fee)
    {
        if ($fee->institute_id !== $this->instituteId()) abort(403);
        $institute = $user->institute;
        return view('institute.fee-collect.receipt', compact('user', 'fee', 'institute'));
    }

    private function requiredAdmissionAmount(\App\Models\CourseBook $courseBook): float
    {
        if ($courseBook->paymentPlan) {
            return (float) ($courseBook->paymentPlan->required_fee
                         ?? $courseBook->paymentPlan->first_payment_amount
                         ?? 0);
        }

        $snapshotTotal = $courseBook->feeSnapshots->sum('final_amount');
        if ($snapshotTotal > 0) {
            return (float) $snapshotTotal;
        }

        return (float) ($courseBook->final_fee ?? 0);
    }

    private function generateEnrollmentNo(\App\Models\CourseBook $courseBook): string
    {
        $iid = $courseBook->institute_id;
        $institute = Auth::guard('institute')->user()->institute
            ?? \App\Models\Owner\Institute::find($iid);
        $short = strtoupper(preg_replace('/[^A-Z0-9]/i', '', $institute?->short_name ?? $institute?->name ?? 'INS'));
        $short = substr($short, 0, 8) ?: 'INS';

        $courseCode = strtoupper(trim($courseBook->course_code ?? ''));
        if (! $courseCode) {
            $courseName = $courseBook->course?->name ?? 'COURSE';
            $courseCode = implode('', array_map(
                fn ($w) => strtoupper($w[0] ?? ''),
                array_filter(explode(' ', $courseName), fn ($w) => strlen($w) > 0)
            ));
        }
        $courseCode = substr($courseCode, 0, 10) ?: 'CRS';

        $year = now()->year;

        $counter = InstituteEnrollmentCounter::where('institute_id', $iid)->lockForUpdate()->first();
        if (! $counter) {
            try {
                InstituteEnrollmentCounter::create(['institute_id' => $iid, 'last_enrollment_no' => 0, 'last_student_no' => 0]);
            } catch (\Throwable $e) {}
            $counter = InstituteEnrollmentCounter::where('institute_id', $iid)->lockForUpdate()->firstOrFail();
        }

        $counter->increment('last_enrollment_no');

        return $short . '/' . $courseCode . '/' . $year . '/' . str_pad((string) $counter->last_enrollment_no, 4, '0', STR_PAD_LEFT);
    }

    private function finalizeAdmissionIfEligible(\App\Models\CourseBook $courseBook): void
    {
        if ($courseBook->status !== 'OPEN') {
            return;
        }

        $paidAmount = (float) FeeCollectDetail::where('course_book_id', $courseBook->id)
            ->whereNull('cancelled_at')
            ->sum(FeeCollectDetail::amountColumn());

        if ($paidAmount <= 0) {
            return;
        }

        $courseBook->loadMissing('course');

        if (! $courseBook->enrollment_no) {
            $courseBook->update(['enrollment_no' => $this->generateEnrollmentNo($courseBook)]);
        }

        $courseBook->update([
            'status' => 'RUN',
            'start_date' => $courseBook->start_date ?? now()->toDateString(),
        ]);
    }
}
