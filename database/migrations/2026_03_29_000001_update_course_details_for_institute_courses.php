<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('course_details', function (Blueprint $table) {
            if (!Schema::hasColumn('course_details', 'course_short_name')) {
                $table->string('course_short_name', 50)->nullable()->after('name');
            }

            if (!Schema::hasColumn('course_details', 'image')) {
                $table->string('image', 300)->nullable()->after('description');
            }

            if (!Schema::hasColumn('course_details', 'duration')) {
                $table->integer('duration')->default(6)->after('course_short_name');
            }

            if (!Schema::hasColumn('course_details', 'course_code')) {
                $table->string('course_code', 20)->nullable()->after('course_type_id');
            }
        });

        Schema::table('course_details', function (Blueprint $table) {
            if (!Schema::hasColumn('course_details', 'fee')) {
                $table->decimal('fee', 11, 2)->default(0.00)->after('duration');
            }
        });

        Schema::table('course_details', function (Blueprint $table) {
            if (!Schema::hasColumn('course_details', 'max_fee')) {
                $table->decimal('max_fee', 11, 2)->default(0.00)->after('fee');
            }
        });

        if (Schema::hasColumn('course_details', 'short_name') && Schema::hasColumn('course_details', 'course_short_name')) {
            DB::table('course_details')
                ->whereNull('course_short_name')
                ->update(['course_short_name' => DB::raw('short_name')]);
        }

        if (Schema::hasColumn('course_details', 'img') && Schema::hasColumn('course_details', 'image')) {
            DB::table('course_details')
                ->whereNull('image')
                ->update(['image' => DB::raw('img')]);
        }

        if (Schema::hasColumn('course_details', 'duration_months') && Schema::hasColumn('course_details', 'duration')) {
            DB::table('course_details')
                ->where('duration', 6)
                ->update(['duration' => DB::raw('duration_months')]);
        }

        if (Schema::hasColumn('course_details', 'max_fee') && Schema::hasColumn('course_details', 'fee')) {
            DB::table('course_details')
                ->where('max_fee', 0)
                ->update(['max_fee' => DB::raw('fee')]);
        }
    }

    public function down(): void
    {
        Schema::table('course_details', function (Blueprint $table) {
            if (Schema::hasColumn('course_details', 'course_short_name')) {
                $table->dropColumn('course_short_name');
            }

            if (Schema::hasColumn('course_details', 'image')) {
                $table->dropColumn('image');
            }

            if (Schema::hasColumn('course_details', 'duration')) {
                $table->dropColumn('duration');
            }

            if (Schema::hasColumn('course_details', 'max_fee')) {
                $table->dropColumn('max_fee');
            }

            if (Schema::hasColumn('course_details', 'fee')) {
                $table->dropColumn('fee');
            }
        });
    }
};
