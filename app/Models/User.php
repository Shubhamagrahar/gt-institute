<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Owner\Institute;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'user_id', 'mobile', 'email', 'password',
        'role', 'user_type', 'institute_id', 'franchise_id', 'channel_partner_id', 'owner_type', 'status',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return ['password' => 'hashed'];
    }

    // ── Roles ──────────────────────────────────────────
    public function isManager(): bool { return $this->role === 'manager'; }
    public function isStaff(): bool   { return $this->role === 'staff'; }
    public function isStudent(): bool { return $this->role === 'student'; }
    public function isFranchiseOwned(): bool { return $this->owner_type === 'franchise'; }
    public function isInstituteOwned(): bool { return $this->owner_type !== 'franchise'; }

    // ── Name helper ────────────────────────────────────
    // users table mein name column nahi hai
    // profile table se aata hai
    public function getNameAttribute(): string
    {
        return $this->profile?->name ?? $this->user_id;
    }

    // ── Relationships ──────────────────────────────────
    public function institute()
    {
        return $this->belongsTo(Institute::class);
    }

    public function franchise()
    {
        return $this->belongsTo(Franchise::class);
    }

    public function channelPartner()
    {
        return $this->belongsTo(ChannelPartner::class);
    }

    // NEW — unified profile (sabke liye ek hi table)
    public function profile()
    {
        return $this->hasOne(UserProfile::class);
    }

    // NEW — education details
    public function education()
    {
        return $this->hasMany(UserEducation::class);
    }

    // NEW — student wallet
    public function studentWallet()
    {
        return $this->hasOne(StudentWallet::class);
    }

    public function latestEnrollmentBook()
    {
        return $this->hasOne(CourseBook::class)->latestOfMany();
    }

    public function getCurrentEnrollmentNoAttribute(): ?string
    {
        return $this->latestEnrollmentBook?->enrollment_no;
    }

    public function transactions()
    {
        return $this->hasMany(StudentTransaction::class);
    }

    // NEW — enrollments
    public function enrollments()
    {
        return $this->hasMany(CourseBook::class);
    }
}
