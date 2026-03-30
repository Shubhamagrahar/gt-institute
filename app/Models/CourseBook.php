<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseBook extends Model
{
    protected $fillable = [
        'institute_id', 'session_id', 'user_id', 'course_id',
        'batch_id', 'enrollment_no', 'final_fee',
        'start_date', 'complete_date', 'status', 'admission_by',
    ];

    protected $casts = [
        'start_date'    => 'date',
        'complete_date' => 'date',
    ];

    public function student()    { return $this->belongsTo(User::class, 'user_id'); }
    public function course()     { return $this->belongsTo(CourseDetail::class, 'course_id'); }
    public function batch()      { return $this->belongsTo(BatchDetail::class, 'batch_id'); }
    public function session()    { return $this->belongsTo(InstituteSession::class, 'session_id'); }
    public function feeSnapshots() { return $this->hasMany(EnrollmentFeeSnapshot::class); }
    public function paymentPlan()  { return $this->hasOne(EnrollmentPaymentPlan::class); }
}