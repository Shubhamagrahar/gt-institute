<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('course_books')) {
            return;
        }

        Schema::table('course_books', function (Blueprint $table) {
            if (!Schema::hasColumn('course_books', 'session_id')) {
                $table->unsignedBigInteger('session_id')->nullable()->after('institute_id');
                $table->index('session_id');
            }

            if (!Schema::hasColumn('course_books', 'final_fee')) {
                $table->decimal('final_fee', 11, 2)->default(0)->after('enrollment_no');
            }

            if (!Schema::hasColumn('course_books', 'admission_by')) {
                $table->unsignedBigInteger('admission_by')->nullable()->after('status');
            }
        });

        if (Schema::hasColumn('course_books', 'fee') && Schema::hasColumn('course_books', 'final_fee')) {
            DB::statement('UPDATE course_books SET final_fee = fee WHERE final_fee = 0 OR final_fee IS NULL');
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('course_books')) {
            return;
        }

        Schema::table('course_books', function (Blueprint $table) {
            if (Schema::hasColumn('course_books', 'session_id')) {
                $table->dropIndex(['session_id']);
                $table->dropColumn('session_id');
            }

            if (Schema::hasColumn('course_books', 'final_fee')) {
                $table->dropColumn('final_fee');
            }

            if (Schema::hasColumn('course_books', 'admission_by')) {
                $table->dropColumn('admission_by');
            }
        });
    }
};
