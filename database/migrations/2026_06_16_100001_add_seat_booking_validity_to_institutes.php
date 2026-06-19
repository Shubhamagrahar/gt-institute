<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('institutes', function (Blueprint $table) {
            $table->unsignedSmallInteger('seat_booking_validity_days')->default(30)->after('website');
        });
    }

    public function down(): void
    {
        Schema::table('institutes', function (Blueprint $table) {
            $table->dropColumn('seat_booking_validity_days');
        });
    }
};
