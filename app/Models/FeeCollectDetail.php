<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeeCollectDetail extends Model
{
    protected $table = 'fee_collect_details';
    protected $fillable = [
        'user_id', 'institute_id', 'course_book_id',
        'invoice_no', 'payment_mode', 'utr', 'amt', 'date', 'by_rcv',
    ];

    public function student()    { return $this->belongsTo(User::class, 'user_id'); }
    public function courseBook() { return $this->belongsTo(CourseBook::class); }
}
