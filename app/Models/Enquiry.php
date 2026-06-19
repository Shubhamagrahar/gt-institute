<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Enquiry extends Model
{
    protected $fillable = [
        'institute_id', 'course_id', 'converted_to_course_book_id',
        'name', 'mobile', 'email', 'source', 'status',
        'notes', 'next_followup_date', 'lost_reason',
    ];

    protected $casts = [
        'next_followup_date' => 'date',
    ];

    public function course()
    {
        return $this->belongsTo(CourseDetail::class, 'course_id');
    }

    public function courseBook()
    {
        return $this->belongsTo(CourseBook::class, 'converted_to_course_book_id');
    }

    public function followups()
    {
        return $this->hasMany(EnquiryFollowup::class)->latest();
    }

    public function isOverdue(): bool
    {
        return $this->status === 'OPEN'
            && $this->next_followup_date
            && $this->next_followup_date->toDateString() < today()->toDateString();
    }

    public function isDueToday(): bool
    {
        return $this->status === 'OPEN'
            && $this->next_followup_date
            && $this->next_followup_date->isToday();
    }
}
