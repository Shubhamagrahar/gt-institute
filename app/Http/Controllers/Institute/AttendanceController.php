<?php

namespace App\Http\Controllers\Institute;

use App\Http\Controllers\Controller;
use App\Models\{AttendanceStudent, AttendanceStaff, BatchDetail, User};
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    private function institute() { return auth()->user()->institute; }

    public function studentIndex()
    {
        $institute = $this->institute();
        $batches   = BatchDetail::where('institute_id', $institute->id)->where('status','active')->get();
        $date      = request('date', now()->toDateString());
        $batch_id  = request('batch_id');

        $studentsQ = User::with(['studentProfile'])
            ->where('institute_id', $institute->id)->where('role','student');
        if ($batch_id) {
            $enrolled = \App\Models\CourseBook::where('batch_id', $batch_id)->pluck('user_id');
            $studentsQ->whereIn('id', $enrolled);
        }
        $students = $studentsQ->get();

        $existing = \App\Models\AttendanceStudent::where('institute_id', $institute->id)
            ->where('date', $date)->when($batch_id, fn($q) => $q->where('batch_id', $batch_id))
            ->pluck('status','user_id')->toArray();

        return view('institute.attendance.student', compact('students','batches','date','batch_id','existing'));
    }

    public function markStudent(Request $request)
    {
        $institute = $this->institute();
        $data = $request->validate([
            'date'       => 'required|date',
            'batch_id'   => 'nullable|exists:batch_details,id',
            'attendance' => 'required|array',
            'attendance.*' => 'in:P,A',
        ]);

        foreach ($data['attendance'] as $userId => $status) {
            \App\Models\AttendanceStudent::updateOrCreate(
                ['institute_id'=>$institute->id,'user_id'=>$userId,'date'=>$data['date'],'batch_id'=>$data['batch_id']??null],
                ['status'=>$status,'created_by'=>auth()->id()]
            );
        }
        return back()->with('success','Attendance marked for '.$data['date'].'.');
    }

    public function staffIndex()
    {
        $institute = $this->institute();
        $date      = request('date', now()->toDateString());
        $staff     = User::where('institute_id',$institute->id)->where('role','staff')->get();
        $existing  = \App\Models\AttendanceStaff::where('institute_id',$institute->id)
            ->where('date',$date)->pluck('status','user_id')->toArray();
        return view('institute.attendance.staff', compact('staff','date','existing'));
    }

    public function markStaff(Request $request)
    {
        $institute = $this->institute();
        $data = $request->validate([
            'date'       => 'required|date',
            'attendance' => 'required|array',
            'attendance.*' => 'in:P,A',
        ]);
        foreach ($data['attendance'] as $userId => $status) {
            \App\Models\AttendanceStaff::updateOrCreate(
                ['institute_id'=>$institute->id,'user_id'=>$userId,'date'=>$data['date']],
                ['status'=>$status,'created_by'=>auth()->id()]
            );
        }
        return back()->with('success','Staff attendance marked.');
    }
}
