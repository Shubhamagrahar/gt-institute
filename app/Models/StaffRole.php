<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StaffRole extends Model
{
    protected $fillable = [
        'institute_id', 'name', 'short_code', 'color', 'description',
        'permissions', 'grace_days', 'status',
    ];

    protected $casts = [
        'permissions' => 'array',
    ];

    public function staff()
    {
        return $this->hasMany(StaffProfile::class, 'staff_role_id');
    }

    public function hasPermission(string $key): bool
    {
        return in_array($key, $this->permissions ?? []);
    }

    // All available permissions grouped by module
    public static function allPermissions(): array
    {
        return [
            'General' => [
                'dashboard' => 'View Dashboard',
            ],
            'Enquiries' => [
                'enquiries.view'    => 'View Enquiries',
                'enquiries.create'  => 'Add New Enquiry',
                'enquiries.followup'=> 'Add Follow-up Notes',
                'enquiries.convert' => 'Convert to Admission',
                'enquiries.edit'    => 'Edit / Delete Enquiry',
            ],
            'Admissions' => [
                'admissions.new'     => 'New Admission',
                'admissions.quick'   => 'Quick Admission',
                'admissions.pending' => 'View Pending Admissions',
                'admissions.edit'    => 'Edit Enrollment',
            ],
            'Students' => [
                'students.view'   => 'View Students',
                'students.edit'   => 'Edit Student Details',
                'students.ledger' => 'View Fee Ledger',
            ],
            'Attendance' => [
                'attendance.mark'     => 'Mark Attendance',
                'attendance.register' => 'Attendance Register',
                'attendance.report'   => 'Student Attendance Report',
                'attendance.export'   => 'Export Attendance',
            ],
            'Fee Management' => [
                'fees.quickpay'  => 'Quick Pay',
                'fees.dashboard' => 'Fees Dashboard',
                'fees.collect'   => 'Collect Fee',
                'fees.receipt'   => 'View / Print Receipts',
                'fees.cancel'    => 'Cancel Fee Entry',
            ],
            'Courses & Batches' => [
                'courses.view'   => 'View Courses & Batches',
                'courses.manage' => 'Add / Edit Courses',
            ],
            'Certificates' => [
                'certificates.issue' => 'Issue Certificate',
                'certificates.view'  => 'View Certificates',
            ],
            'Reports' => [
                'reports.fee'        => 'Fee Collection Report',
                'reports.attendance' => 'Attendance Summary',
                'reports.enrollment' => 'Enrollment Report',
            ],
        ];
    }

    // Flat list: key => label
    public static function flatPermissions(): array
    {
        return collect(self::allPermissions())->flatMap(fn($p) => $p)->toArray();
    }
}
