<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EnquiryFollowup extends Model
{
    protected $fillable = [
        'enquiry_id', 'notes', 'outcome', 'next_followup_date', 'created_by',
    ];

    protected $casts = [
        'next_followup_date' => 'date',
    ];

    public function enquiry()
    {
        return $this->belongsTo(Enquiry::class);
    }

    public function staff()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
