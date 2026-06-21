<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('staff_roles', function (Blueprint $table) {
            $table->string('short_code', 5)->after('name');   // e.g. ACC, MAN, TCH
            $table->unsignedTinyInteger('grace_days')->default(2)->after('permissions');
        });
    }

    public function down(): void
    {
        Schema::table('staff_roles', function (Blueprint $table) {
            $table->dropColumn(['short_code', 'grace_days']);
        });
    }
};
