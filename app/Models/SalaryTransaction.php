<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalaryTransaction extends Model
{
    protected $fillable = [
        'salary_record_id', 'institute_id', 'amount',
        'payment_date', 'payment_mode', 'reference_no', 'notes', 'created_by',
    ];

    protected $casts = ['payment_date' => 'date'];

    public function record()    { return $this->belongsTo(SalaryRecord::class, 'salary_record_id'); }
    public function createdBy() { return $this->belongsTo(User::class, 'created_by'); }
}
