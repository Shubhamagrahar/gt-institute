<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // ── 1. SUPER ADMINS (Owner panel users — completely separate) ──────────
        Schema::create('super_admins', function (Blueprint $table) {
            $table->id();
            $table->string('admin_id', 20)->unique();   // e.g. GT/ADMIN/001
            $table->string('name', 100);
            $table->string('email', 100)->unique();
            $table->string('mobile', 15)->unique();
            $table->string('password');
            $table->string('logo', 300)->default('images/default-admin.png');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->rememberToken();
            $table->timestamps();
        });

        // ── 2. USERS (Institute staff + students — login credentials only) ─────
        // Deliberately lean — no name, no photo, no personal info here
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('user_id', 30)->unique();    // e.g. INST2025001/STU/0001
            $table->string('mobile', 15);
            $table->string('email', 100)->nullable();
            $table->string('password');
            $table->enum('role', ['institute_head', 'staff', 'student'])->default('student');
            $table->unsignedBigInteger('institute_id');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->rememberToken();
            $table->timestamps();

            $table->index('institute_id');
            $table->index(['mobile', 'institute_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('super_admins');
    }
};
