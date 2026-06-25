<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FranchiseFeeStructure extends Model
{
    protected $fillable = [
        'franchise_id',
        'institute_id',
        'course_id',
        'fee_type_id',
        'fee_type_name',
        'amount',
        'enabled',
        'sort_order',
    ];

    protected $casts = [
        'amount'  => 'float',
        'enabled' => 'boolean',
    ];

    public function franchise() { return $this->belongsTo(Franchise::class); }
    public function course()    { return $this->belongsTo(CourseDetail::class, 'course_id'); }
    public function feeType()   { return $this->belongsTo(FeeType::class, 'fee_type_id'); }
}
