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
        'role', 'institute_id', 'status',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return ['password' => 'hashed'];
    }

    // Roles
    public function isInstituteHead(): bool { return $this->role === 'institute_head'; }
    public function isStaff(): bool         { return $this->role === 'staff'; }
    public function isStudent(): bool       { return $this->role === 'student'; }

    // Name helper — comes from profile
    public function getNameAttribute(): string
    {
        if ($this->role === 'student') {
            return $this->studentProfile?->name ?? $this->user_id;
        }
        return $this->staffProfile?->name ?? $this->user_id;
    }

    public function institute()
    {
        return $this->belongsTo(Institute::class);
    }

    public function studentProfile()
    {
        return $this->hasOne(StudentProfile::class);
    }

    public function staffProfile()
    {
        return $this->hasOne(StaffProfile::class);
    }

    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }
}
