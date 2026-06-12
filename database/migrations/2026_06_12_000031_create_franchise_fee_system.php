<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Tracks individual payment collections against franchise onboarding fee
        Schema::create('franchise_fee_collections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('franchise_id')->constrained()->cascadeOnDelete();
            $table->foreignId('institute_id')->constrained()->cascadeOnDelete();
            $table->string('invoice_no', 30)->nullable()->unique();
            $table->string('payment_mode', 20)->nullable();
            $table->string('utr', 80)->nullable();
            $table->decimal('amount', 10, 2);
            $table->date('date');
            $table->text('note')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->string('cancel_reason', 255)->nullable();
            $table->unsignedBigInteger('cancelled_by')->nullable();
            $table->unsignedBigInteger('collected_by')->nullable();
            $table->timestamps();

            $table->index(['franchise_id', 'date']);
        });

        // Add fee_total to franchises (amount due at time of creation, from level_fee)
        Schema::table('franchises', function (Blueprint $table) {
            $table->decimal('fee_total', 10, 2)->default(0)->after('certificate_charge')
                ->comment('Total onboarding fee due (copied from level_fee at creation time)');
        });
    }

    public function down(): void
    {
        Schema::table('franchises', function (Blueprint $table) {
            $table->dropColumn('fee_total');
        });
        Schema::dropIfExists('franchise_fee_collections');
    }
};
