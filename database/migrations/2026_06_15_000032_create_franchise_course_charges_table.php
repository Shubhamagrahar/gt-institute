<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('franchise_course_charges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('franchise_id')->constrained()->cascadeOnDelete();
            $table->foreignId('institute_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_id')->constrained('course_details')->cascadeOnDelete();
            $table->string('course_name', 150);       // snapshot at time of setup
            $table->integer('duration')->default(0);   // snapshot in months
            $table->decimal('admission_charge', 10, 2)->default(0);
            $table->decimal('certificate_charge', 10, 2)->default(0);
            $table->timestamps();

            $table->unique(['franchise_id', 'course_id']);
            $table->index(['franchise_id', 'institute_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('franchise_course_charges');
    }
};
