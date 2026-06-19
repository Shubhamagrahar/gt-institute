<?php

namespace App\Http\Controllers\Institute;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\{
    AttendanceStudent, AttendanceStaff,
    BatchDetail, CourseDetail, CourseBook,
    InstituteSession, User
};

class AttendanceController extends Controller
{
    private function institute()
    {
        return Auth::guard('institute')->user()->institute;
    }

    // ── MARK ATTENDANCE (live daily marking) ─────────────────────────────────

    public function studentIndex()
    {
        $institute = $this->institute();

        // All active courses — no enrollment filter
        $courses = CourseDetail::where('institute_id', $institute->id)
            ->where('status', 'active')
            ->orderBy('name')
            ->get(['id', 'name', 'course_code']);

        // All active batches — no enrollment filter
        $batches = BatchDetail::where('institute_id', $institute->id)
            ->where('status', 'active')
            ->orderBy('name')
            ->get(['id', 'name', 'start_time', 'end_time']);

        return view('institute.attendance.student', compact('courses', 'batches'));
    }

    // AJAX: all active batches (kept for backwards compat but now unused by new view)
    public function getBatches(Request $request)
    {
        $institute = $this->institute();

        $batches = BatchDetail::where('institute_id', $institute->id)
            ->where('status', 'active')
            ->orderBy('name')
            ->get(['id', 'name', 'start_time', 'end_time']);

        return response()->json($batches);
    }

