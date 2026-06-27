<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FranchiseInstituteWallet extends Model
{
    protected $fillable = ['franchise_id', 'institute_id', 'balance'];

    protected $casts = ['balance' => 'float'];

    public function franchise()
    {
        return $this->belongsTo(Franchise::class);
    }

    public function outstanding(): float
    {
        return (float) $this->balance < 0 ? abs((float) $this->balance) : 0.0;
    }

    public function isSettled(): bool
    {
        return (float) $this->balance >= 0;
    }
}
