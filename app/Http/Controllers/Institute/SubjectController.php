<?php

namespace App\Http\Controllers\Institute;

use App\Http\Controllers\Controller;
use App\Models\CourseDetail;
use App\Models\CourseSubject;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class SubjectController extends Controller
{
    private function instituteId(): int
    {
        return Auth::guard('institute')->user()->institute_id;
    }

    // Subject list
    public function index()
    {
        $subjects = Subject::where('institute_id', $this->instituteId())
            ->latest()->get();
        return view('institute.subject.index', compact('subjects'));
    }

    // Create subject form
    public function create()
    {
        return view('institute.subject.create');
    }

    // Save subject
    public function store(Request $request)
    {
        $data = $request->validate(
            [
                'subject_code' => 'nullable|string|max:20',
                'name'         => [
                    'required',
                    'string',
                    'max:150',
                    Rule::unique('subjects', 'name')->where(fn ($query) => $query->where('institute_id', $this->instituteId())),
                ],
            ],
            [
                'name.unique' => 'This subject name already exists for your institute.',
            ]
        );

        $data['institute_id'] = $this->instituteId();
        $data['status'] = 'active';

        Subject::create($data);

        return redirect()->route('institute.subjects.index')
            ->with('success', 'Subject created successfully.');
    }

    // Edit form
    public function edit(Subject $subject)
    {
        $this->authorizeSubject($subject);
        return view('institute.subject.edit', compact('subject'));
    }

    // Update subject
    public function update(Request $request, Subject $subject)
    {
        $this->authorizeSubject($subject);

        $data = $request->validate(
            [
                'subject_code' => 'nullable|string|max:20',
                'name'         => [
                    'required',
                    'string',
                    'max:150',
                    Rule::unique('subjects', 'name')
                        ->where(fn ($query) => $query->where('institute_id', $this->instituteId()))
                        ->ignore($subject->id),
                ],
                'status'       => 'required|in:active,inactive',
            ],
            [
                'name.unique' => 'This subject name already exists for your institute.',
            ]
        );

        $subject->update($data);

        return redirect()->route('institute.subjects.index')
            ->with('success', 'Subject updated.');
    }

    public function toggle(Subject $subject)
    {
        $this->authorizeSubject($subject);
        $subject->update([
            'status' => $subject->status === 'active' ? 'inactive' : 'active',
        ]);

        return back()->with('success', 'Subject status updated.');
    }

    // Delete subject
    public function destroy(Subject $subject)
    {
        $this->authorizeSubject($subject);
        $subject->delete();
        return back()->with('success', 'Subject deleted.');
    }

    // Course-Subject binding page
    public function bindIndex()
    {
        $instituteId = $this->instituteId();
        $courses  = CourseDetail::where('institute_id', $instituteId)->where('status', 'active')->get();
        $subjects = Subject::where('institute_id', $instituteId)->where('status', 'active')->get();
        $bindings = CourseSubject::with(['course', 'subject'])
            ->where('institute_id', $instituteId)
            ->latest()->get();

        $boundSubjectMap = CourseSubject::where('institute_id', $instituteId)
            ->get(['course_id', 'subject_id'])
            ->groupBy('course_id')
            ->map(fn ($rows) => $rows->pluck('subject_id')->values())
            ->toArray();

        return view('institute.subject.bind', compact('courses', 'subjects', 'bindings', 'boundSubjectMap'));
    }

    // Save course-subject binding
    public function bindStore(Request $request)
    {
        $data = $request->validate([
            'course_id'  => 'required|exists:course_details,id',
            'subject_id' => 'required|exists:subjects,id',
            'max_marks'  => 'required|integer|min:1|max:1000',
        ]);

        $data['institute_id'] = $this->instituteId();

        // Check duplicate
        $exists = CourseSubject::where('course_id', $data['course_id'])
            ->where('subject_id', $data['subject_id'])
            ->exists();

        if ($exists) {
            return back()->withErrors(['subject_id' => 'This subject is already linked to the selected course.']);
        }

        CourseSubject::create($data);

        return back()->with('success', 'Subject linked to course successfully.');
    }

    // Remove binding
    public function bindDestroy(CourseSubject $binding)
    {
        if ($binding->institute_id !== $this->instituteId()) abort(403);
        $binding->delete();
        return back()->with('success', 'Binding removed.');
    }

    private function authorizeSubject(Subject $subject): void
    {
        if ($subject->institute_id !== $this->instituteId()) abort(403);
    }
}
