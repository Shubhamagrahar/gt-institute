<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institute_id')->constrained()->cascadeOnDelete();
            $table->foreignId('franchise_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('course_book_id')->nullable()->constrained('course_books')->nullOnDelete();
            $table->foreignId('generated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->string('certificate_no')->unique();
            $table->string('source', 20)->default('direct'); // direct | walkin
            $table->string('enrollment_status_at_issue', 20)->nullable();

            $table->string('student_name');
            $table->string('father_name')->nullable();
            $table->string('mother_name')->nullable();
            $table->string('mobile')->nullable();
            $table->date('dob')->nullable();
            $table->string('photo')->nullable();
            $table->string('enrollment_no')->nullable();
            $table->string('course_name');
            $table->string('duration')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('academic_session')->nullable();

            $table->decimal('total_max', 8, 2)->default(0);
            $table->decimal('total_obtained', 8, 2)->default(0);
            $table->decimal('percentage', 5, 2)->default(0);
            $table->string('overall_grade', 5)->nullable();
            $table->string('result', 10)->nullable(); // PASS | FAIL

            $table->timestamps();
            $table->index(['institute_id', 'created_at']);
        });

        Schema::create('certificate_subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('certificate_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subject_id')->nullable()->constrained()->nullOnDelete();
            $table->string('subject_code', 20)->nullable();
            $table->string('subject_name', 150);
            $table->decimal('max_marks', 6, 2)->default(0);
            $table->decimal('obtained_marks', 6, 2)->default(0);
            $table->string('grade', 5)->nullable();
        });

        Schema::create('institute_certificate_counters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institute_id')->unique()->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('last_certificate_no')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('institute_certificate_counters');
        Schema::dropIfExists('certificate_subjects');
        Schema::dropIfExists('certificates');
    }
};
