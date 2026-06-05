<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('admission_form_fields')) {
            Schema::table('admission_form_fields', function (Blueprint $table) {
                if (! Schema::hasColumn('admission_form_fields', 'quick_is_active')) {
                    $table->boolean('quick_is_active')->default(false)->after('is_active');
                }

                if (! Schema::hasColumn('admission_form_fields', 'quick_is_required')) {
                    $table->boolean('quick_is_required')->default(false)->after('quick_is_active');
                }
            });

            DB::table('admission_form_fields')
                ->where('is_active', true)
                ->whereIn('field_key', ['email', 'gender', 'qualification', 'address', 'state'])
                ->update([
                    'quick_is_active' => true,
                ]);
        }

        if (Schema::hasTable('course_books')) {
            Schema::table('course_books', function (Blueprint $table) {
                if (! Schema::hasColumn('course_books', 'booking_mode')) {
                    $table->string('booking_mode', 20)->default('full')->after('status');
                }

                if (! Schema::hasColumn('course_books', 'profile_completed_at')) {
                    $table->timestamp('profile_completed_at')->nullable()->after('booking_mode');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('course_books')) {
            Schema::table('course_books', function (Blueprint $table) {
                if (Schema::hasColumn('course_books', 'profile_completed_at')) {
                    $table->dropColumn('profile_completed_at');
                }

                if (Schema::hasColumn('course_books', 'booking_mode')) {
                    $table->dropColumn('booking_mode');
                }
            });
        }

        if (Schema::hasTable('admission_form_fields')) {
            Schema::table('admission_form_fields', function (Blueprint $table) {
                if (Schema::hasColumn('admission_form_fields', 'quick_is_required')) {
                    $table->dropColumn('quick_is_required');
                }

                if (Schema::hasColumn('admission_form_fields', 'quick_is_active')) {
                    $table->dropColumn('quick_is_active');
                }
            });
        }
    }
};
