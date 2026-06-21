<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\FeeCollectDetail;
use App\Models\CourseBook;
use App\Models\CourseDetail;
use App\Models\CourseType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    private function student()
    {
        return Auth::guard('student')->user();
    }

    public function dashboard()
    {
        $student   = $this->student()->load('profile');
        $userId    = $student->id;
        $amtCol    = FeeCollectDetail::amountColumn();

        // Latest enrollment
        $enrollment = CourseBook::with(['course', 'batch'])
            ->where('user_id', $userId)
            ->orderByDesc('created_at')
            ->first();

        // All enrollments
        $allEnrollments = CourseBook::with(['course', 'batch'])
            ->where('user_id', $userId)
            ->orderByDesc('created_at')
            ->get();

        // Fee summary
        $totalFee  = $allEnrollments->sum('final_fee');
        $paidFee   = FeeCollectDetail::where('user_id', $userId)
            ->whereNull('cancelled_at')
            ->sum($amtCol);
        $balance   = max(0, $totalFee - $paidFee);

        // Recent fee transactions (last 5)
        $recentFees = FeeCollectDetail::where('user_id', $userId)
            ->whereNull('cancelled_at')
            ->with('student')
            ->orderByDesc('date')
            ->limit(5)
            ->get();

        // Attendance this month (student attendance table)
        $now    = Carbon::now();
        $present = 0;
        $absent  = 0;
        $total   = 0;
        try {
            $att = DB::table('attendance_students')
                ->where('user_id', $userId)
                ->whereYear('date', $now->year)
                ->whereMonth('date', $now->month)
                ->select('status')
                ->get();
            $present = $att->where('status', 'present')->count();
            $absent  = $att->where('status', 'absent')->count();
            $total   = $att->count();
        } catch (\Exception $e) {}

        return view('student.dashboard', compact(
            'student', 'enrollment', 'allEnrollments',
            'totalFee', 'paidFee', 'balance',
            'recentFees', 'present', 'absent', 'total', 'now'
        ));
    }

    public function fees()
    {
        $student    = $this->student()->load('profile');
        $userId     = $student->id;
        $amtCol     = FeeCollectDetail::amountColumn();

        $enrollments = CourseBook::with(['course', 'batch'])
            ->where('user_id', $userId)
            ->orderByDesc('created_at')
            ->get();

        $fees = FeeCollectDetail::where('user_id', $userId)
            ->orderByDesc('date')
            ->get();

        $totalFee = $enrollments->sum('final_fee');
        $paidFee  = $fees->whereNull('cancelled_at')->sum($amtCol);
        $balance  = max(0, $totalFee - $paidFee);

        return view('student.fees', compact('student', 'enrollments', 'fees', 'totalFee', 'paidFee', 'balance'));
    }

    public function attendance()
    {
        $student = $this->student()->load('profile');
        $userId  = $student->id;

        $records = collect();
        $months  = [];
        try {
            $records = DB::table('attendance_students')
                ->where('user_id', $userId)
                ->orderByDesc('date')
                ->get();

            $months = DB::table('attendance_students')
                ->where('user_id', $userId)
                ->selectRaw('YEAR(date) as yr, MONTH(date) as mo, COUNT(*) as total,
                              SUM(CASE WHEN status="present" THEN 1 ELSE 0 END) as present,
                              SUM(CASE WHEN status="absent" THEN 1 ELSE 0 END) as absent')
                ->groupBy('yr', 'mo')
                ->orderByDesc('yr')->orderByDesc('mo')
                ->get();
        } catch (\Exception $e) {}

        return view('student.attendance', compact('student', 'records', 'months'));
    }

    public function profile()
    {
        $student = $this->student()->load(['profile', 'enrollments.course', 'enrollments.batch']);
        return view('student.profile', compact('student'));
    }

    public function courses()
    {
        $student = $this->student()->load('profile');
        $bookedCourseIds = CourseBook::where('user_id', $student->id)->pluck('course_id');

        // Find the institute of this student via their enrollment
        $instituteId = CourseBook::where('user_id', $student->id)
            ->join('course_details', 'course_books.course_id', '=', 'course_details.id')
            ->value('course_details.institute_id');

        $courseTypes = collect();
        $courses = collect();
        if ($instituteId) {
            $courses = CourseDetail::with(['courseType', 'feeStructures.feeType'])
                ->where('institute_id', $instituteId)
                ->where('status', 'active')
                ->orderBy('name')
                ->get();
            $courseTypes = CourseType::where('institute_id', $instituteId)->orderBy('name')->get();
        }

        return view('student.courses', compact('student', 'courses', 'courseTypes', 'bookedCourseIds'));
    }
}
