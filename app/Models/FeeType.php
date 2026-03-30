<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeeType extends Model
{
    protected $fillable = [
        'institute_id', 'name', 'is_mandatory', 'is_active',
    ];

    protected $casts = [
        'is_mandatory' => 'boolean',
        'is_active'    => 'boolean',
    ];

    public function institute()
    {
        return $this->belongsTo(\App\Models\Owner\Institute::class);
    }

    public function courseFeeStructures()
    {
        return $this->hasMany(CourseFeeStructure::class);
    }
}