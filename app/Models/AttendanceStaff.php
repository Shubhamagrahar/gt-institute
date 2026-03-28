<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceStaff extends Model
{
    protected $table = 'attendance_staffs';
    protected $fillable = [
        'institute_id', 'user_id',
        'date', 'in_time', 'out_time', 'status', 'created_by',
    ];

    public function staff() { return $this->belongsTo(User::class, 'user_id'); }
}
