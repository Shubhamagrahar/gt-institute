<?php

namespace App\Models\Owner;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;

class Institute extends Model
{
    use HasFactory;

    protected $fillable = [
        'unique_id', 'name', 'short_name', 'email', 'mobile',
        'owner_name', 'owner_mobile', 'logo', 'address', 'state',
        'pin_code', 'website', 'type', 'status', 'slug',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function head()
    {
        return $this->hasOne(User::class)->where('role', 'institute_head');
    }

    public function subscription()
    {
        return $this->hasOne(InstituteSubscription::class)->where('status', 'active')->latest();
    }

    public function subscriptions()
    {
        return $this->hasMany(InstituteSubscription::class);
    }

    public function features()
    {
        return $this->hasMany(InstituteFeature::class);
    }

    public function wallet()
    {
        return $this->hasOne(InstituteWallet::class);
    }

    public function transactions()
    {
        return $this->hasMany(InstituteTransaction::class)->orderByDesc('id');
    }

    public function payCollects()
    {
        return $this->hasMany(InstitutePayCollect::class)->orderByDesc('id');
    }

    public function hasFeature(string $featureSlug): bool
    {
        return $this->features()
            ->whereHas('feature', fn($q) => $q->where('slug', $featureSlug))
            ->exists();
    }
}
