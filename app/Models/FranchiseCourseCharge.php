<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FranchiseCourseCharge extends Model
{
    protected $fillable = [
        'franchise_id',
        'institute_id',
        'course_id',
        'course_name',
        'duration',
        'admission_charge',
        'certificate_charge',
    ];

    protected $casts = [
        'admission_charge'   => 'float',
        'certificate_charge' => 'float',
        'duration'           => 'integer',
    ];

    public function franchise() { return $this->belongsTo(Franchise::class); }
    public function course()    { return $this->belongsTo(CourseDetail::class, 'course_id'); }
}
