<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentTransaction extends Model
{
    protected $fillable = [
        'user_id', 'institute_id', 'description',
        'credit', 'debit', 'type',
        'date', 'c_date', 'op_bal', 'cl_bal', 'by_user_id',
    ];

    public function user() { return $this->belongsTo(User::class); }
}