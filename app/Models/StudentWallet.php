<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentWallet extends Model
{
    protected $fillable = ['user_id', 'institute_id', 'balance'];

    public function user() { return $this->belongsTo(User::class); }
}