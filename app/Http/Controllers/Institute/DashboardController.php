<?php

namespace App\Http\Controllers\Institute;

use App\Http\Controllers\Controller;
use App\Models\Owner\Institute;
use Illuminate\Support\Facades\Auth;
use App\Models\CourseBook;
use App\Models\CourseDetail;
use App\Models\FeeCollectDetail;
use App\Models\InstituteStudentWallet;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::guard('institute')->user();
        $institute = $user?->institute ?? ($user?->institute_id ? Institute::find($user->institute_id) : null);
        $feeAmountColumn = FeeCollectDetail::amountColumn();

        if (!$user || !$institute) {
            Auth::guard('institute')->logout();

            return redirect()
                ->route('login')
                ->withErrors(['login' => 'Your institute account is not linked to any institute. Please contact support/admin.']);
        }

        $stats = [
            'total_students' => User::where('institute_id', $institute->id)->where('role', 'student')->count(),
            'total_staff'    => User::where('institute_id', $institute->id)->where('role', 'staff')->count(),
            'active_courses' => CourseDetail::where('institute_id', $institute->id)->where('status', 'active')->count(),
            'enrollments'    => CourseBook::where('institute_id', $institute->id)->whereIn('status', ['OPEN','RUN'])->count(),
            'fee_this_month' => FeeCollectDetail::where('institute_id', $institute->id)
                ->whereMonth('date', now()->month)->whereYear('date', now()->year)->sum($feeAmountColumn),
            'fee_today'      => FeeCollectDetail::where('institute_id', $institute->id)
                ->whereDate('date', today())->sum($feeAmountColumn),
            'total_fee_due'  => 0, // calculated separately if needed
            'student_wallet_balance' => (float) InstituteStudentWallet::where('institute_id', $institute->id)->value('balance'),
        ];
        $recentStudents = User::where('institute_id', $institute->id)->where('role', 'student')->latest()->take(5)->get();
        return view('institute.dashboard', compact('stats', 'recentStudents', 'institute'));
    }
}
