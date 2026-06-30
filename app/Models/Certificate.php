<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    protected $fillable = [
        'institute_id', 'franchise_id', 'user_id', 'course_book_id', 'generated_by',
        'certificate_no', 'source', 'enrollment_status_at_issue',
        'student_name', 'father_name', 'mother_name', 'mobile', 'dob', 'photo',
        'enrollment_no', 'course_name', 'duration', 'start_date', 'end_date', 'academic_session',
        'total_max', 'total_obtained', 'percentage', 'overall_grade', 'result',
    ];

    protected $casts = [
        'dob'        => 'date',
        'start_date' => 'date',
        'end_date'   => 'date',
    ];

    public function institute()   { return $this->belongsTo(\App\Models\Owner\Institute::class); }
    public function franchise()   { return $this->belongsTo(Franchise::class); }
    public function student()     { return $this->belongsTo(User::class, 'user_id'); }
    public function courseBook()  { return $this->belongsTo(CourseBook::class); }
    public function generatedBy() { return $this->belongsTo(User::class, 'generated_by'); }
    public function subjects()    { return $this->hasMany(CertificateSubject::class); }
}
