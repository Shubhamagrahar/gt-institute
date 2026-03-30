<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    protected $table = 'user_profiles';

    protected $fillable = [
        'user_id', 'name', 'photo',
        'father_name', 'mother_name', 'guardian_name',
        'guardian_relation', 'guardian_mobile', 'guardian_occupation',
        'dob', 'gender', 'category', 'religion', 'nationality',
        'whatsapp_no', 'alternate_mobile', 'aadhar_no', 'pan_no',
        'blood_group', 'employment_status', 'computer_literacy',
        'qualification', 'address', 'permanent_address',
        'state', 'district', 'pin_code',
    ];

    protected $casts = [
        'dob' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}