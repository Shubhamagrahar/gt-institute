<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('franchises', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institute_id')->constrained()->cascadeOnDelete();
            $table->string('unique_id', 24)->unique();
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
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');
            $table->string('slug', 160)->unique();
            $table->timestamps();

            $table->index(['institute_id', 'status']);
        });

        Schema::create('franchise_wallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('franchise_id')->constrained()->cascadeOnDelete()->unique();
            $table->foreignId('institute_id')->constrained()->cascadeOnDelete();
            $table->decimal('balance', 11, 2)->default(0.00);
            $table->timestamps();
        });

        Schema::create('franchise_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('franchise_id')->constrained()->cascadeOnDelete();
            $table->foreignId('institute_id')->constrained()->cascadeOnDelete();
            $table->string('txn_no', 30)->nullable();
            $table->text('description');
            $table->decimal('credit', 11, 2)->default(0.00);
            $table->decimal('debit', 11, 2)->default(0.00);
            $table->tinyInteger('type')->comment('1=opening, 2=recharge, 3=bonus, 4=admission, 5=certificate, 6=manual, 7=refund');
            $table->string('payment_mode', 20)->nullable();
            $table->string('utr', 80)->nullable();
            $table->decimal('op_bal', 11, 2)->default(0.00);
            $table->decimal('cl_bal', 11, 2)->default(0.00);
            $table->date('date');
            $table->dateTime('c_date');
            $table->unsignedBigInteger('by_userid')->nullable();
            $table->timestamps();

            $table->index(['franchise_id', 'date']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('franchise_id')->nullable()->after('institute_id')->constrained('franchises')->nullOnDelete();
        });

        DB::statement("
            ALTER TABLE users
            MODIFY COLUMN role ENUM(
                'institute_head',
                'staff',
                'student',
                'franchise_head',
                'franchise_staff',
                'franchise_student'
            ) NOT NULL DEFAULT 'student'
        ");
    }

    public function down(): void
    {
        DB::statement("
            ALTER TABLE users
            MODIFY COLUMN role ENUM(
                'institute_head',
                'staff',
                'student'
            ) NOT NULL DEFAULT 'student'
        ");

        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('franchise_id');
        });

        Schema::dropIfExists('franchise_transactions');
        Schema::dropIfExists('franchise_wallets');
        Schema::dropIfExists('franchises');
    }
};
