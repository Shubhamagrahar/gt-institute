<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FranchiseInstituteTransaction extends Model
{
    protected $fillable = [
        'franchise_id', 'institute_id', 'txn_no', 'type', 'description',
        'credit', 'debit', 'op_bal', 'cl_bal',
        'payment_mode', 'utr', 'invoice_no',
        'date', 'c_date', 'by_userid',
    ];

    protected $casts = [
        'credit' => 'float',
        'debit'  => 'float',
        'op_bal' => 'float',
        'cl_bal' => 'float',
    ];

    public function franchise()
    {
        return $this->belongsTo(Franchise::class);
    }

    public function payment()
    {
        return $this->belongsTo(FranchisePayDetail::class, 'invoice_no', 'invoice_no');
    }
}
