<?php

namespace App\Models\Owner;

use Illuminate\Database\Eloquent\Model;

class InstitutePayCollect extends Model
{
    protected $fillable = [
        'institute_id','invoice_no','payment_mode','utr',
        'amt','date','note','status','received_by','c_date',
    ];
    public function institute() { return $this->belongsTo(Institute::class); }
}
