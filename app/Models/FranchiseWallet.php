<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FranchiseWallet extends Model
{
    protected $fillable = [
        'franchise_id',
        'institute_id',
        'balance',
    ];

    public function franchise()
    {
        return $this->belongsTo(Franchise::class);
    }
}
