<?php namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class CourseDetail extends Model {
    protected $table = 'course_details';
    protected $fillable = ['institute_id','course_type_id','name','course_code','course_short_name','image','duration','max_fee','fee','description','status'];
    public function institute() { return $this->belongsTo(Owner\Institute::class); }
    public function courseType(){ return $this->belongsTo(CourseType::class); }
    public function enrollments(){ return $this->hasMany(CourseBook::class,'course_id'); }
}

class CourseType extends Model {
    protected $table = 'course_types';
    protected $fillable = ['institute_id','name','status'];
}

class BatchDetail extends Model {
    protected $table = 'batch_details';
    protected $fillable = ['institute_id','name','start_time','end_time','status'];
}

class CourseBook extends Model {
    protected $table = 'course_books';
    protected $fillable = ['institute_id','user_id','course_id','batch_id','fee','book_date','start_date','complete_date','status'];
    public function student() { return $this->belongsTo(User::class,'user_id'); }
    public function course()  { return $this->belongsTo(CourseDetail::class,'course_id'); }
    public function batch()   { return $this->belongsTo(BatchDetail::class,'batch_id'); }
}

class Wallet extends Model {
    protected $fillable = ['user_id','main_b'];
    public function user() { return $this->belongsTo(User::class); }
}

class Transaction extends Model {
    protected $fillable = ['user_id','institute_id','des','credit','debit','type','date','c_date','op_bal','cl_bal','by_userid'];
    public function user()      { return $this->belongsTo(User::class); }
    public function institute() { return $this->belongsTo(Owner\Institute::class); }
}

class FeeCollectDetail extends Model {
    protected $table = 'fee_collect_details';
    protected $fillable = ['user_id','institute_id','course_book_id','invoice_no','payment_mode','utr','amt','date','by_rcv'];
    public function student()    { return $this->belongsTo(User::class,'user_id'); }
    public function courseBook() { return $this->belongsTo(CourseBook::class); }
}
