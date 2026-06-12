<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('franchise_levels', function (Blueprint $table) {
            // One-time joining fee franchises at this level must pay the institute
            $table->decimal('level_fee', 10, 2)->default(0)->after('commission_percent')
                ->comment('One-time onboarding fee for franchises joining at this level');
        });
    }

    public function down(): void
    {
        Schema::table('franchise_levels', function (Blueprint $table) {
            $table->dropColumn('level_fee');
        });
    }
};
