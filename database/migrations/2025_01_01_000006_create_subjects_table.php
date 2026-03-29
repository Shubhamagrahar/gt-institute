<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Subjects table
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institute_id')->constrained()->cascadeOnDelete();
            $table->string('subject_code', 20)->nullable();
            $table->string('name', 150);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();

            $table->index('institute_id');
        });

        // Course-Subject binding with max marks
        Schema::create('course_subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institute_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_id')->constrained('course_details')->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->integer('max_marks')->default(100);
            $table->timestamps();

            $table->unique(['course_id', 'subject_id']); // ek course mein ek subject ek baar
            $table->index('institute_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_subjects');
        Schema::dropIfExists('subjects');
    }
};