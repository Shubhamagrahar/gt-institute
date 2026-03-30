<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EnrollmentFeeSnapshot extends Model
{
    protected $fillable = [
        'institute_id', 'course_book_id', 'fee_type_id',
        'fee_type_name', 'original_amount',
        'discount_percent', 'discount_amount', 'final_amount',
    ];

    public function courseBook()
    {
        return $this->belongsTo(CourseBook::class);
    }
}