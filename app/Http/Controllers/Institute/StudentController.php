<?php

namespace App\Http\Controllers\Institute;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\{StudentProfile, Transaction, User, Wallet};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB, Hash};

class StudentController extends Controller
{
    private function institute() { return Auth::guard('institute')->user()->institute; }

    public function index()
    {
        $students = User::with('studentProfile')
            ->where('institute_id', $this->institute()->id)
            ->where('role', 'student')
            ->latest()->paginate(20);
        return view('institute.students.index', compact('students'));
    }

    public function create()
    {
        return view('institute.students.create');
    }

    public function store(Request $request)
    {
        $institute = $this->institute();

        $data = $request->validate([
            'name'            => 'required|string|max:100',
            'mobile'          => 'required|string|max:15|unique:users,mobile',
            'email'           => 'nullable|email|max:100',
            'father_name'     => 'nullable|string|max:100',
            'mother_name'     => 'nullable|string|max:100',
            'father_mobile'   => 'nullable|string|max:15',
            'dob'             => 'nullable|date',
            'gender'          => 'nullable|in:Male,Female,Other',
            'qualification'   => 'nullable|string|max:50',
            'state'           => 'nullable|string|max:60',
            'pin_code'        => 'nullable|string|max:10',
            'full_add'        => 'nullable|string',
            'fee_collect_type'=> 'required|in:MONTHLY,PART,OTP',
            'monthly_fee'     => 'nullable|numeric|min:0',
            'r_date'          => 'required|date',
        ]);

        $plain = strtoupper(substr(str_shuffle('ABCDEFGHJKLMNPQRSTUVWXYZ0123456789'), 0, 8));

        DB::transaction(function () use ($data, $institute, $plain) {
            // Generate reg_no
            $count  = User::where('institute_id', $institute->id)->where('role', 'student')->count();
            $regNo  = $institute->short_name
                ? strtoupper($institute->short_name) . '/' . now()->year . '/' . str_pad($count + 1, 4, '0', STR_PAD_LEFT)
                : 'STU/' . now()->year . '/' . str_pad($count + 1, 4, '0', STR_PAD_LEFT);

            $user = User::create([
                'user_id'      => $regNo,
                'name'         => $data['name'],
                'mobile'       => $data['mobile'],
                'email'        => $data['email'] ?? null,
                'password'     => Hash::make($plain),
                'role'         => 'student',
                'institute_id' => $institute->id,
                'status'       => 'active',
            ]);

            StudentProfile::create([
                'user_id'          => $user->id,
                'institute_id'     => $institute->id,
                'reg_no'           => $regNo,
                'father_name'      => $data['father_name'] ?? null,
                'mother_name'      => $data['mother_name'] ?? null,
                'father_mobile'    => $data['father_mobile'] ?? null,
                'dob'              => $data['dob'] ?? null,
                'gender'           => $data['gender'] ?? null,
                'qualification'    => $data['qualification'] ?? null,
                'state'            => $data['state'] ?? null,
                'pin_code'         => $data['pin_code'] ?? null,
                'full_add'         => $data['full_add'] ?? null,
                'fee_collect_type' => $data['fee_collect_type'],
                'monthly_fee'      => $data['monthly_fee'] ?? 0,
                'r_date'           => $data['r_date'],
            ]);

            Wallet::create(['user_id' => $user->id, 'main_b' => 0.00]);
        });

        return redirect()->route('institute.students.index')->with('success', 'Student added successfully.');
    }

    public function show(User $student)
    {
        abort_unless($student->institute_id === $this->institute()->id, 403);
        $student->load('studentProfile', 'wallet');
        $enrollments = \App\Models\CourseBook::with('course', 'batch')
            ->where('user_id', $student->id)->latest()->get();
        return view('institute.students.show', compact('student', 'enrollments'));
    }

    public function edit(User $student)
    {
        abort_unless($student->institute_id === $this->institute()->id, 403);
        $student->load('studentProfile');
        return view('institute.students.edit', compact('student'));
    }

    public function update(Request $request, User $student)
    {
        abort_unless($student->institute_id === $this->institute()->id, 403);
        $data = $request->validate([
            'name'          => 'required|string|max:100',
            'father_name'   => 'nullable|string|max:100',
            'mother_name'   => 'nullable|string|max:100',
            'father_mobile' => 'nullable|string|max:15',
            'dob'           => 'nullable|date',
            'gender'        => 'nullable|in:Male,Female,Other',
            'qualification' => 'nullable|string|max:50',
            'state'         => 'nullable|string|max:60',
            'pin_code'      => 'nullable|string|max:10',
            'full_add'      => 'nullable|string',
            'fee_collect_type' => 'required|in:MONTHLY,PART,OTP',
            'monthly_fee'   => 'nullable|numeric|min:0',
        ]);

        $student->update(['name' => $data['name']]);
        $student->studentProfile?->update($data);

        return redirect()->route('institute.students.show', $student)->with('success', 'Student updated.');
    }

    public function destroy(User $student)
    {
        abort_unless($student->institute_id === $this->institute()->id, 403);
        $student->delete();
        return redirect()->route('institute.students.index')->with('success', 'Student removed.');
    }

    public function ledger(User $student)
    {
        abort_unless($student->institute_id === $this->institute()->id, 403);
        $transactions = Transaction::where('user_id', $student->id)->orderByDesc('id')->paginate(30);
        $wallet       = $student->wallet;
        return view('institute.students.ledger', compact('student', 'transactions', 'wallet'));
    }
}
