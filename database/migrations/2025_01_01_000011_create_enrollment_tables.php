<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Course Books (Enrollment)
        if (!Schema::hasTable('course_books')) {
            Schema::create('course_books', function (Blueprint $table) {
                $table->id();
                $table->foreignId('institute_id')->constrained()->cascadeOnDelete();
                $table->foreignId('session_id')->constrained('institute_sessions')->cascadeOnDelete();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->foreignId('course_id')->constrained('course_details')->cascadeOnDelete();
                $table->foreignId('batch_id')->nullable()->constrained('batch_details')->nullOnDelete();
                $table->string('enrollment_no', 40)->unique();
                $table->decimal('final_fee', 11, 2)->default(0);
                $table->date('start_date')->nullable();
                $table->date('complete_date')->nullable();
                $table->enum('status', ['OPEN','RUN','CLOSE','CANCEL'])->default('OPEN');
                $table->unsignedBigInteger('admission_by');
                $table->timestamps();

                $table->index('institute_id');
                $table->index('user_id');
                $table->index('session_id');
            });
        }

        // Enrollment Fee Snapshot
        if (!Schema::hasTable('enrollment_fee_snapshots')) {
            Schema::create('enrollment_fee_snapshots', function (Blueprint $table) {
                $table->id();
                $table->foreignId('institute_id')->constrained()->cascadeOnDelete();
                $table->foreignId('course_book_id')->constrained()->cascadeOnDelete();
                $table->unsignedBigInteger('fee_type_id')->nullable();
                $table->string('fee_type_name', 100);
                $table->decimal('original_amount', 11, 2);
                $table->decimal('discount_percent', 5, 2)->default(0);
                $table->decimal('discount_amount', 11, 2)->default(0);
                $table->decimal('final_amount', 11, 2);
                $table->timestamps();
            });
        }

        // Enrollment Payment Plan
        if (!Schema::hasTable('enrollment_payment_plans')) {
            Schema::create('enrollment_payment_plans', function (Blueprint $table) {
                $table->id();
                $table->foreignId('institute_id')->constrained()->cascadeOnDelete();
                $table->foreignId('course_book_id')->unique()->constrained()->cascadeOnDelete();
                $table->unsignedBigInteger('payment_plan_type_id')->nullable();
                $table->enum('plan_type', ['OTP','MONTHLY','PART']);
                $table->decimal('monthly_amount', 11, 2)->nullable();
                $table->integer('grace_days')->default(0);
                $table->decimal('late_fee_per_day', 8, 2)->default(0);
                $table->date('next_due_date')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('enrollment_payment_plans');
        Schema::dropIfExists('enrollment_fee_snapshots');
        Schema::dropIfExists('course_books');
    }
};
