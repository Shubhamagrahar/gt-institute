<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InstituteStudentTransaction extends Model
{
    protected $fillable = [
        'institute_id', 'ref_user_id', 'description',
        'credit', 'debit', 'type',
        'date', 'c_date', 'op_bal', 'cl_bal', 'by_user_id',
    ];

    public function institute()
    {
        return $this->belongsTo(\App\Models\Owner\Institute::class);
    }
}