<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('course_books', function (Blueprint $table) {
            $table->string('course_code', 20)->nullable()->after('course_id');
        });
    }

    public function down(): void
    {
        Schema::table('course_books', function (Blueprint $table) {
            $table->dropColumn('course_code');
        });
    }
};
