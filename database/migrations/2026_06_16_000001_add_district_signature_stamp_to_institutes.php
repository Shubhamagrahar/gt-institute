<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('institutes', function (Blueprint $table) {
            $table->string('district', 60)->nullable()->after('state');
            $table->string('signature', 300)->nullable()->after('logo');
            $table->string('stamp', 300)->nullable()->after('signature');
        });
    }

    public function down(): void
    {
        Schema::table('institutes', function (Blueprint $table) {
            $table->dropColumn(['district', 'signature', 'stamp']);
        });
    }
};
