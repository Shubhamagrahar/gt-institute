<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InstituteStudentWallet extends Model
{
    protected $fillable = ['institute_id', 'balance'];

    public function institute()
    {
        return $this->belongsTo(\App\Models\Owner\Institute::class);
    }
}