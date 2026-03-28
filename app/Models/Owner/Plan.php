<?php

namespace App\Models\Owner;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Plan extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'price', 'duration', 'description', 'status'];

    public function features()
    {
        return $this->belongsToMany(Feature::class, 'plan_features');
    }

    public function subscriptions()
    {
        return $this->hasMany(InstituteSubscription::class);
    }
}
