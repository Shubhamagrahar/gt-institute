<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseType extends Model
{
    protected $table = 'course_types';
    protected $fillable = ['institute_id', 'name', 'status'];
}
