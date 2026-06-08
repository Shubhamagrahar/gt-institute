<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChannelPartner extends Model
{
    use HasFactory;

    protected $fillable = [
        'institute_id',
        'name',
        'mobile',
        'email',
        'whatsapp_no',
        'alternate_mobile',
        'father_name',
        'dob',
        'gender',
        'occupation',
        'aadhar_no',
        'pan_no',
        'address',
        'state',
        'district',
        'city',
        'pin_code',
        'notes',
        'status',
    ];

    protected $casts = [
        'dob' => 'date',
    ];

    public function admissions()
    {
        return $this->hasMany(User::class)->where('role', 'student');
    }
}
