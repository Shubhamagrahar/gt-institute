<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceStudent extends Model
{
    protected $table = 'attendance_students';
    protected $fillable = [
        'institute_id', 'user_id', 'batch_id',
        'date', 'in_time', 'out_time', 'status', 'created_by',
    ];

    public function student() { return $this->belongsTo(User::class, 'user_id'); }
}
