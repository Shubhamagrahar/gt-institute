<?php

namespace App\Models;

use App\Models\Owner\Institute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FranchiseLevel extends Model
{
    use HasFactory;

    protected $fillable = [
        'institute_id',
        'name',
        'commission_percent',
        'notes',
        'status',
    ];

    public function institute()
    {
        return $this->belongsTo(Institute::class);
    }

    public function franchises()
    {
        return $this->hasMany(Franchise::class);
    }
}
