<?php

namespace App\Models;

use App\Models\Owner\Institute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Franchise extends Model
{
    use HasFactory;

    protected $fillable = [
        'institute_id',
        'franchise_level_id',
        'unique_id',
        'name',
        'short_name',
        'email',
        'mobile',
        'owner_name',
        'owner_mobile',
        'logo',
        'address',
        'state',
        'pin_code',
        'website',
        'commission_percent',
        'wallet_enabled',
        'low_wallet_alert',
        'has_sub_franchise',
        'management_type',
        'onboarding_fee',
        'fee_total',
        'status',
        'slug',
    ];

    public function institute()
    {
        return $this->belongsTo(Institute::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function level()
    {
        return $this->belongsTo(FranchiseLevel::class, 'franchise_level_id');
    }

    public function head()
    {
        return $this->hasOne(User::class)->where('role', 'franchise_head');
    }

    public function wallet()
    {
        return $this->hasOne(FranchiseWallet::class);
    }

    public function transactions()
    {
        return $this->hasMany(FranchiseTransaction::class)->orderByDesc('id');
    }

    public function feeCollections()
    {
        return $this->hasMany(FranchiseFeeCollection::class)->orderByDesc('id');
    }

    public function feePaid(): float
    {
        return (float) $this->feeCollections()->whereNull('cancelled_at')->sum('amount');
    }

    public function feeOutstanding(): float
    {
        return max(0, (float) $this->fee_total - $this->feePaid());
    }

    public function courseCharges()
    {
        return $this->hasMany(FranchiseCourseCharge::class);
    }

    public function joiningWallet()
    {
        return $this->hasOne(FranchiseJoiningWallet::class);
    }
}
