<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FranchiseTransaction extends Model
{
    protected $fillable = [
        'franchise_id',
        'institute_id',
        'txn_no',
        'description',
        'credit',
        'debit',
        'type',
        'payment_mode',
        'utr',
        'op_bal',
        'cl_bal',
        'date',
        'c_date',
        'by_userid',
    ];

    public function franchise()
    {
        return $this->belongsTo(Franchise::class);
    }
}
