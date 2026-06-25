<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('franchise_course_charges', function (Blueprint $table) {
            // Which course type this course belongs to (denormalized for fast grouping)
            $table->unsignedBigInteger('course_type_id')->nullable()->after('course_id');
            // What the franchise charges the student (set by franchise, ≤ course.fee)
            $table->decimal('student_fee', 10, 2)->nullable()->after('certificate_charge');
            // Whether this course is currently accessible for this franchise
            $table->boolean('enabled')->default(true)->after('student_fee');

            $table->foreign('course_type_id')->references('id')->on('course_types')->nullOnDelete();
            $table->index('course_type_id');
        });

        // Backfill course_type_id for existing records
        \Illuminate\Support\Facades\DB::statement('
            UPDATE franchise_course_charges fcc
            INNER JOIN course_details cd ON cd.id = fcc.course_id
            SET fcc.course_type_id = cd.course_type_id
            WHERE fcc.course_type_id IS NULL
        ');
    }

    public function down(): void
    {
        Schema::table('franchise_course_charges', function (Blueprint $table) {
            $table->dropForeign(['course_type_id']);
            $table->dropIndex(['course_type_id']);
            $table->dropColumn(['course_type_id', 'student_fee', 'enabled']);
        });
    }
};
