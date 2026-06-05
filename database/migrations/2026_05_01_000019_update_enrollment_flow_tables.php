<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'enrollment_no')) {
                $table->string('enrollment_no', 50)->nullable()->unique()->after('user_id');
            }
        });

        Schema::table('user_education', function (Blueprint $table) {
            if (!Schema::hasColumn('user_education', 'institute_name')) {
                $table->string('institute_name', 150)->nullable()->after('examination');
            }

            if (!Schema::hasColumn('user_education', 'division')) {
                $table->string('division', 50)->nullable()->after('passing_year');
            }
        });

        Schema::table('fee_collect_details', function (Blueprint $table) {
            if (!Schema::hasColumn('fee_collect_details', 'course_book_id')) {
                $table->foreignId('course_book_id')
                    ->nullable()
                    ->after('user_id')
                    ->constrained('course_books')
                    ->nullOnDelete();
            }
        });

        Schema::table('enrollment_payment_plans', function (Blueprint $table) {
            if (!Schema::hasColumn('enrollment_payment_plans', 'total_fee')) {
                $table->decimal('total_fee', 11, 2)->default(0)->after('plan_type');
            }

            if (!Schema::hasColumn('enrollment_payment_plans', 'required_fee')) {
                $table->decimal('required_fee', 11, 2)->default(0)->after('total_fee');
            }

            if (!Schema::hasColumn('enrollment_payment_plans', 'first_payment_amount')) {
                $table->decimal('first_payment_amount', 11, 2)->default(0)->after('required_fee');
            }

            if (!Schema::hasColumn('enrollment_payment_plans', 'remaining_fee')) {
                $table->decimal('remaining_fee', 11, 2)->default(0)->after('first_payment_amount');
            }
        });

        if (!Schema::hasTable('institute_enrollment_counters')) {
            Schema::create('institute_enrollment_counters', function (Blueprint $table) {
                $table->id();
                $table->foreignId('institute_id')->unique()->constrained()->cascadeOnDelete();
                $table->unsignedBigInteger('last_enrollment_no')->default(0);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('institute_enrollment_counters');

        Schema::table('enrollment_payment_plans', function (Blueprint $table) {
            $drops = [];
            foreach (['total_fee', 'required_fee', 'first_payment_amount', 'remaining_fee'] as $column) {
                if (Schema::hasColumn('enrollment_payment_plans', $column)) {
                    $drops[] = $column;
                }
            }

            if ($drops) {
                $table->dropColumn($drops);
            }
        });

        Schema::table('fee_collect_details', function (Blueprint $table) {
            if (Schema::hasColumn('fee_collect_details', 'course_book_id')) {
                $table->dropConstrainedForeignId('course_book_id');
            }
        });

        Schema::table('user_education', function (Blueprint $table) {
            $drops = [];
            foreach (['institute_name', 'division'] as $column) {
                if (Schema::hasColumn('user_education', $column)) {
                    $drops[] = $column;
                }
            }

            if ($drops) {
                $table->dropColumn($drops);
            }
        });

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'enrollment_no')) {
                $table->dropUnique(['enrollment_no']);
                $table->dropColumn('enrollment_no');
            }
        });
    }
};
