<?php

namespace App\Http\Controllers\Institute;

use App\Http\Controllers\Controller;
use App\Models\CourseBook;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CertificateController extends Controller
{
    private function instituteId(): int
    {
        return Auth::guard('institute')->user()->institute_id;
    }

    public function index()
    {
        return view('institute.certificates.index');
    }

    public function generate(Request $request)
    {
        return view('institute.certificates.generate');
    }

    public function walkin()
    {
        return view('institute.certificates.walk-in');
    }

    public function requests(Request $request)
    {
        $status       = $request->get('status', 'pending');
        $q            = $request->get('q', '');
        $pendingCount = 3; // placeholder
        return view('institute.certificates.requests', compact('status', 'q', 'pendingCount'));
    }

    public function history(Request $request)
    {
        return view('institute.certificates.history');
    }

    // AJAX: return CLOSE enrollments for a student (for Generate page)
    public function enrollments(User $user)
    {
        if ($user->institute_id !== $this->instituteId()) abort(403);

        $enrollments = CourseBook::where('user_id', $user->id)
            ->where('institute_id', $this->instituteId())
            ->where('status', 'CLOSE')
            ->with(['course', 'student.profile'])
            ->get()
            ->map(fn($e) => [
                'id'            => $e->id,
                'enrollment_no' => $e->enrollment_no,
                'course_name'   => $e->course?->name ?? '—',
                'start_date'    => $e->start_date,
                'end_date'      => $e->end_date ?? null,
                'father_name'   => $e->student?->profile?->father_name ?? '',
            ]);

        return response()->json($enrollments);
    }

    // Store: prototype stub
    public function store(Request $request)
    {
        return back()->with('info', 'Certificate generation backend — coming soon. UI prototype is ready.');
    }

    public function reject(Request $request)
    {
        return back()->with('info', 'Reject logic — coming soon.');
    }
}
