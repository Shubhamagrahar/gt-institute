<?php

namespace App\Models\Owner;

use Illuminate\Database\Eloquent\Model;

class InstituteSubscription extends Model
{
    protected $fillable = [
        'institute_id', 'plan_id', 'start_date', 'end_date',
        'price', 'discount_type', 'discount_value', 'discount_amount',
        'final_price', 'status',
    ];

    public function institute() { return $this->belongsTo(Institute::class); }
    public function plan()      { return $this->belongsTo(Plan::class); }
    public function features()  { return $this->hasMany(InstituteFeature::class); }
}
