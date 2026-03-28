<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Institutes table
        Schema::create('institutes', function (Blueprint $table) {
            $table->id();
            $table->string('unique_id', 20)->unique(); // INST20250001
            $table->string('name', 150);
            $table->string('short_name', 50)->nullable();
            $table->string('email', 100)->unique();
            $table->string('mobile', 15);
            $table->string('owner_name', 100);
            $table->string('owner_mobile', 15);
            $table->string('logo', 300)->default('images/default-institute.png');
            $table->text('address')->nullable();
            $table->string('state', 60)->nullable();
            $table->string('pin_code', 10)->nullable();
            $table->string('website', 150)->nullable();
            $table->enum('type', ['PRIVATE', 'GOVT', 'FRANCHISE'])->default('PRIVATE');
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');
            $table->string('slug', 120)->unique();
            $table->timestamps();
        });

        // Institute Subscriptions
        Schema::create('institute_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institute_id')->constrained()->cascadeOnDelete();
            $table->foreignId('plan_id')->constrained()->cascadeOnDelete();
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('price', 11, 2)->comment('original plan price');
            $table->enum('discount_type', ['NONE', 'PERCENT', 'FLAT'])->default('NONE');
            $table->decimal('discount_value', 11, 2)->default(0.00);
            $table->decimal('discount_amount', 11, 2)->default(0.00);
            $table->decimal('final_price', 11, 2);
            $table->enum('status', ['active', 'expired', 'cancelled'])->default('active');
            $table->timestamps();
        });

        // Institute Features (which features the institute has access to)
        Schema::create('institute_features', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institute_id')->constrained()->cascadeOnDelete();
            $table->foreignId('institute_subscription_id')->constrained()->cascadeOnDelete();
            $table->foreignId('feature_id')->constrained()->cascadeOnDelete();
            $table->decimal('price', 11, 2)->default(0.00)->comment('0 = plan included, >0 = addon');
            $table->boolean('is_addon')->default(0)->comment('0=plan included, 1=addon');
            $table->timestamps();
        });

        // Institute Wallets
        Schema::create('institute_wallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institute_id')->constrained()->cascadeOnDelete()->unique();
            $table->decimal('main_b', 11, 2)->default(0.00);
            $table->timestamps();
        });

        // Institute Transactions
        Schema::create('institute_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institute_id')->constrained()->cascadeOnDelete();
            $table->text('des');
            $table->decimal('credit', 11, 2)->default(0.00);
            $table->decimal('debit', 11, 2)->default(0.00);
            $table->tinyInteger('type')->comment('1=Subscription, 2=Addon, 3=Payment, 4=Discount, 5=Manual');
            $table->date('date');
            $table->dateTime('c_date');
            $table->decimal('op_bal', 11, 2);
            $table->decimal('cl_bal', 11, 2);
            $table->string('invoice_no', 30)->nullable();
            $table->unsignedBigInteger('by_userid');
            $table->timestamps();
        });

        // Institute Pay Collects (payments received from institute)
        Schema::create('institute_pay_collects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institute_id')->constrained()->cascadeOnDelete();
            $table->string('invoice_no', 30)->unique();
            $table->enum('payment_mode', ['CASH', 'UPI', 'NEFT', 'IMPS', 'CHEQUE'])->default('CASH');
            $table->string('utr', 80)->nullable();
            $table->decimal('amt', 11, 2);
            $table->date('date');
            $table->string('note', 255)->nullable();
            $table->enum('status', ['received', 'pending', 'cancelled'])->default('received');
            $table->unsignedBigInteger('received_by');
            $table->dateTime('c_date');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('institute_pay_collects');
        Schema::dropIfExists('institute_transactions');
        Schema::dropIfExists('institute_wallets');
        Schema::dropIfExists('institute_features');
        Schema::dropIfExists('institute_subscriptions');
        Schema::dropIfExists('institutes');
    }
};
