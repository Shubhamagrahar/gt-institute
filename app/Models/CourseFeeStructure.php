<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseFeeStructure extends Model
{
    protected $fillable = [
        'institute_id', 'course_id', 'fee_type_id',
        'fee_type_name', 'amount',
    ];

    public function course()
    {
        return $this->belongsTo(CourseDetail::class, 'course_id');
    }

    public function feeType()
    {
        return $this->belongsTo(FeeType::class);
    }
}