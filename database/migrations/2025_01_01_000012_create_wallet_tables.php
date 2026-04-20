<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Student Wallet
        if (!Schema::hasTable('student_wallets')) {
            Schema::create('student_wallets', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
                $table->foreignId('institute_id')->constrained()->cascadeOnDelete();
                $table->decimal('balance', 11, 2)->default(0.00);
                $table->timestamps();
            });
        }

        // Student Transactions
        if (!Schema::hasTable('student_transactions')) {
            Schema::create('student_transactions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->foreignId('institute_id')->constrained()->cascadeOnDelete();
                $table->text('description');
                $table->decimal('credit', 11, 2)->default(0);
                $table->decimal('debit', 11, 2)->default(0);
                $table->tinyInteger('type')->comment(
                    '1=enrollment_debit, 2=fee_paid, 3=discount, 4=late_fee, 5=refund'
                );
                $table->date('date');
                $table->dateTime('c_date');
                $table->decimal('op_bal', 11, 2);
                $table->decimal('cl_bal', 11, 2);
                $table->unsignedBigInteger('by_user_id');
                $table->timestamps();

                $table->index('user_id');
                $table->index('institute_id');
            });
        }

        // Institute Student Wallet (total collection)
        if (!Schema::hasTable('institute_student_wallets')) {
            Schema::create('institute_student_wallets', function (Blueprint $table) {
                $table->id();
                $table->foreignId('institute_id')->unique()->constrained()->cascadeOnDelete();
                $table->decimal('balance', 11, 2)->default(0.00);
                $table->timestamps();
            });
        }

        // Institute Student Transactions
        if (!Schema::hasTable('institute_student_transactions')) {
            Schema::create('institute_student_transactions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('institute_id')->constrained()->cascadeOnDelete();
                $table->unsignedBigInteger('ref_user_id')->nullable();
                $table->text('description');
                $table->decimal('credit', 11, 2)->default(0);
                $table->decimal('debit', 11, 2)->default(0);
                $table->tinyInteger('type')->comment(
                    '1=fee_received, 2=refund, 3=adjustment'
                );
                $table->date('date');
                $table->dateTime('c_date');
                $table->decimal('op_bal', 11, 2);
                $table->decimal('cl_bal', 11, 2);
                $table->unsignedBigInteger('by_user_id');
                $table->timestamps();

                $table->index('institute_id');
            });
        }

        // Fee Collect Details
        if (!Schema::hasTable('fee_collect_details')) {
            Schema::create('fee_collect_details', function (Blueprint $table) {
                $table->id();
                $table->foreignId('institute_id')->constrained()->cascadeOnDelete();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->string('invoice_no', 30)->unique();
                $table->enum('payment_mode', ['CASH','UPI','NEFT','IMPS','CHEQUE'])
                    ->default('CASH');
                $table->string('utr', 80)->nullable();
                $table->decimal('amount', 11, 2);
                $table->date('date');
                $table->text('note')->nullable();
                $table->unsignedBigInteger('received_by');
                $table->timestamps();

                $table->index('institute_id');
                $table->index('user_id');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('fee_collect_details');
        Schema::dropIfExists('institute_student_transactions');
        Schema::dropIfExists('institute_student_wallets');
        Schema::dropIfExists('student_transactions');
        Schema::dropIfExists('student_wallets');
    }
};
