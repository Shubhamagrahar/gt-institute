<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // One record per staff per month
        Schema::create('salary_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('institute_id');
            $table->unsignedBigInteger('staff_user_id');
            $table->date('month');                              // stored as YYYY-MM-01
            $table->decimal('expected_amount', 10, 2);         // salary at that time
            $table->decimal('paid_amount', 10, 2)->default(0); // sum of all transactions
            $table->enum('status', ['pending','partial','paid'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['staff_user_id', 'month']);
            $table->index(['institute_id', 'month']);
        });

        // Individual payment transactions per month record
        Schema::create('salary_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('salary_record_id');
            $table->unsignedBigInteger('institute_id');
            $table->decimal('amount', 10, 2);
            $table->date('payment_date');
            $table->enum('payment_mode', ['cash','bank','upi','cheque'])->default('cash');
            $table->string('reference_no', 100)->nullable(); // bank/UPI ref
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by');         // institute user id
            $table->timestamps();

            $table->index('salary_record_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('salary_transactions');
        Schema::dropIfExists('salary_records');
    }
};
