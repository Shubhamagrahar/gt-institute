<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('franchises', function (Blueprint $table) {
            // 'independent' = flat fee at onboarding, no per-txn wallet deductions
            // 'wallet'      = no upfront fee, per-admission and per-certificate deductions
            $table->enum('management_type', ['independent', 'wallet'])
                ->default('wallet')
                ->after('has_sub_franchise');

            $table->decimal('onboarding_fee', 10, 2)->default(0)->after('management_type')
                ->comment('One-time fee charged at onboarding (independent mode)');

            $table->decimal('admission_charge', 10, 2)->default(0)->after('onboarding_fee')
                ->comment('Per-admission deduction from franchise wallet (wallet mode)');

            $table->decimal('certificate_charge', 10, 2)->default(0)->after('admission_charge')
                ->comment('Per-certificate deduction from franchise wallet (wallet mode)');
        });
    }

    public function down(): void
    {
        Schema::table('franchises', function (Blueprint $table) {
            $table->dropColumn(['management_type', 'onboarding_fee', 'admission_charge', 'certificate_charge']);
        });
    }
};
