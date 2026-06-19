<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('institutes', function (Blueprint $table) {
            $table->boolean('use_signature')->default(false)->after('signature');
            $table->boolean('use_stamp')->default(false)->after('stamp');
        });
    }

    public function down(): void
    {
        Schema::table('institutes', function (Blueprint $table) {
            $table->dropColumn(['use_signature', 'use_stamp']);
        });
    }
};
