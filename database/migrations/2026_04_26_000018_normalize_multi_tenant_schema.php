<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'user_type')) {
                $table->enum('user_type', ['staff', 'student'])
                    ->nullable()
                    ->after('role');
            }

            if (!Schema::hasColumn('users', 'owner_type')) {
                $table->enum('owner_type', ['institute', 'franchise'])
                    ->default('institute')
                    ->after('franchise_id');
            }

            $table->index(['institute_id', 'franchise_id', 'owner_type'], 'users_owner_scope_idx');
            $table->index(['institute_id', 'user_type'], 'users_type_scope_idx');
        });

        DB::table('users')->whereIn('role', ['staff', 'institute_head', 'franchise_head', 'franchise_staff'])
            ->update(['user_type' => 'staff']);
        DB::table('users')->whereIn('role', ['student', 'franchise_student'])
            ->update(['user_type' => 'student']);
        DB::table('users')->whereNotNull('franchise_id')
            ->update(['owner_type' => 'franchise']);

        Schema::table('user_profiles', function (Blueprint $table) {
            if (!Schema::hasColumn('user_profiles', 'institute_id')) {
                $table->foreignId('institute_id')
                    ->nullable()
                    ->after('user_id')
                    ->constrained()
                    ->nullOnDelete();
            }

            if (!Schema::hasColumn('user_profiles', 'franchise_id')) {
                $table->foreignId('franchise_id')
                    ->nullable()
                    ->after('institute_id')
                    ->constrained('franchises')
                    ->nullOnDelete();
            }

            if (!Schema::hasColumn('user_profiles', 'reg_no')) {
                $table->string('reg_no', 50)->nullable()->after('pin_code');
            }
            if (!Schema::hasColumn('user_profiles', 'admission_no')) {
                $table->string('admission_no', 50)->nullable()->after('reg_no');
            }
            if (!Schema::hasColumn('user_profiles', 'roll_no')) {
                $table->string('roll_no', 50)->nullable()->after('admission_no');
            }
            if (!Schema::hasColumn('user_profiles', 'fee_collect_type')) {
                $table->enum('fee_collect_type', ['MONTHLY', 'PART', 'OTP'])
                    ->nullable()
                    ->after('roll_no');
            }
            if (!Schema::hasColumn('user_profiles', 'monthly_fee')) {
                $table->decimal('monthly_fee', 11, 2)->default(0.00)->after('fee_collect_type');
            }
            if (!Schema::hasColumn('user_profiles', 'daily_late_fee')) {
                $table->decimal('daily_late_fee', 11, 2)->default(0.00)->after('monthly_fee');
            }
            if (!Schema::hasColumn('user_profiles', 'late_fee_count_after')) {
                $table->integer('late_fee_count_after')->default(0)->after('daily_late_fee');
            }
            if (!Schema::hasColumn('user_profiles', 'next_fee_date')) {
                $table->date('next_fee_date')->nullable()->after('late_fee_count_after');
            }
            if (!Schema::hasColumn('user_profiles', 'issue_date')) {
                $table->date('issue_date')->nullable()->after('next_fee_date');
            }
            if (!Schema::hasColumn('user_profiles', 'valid_till_date')) {
                $table->date('valid_till_date')->nullable()->after('issue_date');
            }
            if (!Schema::hasColumn('user_profiles', 'r_date')) {
                $table->date('r_date')->nullable()->after('valid_till_date');
            }

            $table->index(['institute_id', 'franchise_id'], 'user_profiles_owner_scope_idx');
        });

        if (Schema::hasTable('student_profiles')) {
            DB::statement("
                UPDATE user_profiles up
                INNER JOIN users u ON u.id = up.user_id
                LEFT JOIN student_profiles sp ON sp.user_id = up.user_id
                SET
                    up.institute_id = COALESCE(up.institute_id, u.institute_id),
                    up.franchise_id = COALESCE(up.franchise_id, u.franchise_id),
                    up.reg_no = COALESCE(up.reg_no, sp.reg_no),
                    up.fee_collect_type = COALESCE(up.fee_collect_type, sp.fee_collect_type),
                    up.monthly_fee = COALESCE(up.monthly_fee, sp.monthly_fee, 0),
                    up.daily_late_fee = COALESCE(up.daily_late_fee, sp.daily_late_fee, 0),
                    up.late_fee_count_after = COALESCE(up.late_fee_count_after, sp.late_fee_count_after, 0),
                    up.next_fee_date = COALESCE(up.next_fee_date, sp.next_fee_date),
                    up.issue_date = COALESCE(up.issue_date, sp.issue_date),
                    up.valid_till_date = COALESCE(up.valid_till_date, sp.valid_till_date),
                    up.r_date = COALESCE(up.r_date, sp.r_date)
            ");
        } else {
            DB::statement("
                UPDATE user_profiles up
                INNER JOIN users u ON u.id = up.user_id
                SET
                    up.institute_id = COALESCE(up.institute_id, u.institute_id),
                    up.franchise_id = COALESCE(up.franchise_id, u.franchise_id)
            ");
        }

        Schema::table('user_education', function (Blueprint $table) {
            if (!Schema::hasColumn('user_education', 'institute_id')) {
                $table->foreignId('institute_id')
                    ->nullable()
                    ->after('user_id')
                    ->constrained()
                    ->nullOnDelete();
            }

            if (!Schema::hasColumn('user_education', 'franchise_id')) {
                $table->foreignId('franchise_id')
                    ->nullable()
                    ->after('institute_id')
                    ->constrained('franchises')
                    ->nullOnDelete();
            }

            $table->index(['institute_id', 'franchise_id'], 'user_education_owner_scope_idx');
        });

        DB::statement("
            UPDATE user_education ue
            INNER JOIN users u ON u.id = ue.user_id
            SET
                ue.institute_id = COALESCE(ue.institute_id, u.institute_id),
                ue.franchise_id = COALESCE(ue.franchise_id, u.franchise_id)
        ");

        Schema::table('course_books', function (Blueprint $table) {
            if (!Schema::hasColumn('course_books', 'franchise_id')) {
                $table->foreignId('franchise_id')
                    ->nullable()
                    ->after('institute_id')
                    ->constrained('franchises')
                    ->nullOnDelete();
            }

            $table->index(['institute_id', 'franchise_id'], 'course_books_owner_scope_idx');
        });

        Schema::table('student_wallets', function (Blueprint $table) {
            if (!Schema::hasColumn('student_wallets', 'franchise_id')) {
                $table->foreignId('franchise_id')
                    ->nullable()
                    ->after('institute_id')
                    ->constrained('franchises')
                    ->nullOnDelete();
            }

            if (!Schema::hasColumn('student_wallets', 'owner_type')) {
                $table->enum('owner_type', ['institute', 'franchise'])
                    ->default('institute')
                    ->after('franchise_id');
            }

            $table->index(['institute_id', 'franchise_id', 'owner_type'], 'student_wallets_owner_scope_idx');
        });

        DB::statement("
            UPDATE student_wallets sw
            INNER JOIN users u ON u.id = sw.user_id
            SET
                sw.franchise_id = COALESCE(sw.franchise_id, u.franchise_id),
                sw.owner_type = CASE
                    WHEN COALESCE(sw.franchise_id, u.franchise_id) IS NULL THEN 'institute'
                    ELSE 'franchise'
                END
        ");

        Schema::table('student_transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('student_transactions', 'franchise_id')) {
                $table->foreignId('franchise_id')
                    ->nullable()
                    ->after('institute_id')
                    ->constrained('franchises')
                    ->nullOnDelete();
            }

            if (!Schema::hasColumn('student_transactions', 'owner_type')) {
                $table->enum('owner_type', ['institute', 'franchise'])
                    ->default('institute')
                    ->after('franchise_id');
            }

            if (!Schema::hasColumn('student_transactions', 'ref_type')) {
                $table->string('ref_type', 50)->nullable()->after('type');
            }

            if (!Schema::hasColumn('student_transactions', 'ref_id')) {
                $table->unsignedBigInteger('ref_id')->nullable()->after('ref_type');
            }

            $table->index(['institute_id', 'franchise_id', 'owner_type'], 'student_transactions_owner_scope_idx');
            $table->index(['ref_type', 'ref_id'], 'student_transactions_ref_idx');
        });

        DB::statement("
            UPDATE student_transactions st
            INNER JOIN users u ON u.id = st.user_id
            SET
                st.franchise_id = COALESCE(st.franchise_id, u.franchise_id),
                st.owner_type = CASE
                    WHEN COALESCE(st.franchise_id, u.franchise_id) IS NULL THEN 'institute'
                    ELSE 'franchise'
                END
        ");

        Schema::table('fee_collect_details', function (Blueprint $table) {
            if (!Schema::hasColumn('fee_collect_details', 'franchise_id')) {
                $table->foreignId('franchise_id')
                    ->nullable()
                    ->after('institute_id')
                    ->constrained('franchises')
                    ->nullOnDelete();
            }

            if (!Schema::hasColumn('fee_collect_details', 'amount')) {
                $table->decimal('amount', 11, 2)->nullable()->after('utr');
            }

            if (!Schema::hasColumn('fee_collect_details', 'received_by')) {
                $table->unsignedBigInteger('received_by')->nullable()->after('note');
            }

            $table->index(['institute_id', 'franchise_id'], 'fee_collect_owner_scope_idx');
        });

        DB::statement("
            UPDATE fee_collect_details fcd
            INNER JOIN users u ON u.id = fcd.user_id
            SET
                fcd.franchise_id = COALESCE(fcd.franchise_id, u.franchise_id),
                fcd.amount = COALESCE(fcd.amount, fcd.amt),
                fcd.received_by = COALESCE(fcd.received_by, fcd.by_rcv)
        ");

        Schema::table('institute_student_transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('institute_student_transactions', 'franchise_id')) {
                $table->foreignId('franchise_id')
                    ->nullable()
                    ->after('institute_id')
                    ->constrained('franchises')
                    ->nullOnDelete();
            }

            $table->index(['institute_id', 'franchise_id'], 'institute_student_txn_scope_idx');
        });

        Schema::create('institute_franchise_wallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institute_id')->constrained()->cascadeOnDelete();
            $table->foreignId('franchise_id')->constrained()->cascadeOnDelete();
            $table->decimal('balance', 11, 2)->default(0.00);
            $table->timestamps();

            $table->unique(['institute_id', 'franchise_id']);
        });

        Schema::create('institute_franchise_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institute_id')->constrained()->cascadeOnDelete();
            $table->foreignId('franchise_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('student_user_id')->nullable();
            $table->string('txn_no', 30)->nullable();
            $table->text('description');
            $table->decimal('credit', 11, 2)->default(0.00);
            $table->decimal('debit', 11, 2)->default(0.00);
            $table->tinyInteger('type')->comment('1=opening, 2=recharge, 3=bonus, 4=student_fee_credit, 5=admission_charge, 6=refund, 7=manual');
            $table->string('payment_mode', 20)->nullable();
            $table->string('utr', 80)->nullable();
            $table->decimal('op_bal', 11, 2)->default(0.00);
            $table->decimal('cl_bal', 11, 2)->default(0.00);
            $table->date('date');
            $table->dateTime('c_date');
            $table->unsignedBigInteger('by_user_id')->nullable();
            $table->timestamps();

            $table->index(['institute_id', 'franchise_id', 'date'], 'institute_franchise_txn_scope_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('institute_franchise_transactions');
        Schema::dropIfExists('institute_franchise_wallets');

        Schema::table('institute_student_transactions', function (Blueprint $table) {
            if (Schema::hasColumn('institute_student_transactions', 'franchise_id')) {
                $table->dropIndex('institute_student_txn_scope_idx');
                $table->dropConstrainedForeignId('franchise_id');
            }
        });

        Schema::table('fee_collect_details', function (Blueprint $table) {
            $table->dropIndex('fee_collect_owner_scope_idx');

            if (Schema::hasColumn('fee_collect_details', 'franchise_id')) {
                $table->dropConstrainedForeignId('franchise_id');
            }

            if (Schema::hasColumn('fee_collect_details', 'amount')) {
                $table->dropColumn('amount');
            }

            if (Schema::hasColumn('fee_collect_details', 'received_by')) {
                $table->dropColumn('received_by');
            }
        });

        Schema::table('student_transactions', function (Blueprint $table) {
            $table->dropIndex('student_transactions_owner_scope_idx');
            $table->dropIndex('student_transactions_ref_idx');

            if (Schema::hasColumn('student_transactions', 'franchise_id')) {
                $table->dropConstrainedForeignId('franchise_id');
            }

            if (Schema::hasColumn('student_transactions', 'owner_type')) {
                $table->dropColumn('owner_type');
            }

            if (Schema::hasColumn('student_transactions', 'ref_type')) {
                $table->dropColumn('ref_type');
            }

            if (Schema::hasColumn('student_transactions', 'ref_id')) {
                $table->dropColumn('ref_id');
            }
        });

        Schema::table('student_wallets', function (Blueprint $table) {
            $table->dropIndex('student_wallets_owner_scope_idx');

            if (Schema::hasColumn('student_wallets', 'franchise_id')) {
                $table->dropConstrainedForeignId('franchise_id');
            }

            if (Schema::hasColumn('student_wallets', 'owner_type')) {
                $table->dropColumn('owner_type');
            }
        });

        Schema::table('course_books', function (Blueprint $table) {
            $table->dropIndex('course_books_owner_scope_idx');

            if (Schema::hasColumn('course_books', 'franchise_id')) {
                $table->dropConstrainedForeignId('franchise_id');
            }
        });

        Schema::table('user_education', function (Blueprint $table) {
            $table->dropIndex('user_education_owner_scope_idx');

            if (Schema::hasColumn('user_education', 'franchise_id')) {
                $table->dropConstrainedForeignId('franchise_id');
            }

            if (Schema::hasColumn('user_education', 'institute_id')) {
                $table->dropConstrainedForeignId('institute_id');
            }
        });

        Schema::table('user_profiles', function (Blueprint $table) {
            $table->dropIndex('user_profiles_owner_scope_idx');

            if (Schema::hasColumn('user_profiles', 'franchise_id')) {
                $table->dropConstrainedForeignId('franchise_id');
            }

            if (Schema::hasColumn('user_profiles', 'institute_id')) {
                $table->dropConstrainedForeignId('institute_id');
            }

            $table->dropColumn([
                'reg_no',
                'admission_no',
                'roll_no',
                'fee_collect_type',
                'monthly_fee',
                'daily_late_fee',
                'late_fee_count_after',
                'next_fee_date',
                'issue_date',
                'valid_till_date',
                'r_date',
            ]);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('users_owner_scope_idx');
            $table->dropIndex('users_type_scope_idx');

            if (Schema::hasColumn('users', 'owner_type')) {
                $table->dropColumn('owner_type');
            }

            if (Schema::hasColumn('users', 'user_type')) {
                $table->dropColumn('user_type');
            }
        });
    }
};
