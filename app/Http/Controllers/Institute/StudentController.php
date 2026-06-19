<?php

namespace App\Http\Controllers\Institute;

use App\Http\Controllers\Controller;
use App\Models\CourseBook;
use App\Models\State;
use App\Models\StudentTransaction;
use App\Models\StudentWallet;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentController extends Controller
{
    private function instituteId(): int
    {
        return Auth::guard('institute')->user()->institute_id;
    }

    public function index(Request $request)
    {
        $iid       = $this->instituteId();
        $sessionId = $request->get('session_id');
        $courseId  = $request->get('course_id');
        $batchId   = $request->get('batch_id');
        $search    = trim($request->get('q', ''));

        $sessions = \App\Models\InstituteSession::where('institute_id', $iid)
            ->orderByDesc('is_active')->orderByDesc('start_date')->get();

        $courses = \App\Models\CourseDetail::where('institute_id', $iid)
            ->where('status', 'active')
            ->orderBy('name')
            ->get(['id', 'name']);

        $batches = \App\Models\BatchDetail::where('institute_id', $iid)
            ->where('status', 'active')
            ->orderBy('name')
            ->get(['id', 'name']);

        $query = CourseBook::with(['student.profile', 'course', 'batch'])
            ->where('institute_id', $iid)
            ->where('status', 'RUN');

        if ($sessionId) $query->where('session_id', $sessionId);
        if ($courseId)  $query->where('course_id', $courseId);
        if ($batchId)   $query->where('batch_id', $batchId);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('student', function ($sq) use ($search) {
                    $sq->where('user_id', 'like', "%{$search}%")
                       ->orWhere('mobile', 'like', "%{$search}%")
                       ->orWhereHas('profile', fn ($p) => $p->where('name', 'like', "%{$search}%"));
                })->orWhere('enrollment_no', 'like', "%{$search}%");
            });
        }

        $enrollments = $query->latest()->paginate(25)->withQueryString();

        return view('institute.students.index', compact(
            'enrollments', 'sessions', 'sessionId', 'search',
            'courses', 'batches', 'courseId', 'batchId'
        ));
    }

    public function expired(Request $request)
    {
        $iid    = $this->instituteId();
        $search = trim($request->get('q', ''));

        $query = CourseBook::with(['student.profile', 'course'])
            ->where('institute_id', $iid)
            ->where('status', 'EXPIRED');

        if ($search) {
            $query->whereHas('student', function ($q) use ($search) {
                $q->where('user_id', 'like', "%{$search}%")
                  ->orWhere('mobile', 'like', "%{$search}%")
                  ->orWhereHas('profile', fn ($p) => $p->where('name', 'like', "%{$search}%"));
            });
        }

        $enrollments = $query->latest()->paginate(25)->withQueryString();

        return view('institute.students.expired', compact('enrollments', 'search'));
    }

    public function create()
    {
        return view('institute.students.create');
    }

    public function store(Request $request)
    {
        return redirect()->route('institute.enrollment.new')
            ->with('error', 'Student creation ka recommended flow ab admission module se hai. Wahi se seat booking/admission karo.');
    }

    public function show(User $student)
    {
        $this->authorizeStudent($student);

        $student->load(['profile', 'studentWallet', 'education']);
        $enrollments = CourseBook::with(['course', 'batch', 'paymentPlan'])
            ->where('user_id', $student->id)
            ->latest()
            ->get();

        return view('institute.students.show', compact('student', 'enrollments'));
    }

    public function edit(User $student)
    {
        $this->authorizeStudent($student);
        $student->load(['profile', 'education']);
        $states = State::orderBy('name')->pluck('name');

        return view('institute.students.edit', compact('student', 'states'));
    }

    public function update(Request $request, User $student)
    {
        $this->authorizeStudent($student);

        $data = $request->validate([
            'name' => 'required|string|max:100',
            'mobile' => 'required|string|max:15|unique:users,mobile,' . $student->id,
            'email' => 'nullable|email|max:100|unique:users,email,' . $student->id,
            'father_name' => 'nullable|string|max:100',
            'mother_name' => 'nullable|string|max:100',
            'guardian_name' => 'nullable|string|max:100',
            'guardian_relation' => 'nullable|string|max:50',
            'guardian_mobile' => 'nullable|string|max:15',
            'guardian_occupation' => 'nullable|string|max:80',
            'dob' => 'nullable|date',
            'gender' => 'nullable|in:Male,Female,Other',
            'category' => 'nullable|string|max:20',
            'religion' => 'nullable|string|max:50',
            'nationality' => 'nullable|string|max:50',
            'whatsapp_no' => 'nullable|string|max:15',
            'alternate_mobile' => 'nullable|string|max:15',
            'aadhar_no' => 'nullable|string|max:16',
            'pan_no' => 'nullable|string|max:10',
            'blood_group' => 'nullable|string|max:5',
            'employment_status' => 'nullable|in:Employed,Unemployed',
            'computer_literacy' => 'nullable|in:Yes,No',
            'qualification' => 'nullable|string|max:80',
            'address' => 'nullable|string',
            'permanent_address' => 'nullable|string',
            'state' => 'nullable|string|max:100|exists:states,name',
            'district' => 'nullable|string|max:60',
            'pin_code' => 'nullable|string|max:10',
        ]);

        $student->update([
            'mobile' => $data['mobile'],
            'email' => $data['email'] ?? null,
        ]);

        $profile = $student->profile ?? UserProfile::make([
            'user_id' => $student->id,
            'institute_id' => $student->institute_id,
            'photo' => 'images/user.png',
            'r_date' => now()->toDateString(),
        ]);
        $profile->fill(array_merge($data, ['name' => $data['name']]));
        $profile->save();

        return redirect()->route('institute.students.show', $student)->with('success', 'Student profile updated.');
    }

    public function destroy(User $student)
    {
        $this->authorizeStudent($student);
        $student->delete();

        return redirect()->route('institute.students.index')->with('success', 'Student removed.');
    }

    public function ledger(User $student)
    {
        $this->authorizeStudent($student);
        $transactions = StudentTransaction::where('user_id', $student->id)->orderByDesc('id')->paginate(30);
        $wallet = $student->studentWallet;

        return view('institute.students.ledger', compact('student', 'transactions', 'wallet'));
    }

    private function authorizeStudent(User $student): void
    {
        abort_unless($student->institute_id === $this->instituteId(), 403);
    }
}
