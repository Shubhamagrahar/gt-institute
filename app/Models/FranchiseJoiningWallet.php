<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FranchiseJoiningWallet extends Model
{
    protected $fillable = [
        'franchise_id',
        'institute_id',
        'total_due',
        'total_paid',
        'balance',
    ];

    protected $casts = [
        'total_due'  => 'float',
        'total_paid' => 'float',
        'balance'    => 'float',
    ];

    public function franchise() { return $this->belongsTo(Franchise::class); }

    public function recalculate(): void
    {
        $paid = FranchiseFeeCollection::where('franchise_id', $this->franchise_id)
            ->whereNull('cancelled_at')
            ->sum('amount');

        $this->update([
            'total_paid' => $paid,
            'balance'    => max(0, $this->total_due - $paid),
        ]);
    }
}
