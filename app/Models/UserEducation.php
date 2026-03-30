<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserEducation extends Model
{
    protected $table = 'user_education';

    protected $fillable = [
        'user_id', 'examination',
        'board_university', 'passing_year', 'marks_percentage',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}