<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InstituteEnrollmentCounter extends Model
{
    protected $fillable = [
        'institute_id',
        'last_enrollment_no',
        'last_student_no',
    ];
}
