<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Super Admin (Owner Panel) ─────────────────────────────────────────
        DB::table('super_admins')->insertOrIgnore([
            'admin_id'   => 'GT/ADMIN/001',
            'name'       => 'GT Admin',
            'email'      => 'admin@gtinstitute.com',
            'mobile'     => '9999999999',
            'password'   => Hash::make('gt@admin@2025'),
            'logo'       => 'images/default-admin.png',
            'status'     => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // ── Default Features ──────────────────────────────────────────────────
        $features = [
            ['name' => 'Student Management',    'slug' => 'student_system',     'price' => 0,    'description' => 'Add, manage and track students'],
            ['name' => 'Fee Collection',         'slug' => 'fee_system',         'price' => 0,    'description' => 'Collect and track student fees'],
            ['name' => 'Course Management',      'slug' => 'course_system',      'price' => 0,    'description' => 'Add courses, batches and enrollments'],
            ['name' => 'Attendance System',      'slug' => 'attendance_system',  'price' => 500,  'description' => 'Mark and track student & staff attendance'],
            ['name' => 'Staff Management',       'slug' => 'staff_system',       'price' => 500,  'description' => 'Manage institute staff and salaries'],
            ['name' => 'Enquiry System',         'slug' => 'enquiry_system',     'price' => 500,  'description' => 'Track enquiries and follow-ups'],
            ['name' => 'Certificate Generator',  'slug' => 'certificate_system', 'price' => 1000, 'description' => 'Generate student certificates'],
            ['name' => 'LMS (Learning Mgmt)',    'slug' => 'lms_system',         'price' => 1500, 'description' => 'Videos, documents and live classes'],
            ['name' => 'Online Exam System',     'slug' => 'exam_system',        'price' => 1500, 'description' => 'Create and conduct online exams'],
            ['name' => 'Test Series',            'slug' => 'test_series_system', 'price' => 1000, 'description' => 'Practice test series for students'],
        ];

        foreach ($features as $f) {
            DB::table('features')->insertOrIgnore([
                'name'        => $f['name'],
                'slug'        => $f['slug'],
                'description' => $f['description'],
                'price'       => $f['price'],
                'status'      => 'active',
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }

        $this->command->info('');
        $this->command->info('✅ Super Admin created');
        $this->command->info('   Email    : admin@gtinstitute.com');
        $this->command->info('   Password : gt@admin@2025');
        $this->command->info('');
        $this->command->info('✅ Default features seeded (10 features)');
    }
}
