<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeeCollectDetail extends Model
{
    protected $table = 'fee_collect_details';

    protected $fillable = [
        'institute_id', 'user_id', 'invoice_no',
        'payment_mode', 'utr', 'amount', 'date',
        'note', 'received_by',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}