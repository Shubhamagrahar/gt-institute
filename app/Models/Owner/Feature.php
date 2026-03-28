<?php

namespace App\Models\Owner;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Feature extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'slug', 'description', 'price', 'status'];

    public function plans()
    {
        return $this->belongsToMany(Plan::class, 'plan_features');
    }
}
