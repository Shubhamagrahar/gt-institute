<?php

namespace App\Http\Controllers\Institute;

use App\Http\Controllers\Controller;
use App\Models\{BatchDetail, CourseBook, CourseDetail, CourseType, Transaction, User, Wallet};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CourseController extends Controller
{
    private function institute() { return auth()->user()->institute; }

    public function index()
    {
        $courses = CourseDetail::with('courseType')
            ->where('institute_id', $this->institute()->id)->latest()->get();
        return view('institute.courses.index', compact('courses'));
    }

    public function create()
    {
        $types = CourseType::where('institute_id', $this->institute()->id)->get();
        return view('institute.courses.create', compact('types'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'            => 'required|string|max:150',
            'short_name'      => 'nullable|string|max:50',
            'course_type_id'  => 'nullable|exists:course_types,id',
            'duration_months' => 'required|integer|min:1',
            'fee'             => 'required|numeric|min:0',
            'description'     => 'nullable|string',
            'status'          => 'required|in:active,inactive',
        ]);
        $data['institute_id'] = $this->institute()->id;
        CourseDetail::create($data);
        return redirect()->route('institute.courses.index')->with('success', 'Course added.');
    }

    public function edit(CourseDetail $course)
    {
        abort_unless($course->institute_id === $this->institute()->id, 403);
        $types = CourseType::where('institute_id', $this->institute()->id)->get();
        return view('institute.courses.edit', compact('course', 'types'));
    }

    public function update(Request $request, CourseDetail $course)
    {
        abort_unless($course->institute_id === $this->institute()->id, 403);
        $data = $request->validate([
            'name'            => 'required|string|max:150',
            'short_name'      => 'nullable|string|max:50',
            'course_type_id'  => 'nullable|exists:course_types,id',
            'duration_months' => 'required|integer|min:1',
            'fee'             => 'required|numeric|min:0',
            'description'     => 'nullable|string',
            'status'          => 'required|in:active,inactive',
        ]);
        $course->update($data);
        return redirect()->route('institute.courses.index')->with('success', 'Course updated.');
    }

    public function destroy(CourseDetail $course)
    {
        abort_unless($course->institute_id === $this->institute()->id, 403);
        $course->delete();
        return redirect()->route('institute.courses.index')->with('success', 'Course deleted.');
    }

    public function enrollmentList()
    {
        $institute   = $this->institute();
        $enrollments = CourseBook::with(['student', 'course', 'batch'])
            ->where('institute_id', $institute->id)->latest()->paginate(20);
        $students    = User::where('institute_id', $institute->id)->where('role', 'student')->get();
        $courses     = CourseDetail::where('institute_id', $institute->id)->where('status', 'active')->get();
        $batches     = BatchDetail::where('institute_id', $institute->id)->where('status', 'active')->get();
        return view('institute.courses.enrollments', compact('enrollments', 'students', 'courses', 'batches'));
    }

    public function enroll(Request $request, User $student)
    {
        $institute = $this->institute();
        abort_unless($student->institute_id === $institute->id, 403);

        $data = $request->validate([
            'course_id'    => 'required|exists:course_details,id',
            'batch_id'     => 'nullable|exists:batch_details,id',
            'fee'          => 'required|numeric|min:0',
            'start_date'   => 'nullable|date',
            'book_date'    => 'required|date',
            'discount_type'=> 'nullable|in:NONE,PERCENT,FLAT',
            'discount_val' => 'nullable|numeric|min:0',
        ]);

        $fee = (float)$data['fee'];
        $discountAmt = 0;
        if (($data['discount_type'] ?? 'NONE') === 'PERCENT') {
            $discountAmt = round($fee * (float)$data['discount_val'] / 100, 2);
        } elseif (($data['discount_type'] ?? 'NONE') === 'FLAT') {
            $discountAmt = (float)$data['discount_val'];
        }
        $finalFee = $fee - $discountAmt;

        DB::transaction(function () use ($data, $student, $institute, $finalFee, $discountAmt) {
            $enrollment = CourseBook::create([
                'institute_id' => $institute->id,
                'user_id'      => $student->id,
                'course_id'    => $data['course_id'],
                'batch_id'     => $data['batch_id'] ?? null,
                'fee'          => $finalFee,
                'book_date'    => $data['book_date'],
                'start_date'   => $data['start_date'] ?? null,
                'status'       => $data['start_date'] ? 'RUN' : 'OPEN',
            ]);

            // Debit from student wallet
            $wallet = Wallet::where('user_id', $student->id)->lockForUpdate()->first();
            if ($wallet) {
                $opBal = (float)$wallet->main_b;
                $clBal = $opBal - $finalFee;
                Transaction::create([
                    'user_id'      => $student->id,
                    'institute_id' => $institute->id,
                    'des'          => 'Course Enrollment: ' . CourseDetail::find($data['course_id'])->name,
                    'credit'       => 0,
                    'debit'        => $finalFee,
                    'type'         => 1,
                    'date'         => $data['book_date'],
                    'c_date'       => now(),
                    'op_bal'       => $opBal,
                    'cl_bal'       => $clBal,
                    'by_userid'    => auth()->id(),
                ]);
                $wallet->update(['main_b' => $clBal]);

                // Discount transaction
                if ($discountAmt > 0) {
                    $opBal2 = $clBal;
                    $clBal2 = $opBal2 + $discountAmt;
                    Transaction::create([
                        'user_id'      => $student->id,
                        'institute_id' => $institute->id,
                        'des'          => 'Enrollment Discount Applied',
                        'credit'       => $discountAmt,
                        'debit'        => 0,
                        'type'         => 2,
                        'date'         => $data['book_date'],
                        'c_date'       => now(),
                        'op_bal'       => $opBal2,
                        'cl_bal'       => $clBal2,
                        'by_userid'    => auth()->id(),
                    ]);
                    $wallet->update(['main_b' => $clBal2]);
                }
            }
        });

        return back()->with('success', 'Student enrolled successfully.');
    }
}
