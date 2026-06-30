<?php

namespace App\Http\Controllers\Institute;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\CertificateSubject;
use App\Models\CourseBook;
use App\Models\CourseSubject;
use App\Models\InstituteCertificateCounter;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CertificateController extends Controller
{
    private function instituteId(): int
    {
        return Auth::guard('institute')->user()->institute_id;
    }

    private function gradeFor(float $percent): string
    {
        return match (true) {
            $percent >= 90 => 'A+',
            $percent >= 75 => 'A',
            $percent >= 60 => 'B',
            $percent >= 45 => 'C',
            $percent >= 35 => 'D',
            default        => 'F',
        };
    }

    public function index()
    {
        $iid = $this->instituteId();

        $generatedThisMonth = Certificate::where('institute_id', $iid)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        $totalGenerated = Certificate::where('institute_id', $iid)->count();
        $pendingCount = 3; // franchise request flow — still preview, not wired this round

        return view('institute.certificates.index', compact('generatedThisMonth', 'totalGenerated', 'pendingCount'));
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
        $pendingCount = 3; // placeholder — franchise request flow not built yet
        return view('institute.certificates.requests', compact('status', 'q', 'pendingCount'));
    }

    public function history(Request $request)
    {
        $iid = $this->instituteId();

        $certificates = Certificate::where('institute_id', $iid)
            ->when($request->filled('source'), fn($q) => $q->where('source', $request->get('source')))
            ->when($request->filled('from'), fn($q) => $q->whereDate('created_at', '>=', $request->get('from')))
            ->when($request->filled('to'), fn($q) => $q->whereDate('created_at', '<=', $request->get('to')))
            ->when($request->filled('q'), function ($q) use ($request) {
                $term = $request->get('q');
                $q->where(function ($w) use ($term) {
                    $w->where('student_name', 'like', "%{$term}%")
                      ->orWhere('certificate_no', 'like', "%{$term}%")
                      ->orWhere('enrollment_no', 'like', "%{$term}%");
                });
            })
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        return view('institute.certificates.history', compact('certificates'));
    }

    // AJAX: return all enrollments for a student (any status), with bound subjects + duplicate flag
    public function enrollments(User $user)
    {
        $iid = $this->instituteId();
        if ($user->institute_id !== $iid) abort(403);

        $enrollments = CourseBook::where('user_id', $user->id)
            ->where('institute_id', $iid)
            ->with(['course', 'session'])
            ->orderByDesc('id')
            ->get()
            ->map(function ($e) use ($iid) {
                $subjects = CourseSubject::where('institute_id', $iid)
                    ->where('course_id', $e->course_id)
                    ->with('subject')
                    ->get()
                    ->map(fn($cs) => [
                        'subject_id' => $cs->subject_id,
                        'code'       => $cs->subject?->subject_code,
                        'name'       => $cs->subject?->name,
                        'max'        => (float) $cs->max_marks,
                    ])->values();

                return [
                    'id'                => $e->id,
                    'status'            => $e->status,
                    'enrollment_no'     => $e->enrollment_no,
                    'course_name'       => $e->course?->name ?? '—',
                    'duration'          => $e->course?->duration,
                    'academic_session'  => $e->session?->name,
                    'start_date'        => $e->start_date,
                    'end_date'          => $e->complete_date,
                    'already_certified' => Certificate::where('course_book_id', $e->id)->exists(),
                    'subjects'          => $subjects,
                ];
            });

        $profile = $user->profile;

        return response()->json([
            'student' => [
                'id'          => $user->id,
                'uid'         => $user->user_id,
                'name'        => $profile?->name ?? $user->user_id,
                'mobile'      => $user->mobile,
                'father_name' => $profile?->father_name,
                'mother_name' => $profile?->mother_name,
                'dob'         => optional($profile?->dob)->format('Y-m-d'),
                'photo'       => $profile?->photo,
            ],
            'enrollments' => $enrollments,
        ]);
    }

    public function store(Request $request)
    {
        $iid = $this->instituteId();
        $isWalkIn = (bool) $request->boolean('is_walk_in');

        $data = $request->validate([
            'student_name'     => 'required|string|max:150',
            'father_name'      => 'nullable|string|max:150',
            'mother_name'      => 'nullable|string|max:150',
            'mobile'           => 'nullable|string|max:20',
            'dob'              => 'nullable|date',
            'enrollment_no'    => 'nullable|string|max:50',
            'course_name'      => 'required|string|max:150',
            'duration'         => 'nullable|string|max:50',
            'start_date'       => 'nullable|date',
            'end_date'         => 'nullable|date',
            'academic_session' => 'nullable|string|max:30',
            'course_book_id'   => 'nullable|integer',
            'subjects'         => 'nullable|array',
            'subjects.*.name'  => 'nullable|string|max:150',
            'subjects.*.code'  => 'nullable|string|max:20',
            'subjects.*.max'   => 'nullable|numeric|min:0',
            'subjects.*.obtained' => 'nullable|numeric|min:0',
        ]);

        $courseBook = null;
        if (!$isWalkIn) {
            if (empty($data['course_book_id'])) {
                return back()->withErrors(['course_book_id' => 'Please select an enrollment.'])->withInput();
            }
            $courseBook = CourseBook::where('id', $data['course_book_id'])
                ->where('institute_id', $iid)
                ->first();
            if (!$courseBook) {
                return back()->withErrors(['course_book_id' => 'Invalid enrollment selected.'])->withInput();
            }
            if (Certificate::where('course_book_id', $courseBook->id)->exists()) {
                return back()->withErrors(['course_book_id' => 'Is course ka certificate pehle se generate ho chuka hai.'])->withInput();
            }
        }

        $subjectRows = collect($data['subjects'] ?? [])
            ->filter(fn($s) => !empty($s['name']))
            ->values();

        $totalMax = (float) $subjectRows->sum(fn($s) => (float) ($s['max'] ?? 0));
        $totalObtained = (float) $subjectRows->sum(fn($s) => (float) ($s['obtained'] ?? 0));
        $percentage = $totalMax > 0 ? round(($totalObtained / $totalMax) * 100, 2) : 0;
        $overallGrade = $subjectRows->isNotEmpty() ? $this->gradeFor($percentage) : null;
        $result = $subjectRows->isNotEmpty() ? ($percentage < 35 ? 'FAIL' : 'PASS') : null;

        $institute = Auth::guard('institute')->user()->institute;

        $certificate = DB::transaction(function () use ($iid, $institute, $isWalkIn, $courseBook, $data, $subjectRows, $totalMax, $totalObtained, $percentage, $overallGrade, $result) {
            $counter = InstituteCertificateCounter::where('institute_id', $iid)->lockForUpdate()->first();
            if (!$counter) {
                InstituteCertificateCounter::create(['institute_id' => $iid, 'last_certificate_no' => 0]);
                $counter = InstituteCertificateCounter::where('institute_id', $iid)->lockForUpdate()->firstOrFail();
            }
            $counter->last_certificate_no++;
            $counter->save();

            $certNo = ($institute->unique_id ?? 'INST') . '/CERT/' . str_pad((string) $counter->last_certificate_no, 4, '0', STR_PAD_LEFT);

            $certificate = Certificate::create([
                'institute_id'    => $iid,
                'franchise_id'    => $courseBook->franchise_id ?? null,
                'user_id'         => $courseBook->user_id ?? null,
                'course_book_id'  => $courseBook->id ?? null,
                'generated_by'    => Auth::guard('institute')->id(),
                'certificate_no'  => $certNo,
                'source'          => $isWalkIn ? 'walkin' : 'direct',
                'enrollment_status_at_issue' => $courseBook->status ?? null,
                'student_name'    => $data['student_name'],
                'father_name'     => $data['father_name'] ?? null,
                'mother_name'     => $data['mother_name'] ?? null,
                'mobile'          => $data['mobile'] ?? null,
                'dob'             => $data['dob'] ?? null,
                'photo'           => $courseBook?->student?->profile?->photo,
                'enrollment_no'   => $data['enrollment_no'] ?? null,
                'course_name'     => $data['course_name'],
                'duration'        => $data['duration'] ?? null,
                'start_date'      => $data['start_date'] ?? null,
                'end_date'        => $data['end_date'] ?? null,
                'academic_session' => $data['academic_session'] ?? null,
                'total_max'       => $totalMax,
                'total_obtained'  => $totalObtained,
                'percentage'      => $percentage,
                'overall_grade'   => $overallGrade,
                'result'          => $result,
            ]);

            foreach ($subjectRows as $row) {
                $max = (float) ($row['max'] ?? 0);
                $obtained = (float) ($row['obtained'] ?? 0);
                $subjPercent = $max > 0 ? ($obtained / $max) * 100 : 0;

                CertificateSubject::create([
                    'certificate_id'  => $certificate->id,
                    'subject_id'      => $row['subject_id'] ?? null,
                    'subject_code'    => $row['code'] ?? null,
                    'subject_name'    => $row['name'],
                    'max_marks'       => $max,
                    'obtained_marks'  => $obtained,
                    'grade'           => $this->gradeFor($subjPercent),
                ]);
            }

            return $certificate;
        });

        return redirect()->route('institute.certificates.history')
            ->with('success', "Certificate {$certificate->certificate_no} generated successfully.");
    }

    public function reject(Request $request)
    {
        return back()->with('info', 'Reject logic — coming soon.');
    }

    public function printMarksheet(Certificate $certificate)
    {
        if ($certificate->institute_id !== $this->instituteId()) abort(403);
        $certificate->load('subjects');
        $institute = Auth::guard('institute')->user()->institute;
        return view('institute.certificates.print-marksheet', compact('certificate', 'institute'));
    }

    public function printCertificate(Certificate $certificate)
    {
        if ($certificate->institute_id !== $this->instituteId()) abort(403);
        $institute = Auth::guard('institute')->user()->institute;
        return view('institute.certificates.print-certificate', compact('certificate', 'institute'));
    }
}
