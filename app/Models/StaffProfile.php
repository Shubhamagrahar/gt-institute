<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StaffProfile extends Model
{
    protected $fillable = [
        'user_id', 'institute_id', 'staff_role_id', 'custom_permissions',
        'name', 'photo',
        'designation', 'father_name', 'dob', 'gender', 'blood_group',
        'qualification', 'experience_years', 'department', 'whatsapp',
        'address', 'city', 'state', 'pin',
        'joining_date', 'salary', 'salary_type',
        'aadhar_no', 'pan_no',
        'bank_name', 'account_no', 'ifsc', 'branch_name',
        'emergency_name', 'emergency_phone', 'emergency_relation',
        'notes',
    ];

    protected $casts = [
        'custom_permissions' => 'array',
        'dob'                => 'date',
        'joining_date'       => 'date',
    ];

    public function user()      { return $this->belongsTo(User::class); }
    public function institute() { return $this->belongsTo(Owner\Institute::class); }
    public function staffRole() { return $this->belongsTo(StaffRole::class, 'staff_role_id'); }

    // Resolved permissions: custom overrides role, role is fallback
    public function resolvedPermissions(): array
    {
        if (!is_null($this->custom_permissions)) {
            return $this->custom_permissions;
        }
        return $this->staffRole?->permissions ?? [];
    }

    public function hasPermission(string $key): bool
    {
        return in_array($key, $this->resolvedPermissions());
    }
}
