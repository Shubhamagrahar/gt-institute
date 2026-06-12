<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FranchiseFeeCollection extends Model
{
    protected $fillable = [
        'franchise_id',
        'institute_id',
        'invoice_no',
        'payment_mode',
        'utr',
        'amount',
        'date',
        'note',
        'cancelled_at',
        'cancel_reason',
        'cancelled_by',
        'collected_by',
    ];

    protected $casts = [
        'cancelled_at' => 'datetime',
        'date' => 'date',
    ];

    public function franchise()
    {
        return $this->belongsTo(Franchise::class);
    }

    public function scopeActive($query)
    {
        return $query->whereNull('cancelled_at');
    }
}
