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

        $districtsByState = \Illuminate\Support\Facades\Schema::hasTable('districts')
            ? \App\Models\District::query()
                ->select('districts.name as district_name', 'states.name as state_name')
                ->join('states', 'states.id', '=', 'districts.state_id')
                ->orderBy('states.name')->orderBy('districts.name')
                ->get()
                ->groupBy('state_name')
                ->map(fn($rows) => $rows->pluck('district_name')->values())
                ->toArray()
            : [];

        return view('institute.students.edit', compact('student', 'states', 'districtsByState'));
    }

    public function update(Request $request, User $student)
    {
        $this->authorizeStudent($student);
        $section = $request->input('_section', 'basic');

        $profile = $student->profile ?? UserProfile::firstOrNew([
            'user_id'      => $student->id,
            'institute_id' => $student->institute_id,
        ]);
        if (! $profile->exists) {
            $profile->photo  = 'images/user.png';
            $profile->r_date = now()->toDateString();
        }

        // ── Photo ──────────────────────────────────────────────
        if ($section === 'photo') {
            $request->validate(['photo' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048']);
            $path = $request->file('photo')->store('student-photos', 'public');
            $profile->photo = 'storage/' . $path;
            $profile->save();
            return back()->with('success_photo', 'Photo updated successfully.');
        }

        // ── Basic Info ─────────────────────────────────────────
        if ($section === 'basic') {
            $data = $request->validate([
                'name'              => 'required|string|max:100',
                'mobile'            => 'required|string|max:15|unique:users,mobile,' . $student->id,
                'email'             => 'nullable|email|max:100|unique:users,email,' . $student->id,
                'dob'               => 'nullable|date',
                'gender'            => 'nullable|in:Male,Female,Other',
                'category'          => 'nullable|string|max:20',
                'religion'          => 'nullable|string|max:50',
                'nationality'       => 'nullable|string|max:50',
                'blood_group'       => 'nullable|string|max:5',
                'aadhar_no'         => 'nullable|string|max:20',
                'pan_no'            => 'nullable|string|max:10',
                'qualification'     => 'nullable|string|max:80',
                'employment_status' => 'nullable|in:Employed,Unemployed',
                'computer_literacy' => 'nullable|in:Yes,No',
                'whatsapp_no'       => 'nullable|string|max:15',
                'alternate_mobile'  => 'nullable|string|max:15',
            ]);
            $student->update(['mobile' => $data['mobile'], 'email' => $data['email'] ?? null]);
            $profile->fill($data);
            $profile->save();
            return back()->with('success_basic', 'Basic info saved.')->withFragment('basic');
        }

        // ── Guardian ───────────────────────────────────────────
        if ($section === 'guardian') {
            $data = $request->validate([
                'father_name'        => 'nullable|string|max:100',
                'mother_name'        => 'nullable|string|max:100',
                'guardian_name'      => 'nullable|string|max:100',
                'guardian_relation'  => 'nullable|string|max:50',
                'guardian_mobile'    => 'nullable|string|max:15',
                'guardian_occupation'=> 'nullable|string|max:80',
            ]);
            $profile->fill($data);
            $profile->save();
            return back()->with('success_guardian', 'Guardian details saved.')->withFragment('guardian');
        }

        // ── Address ────────────────────────────────────────────
        if ($section === 'address') {
            $data = $request->validate([
                'address'           => 'nullable|string',
                'state'             => 'nullable|string|max:100',
                'district'          => 'nullable|string|max:60',
                'city'              => 'nullable|string|max:60',
                'pin_code'          => 'nullable|string|max:10',
                'permanent_address' => 'nullable|string',
                'permanent_state'   => 'nullable|string|max:100',
                'permanent_district'=> 'nullable|string|max:60',
                'permanent_city'    => 'nullable|string|max:60',
                'permanent_pin_code'=> 'nullable|string|max:10',
            ]);
            $profile->fill($data);
            $profile->save();
            return back()->with('success_address', 'Address saved.')->withFragment('address');
        }

        return back()->with('success', 'Profile updated.');
    }

    public function destroy(User $student)
    {
        $this->authorizeStudent($student);
        $student->delete();

        return redirect()->route('institute.students.index')->with('success', 'Student removed.');
    }

    public function closed(Request $request)
    {
        $iid    = $this->instituteId();
        $search = trim($request->get('q', ''));

        $query = CourseBook::with(['student.profile', 'course'])
            ->where('institute_id', $iid)
            ->where('status', 'CLOSE');

        if ($search) {
            $query->whereHas('student', function ($q) use ($search) {
                $q->where('user_id', 'like', "%{$search}%")
                  ->orWhere('mobile', 'like', "%{$search}%")
                  ->orWhereHas('profile', fn ($p) => $p->where('name', 'like', "%{$search}%"));
            });
        }

        $enrollments = $query->latest()->paginate(25)->withQueryString();
        return view('institute.students.closed', compact('enrollments', 'search'));
    }

    public function cancelled(Request $request)
    {
        $iid    = $this->instituteId();
        $search = trim($request->get('q', ''));

        $query = CourseBook::with(['student.profile', 'course'])
            ->where('institute_id', $iid)
            ->where('status', 'CANCEL');

        if ($search) {
            $query->whereHas('student', function ($q) use ($search) {
                $q->where('user_id', 'like', "%{$search}%")
                  ->orWhere('mobile', 'like', "%{$search}%")
                  ->orWhereHas('profile', fn ($p) => $p->where('name', 'like', "%{$search}%"));
            });
        }

        $enrollments = $query->latest()->paginate(25)->withQueryString();
        return view('institute.students.cancelled', compact('enrollments', 'search'));
    }

    public function suggest(Request $request)
    {
        $iid = $this->instituteId();
        $q   = trim($request->get('q', ''));

        if (mb_strlen($q) < 2) {
            return response()->json([]);
        }

        $students = User::where('institute_id', $iid)
            ->where('role', 'student')
            ->where(function ($query) use ($q) {
                $query->where('mobile', 'like', "%{$q}%")
                      ->orWhere('user_id', 'like', "%{$q}%")
                      ->orWhereHas('profile', fn ($p) => $p->where('name', 'like', "%{$q}%"));
            })
            ->with('profile')
            ->limit(8)
            ->get()
            ->map(fn ($s) => [
                'id'     => $s->id,
                'name'   => $s->profile?->name ?? $s->user_id,
                'mobile' => $s->mobile,
                'uid'    => $s->user_id,
            ]);

        return response()->json($students);
    }

    public function academic(Request $request)
    {
        $iid    = $this->instituteId();
        $search = trim($request->get('q', ''));
        $student = null;
        $enrollments = collect();

        if ($search) {
            $student = User::where('institute_id', $iid)
                ->where('role', 'student')
                ->where(function ($q) use ($search) {
                    $q->where('mobile', $search)
                      ->orWhere('user_id', $search)
                      ->orWhereHas('profile', fn ($p) => $p->where('name', 'like', "%{$search}%"));
                })
                ->with('profile')
                ->first();

            if ($student) {
                $enrollments = \App\Models\CourseBook::with(['course', 'batch', 'paymentPlan'])
                    ->where('user_id', $student->id)
                    ->where('institute_id', $iid)
                    ->orderByDesc('created_at')
                    ->get()
                    ->map(function ($e) {
                        $amtCol = \App\Models\FeeCollectDetail::amountColumn();
                        $paid   = (float) \App\Models\FeeCollectDetail::where('course_book_id', $e->id)
                            ->whereNull('cancelled_at')->sum($amtCol);
                        $e->setAttribute('paid_total', $paid);
                        $e->setAttribute('due_total', max($e->final_fee - $paid, 0));
                        return $e;
                    });
            }
        }

        return view('institute.students.academic', compact('search', 'student', 'enrollments'));
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
