<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('level_course_charges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institute_id')->constrained()->cascadeOnDelete();
            $table->foreignId('franchise_level_id')->constrained('franchise_levels')->cascadeOnDelete();
            $table->foreignId('course_id')->constrained('course_details')->cascadeOnDelete();
            $table->string('course_name', 150);
            $table->unsignedSmallInteger('duration')->default(0);
            $table->decimal('student_admission_charge', 10, 2)->default(0);
            $table->decimal('student_certificate_charge', 10, 2)->default(0);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            $table->unique(['franchise_level_id', 'course_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('level_course_charges');
    }
};
