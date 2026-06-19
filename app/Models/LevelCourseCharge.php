<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LevelCourseCharge extends Model
{
    protected $fillable = [
        'institute_id',
        'franchise_level_id',
        'course_id',
        'course_name',
        'duration',
        'student_admission_charge',
        'student_certificate_charge',
        'status',
    ];

    public function level()
    {
        return $this->belongsTo(FranchiseLevel::class, 'franchise_level_id');
    }

    public function course()
    {
        return $this->belongsTo(CourseDetail::class, 'course_id');
    }
}