    // AJAX: seed NULL-status records for date+course+batch, return list
    public function loadAttendance(Request $request)
    {
        $request->validate([
            'date'      => 'required|date',
            'course_id' => 'required|exists:course_details,id',
            'batch_id'  => 'required|exists:batch_details,id',
        ]);

        $institute = $this->institute();
        $batch     = BatchDetail::findOrFail($request->batch_id);
        $session   = InstituteSession::where('institute_id', $institute->id)
                        ->where('is_active', true)->first();
        $markedBy  = Auth::guard('institute')->id();
        $now       = now();

        $enrollments = CourseBook::where('institute_id', $institute->id)
            ->where('course_id', $request->course_id)
            ->where('batch_id', $request->batch_id)
            ->where('status', 'RUN')
            ->get();

        if ($enrollments->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'No active students enrolled in this course & batch.']);
        }

        // Bulk insert only new records — status NULL (unmarked), existing records untouched
        $rows = $enrollments->map(fn($e) => [
            'institute_id'   => $institute->id,
            'user_id'        => $e->user_id,
            'course_id'      => $request->course_id,
            'batch_id'       => $request->batch_id,
            'session_id'     => $session?->id,
            'course_book_id' => $e->id,
            'date'           => $request->date,
            'status'         => null,
            'in_time'        => $batch->start_time,
            'out_time'       => $batch->end_time,
            'created_by'     => $markedBy,
            'created_at'     => $now,
            'updated_at'     => $now,
        ])->toArray();

        AttendanceStudent::insertOrIgnore($rows);

        return $this->buildAttendanceResponse($institute->id, $request->course_id, $request->batch_id, $request->date);
    }

    // AJAX: set status for a single record (P / A / L / null)
    public function setStatus(Request $request)
    {
        $request->validate([
            'id'      => 'required|integer',
            'status'  => 'nullable|in:P,A,L',
            'in_time' => 'nullable|date_format:H:i',
        ]);

        $institute = $this->institute();
        $record    = AttendanceStudent::where('id', $request->id)
                        ->where('institute_id', $institute->id)
                        ->firstOrFail();

        $record->status = $request->status;

        // Only update in_time when explicitly sent (Late marking)
        if ($request->has('in_time')) {
            $record->in_time = $request->in_time ? $request->in_time . ':00' : $record->in_time;
        }

        $record->save();

        return response()->json(['success' => true, 'status' => $record->status, 'in_time' => $record->in_time ? substr($record->in_time, 0, 5) : null]);
    }

    // AJAX: bulk update all records for date+course+batch to given status
    public function markAll(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:course_details,id',
            'batch_id'  => 'required|exists:batch_details,id',
            'date'      => 'required|date',
            'status'    => 'required|in:P,A',
        ]);

        $institute = $this->institute();

        AttendanceStudent::where('institute_id', $institute->id)
            ->where('course_id', $request->course_id)
            ->where('batch_id', $request->batch_id)
            ->where('date', $request->date)
            ->update(['status' => $request->status]);

        return $this->buildAttendanceResponse($institute->id, $request->course_id, $request->batch_id, $request->date);
    }

    private function buildAttendanceResponse($instituteId, $courseId, $batchId, $date)
    {
        $records = AttendanceStudent::with('student.profile', 'courseBook')
            ->where('institute_id', $instituteId)
            ->where('course_id', $courseId)
            ->where('batch_id', $batchId)
            ->where('date', $date)
            ->get();

        $present   = $records->where('status', 'P')->count();
        $absent    = $records->where('status', 'A')->count();
        $late      = $records->where('status', 'L')->count();
        $unmarked  = $records->whereNull('status')->count();
        $total     = $records->count();

        return response()->json([
            'success'  => true,
            'total'    => $total,
            'present'  => $present,
            'absent'   => $absent,
            'late'     => $late,
            'unmarked' => $unmarked,
            'records'  => $records->map(function ($r) {
                $photo = $r->student?->profile?->photo;
                return [
                    'id'            => $r->id,
                    'name'          => $r->student?->name ?? '—',
                    'photo'         => ($photo && $photo !== 'images/user.png') ? asset($photo) : null,
                    'enrollment_no' => $r->courseBook?->enrollment_no ?? '—',
                    'status'        => $r->status,
                    'in_time'       => $r->in_time ? substr($r->in_time, 0, 5) : null,
                    'out_time'      => $r->out_time ? substr($r->out_time, 0, 5) : null,
                ];
            }),
        ]);
    }

    // ── ATTENDANCE REGISTER (monthly grid, editable) ──────────────────────────

    public function registerIndex()
    {
        $institute = $this->institute();

        $courses = CourseDetail::where('institute_id', $institute->id)
            ->where('status', 'active')
            ->orderBy('name')
            ->get(['id', 'name', 'course_code']);

        $batches = BatchDetail::where('institute_id', $institute->id)
            ->where('status', 'active')
            ->orderBy('name')
            ->get(['id', 'name', 'start_time', 'end_time']);

        return view('institute.attendance.register', compact('courses', 'batches'));
    }

    // AJAX: load register grid data
    public function registerLoad(Request $request)
    {
        $request->validate([
            'view_by'   => 'required|in:course,batch',
            'month'     => 'required|date_format:Y-m',
            'course_id' => 'required_if:view_by,course|nullable|exists:course_details,id',
            'batch_id'  => 'required_if:view_by,batch|nullable|exists:batch_details,id',
        ]);

        $institute = $this->institute();
        $month     = Carbon::parse($request->month . '-01');
        $start     = $month->copy()->startOfMonth()->toDateString();
        $end       = $month->copy()->endOfMonth()->toDateString();
        $days      = $month->daysInMonth;

        // Get enrolled students based on view_by
        $enrollmentQuery = CourseBook::with('student.profile')
            ->where('institute_id', $institute->id)
            ->where('status', 'RUN');

        if ($request->view_by === 'course') {
            $enrollmentQuery->where('course_id', $request->course_id);
        } else {
            $enrollmentQuery->where('batch_id', $request->batch_id);
        }

        $enrollments = $enrollmentQuery->get();

        if ($enrollments->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Koi enrolled student nahi mila.']);
        }

        // Get all attendance records for these students in the month
        $userIds = $enrollments->pluck('user_id')->unique();

        $attQuery = AttendanceStudent::where('institute_id', $institute->id)
            ->whereIn('user_id', $userIds)
            ->whereBetween('date', [$start, $end]);

        if ($request->view_by === 'course') {
            $attQuery->where('course_id', $request->course_id);
        } else {
            $attQuery->where('batch_id', $request->batch_id);
        }

        // attendance keyed by user_id → day
        $rawAttendance = $attQuery->get()->groupBy('user_id');

        $studentData = $enrollments->unique('user_id')->map(function ($enrollment) use ($rawAttendance, $days) {
            $userId = $enrollment->user_id;
            $userAtt = $rawAttendance->get($userId, collect());

            // Map day → record
            $dayMap = $userAtt->keyBy(fn($a) => (int) Carbon::parse($a->date)->format('j'));

            $dayStatuses = [];
            $present = $absent = $late = $unmarked = 0;

            for ($d = 1; $d <= $days; $d++) {
                $rec = $dayMap->get($d);
                $status = $rec ? $rec->status : null;
                $dayStatuses[] = [
                    'day'    => $d,
                    'att_id' => $rec?->id,
                    'status' => $status,
                ];
                if ($status === 'P') $present++;
                elseif ($status === 'A') $absent++;
                elseif ($status === 'L') $late++;
                else $unmarked++;
            }

            $photo = $enrollment->student?->profile?->photo;
            $marked = $present + $absent + $late;
            $pct    = $marked > 0 ? round(($present / $marked) * 100) : null;

            return [
                'user_id'       => $userId,
                'name'          => $enrollment->student?->name ?? '—',
                'photo'         => ($photo && $photo !== 'images/user.png') ? asset($photo) : null,
                'enrollment_no' => $enrollment->enrollment_no ?? '—',
                'days'          => $dayStatuses,
                'present'       => $present,
                'absent'        => $absent,
                'late'          => $late,
                'unmarked'      => $unmarked,
                'pct'           => $pct,
            ];
        })->values();

        return response()->json([
            'success'    => true,
            'days_count' => $days,
            'month_label'=> $month->format('F Y'),
            'students'   => $studentData,
        ]);
    }

    // AJAX: update a single cell in the register
    public function registerCellUpdate(Request $request)
    {
        $request->validate([
            'att_id'    => 'nullable|integer|exists:attendance_students,id',
            'status'    => 'nullable|in:P,A,L',
            // Needed only when att_id is null (new record for that day)
            'user_id'   => 'required_without:att_id|integer',
            'course_id' => 'required_without:att_id|integer',
            'batch_id'  => 'nullable|integer',
            'date'      => 'required_without:att_id|date',
        ]);

        $institute = $this->institute();

        if ($request->att_id) {
            $record = AttendanceStudent::where('id', $request->att_id)
                ->where('institute_id', $institute->id)
                ->firstOrFail();
            $record->status = $request->status;
            $record->save();
        } else {
            // Create record for a day that wasn't seeded (student was added after that date)
            $session = InstituteSession::where('institute_id', $institute->id)
                ->where('is_active', true)->first();

            $enrollment = CourseBook::where('institute_id', $institute->id)
                ->where('user_id', $request->user_id)
                ->where('course_id', $request->course_id)
                ->first();

            $record = AttendanceStudent::create([
                'institute_id'   => $institute->id,
                'user_id'        => $request->user_id,
                'course_id'      => $request->course_id,
                'batch_id'       => $request->batch_id,
                'session_id'     => $session?->id,
                'course_book_id' => $enrollment?->id,
                'date'           => $request->date,
                'status'         => $request->status,
                'created_by'     => Auth::guard('institute')->id(),
            ]);
        }

        return response()->json(['success' => true, 'att_id' => $record->id, 'status' => $record->status]);
    }

    // ── STUDENT REPORT ────────────────────────────────────────────────────────

    public function studentReportIndex()
    {
        return view('institute.attendance.student-report');
    }

    // AJAX: search students by name (in profile) or mobile
    public function studentReportSearch(Request $request)
    {
        $institute = $this->institute();
        $query     = trim($request->get('q', ''));

        if (strlen($query) < 2) {
            return response()->json(['students' => []]);
        }

        $students = User::where('institute_id', $institute->id)
            ->where('role', 'student')
            ->where(function ($q) use ($query) {
                $q->whereHas('profile', fn($pq) => $pq->where('name', 'like', "%$query%"))
                  ->orWhere('mobile', 'like', "%$query%");
            })
            ->with('profile')
            ->limit(20)
            ->get()
            ->map(function ($u) {
                $photo = $u->profile?->photo;
                return [
                    'id'     => $u->id,
                    'name'   => $u->profile?->name ?? '—',
                    'mobile' => $u->mobile ?? '',
                    'photo'  => ($photo && $photo !== 'images/user.png') ? asset($photo) : null,
                ];
            });

        return response()->json(['students' => $students]);
    }

    // AJAX: load attendance summary for a student
    public function studentReportLoad(Request $request)
    {
        $request->validate(['user_id' => 'required|integer']);

        $institute = $this->institute();
        $user      = User::with('profile')
                        ->where('id', $request->user_id)
                        ->where('institute_id', $institute->id)
                        ->firstOrFail();

        $enrollments = CourseBook::with('course', 'batch')
            ->where('user_id', $user->id)
            ->where('institute_id', $institute->id)
            ->whereIn('status', ['RUN', 'CLOSE'])
            ->get();

        $courseData = $enrollments->map(function ($enrollment) {
            $records = AttendanceStudent::where('user_id', $enrollment->user_id)
                ->where('course_id', $enrollment->course_id)
                ->whereNotNull('status')
                ->orderBy('date')
                ->get();

            $present = $records->where('status', 'P')->count();
            $absent  = $records->where('status', 'A')->count();
            $late    = $records->where('status', 'L')->count();
            $total   = $records->count();
            $pct     = $total > 0 ? round(($present / $total) * 100) : null;

            $byMonth = $records->groupBy(fn($r) => Carbon::parse($r->date)->format('Y-m'))
                ->map(fn($g, $month) => [
                    'label'   => Carbon::parse($month . '-01')->format('M Y'),
                    'present' => $g->where('status', 'P')->count(),
                    'absent'  => $g->where('status', 'A')->count(),
                    'late'    => $g->where('status', 'L')->count(),
                    'total'   => $g->count(),
                ])
                ->values();

            return [
                'course_name'   => $enrollment->course?->name ?? '—',
                'course_id'     => $enrollment->course_id,
                'batch_id'      => $enrollment->batch_id,
                'batch_name'    => $enrollment->batch?->name ?? '—',
                'enrollment_no' => $enrollment->enrollment_no ?? '—',
                'start_date'    => $enrollment->start_date ? Carbon::parse($enrollment->start_date)->format('d M Y') : null,
                'status'        => $enrollment->status,
                'present'       => $present,
                'absent'        => $absent,
                'late'          => $late,
                'total'         => $total,
                'pct'           => $pct,
                'by_month'      => $byMonth,
            ];
        });

        $photo = $user->profile?->photo;
        return response()->json([
            'success' => true,
            'name'    => $user->profile?->name ?? '—',
            'mobile'  => $user->mobile ?? '',
            'photo'   => ($photo && $photo !== 'images/user.png') ? asset($photo) : null,
            'courses' => $courseData,
        ]);
    }

    // ── REGISTER: student list (no-scroll view) ───────────────────────────────

    public function registerStudentList(Request $request)
    {
        $request->validate([
            'view_by'   => 'required|in:course,batch',
            'course_id' => 'nullable|integer',
            'batch_id'  => 'nullable|integer',
        ]);

        $institute = $this->institute();

        $q = CourseBook::with('student.profile')
            ->where('institute_id', $institute->id)
            ->where('status', 'RUN');

        if ($request->view_by === 'course' && $request->course_id) {
            $q->where('course_id', $request->course_id);
        } elseif ($request->view_by === 'batch' && $request->batch_id) {
            $q->where('batch_id', $request->batch_id);
        } else {
            return response()->json(['success' => false, 'message' => 'Please select a course or batch.']);
        }

        $enrollments = $q->get()->unique('user_id');

        if ($enrollments->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'No active students found for this selection.']);
        }

        $userIds = $enrollments->pluck('user_id');

        $attQ = AttendanceStudent::where('institute_id', $institute->id)
            ->whereIn('user_id', $userIds)
            ->whereNotNull('status');

        if ($request->view_by === 'course' && $request->course_id) {
            $attQ->where('course_id', $request->course_id);
        } else {
            $attQ->where('batch_id', $request->batch_id);
        }

        $rawAtt = $attQ->get()->groupBy('user_id');

        $students = $enrollments->map(function ($enrollment) use ($rawAtt) {
            $userId  = $enrollment->user_id;
            $records = $rawAtt->get($userId, collect());

            $present = $records->where('status', 'P')->count();
            $absent  = $records->where('status', 'A')->count();
            $late    = $records->where('status', 'L')->count();
            $total   = $records->count();
            $pct     = $total > 0 ? round(($present / $total) * 100) : null;

            $photo = $enrollment->student?->profile?->photo;

            return [
                'user_id'       => $userId,
                'name'          => $enrollment->student?->name ?? '—',
                'photo'         => ($photo && $photo !== 'images/user.png') ? asset($photo) : null,
                'enrollment_no' => $enrollment->enrollment_no ?? '—',
                'course_id'     => $enrollment->course_id,
                'batch_id'      => $enrollment->batch_id,
                'present'       => $present,
                'absent'        => $absent,
                'late'          => $late,
                'total'         => $total,
                'pct'           => $pct,
            ];
        })->values();

        $courseName = null;
        if ($request->view_by === 'course' && $request->course_id) {
            $courseName = CourseDetail::find($request->course_id)?->name;
        }

        return response()->json(['success' => true, 'students' => $students, 'course_name' => $courseName]);
    }

    // AJAX: all months data for a student (used in modal)
    public function studentMonths(Request $request)
    {
        $request->validate([
            'user_id'   => 'required|integer',
            'course_id' => 'nullable|integer',
            'batch_id'  => 'nullable|integer',
        ]);

        $institute = $this->institute();

        $enrollQ = CourseBook::where('user_id', $request->user_id)
            ->where('institute_id', $institute->id);
        if ($request->course_id) $enrollQ->where('course_id', $request->course_id);
        if ($request->batch_id)  $enrollQ->where('batch_id',  $request->batch_id);

        $enrollment = $enrollQ->first();
        if (!$enrollment) {
            return response()->json(['success' => false, 'message' => 'Enrollment not found.']);
        }

        $startMonth = Carbon::parse($enrollment->start_date ?? $enrollment->created_at)->startOfMonth();
        $endMonth   = Carbon::now()->startOfMonth();

        // All attendance for this student in this course
        $allAtt = AttendanceStudent::where('user_id', $request->user_id)
            ->where('institute_id', $institute->id)
            ->where('course_id', $enrollment->course_id)
            ->orderBy('date')
            ->get();

        $months  = [];
        $current = $startMonth->copy();

        while ($current->lte($endMonth)) {
            $monthStart = $current->copy()->startOfMonth()->toDateString();
            $monthEnd   = $current->copy()->endOfMonth()->toDateString();
            $daysCount  = $current->daysInMonth;
            $y          = $current->year;
            $m          = $current->month;

            $monthRecs = $allAtt->filter(fn($r) => $r->date >= $monthStart && $r->date <= $monthEnd)
                                ->keyBy(fn($r) => (int) Carbon::parse($r->date)->format('j'));

            $dayData = [];
            $present = $absent = $late = 0;

            for ($d = 1; $d <= $daysCount; $d++) {
                $rec  = $monthRecs->get($d);
                $st   = $rec?->status;
                $dayData[] = [
                    'day'    => $d,
                    'att_id' => $rec?->id,
                    'status' => $st,
                    'date'   => $current->format('Y-m') . '-' . str_pad($d, 2, '0', STR_PAD_LEFT),
                ];
                if ($st === 'P') $present++;
                elseif ($st === 'A') $absent++;
                elseif ($st === 'L') $late++;
            }

            $marked   = $present + $absent + $late;
            $firstDow = Carbon::createFromDate($y, $m, 1)->dayOfWeek; // 0=Sun

            $months[] = [
                'key'       => $current->format('Y-m'),
                'label'     => $current->format('M Y'),
                'days'      => $dayData,
                'days_count'=> $daysCount,
                'first_dow' => $firstDow,
                'present'   => $present,
                'absent'    => $absent,
                'late'      => $late,
                'marked'    => $marked,
                'pct'       => $marked > 0 ? round(($present / $marked) * 100) : null,
            ];

            $current->addMonth();
        }

        return response()->json([
            'success'       => true,
            'months'        => $months,
            'enrollment_no' => $enrollment->enrollment_no,
            'course_id'     => $enrollment->course_id,
            'batch_id'      => $enrollment->batch_id,
        ]);
    }

    // Export: all students summary for a course/batch
    public function exportAll(Request $request)
    {
        $request->validate([
            'view_by'   => 'required|in:course,batch',
            'course_id' => 'nullable|integer',
            'batch_id'  => 'nullable|integer',
        ]);

        $institute = $this->institute();

        $q = CourseBook::with('student')
            ->where('institute_id', $institute->id)
            ->where('status', 'RUN');

        if ($request->view_by === 'course' && $request->course_id) {
            $q->where('course_id', $request->course_id);
        } elseif ($request->view_by === 'batch' && $request->batch_id) {
            $q->where('batch_id', $request->batch_id);
        }

        $enrollments = $q->get()->unique('user_id');
        $userIds = $enrollments->pluck('user_id');

        $attQ = AttendanceStudent::where('institute_id', $institute->id)
            ->whereIn('user_id', $userIds)
            ->whereNotNull('status');
        if ($request->view_by === 'course' && $request->course_id) {
            $attQ->where('course_id', $request->course_id);
        } else {
            $attQ->where('batch_id', $request->batch_id);
        }

        $rawAtt     = $attQ->get()->groupBy('user_id');
        $courseName = $request->course_id ? (CourseDetail::find($request->course_id)?->name ?? 'Course') : 'Batch';
        $label      = $request->view_by === 'course' ? $courseName : (BatchDetail::find($request->batch_id)?->name ?? 'Batch');

        $filename = 'attendance-all-students-' . now()->format('Ymd') . '.csv';

        return response()->streamDownload(function () use ($enrollments, $rawAtt, $institute, $label) {
            $f = fopen('php://output', 'w');
            fputs($f, "\xEF\xBB\xBF"); // UTF-8 BOM

            fputcsv($f, ['ATTENDANCE SUMMARY REPORT']);
            fputcsv($f, ['Institute', $institute->name ?? '']);
            fputcsv($f, ['Course / Batch', $label]);
            fputcsv($f, ['Generated', now()->format('d-M-Y h:i A')]);
            fputcsv($f, []);
            fputcsv($f, ['STUDENT', 'ENROLLMENT NO', 'PRESENT', 'ABSENT', 'LATE', 'TOTAL MARKED', 'ATTENDANCE %']);

            foreach ($enrollments as $enrollment) {
                $records = $rawAtt->get($enrollment->user_id, collect());
                $p = $records->where('status','P')->count();
                $a = $records->where('status','A')->count();
                $l = $records->where('status','L')->count();
                $t = $records->count();
                $pct = $t > 0 ? round(($p/$t)*100).'%' : 'N/A';
                fputcsv($f, [
                    $enrollment->student?->name ?? '—',
                    $enrollment->enrollment_no ?? '—',
                    $p, $a, $l, $t, $pct,
                ]);
            }
            fclose($f);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    // Export: one student, all months
    public function exportStudent(Request $request)
    {
        $request->validate([
            'user_id'   => 'required|integer',
            'course_id' => 'nullable|integer',
        ]);

        $institute  = $this->institute();
        $user       = User::where('id', $request->user_id)
                          ->where('institute_id', $institute->id)
                          ->with('profile')
                          ->firstOrFail();

        $enrollment = CourseBook::where('user_id', $request->user_id)
            ->where('institute_id', $institute->id)
            ->when($request->course_id, fn($q) => $q->where('course_id', $request->course_id))
            ->with(['course', 'batch'])
            ->first();

        $allAtt = AttendanceStudent::where('user_id', $request->user_id)
            ->where('institute_id', $institute->id)
            ->when($request->course_id, fn($q) => $q->where('course_id', $request->course_id))
            ->whereNotNull('status')
            ->orderBy('date')
            ->get();

        $studentName = $user->profile?->name ?? '—';
        $courseName  = $enrollment?->course?->name ?? ($request->course_id ? (CourseDetail::find($request->course_id)?->name ?? '—') : '—');
        $batchName   = $enrollment?->batch?->name ?? '—';
        $enrollNo    = $enrollment?->enrollment_no ?? '—';

        // Build month groups from enrollment start to today
        $startMonth = Carbon::parse($enrollment?->start_date ?? $enrollment?->created_at ?? now())->startOfMonth();
        $endMonth   = Carbon::now()->startOfMonth();

        $statusLabel = ['P' => 'Present', 'A' => 'Absent', 'L' => 'Late'];

        $totalP = 0; $totalA = 0; $totalL = 0; $totalT = 0;

        // Group attendance records by date for quick lookup
        $attByDate = $allAtt->keyBy('date');

        // Build month groups
        $monthGroups = [];
        $cur = $startMonth->copy();
        while ($cur->lte($endMonth)) {
            $ms   = $cur->copy()->startOfMonth()->toDateString();
            $me   = $cur->copy()->endOfMonth()->toDateString();
            $recs = $allAtt->filter(fn($r) => $r->date >= $ms && $r->date <= $me)->values();
            if ($recs->count() > 0) {
                $monthGroups[] = [
                    'label' => $cur->format('F Y'),
                    'recs'  => $recs,
                ];
            }
            $cur->addMonth();
        }

        $safeStudentName = str_replace([' ', '/'], '-', strtolower($studentName));
        $filename = 'attendance-' . $safeStudentName . '-' . now()->format('Ymd') . '.csv';

        return response()->streamDownload(function () use (
            $user, $studentName, $enrollment, $courseName, $batchName, $enrollNo,
            $monthGroups, $statusLabel, $institute, &$totalP, &$totalA, &$totalL, &$totalT
        ) {
            $f = fopen('php://output', 'w');
            fputs($f, "\xEF\xBB\xBF");

            // ── Header info ──────────────────────────────────────────────
            fputcsv($f, ['STUDENT ATTENDANCE REPORT — DAY WISE']);
            fputcsv($f, []);
            fputcsv($f, ['Institute',     $institute->name ?? '']);
            fputcsv($f, ['Course',        $courseName]);
            fputcsv($f, ['Batch',         $batchName]);
            fputcsv($f, ['Student Name',  $studentName]);
            fputcsv($f, ['Mobile',        $user->mobile ?? '']);
            fputcsv($f, ['Enrollment No', $enrollNo]);
            fputcsv($f, ['Generated On',  now()->format('d-M-Y h:i A')]);
            fputcsv($f, []);

            // ── Month-wise day records ────────────────────────────────────
            foreach ($monthGroups as $mg) {
                // Month header
                fputcsv($f, ['--- ' . $mg['label'] . ' ---']);
                fputcsv($f, ['SR.', 'DATE', 'DAY', 'STATUS', 'IN TIME', 'OUT TIME']);

                $sr = 1; $mp = 0; $ma = 0; $ml = 0;
                foreach ($mg['recs'] as $rec) {
                    $date   = Carbon::parse($rec->date);
                    $status = $statusLabel[$rec->status] ?? $rec->status;
                    $inT    = $rec->in_time  ? Carbon::parse($rec->in_time)->format('h:i A')  : '—';
                    $outT   = $rec->out_time ? Carbon::parse($rec->out_time)->format('h:i A') : '—';

                    fputcsv($f, [
                        $sr++,
                        $date->format('d-M-Y'),
                        $date->format('l'),
                        $status,
                        $inT,
                        $outT,
                    ]);

                    if ($rec->status === 'P') $mp++;
                    elseif ($rec->status === 'A') $ma++;
                    elseif ($rec->status === 'L') $ml++;
                }

                $mt   = $mp + $ma + $ml;
                $mpct = $mt > 0 ? round(($mp / $mt) * 100) . '%' : 'N/A';

                // Month summary row
                fputcsv($f, []);
                fputcsv($f, [
                    $mg['label'] . ' Summary',
                    'Present: ' . $mp,
                    'Absent: ' . $ma,
                    'Late: ' . $ml,
                    'Total: ' . $mt,
                    'Attendance: ' . $mpct,
                ]);
                fputcsv($f, []);

                $totalP += $mp; $totalA += $ma; $totalL += $ml; $totalT += $mt;
            }

            // ── Overall summary ───────────────────────────────────────────
            $overallPct = $totalT > 0 ? round(($totalP / $totalT) * 100) . '%' : 'N/A';
            fputcsv($f, ['═══════════════════════ OVERALL SUMMARY ═══════════════════════']);
            fputcsv($f, ['TOTAL PRESENT', 'TOTAL ABSENT', 'TOTAL LATE', 'TOTAL MARKED', 'OVERALL ATTENDANCE']);
            fputcsv($f, [$totalP, $totalA, $totalL, $totalT, $overallPct]);

            fclose($f);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    // Export: one student, one month (day-by-day)
    public function exportMonthStudent(Request $request)
    {
        $request->validate([
            'user_id'   => 'required|integer',
            'course_id' => 'nullable|integer',
            'month'     => 'required|date_format:Y-m',
        ]);

        $institute  = $this->institute();
        $user       = User::where('id', $request->user_id)
                          ->where('institute_id', $institute->id)
                          ->with('profile')
                          ->firstOrFail();

        $enrollment = CourseBook::where('user_id', $request->user_id)
            ->where('institute_id', $institute->id)
            ->when($request->course_id, fn($q) => $q->where('course_id', $request->course_id))
            ->with(['course', 'batch'])
            ->first();

        $month      = Carbon::parse($request->month . '-01');
        $start      = $month->copy()->startOfMonth()->toDateString();
        $end        = $month->copy()->endOfMonth()->toDateString();
        $days       = $month->daysInMonth;
        $studentName = $user->profile?->name ?? '—';
        $courseName  = $enrollment?->course?->name ?? ($request->course_id ? (CourseDetail::find($request->course_id)?->name ?? '—') : '—');
        $batchName   = $enrollment?->batch?->name ?? '—';
        $enrollNo    = $enrollment?->enrollment_no ?? '—';

        $recs = AttendanceStudent::where('user_id', $request->user_id)
            ->where('institute_id', $institute->id)
            ->when($request->course_id, fn($q) => $q->where('course_id', $request->course_id))
            ->whereBetween('date', [$start, $end])
            ->whereNotNull('status')
            ->get()
            ->keyBy(fn($r) => (int) Carbon::parse($r->date)->format('j'));

        $statusLabels = ['P' => 'Present', 'A' => 'Absent', 'L' => 'Late'];

        $safeStudentName = str_replace([' ', '/'], '-', strtolower($studentName));
        $filename = 'attendance-' . $safeStudentName . '-' . $request->month . '.csv';

        return response()->streamDownload(function () use (
            $user, $studentName, $enrollment, $courseName, $batchName, $enrollNo,
            $month, $days, $recs, $statusLabels, $institute
        ) {
            $f = fopen('php://output', 'w');
            fputs($f, "\xEF\xBB\xBF");

            // ── Header info ──────────────────────────────────────────────
            fputcsv($f, ['MONTHLY ATTENDANCE REPORT — ' . strtoupper($month->format('F Y'))]);
            fputcsv($f, []);
            fputcsv($f, ['Institute',     $institute->name ?? '']);
            fputcsv($f, ['Course',        $courseName]);
            fputcsv($f, ['Batch',         $batchName]);
            fputcsv($f, ['Student Name',  $studentName]);
            fputcsv($f, ['Mobile',        $user->mobile ?? '']);
            fputcsv($f, ['Enrollment No', $enrollNo]);
            fputcsv($f, ['Month',         $month->format('F Y')]);
            fputcsv($f, ['Generated On',  now()->format('d-M-Y h:i A')]);
            fputcsv($f, []);

            // ── Day-wise rows ─────────────────────────────────────────────
            fputcsv($f, ['SR.', 'DATE', 'DAY', 'STATUS', 'IN TIME', 'OUT TIME']);

            $y  = $month->year;
            $mo = $month->month;
            $present = $absent = $late = $notMarked = 0;
            $sr = 1;

            for ($d = 1; $d <= $days; $d++) {
                $rec  = $recs->get($d);
                $st   = $rec?->status;
                $date = Carbon::createFromDate($y, $mo, $d);
                $inT  = $rec?->in_time  ? Carbon::parse($rec->in_time)->format('h:i A')  : '—';
                $outT = $rec?->out_time ? Carbon::parse($rec->out_time)->format('h:i A') : '—';

                if ($st === 'P')      $present++;
                elseif ($st === 'A') $absent++;
                elseif ($st === 'L') $late++;
                else                 $notMarked++;

                // Only include days that have a record OR show all days
                fputcsv($f, [
                    $sr++,
                    $date->format('d-M-Y'),
                    $date->format('l'),
                    isset($statusLabels[$st]) ? $statusLabels[$st] : 'Not Marked',
                    $st ? $inT  : '—',
                    $st ? $outT : '—',
                ]);
            }

            // ── Summary ───────────────────────────────────────────────────
            $marked = $present + $absent + $late;
            fputcsv($f, []);
            fputcsv($f, ['═══════════════════════ SUMMARY ═══════════════════════']);
            fputcsv($f, ['PRESENT', 'ABSENT', 'LATE', 'NOT MARKED', 'TOTAL DAYS', 'ATTENDANCE %']);
            fputcsv($f, [
                $present, $absent, $late, $notMarked, $days,
                $marked > 0 ? round(($present / $marked) * 100) . '%' : 'N/A',
            ]);
            fclose($f);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    // ── STAFF ATTENDANCE ──────────────────────────────────────────────────────

    public function staffIndex()
    {
        $institute = $this->institute();
        $date      = request('date', now()->toDateString());
        $staff     = User::where('institute_id', $institute->id)->where('role', 'staff')->get();
        $existing  = AttendanceStaff::where('institute_id', $institute->id)
                        ->where('date', $date)
                        ->pluck('status', 'user_id')
                        ->toArray();

        return view('institute.attendance.staff', compact('staff', 'date', 'existing'));
    }

    public function markStaff(Request $request)
    {
        $institute = $this->institute();
        $data = $request->validate([
            'date'         => 'required|date',
            'attendance'   => 'required|array',
            'attendance.*' => 'in:P,A',
        ]);

        foreach ($data['attendance'] as $userId => $status) {
            AttendanceStaff::updateOrCreate(
                ['institute_id' => $institute->id, 'user_id' => $userId, 'date' => $data['date']],
                ['status' => $status, 'created_by' => Auth::guard('institute')->id()]
            );
        }

        return back()->with('success', 'Staff attendance saved for ' . $data['date'] . '.');
    }
}
