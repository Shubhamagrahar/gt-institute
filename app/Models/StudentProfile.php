<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentProfile extends Model
{
    protected $fillable = [
        'user_id','institute_id','reg_no','father_name','mother_name',
        'father_mobile','dob','gender','w_mob','qualification','state',
        'pin_code','full_add','full_add_permanent','photo',
        'fee_collect_type','monthly_fee','daily_late_fee',
        'late_fee_count_after','next_fee_date','issue_date','valid_till_date','r_date',
    ];
    public function user()      { return $this->belongsTo(User::class); }
    public function institute() { return $this->belongsTo(Owner\Institute::class); }
}
