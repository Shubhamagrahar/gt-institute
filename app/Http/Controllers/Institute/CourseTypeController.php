<?php

namespace App\Http\Controllers\Institute;

use App\Http\Controllers\Controller;
use App\Models\CourseType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class CourseTypeController extends Controller
{
    private function institute()
    {
        return Auth::guard('institute')->user()->institute;
    }

    public function index()
    {
        $courseTypes = CourseType::where('institute_id', $this->institute()->id)
            ->latest()
            ->get();

        return view('institute.course-types.index', compact('courseTypes'));
    }

    public function store(Request $request)
    {
        $institute = $this->institute();

        $data = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('course_types', 'name')->where(fn ($query) => $query->where('institute_id', $institute->id)),
            ],
            'status' => 'required|in:active,inactive',
        ]);

        $data['institute_id'] = $institute->id;
        CourseType::create($data);

        return redirect()->route('institute.course-types.index')->with('success', 'Course type added.');
    }

    public function edit(CourseType $courseType)
    {
        abort_unless($courseType->institute_id === $this->institute()->id, 403);

        return view('institute.course-types.edit', compact('courseType'));
    }

    public function update(Request $request, CourseType $courseType)
    {
        abort_unless($courseType->institute_id === $this->institute()->id, 403);

        $data = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('course_types', 'name')
                    ->where(fn ($query) => $query->where('institute_id', $courseType->institute_id))
                    ->ignore($courseType->id),
            ],
            'status' => 'required|in:active,inactive',
        ]);

        $courseType->update($data);

        return redirect()->route('institute.course-types.index')->with('success', 'Course type updated.');
    }

    public function destroy(CourseType $courseType)
    {
        abort_unless($courseType->institute_id === $this->institute()->id, 403);
        $courseType->delete();

        return redirect()->route('institute.course-types.index')->with('success', 'Course type deleted.');
    }
}
