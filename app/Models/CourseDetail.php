<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseDetail extends Model
{
    protected $table = 'course_details';
    protected $fillable = [
        'institute_id', 'course_type_id', 'name', 'course_code',
        'course_short_name', 'image', 'duration', 'max_fee',
        'fee', 'description', 'status',
    ];

    public function institute()   { return $this->belongsTo(\App\Models\Owner\Institute::class); }
    public function courseType()  { return $this->belongsTo(CourseType::class); }
    public function enrollments() { return $this->hasMany(CourseBook::class, 'course_id'); }
}
