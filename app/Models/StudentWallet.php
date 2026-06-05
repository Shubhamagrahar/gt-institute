<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentWallet extends Model
{
    protected $fillable = ['user_id', 'institute_id', 'franchise_id', 'owner_type', 'balance'];

    public function user() { return $this->belongsTo(User::class); }
}
