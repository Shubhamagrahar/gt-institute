<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('course_details', function (Blueprint $table) {
            $columnsToDrop = [];

            if (Schema::hasColumn('course_details', 'short_name')) {
                $columnsToDrop[] = 'short_name';
            }

            if (Schema::hasColumn('course_details', 'duration_months')) {
                $columnsToDrop[] = 'duration_months';
            }

            if (Schema::hasColumn('course_details', 'img')) {
                $columnsToDrop[] = 'img';
            }

            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }

    public function down(): void
    {
        Schema::table('course_details', function (Blueprint $table) {
            if (!Schema::hasColumn('course_details', 'short_name')) {
                $table->string('short_name', 50)->nullable()->after('course_short_name');
            }

            if (!Schema::hasColumn('course_details', 'duration_months')) {
                $table->integer('duration_months')->default(6)->after('short_name');
            }

            if (!Schema::hasColumn('course_details', 'img')) {
                $table->string('img', 300)->nullable()->after('image');
            }
        });
    }
};
