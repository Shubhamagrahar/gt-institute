<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentPlanType extends Model
{
    protected $fillable = [
        'institute_id', 'name', 'type',
        'grace_days', 'late_fee_per_day', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function institute()
    {
        return $this->belongsTo(\App\Models\Owner\Institute::class);
    }
}