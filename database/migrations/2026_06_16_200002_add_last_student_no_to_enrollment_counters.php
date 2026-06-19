<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('institute_enrollment_counters', function (Blueprint $table) {
            $table->unsignedInteger('last_student_no')->default(0)->after('last_enrollment_no');
        });
    }

    public function down(): void
    {
        Schema::table('institute_enrollment_counters', function (Blueprint $table) {
            $table->dropColumn('last_student_no');
        });
    }
};
