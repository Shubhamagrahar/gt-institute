<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceStudent extends Model
{
    protected $table = 'attendance_students';

    protected $fillable = [
        'institute_id', 'user_id', 'batch_id',
        'course_id', 'session_id', 'course_book_id',
        'date', 'in_time', 'out_time', 'status', 'created_by',
    ];

    public function student()    { return $this->belongsTo(User::class, 'user_id'); }
    public function batch()      { return $this->belongsTo(BatchDetail::class, 'batch_id'); }
    public function course()     { return $this->belongsTo(CourseDetail::class, 'course_id'); }
    public function courseBook() { return $this->belongsTo(CourseBook::class, 'course_book_id'); }
}
