<?php

namespace App\Models\Owner;

use Illuminate\Database\Eloquent\Model;

class InstituteTransaction extends Model
{
    protected $fillable = [
        'institute_id','des','credit','debit','type',
        'date','c_date','op_bal','cl_bal','invoice_no','by_userid',
    ];
    public function institute() { return $this->belongsTo(Institute::class); }
}
