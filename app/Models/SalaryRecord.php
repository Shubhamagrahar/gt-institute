<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalaryRecord extends Model
{
    protected $fillable = [
        'institute_id', 'staff_user_id', 'month',
        'expected_amount', 'paid_amount', 'status', 'notes',
    ];

    protected $casts = ['month' => 'date'];

    public function staff()        { return $this->belongsTo(User::class, 'staff_user_id'); }
    public function transactions() { return $this->hasMany(SalaryTransaction::class); }

    public function getPendingAttribute(): float
    {
        return max(0, $this->expected_amount - $this->paid_amount);
    }

    public function recalculate(): void
    {
        $paid = $this->transactions()->sum('amount');
        $status = $paid <= 0 ? 'pending'
            : ($paid >= $this->expected_amount ? 'paid' : 'partial');
        $this->update(['paid_amount' => $paid, 'status' => $status]);
    }
}
