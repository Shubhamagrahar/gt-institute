<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Franchise ↔ Institute financial wallet
        // balance starts negative (= outstanding dues), reaches 0 when fully paid
        Schema::create('franchise_institute_wallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('franchise_id')->unique()->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('institute_id');
            $table->decimal('balance', 12, 2)->default(0); // negative = owes, 0 = settled
            $table->timestamps();
            $table->index('institute_id');
        });

        // Running ledger for franchise ↔ institute financial transactions
        Schema::create('franchise_institute_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('franchise_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('institute_id');
            $table->string('txn_no', 40)->unique();
            $table->tinyInteger('type')->comment('1=opening_due,2=payment_received,3=payment_cancelled');
            $table->text('description');
            $table->decimal('credit', 12, 2)->default(0);
            $table->decimal('debit', 12, 2)->default(0);
            $table->decimal('op_bal', 12, 2);
            $table->decimal('cl_bal', 12, 2);
            $table->string('payment_mode', 20)->nullable();
            $table->string('utr', 80)->nullable();
            $table->string('invoice_no', 40)->nullable();
            $table->date('date');
            $table->dateTime('c_date');
            $table->unsignedBigInteger('by_userid')->nullable();
            $table->timestamps();
            $table->index(['franchise_id', 'institute_id']);
        });

        // Actual payment receipts from franchise to institute
        Schema::create('franchise_pay_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('franchise_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('institute_id');
            $table->string('invoice_no', 40)->unique();
            $table->enum('payment_mode', ['CASH', 'UPI', 'NEFT', 'IMPS', 'CHEQUE'])->default('CASH');
            $table->string('utr', 80)->nullable();
            $table->decimal('amount', 12, 2);
            $table->date('date');
            $table->text('note')->nullable();
            $table->unsignedBigInteger('collected_by')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->string('cancel_reason', 255)->nullable();
            $table->unsignedBigInteger('cancelled_by')->nullable();
            $table->timestamps();
            $table->index(['franchise_id', 'institute_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('franchise_pay_details');
        Schema::dropIfExists('franchise_institute_transactions');
        Schema::dropIfExists('franchise_institute_wallets');
    }
};
