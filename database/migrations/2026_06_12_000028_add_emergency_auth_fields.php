<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Institute head's personal backup OTP (hashed, set once from their panel)
        Schema::table('users', function (Blueprint $table) {
            $table->string('backup_otp')->nullable()->after('password');
            $table->timestamp('backup_otp_set_at')->nullable()->after('backup_otp');
        });

        // Per-institute secret for generating daily rotating emergency code
        Schema::table('institutes', function (Blueprint $table) {
            $table->string('emergency_otp_secret')->nullable()->after('slug');
        });

        // Seed a unique secret for every existing institute
        DB::table('institutes')->get()->each(function ($institute) {
            DB::table('institutes')
                ->where('id', $institute->id)
                ->update(['emergency_otp_secret' => Str::random(48)]);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['backup_otp', 'backup_otp_set_at']);
        });
        Schema::table('institutes', function (Blueprint $table) {
            $table->dropColumn('emergency_otp_secret');
        });
    }
};
