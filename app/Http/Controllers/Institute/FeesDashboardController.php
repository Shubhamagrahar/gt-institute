<?php

namespace App\Http\Controllers\Institute;

use App\Http\Controllers\Controller;
use App\Models\CourseBook;
use App\Models\CourseDetail;
use App\Models\EnrollmentPaymentPlan;
use App\Models\FeeCollectDetail;
use App\Models\InstituteSession;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FeesDashboardController extends Controller
{
    private function instituteId(): int
    {
        return Auth::guard('institute')->user()->institute_id;
    }

    public function index(Request $request)
    {
        $iid    = $this->instituteId();
        $today  = now();
        $tab    = $request->input('tab', 'all-dues');
        $amtCol = FeeCollectDetail::amountColumn();

        // ─── All fees due (any student with outstanding balance) ──────────
        $allDues = CourseBook::with(['student.profile', 'course'])
            ->where('institute_id', $iid)
            ->whereIn('status', ['OPEN', 'RUN'])
            ->whereHas('paymentPlan')
            ->get()
            ->map(function (CourseBook $book) use ($amtCol) {
                $paid = (float) FeeCollectDetail::where('course_book_id', $book->id)
                    ->whereNull('cancelled_at')
                    ->sum($amtCol);
                $due  = round(max(0.0, (float) $book->final_fee - $paid), 2);
                $book->setAttribute('paid_amount', $paid);
                $book->setAttribute('due_amount',  $due);
                return $book;
            })
            ->filter(fn ($b) => $b->due_amount > 0)
            ->sortByDesc('due_amount')
            ->values();

        // ─── Monthly dues (accumulated months + late fee) ─────────────────
        $monthlyDues = CourseBook::with(['student.profile', 'course', 'paymentPlan'])
            ->where('institute_id', $iid)
            ->where('status', 'RUN')
            ->whereHas('paymentPlan', fn ($q) => $q->where('plan_type', 'MONTHLY')
                ->whereNotNull('next_due_date'))
            ->get()
            ->map(function (CourseBook $book) use ($today) {
                $plan    = $book->paymentPlan;
                $nextDue = Carbon::parse($plan->next_due_date)->startOfMonth();

                // Not due yet — skip
                if ($nextDue->gt($today->copy()->startOfMonth())) {
                    return null;
                }

                // Count from next_due_date month up to and including current month
                $monthsCount = (int) $nextDue->diffInMonths($today->copy()->startOfMonth()) + 1;
                $monthlyAmt  = (float) ($plan->monthly_amount ?? 0);
                $lateFee     = $this->calcLateFee($plan);
                $totalDue    = round($monthsCount * $monthlyAmt + $lateFee, 2);

                $book->setAttribute('months_count',   $monthsCount);
                $book->setAttribute('monthly_amount', $monthlyAmt);
                $book->setAttribute('late_fee_amt',   $lateFee);
                $book->setAttribute('total_due',      $totalDue);
                $book->setAttribute('next_due',       $nextDue);
                return $book;
            })
            ->filter()
            ->sortByDesc('total_due')
            ->values();

        // ─── Sessions & courses for enrollment tab ────────────────────────
        $sessions      = InstituteSession::where('institute_id', $iid)->orderByDesc('created_at')->get();
        $courses       = CourseDetail::where('institute_id', $iid)->where('status', 'active')->orderBy('name')->get();
        $activeSession = $sessions->firstWhere('is_active', true);
        $sessionId     = $request->input('session_id', $activeSession?->id);
        $courseId      = $request->input('course_id');

        $enrollments = CourseBook::with(['student.profile', 'course', 'batch', 'paymentPlan'])
            ->where('institute_id', $iid)
            ->when($sessionId, fn ($q) => $q->where('session_id', $sessionId))
            ->when($courseId,  fn ($q) => $q->where('course_id', $courseId))
            ->whereIn('status', ['OPEN', 'RUN'])
            ->latest()
            ->paginate(30)
            ->withQueryString();

        return view('institute.enrollment.fees-dashboard', compact(
            'allDues', 'monthlyDues',
            'sessions', 'courses', 'activeSession', 'sessionId', 'courseId',
            'enrollments', 'today', 'tab'
        ));
    }

    // ─── AJAX student search (Quick Pay) ─────────────────────────────────────
    public function search(Request $request)
    {
        $q   = trim($request->input('q', ''));
        $iid = $this->instituteId();

        if (mb_strlen($q) < 2) {
            return response()->json([]);
        }

        $amtCol = FeeCollectDetail::amountColumn();

        $books = CourseBook::with(['student.profile', 'course'])
            ->where('institute_id', $iid)
            ->whereIn('status', ['OPEN', 'RUN'])
            ->where(function ($query) use ($q) {
                $query->where('enrollment_no', 'like', "%{$q}%")
                    ->orWhereHas('student', function ($u) use ($q) {
                        $u->where('mobile',  'like', "{$q}%")
                          ->orWhere('email',   'like', "{$q}%")
                          ->orWhere('user_id', 'like', "%{$q}%")
                          ->orWhereHas('profile', fn ($p) => $p->where('name', 'like', "%{$q}%"));
                    });
            })
            ->limit(12)
            ->get()
            ->map(function (CourseBook $book) use ($amtCol) {
                $paid = (float) FeeCollectDetail::where('course_book_id', $book->id)
                    ->whereNull('cancelled_at')
                    ->sum($amtCol);
                $due  = round(max(0.0, (float) $book->final_fee - $paid), 2);

                return [
                    'book_id'       => $book->id,
                    'enrollment_no' => $book->enrollment_no ?? '—',
                    'name'          => $book->student->profile?->name ?? $book->student->user_id,
                    'mobile'        => $book->student->mobile ?? '—',
                    'user_id'       => $book->student->user_id,
                    'course'        => $book->course->name,
                    'status'        => $book->status,
                    'total_fee'     => (float) $book->final_fee,
                    'paid'          => $paid,
                    'due'           => $due,
                    'pay_url'       => route('institute.enrollment.payment-complete', $book->id),
                ];
            });

        return response()->json($books->values());
    }

    private function calcLateFee(EnrollmentPaymentPlan $plan): float
    {
        if (! $plan->next_due_date || ! $plan->late_fee_per_day) {
            return 0;
        }

        $graceEnd = Carbon::parse($plan->next_due_date)->addDays((int) ($plan->grace_days ?? 0));

        if (now()->lte($graceEnd)) {
            return 0;
        }

        return round(now()->diffInDays($graceEnd) * (float) $plan->late_fee_per_day, 2);
    }
}
