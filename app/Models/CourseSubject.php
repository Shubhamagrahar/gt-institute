<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseSubject extends Model
{
    protected $fillable = [
        'institute_id', 'course_id', 'subject_id', 'max_marks',
    ];

    public function course()
    {
        return $this->belongsTo(CourseDetail::class, 'course_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
}