<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EnrollmentPaymentPlan extends Model
{
    protected $fillable = [
        'institute_id', 'course_book_id', 'payment_plan_type_id',
        'plan_type', 'monthly_amount', 'grace_days',
        'late_fee_per_day', 'next_due_date',
    ];

    protected $casts = [
        'next_due_date' => 'date',
    ];

    public function courseBook()
    {
        return $this->belongsTo(CourseBook::class);
    }
}