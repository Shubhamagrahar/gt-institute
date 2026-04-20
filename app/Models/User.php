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
        'role', 'institute_id', 'franchise_id', 'status',
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

    // NEW — enrollments
    public function enrollments()
    {
        return $this->hasMany(CourseBook::class);
    }
}
