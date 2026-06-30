<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CertificateSubject extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'certificate_id', 'subject_id', 'subject_code', 'subject_name', 'max_marks', 'obtained_marks', 'grade',
    ];

    public function certificate() { return $this->belongsTo(Certificate::class); }
    public function subject()     { return $this->belongsTo(Subject::class); }
}
