<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('user_id', 20)->unique(); // GT/INS/001, INST2025/0001
            $table->string('name', 100);
            $table->string('mobile', 15)->unique();
            $table->string('email', 100)->unique()->nullable();
            $table->string('password');
            $table->enum('role', ['owner', 'institute_head', 'staff', 'student'])->default('student');
            $table->unsignedBigInteger('institute_id')->nullable(); // null for owner
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
