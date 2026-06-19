<?php

namespace App\Http\Controllers\Institute;

use App\Http\Controllers\Controller;
use App\Models\Owner\Institute;
use Illuminate\Support\Facades\Auth;
use App\Models\CourseBook;
use App\Models\CourseDetail;
use App\Models\Enquiry;
use App\Models\FeeCollectDetail;
use App\Models\InstituteStudentWallet;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $user      = Auth::guard('institute')->user();
        $institute = $user?->institute ?? ($user?->institute_id ? Institute::find($user->institute_id) : null);
        $feeCol    = FeeCollectDetail::amountColumn();

        if (!$user || !$institute) {
            Auth::guard('institute')->logout();
            return redirect()->route('login')
                ->withErrors(['login' => 'Your institute account is not linked to any institute. Please contact support/admin.']);
        }

        $iid = $institute->id;

        // ── Core fee stats ────────────────────────────────────
        $feeThisMonth = (float) FeeCollectDetail::where('institute_id', $iid)
            ->whereMonth('date', now()->month)->whereYear('date', now()->year)
            ->whereNull('cancelled_at')->sum($feeCol);

        $feeLastMonth = (float) FeeCollectDetail::where('institute_id', $iid)
            ->whereMonth('date', now()->subMonth()->month)
            ->whereYear('date', now()->subMonth()->year)
            ->whereNull('cancelled_at')->sum($feeCol);

        $feeToday = (float) FeeCollectDetail::where('institute_id', $iid)
            ->whereDate('date', today())->whereNull('cancelled_at')->sum($feeCol);

        $monthGrowth = $feeLastMonth > 0
            ? round((($feeThisMonth - $feeLastMonth) / $feeLastMonth) * 100, 1)
            : null;

        // ── Student & enrollment stats ────────────────────────
        $totalStudents  = User::where('institute_id', $iid)->where('role', 'student')->count();
        $newThisMonth   = User::where('institute_id', $iid)->where('role', 'student')
            ->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count();
        $enrollmentsRun  = CourseBook::where('institute_id', $iid)->where('status', 'RUN')->count();
        $enrollmentsOpen = CourseBook::where('institute_id', $iid)->where('status', 'OPEN')->count();

        $stats = [
            'total_students'         => $totalStudents,
            'new_students_month'     => $newThisMonth,
            'total_staff'            => User::where('institute_id', $iid)->where('role', 'staff')->count(),
            'active_courses'         => CourseDetail::where('institute_id', $iid)->where('status', 'active')->count(),
            'enrollments_run'        => $enrollmentsRun,
            'enrollments_open'       => $enrollmentsOpen,
            'fee_this_month'         => $feeThisMonth,
            'fee_last_month'         => $feeLastMonth,
            'fee_today'              => $feeToday,
            'month_growth'           => $monthGrowth,
            'total_fee_due'          => 0,
            'student_wallet_balance' => (float) InstituteStudentWallet::where('institute_id', $iid)->value('balance'),
        ];

        // ── Last 6 months revenue (bar chart) ────────────────
        $monthlyRevenue = collect();
        for ($i = 5; $i >= 0; $i--) {
            $d = now()->subMonths($i);
            $monthlyRevenue->push([
                'label'  => $d->format('M y'),
                'amount' => (float) FeeCollectDetail::where('institute_id', $iid)
                    ->whereMonth('date', $d->month)->whereYear('date', $d->year)
                    ->whereNull('cancelled_at')->sum($feeCol),
            ]);
        }

        // ── Recent fee collections ────────────────────────────
        $recentFees = FeeCollectDetail::where('institute_id', $iid)
            ->whereNull('cancelled_at')->latest('id')->take(8)->get();
        $feeUserMap = User::with('profile')
            ->whereIn('id', $recentFees->pluck('user_id')->unique()->filter())
            ->get()->keyBy('id');

        // ── Course-wise enrollments ───────────────────────────
        $courseEnrollments = CourseBook::where('institute_id', $iid)
            ->whereIn('status', ['OPEN', 'RUN'])
            ->with('course')
            ->get()
            ->groupBy('course_id')
            ->map(fn($g) => [
                'name'  => $g->first()->course?->name ?? 'Unknown Course',
                'total' => $g->count(),
                'run'   => $g->where('status', 'RUN')->count(),
                'open'  => $g->where('status', 'OPEN')->count(),
            ])
            ->sortByDesc('total')
            ->take(6)
            ->values();

        // ── Recent students ───────────────────────────────────
        $recentStudents = User::where('institute_id', $iid)
            ->where('role', 'student')->with('profile')->latest()->take(5)->get();

        // ── Enquiry pipeline stats ────────────────────────────
        $enquiryOpen      = Enquiry::where('institute_id', $iid)->where('status', 'OPEN')->count();
        $enquiryDueToday  = Enquiry::where('institute_id', $iid)->where('status', 'OPEN')
                                   ->whereDate('next_followup_date', today())->count();
        $enquiryOverdue   = Enquiry::where('institute_id', $iid)->where('status', 'OPEN')
                                   ->whereDate('next_followup_date', '<', today())->count();
        $enquiryConverted = Enquiry::where('institute_id', $iid)->where('status', 'CONVERTED')->count();
        $enquiryLost      = Enquiry::where('institute_id', $iid)->where('status', 'LOST')->count();
        $enquiryTotal     = $enquiryOpen + $enquiryConverted + $enquiryLost;
        $conversionRate   = $enquiryTotal > 0 ? round(($enquiryConverted / $enquiryTotal) * 100) : 0;

        $enquiryStats = compact(
            'enquiryOpen', 'enquiryDueToday', 'enquiryOverdue',
            'enquiryConverted', 'enquiryLost', 'enquiryTotal', 'conversionRate'
        );

        return view('institute.dashboard', compact(
            'stats', 'recentStudents', 'institute',
            'monthlyRevenue', 'recentFees', 'feeUserMap', 'courseEnrollments',
            'enquiryStats'
        ));
    }
}
