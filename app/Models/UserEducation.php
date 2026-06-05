<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserEducation extends Model
{
    protected $table = 'user_education';

    protected $fillable = [
        'user_id', 'institute_id', 'franchise_id', 'examination',
        'institute_name', 'board_university', 'passing_year', 'division',
        'marks_percentage',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
