<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $fillable = [
        'institute_id', 'subject_code', 'name', 'status',
    ];

    public function institute()
    {
        return $this->belongsTo(\App\Models\Owner\Institute::class);
    }

    public function courseSubjects()
    {
        return $this->hasMany(CourseSubject::class);
    }
}