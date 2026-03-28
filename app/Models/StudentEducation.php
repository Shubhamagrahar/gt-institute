<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentEducation extends Model
{
    protected $table = 'student_education';

    protected $fillable = [
        'user_id', 'level', 'board_university',
        'passing_year', 'percentage', 'subjects',
    ];

    public function user() { return $this->belongsTo(User::class); }
}
