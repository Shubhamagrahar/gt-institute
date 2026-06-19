<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseBook extends Model
{
    protected $fillable = [
        'institute_id', 'franchise_id', 'session_id', 'user_id', 'course_id',
        'course_code', 'batch_id', 'channel_partner_id', 'enrollment_no', 'fee', 'final_fee',
        'book_date', 'start_date', 'complete_date', 'status', 'booking_mode',
        'profile_completed_at', 'admission_by',
    ];

    protected $casts = [
        'book_date'     => 'date',
        'start_date'    => 'date',
        'complete_date' => 'date',
        'profile_completed_at' => 'datetime',
    ];

    public function student()    { return $this->belongsTo(User::class, 'user_id'); }
    public function course()     { return $this->belongsTo(CourseDetail::class, 'course_id'); }
    public function batch()      { return $this->belongsTo(BatchDetail::class, 'batch_id'); }
    public function channelPartner() { return $this->belongsTo(ChannelPartner::class, 'channel_partner_id'); }
    public function session()    { return $this->belongsTo(InstituteSession::class, 'session_id'); }
    public function feeSnapshots() { return $this->hasMany(EnrollmentFeeSnapshot::class); }
    public function paymentPlan()  { return $this->hasOne(EnrollmentPaymentPlan::class); }
}
