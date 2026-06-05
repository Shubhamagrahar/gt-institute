<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    protected $table = 'user_profiles';

    protected $fillable = [
        'user_id', 'institute_id', 'franchise_id', 'name', 'photo',
        'father_name', 'mother_name', 'guardian_name',
        'guardian_relation', 'guardian_mobile', 'guardian_occupation',
        'dob', 'gender', 'category', 'religion', 'nationality',
        'whatsapp_no', 'alternate_mobile', 'aadhar_no', 'pan_no',
        'blood_group', 'employment_status', 'computer_literacy',
        'qualification', 'address', 'permanent_address',
        'state', 'district', 'city', 'pin_code',
        'permanent_state', 'permanent_district', 'permanent_city', 'permanent_pin_code',
        'reg_no', 'admission_no', 'roll_no',
        'fee_collect_type', 'monthly_fee', 'daily_late_fee',
        'late_fee_count_after', 'next_fee_date', 'issue_date',
        'valid_till_date', 'r_date',
    ];

    protected $casts = [
        'dob' => 'date',
        'next_fee_date' => 'date',
        'issue_date' => 'date',
        'valid_till_date' => 'date',
        'r_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
