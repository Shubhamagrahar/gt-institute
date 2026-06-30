<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InstituteCertificateCounter extends Model
{
    protected $fillable = ['institute_id', 'last_certificate_no'];

    public function institute() { return $this->belongsTo(\App\Models\Owner\Institute::class); }
}
