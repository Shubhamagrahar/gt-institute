<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FranchisePayDetail extends Model
{
    protected $fillable = [
        'franchise_id', 'institute_id', 'invoice_no',
        'payment_mode', 'utr', 'amount', 'date', 'note',
        'collected_by', 'cancelled_at', 'cancel_reason', 'cancelled_by',
    ];

    protected $casts = [
        'amount'       => 'float',
        'cancelled_at' => 'datetime',
    ];

    public function franchise()
    {
        return $this->belongsTo(Franchise::class);
    }

    public function isCancelled(): bool
    {
        return $this->cancelled_at !== null;
    }
}
