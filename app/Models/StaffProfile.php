<?php namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class StaffProfile extends Model {
    protected $fillable = ['user_id','institute_id','designation','joining_date','salary','photo'];
    public function user()      { return $this->belongsTo(User::class); }
    public function institute() { return $this->belongsTo(Owner\Institute::class); }
}
