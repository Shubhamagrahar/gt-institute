<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('franchises', function (Blueprint $table) {
            $table->dropColumn(['admission_charge', 'certificate_charge']);
        });
    }

    public function down(): void
    {
        Schema::table('franchises', function (Blueprint $table) {
            $table->decimal('admission_charge', 10, 2)->default(0)->after('onboarding_fee');
            $table->decimal('certificate_charge', 10, 2)->default(0)->after('admission_charge');
        });
    }
};
