<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseBook extends Model
{
    protected $table = 'course_books';
    protected $fillable = [
        'institute_id', 'user_id', 'course_id', 'batch_id',
        'fee', 'book_date', 'start_date', 'complete_date', 'status',
    ];

    public function student() { return $this->belongsTo(User::class, 'user_id'); }
    public function course()  { return $this->belongsTo(CourseDetail::class, 'course_id'); }
    public function batch()   { return $this->belongsTo(BatchDetail::class, 'batch_id'); }
}
