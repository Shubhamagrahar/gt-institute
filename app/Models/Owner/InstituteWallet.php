<?php

namespace App\Models\Owner;

use Illuminate\Database\Eloquent\Model;

class InstituteWallet extends Model
{
    protected $fillable = ['institute_id', 'main_b'];
    public function institute() { return $this->belongsTo(Institute::class); }
}
