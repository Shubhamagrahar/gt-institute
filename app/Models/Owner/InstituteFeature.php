<?php

namespace App\Models\Owner;

use Illuminate\Database\Eloquent\Model;

class InstituteFeature extends Model
{
    protected $fillable = [
        'institute_id', 'institute_subscription_id', 'feature_id', 'price', 'is_addon',
    ];

    public function institute()    { return $this->belongsTo(Institute::class); }
    public function feature()      { return $this->belongsTo(Feature::class); }
    public function subscription() { return $this->belongsTo(InstituteSubscription::class, 'institute_subscription_id'); }
}
