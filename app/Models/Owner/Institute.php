<?php

namespace App\Models\Owner;

use App\Models\Franchise;
use App\Models\InstituteStudentWallet;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;

class Institute extends Model
{
    use HasFactory;

    protected $fillable = [
        'unique_id', 'name', 'short_name', 'email', 'mobile',
        'owner_name', 'owner_mobile', 'logo', 'address', 'state',
        'pin_code', 'website', 'type', 'status', 'slug', 'emergency_otp_secret',
    ];

    public function todayEmergencyCode(): string
    {
        $secret = $this->emergency_otp_secret ?? '';
        if (!$secret) return '------';
        $hash = hash('sha256', $secret . '|' . now()->format('Y-m-d'));
        return str_pad((string) (hexdec(substr($hash, 0, 10)) % 1000000), 6, '0', STR_PAD_LEFT);
    }

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

    public function studentWallet()
    {
        return $this->hasOne(InstituteStudentWallet::class);
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

    public function franchises()
    {
        return $this->hasMany(Franchise::class)->latest();
    }
}
