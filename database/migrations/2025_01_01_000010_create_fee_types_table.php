<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Fee Types (Registration Fee, Practical Fee etc)
        Schema::create('fee_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institute_id')->constrained()->cascadeOnDelete();
            $table->string('name', 100);
            $table->boolean('is_mandatory')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Course Fee Structure
        Schema::create('course_fee_structure', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institute_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_id')->constrained('course_details')->cascadeOnDelete();
            $table->foreignId('fee_type_id')->constrained('fee_types')->cascadeOnDelete();
            $table->string('fee_type_name', 100);  // snapshot
            $table->decimal('amount', 11, 2);
            $table->timestamps();

            $table->unique(['course_id', 'fee_type_id']);
        });

        // Payment Plan Types
        Schema::create('payment_plan_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institute_id')->constrained()->cascadeOnDelete();
            $table->string('name', 100);
            $table->enum('type', ['OTP', 'MONTHLY', 'PART']);
            $table->integer('grace_days')->default(0);
            $table->decimal('late_fee_per_day', 8, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_plan_types');
        Schema::dropIfExists('course_fee_structure');
        Schema::dropIfExists('fee_types');
    }
};