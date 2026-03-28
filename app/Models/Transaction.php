<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'user_id', 'institute_id', 'des', 'credit', 'debit',
        'type', 'date', 'c_date', 'op_bal', 'cl_bal', 'by_userid',
    ];

    public function user()      { return $this->belongsTo(User::class); }
    public function institute() { return $this->belongsTo(\App\Models\Owner\Institute::class); }
}
