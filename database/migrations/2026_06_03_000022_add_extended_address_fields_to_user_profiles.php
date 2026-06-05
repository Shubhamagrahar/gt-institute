<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->string('city', 100)->nullable()->after('district');
            $table->string('permanent_state', 100)->nullable()->after('city');
            $table->string('permanent_district', 100)->nullable()->after('permanent_state');
            $table->string('permanent_city', 100)->nullable()->after('permanent_district');
            $table->string('permanent_pin_code', 10)->nullable()->after('permanent_city');
        });
    }

    public function down(): void
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'city',
                'permanent_state',
                'permanent_district',
                'permanent_city',
                'permanent_pin_code',
            ]);
        });
    }
};
