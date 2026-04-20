<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class CourseDetail extends Model
{
    protected $table = 'course_details';
    protected $fillable = [
        'institute_id', 'course_type_id', 'name', 'course_code',
        'course_short_name', 'image', 'duration', 'max_fee',
        'fee', 'description', 'status',
    ];

    public function institute()   { return $this->belongsTo(\App\Models\Owner\Institute::class); }
    public function courseType()  { return $this->belongsTo(CourseType::class); }
    public function enrollments() { return $this->hasMany(CourseBook::class, 'course_id'); }

    public static function hasMaxFeeColumn(): bool
    {
        return Schema::hasColumn('course_details', 'max_fee');
    }

    public function getDisplayMaxFeeAttribute()
    {
        return static::hasMaxFeeColumn() ? ($this->max_fee ?? $this->fee) : $this->fee;
    }
}
