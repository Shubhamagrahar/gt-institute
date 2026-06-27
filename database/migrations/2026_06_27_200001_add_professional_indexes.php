<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Professional indexing migration.
 *
 * Covers every business table with:
 *  - Composite (institute_id, status / date / ...) indexes for tenant-scoped filters
 *  - Foreign-key + filter composites for the most common JOIN + WHERE patterns
 *  - Date-range indexes for reporting/filtering
 *  - Lookup indexes for identifier columns (reg_no, admission_no, etc.)
 *
 * Columns already indexed by FK constrained() or previous migrations are
 * NOT duplicated here — only net-new indexes are added.
 */
return new class extends Migration {

    public function up(): void
    {
        // ── institute_subscriptions ───────────────────────────────────────────
        Schema::table('institute_subscriptions', function (Blueprint $table) {
            $table->index(['institute_id', 'status'],   'inst_subs_status_idx');
            $table->index(['institute_id', 'end_date'], 'inst_subs_expiry_idx');
        });

        // ── institute_transactions ────────────────────────────────────────────
        Schema::table('institute_transactions', function (Blueprint $table) {
            $table->index(['institute_id', 'type'], 'inst_txn_type_idx');
            $table->index(['institute_id', 'date'], 'inst_txn_date_idx');
        });

        // ── course_types ──────────────────────────────────────────────────────
        Schema::table('course_types', function (Blueprint $table) {
            $table->index(['institute_id', 'status'], 'course_types_status_idx');
        });

        // ── course_details ────────────────────────────────────────────────────
        Schema::table('course_details', function (Blueprint $table) {
            $table->index(['institute_id', 'status'],         'course_details_status_idx');
            $table->index(['institute_id', 'course_type_id'], 'course_details_type_idx');
        });

        // ── batch_details ─────────────────────────────────────────────────────
        Schema::table('batch_details', function (Blueprint $table) {
            $table->index(['institute_id', 'status'],      'batch_details_status_idx');
            $table->index(['institute_id', 'franchise_id'],'batch_details_franchise_idx');
        });

        // ── subjects ──────────────────────────────────────────────────────────
        Schema::table('subjects', function (Blueprint $table) {
            $table->index(['institute_id', 'status'], 'subjects_status_idx');
        });

        // ── fee_types ─────────────────────────────────────────────────────────
        Schema::table('fee_types', function (Blueprint $table) {
            $table->index(['institute_id', 'is_active'], 'fee_types_active_idx');
        });

        // ── payment_plan_types ────────────────────────────────────────────────
        Schema::table('payment_plan_types', function (Blueprint $table) {
            $table->index(['institute_id', 'is_active'], 'payment_plan_types_active_idx');
        });

        // ── course_books ──────────────────────────────────────────────────────
        // (enrollment_no UNIQUE, institute_id + user_id + session_id already indexed)
        Schema::table('course_books', function (Blueprint $table) {
            $table->index(['institute_id', 'status'],                    'course_books_status_idx');
            $table->index(['institute_id', 'course_id'],                 'course_books_course_idx');
            $table->index(['institute_id', 'session_id', 'status'],      'course_books_session_status_idx');
            $table->index(['user_id', 'status'],                         'course_books_user_status_idx');
        });

        // ── enrollment_fee_snapshots ──────────────────────────────────────────
        Schema::table('enrollment_fee_snapshots', function (Blueprint $table) {
            $table->index('institute_id', 'enroll_snapshots_inst_idx');
        });

        // ── fee_collect_details ───────────────────────────────────────────────
        // (invoice_no UNIQUE, institute_id + user_id already indexed)
        Schema::table('fee_collect_details', function (Blueprint $table) {
            $table->index(['institute_id', 'date'],            'fee_collect_date_idx');
            $table->index(['institute_id', 'cancelled_at'],    'fee_collect_cancel_idx');
        });

        // ── student_certificates ──────────────────────────────────────────────
        Schema::table('student_certificates', function (Blueprint $table) {
            $table->index(['institute_id', 'user_id'], 'student_certs_user_idx');
            $table->index(['institute_id', 'status'],  'student_certs_status_idx');
        });

        // ── attendance_students ───────────────────────────────────────────────
        // (UNIQUE on (user_id, date, batch_id) and institute_id already exist)
        Schema::table('attendance_students', function (Blueprint $table) {
            $table->index(['institute_id', 'date'],          'attend_students_date_idx');
            $table->index(['institute_id', 'batch_id', 'date'], 'attend_students_batch_date_idx');
        });

        // ── attendance_staffs ─────────────────────────────────────────────────
        Schema::table('attendance_staffs', function (Blueprint $table) {
            $table->index(['institute_id', 'date'], 'attend_staffs_date_idx');
        });

        // ── institute_sessions ────────────────────────────────────────────────
        Schema::table('institute_sessions', function (Blueprint $table) {
            $table->index(['institute_id', 'is_active'], 'inst_sessions_active_idx');
        });

        // ── user_profiles ─────────────────────────────────────────────────────
        // ((institute_id, franchise_id) scope index already exists)
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->index(['institute_id', 'reg_no'],       'user_profiles_reg_no_idx');
            $table->index(['institute_id', 'admission_no'], 'user_profiles_admission_idx');
        });

        // ── enquiries ─────────────────────────────────────────────────────────
        // ((institute_id, status) and (institute_id, mobile) already exist)
        Schema::table('enquiries', function (Blueprint $table) {
            $table->index(['institute_id', 'next_followup_date'], 'enquiries_followup_idx');
            $table->index(['institute_id', 'source'],             'enquiries_source_idx');
        });

        // ── enquiry_followups ─────────────────────────────────────────────────
        Schema::table('enquiry_followups', function (Blueprint $table) {
            $table->index('next_followup_date',            'enq_followups_date_idx');
            $table->index(['enquiry_id', 'created_at'],    'enq_followups_scope_idx');
        });

        // ── franchise_levels ──────────────────────────────────────────────────
        Schema::table('franchise_levels', function (Blueprint $table) {
            $table->index(['institute_id', 'status'], 'franchise_levels_status_idx');
        });

        // ── franchise_transactions ────────────────────────────────────────────
        // ((franchise_id, date) already exists)
        Schema::table('franchise_transactions', function (Blueprint $table) {
            $table->index(['institute_id', 'date'],                'frn_txn_inst_date_idx');
            $table->index(['institute_id', 'franchise_id', 'date'],'frn_txn_scope_idx');
        });

        // ── franchise_fee_collections ─────────────────────────────────────────
        // ((franchise_id, date) and UNIQUE invoice_no already exist)
        Schema::table('franchise_fee_collections', function (Blueprint $table) {
            $table->index(['franchise_id', 'cancelled_at'],  'frn_fee_col_cancel_idx');
            $table->index(['institute_id', 'franchise_id'],  'frn_fee_col_scope_idx');
        });

        // ── franchise_institute_transactions ──────────────────────────────────
        // ((franchise_id, institute_id) already exists)
        Schema::table('franchise_institute_transactions', function (Blueprint $table) {
            $table->index(['franchise_id', 'date'],  'fi_txn_frn_date_idx');
            $table->index(['institute_id', 'date'],  'fi_txn_inst_date_idx');
            $table->index(['institute_id', 'type'],  'fi_txn_type_idx');
        });

        // ── franchise_pay_details ─────────────────────────────────────────────
        // ((franchise_id, institute_id) and UNIQUE invoice_no already exist)
        Schema::table('franchise_pay_details', function (Blueprint $table) {
            $table->index(['franchise_id', 'date'],        'frn_pay_date_idx');
            $table->index(['franchise_id', 'cancelled_at'],'frn_pay_cancel_idx');
        });

        // ── franchise_fee_structures ──────────────────────────────────────────
        // ((franchise_id, course_id) and UNIQUE (franchise_id, course_id, fee_type_id) exist)
        Schema::table('franchise_fee_structures', function (Blueprint $table) {
            $table->index('institute_id', 'frn_fee_structs_inst_idx');
        });

        // ── districts ─────────────────────────────────────────────────────────
        Schema::table('districts', function (Blueprint $table) {
            $table->index(['state_id', 'name'], 'districts_state_name_idx');
        });

        // ── staff_roles ───────────────────────────────────────────────────────
        Schema::table('staff_roles', function (Blueprint $table) {
            $table->index(['institute_id', 'status'], 'staff_roles_status_idx');
        });

        // ── salary_records ────────────────────────────────────────────────────
        // (UNIQUE (staff_user_id, month) and (institute_id, month) already exist)
        Schema::table('salary_records', function (Blueprint $table) {
            $table->index(['institute_id', 'status'],         'salary_records_status_idx');
            $table->index(['institute_id', 'staff_user_id'],  'salary_records_staff_idx');
        });

        // ── salary_transactions ───────────────────────────────────────────────
        Schema::table('salary_transactions', function (Blueprint $table) {
            $table->index(['institute_id', 'payment_date'], 'salary_txn_date_idx');
        });

        // ── login_otps ────────────────────────────────────────────────────────
        // (email already indexed)
        Schema::table('login_otps', function (Blueprint $table) {
            $table->index(['email', 'guard'], 'login_otps_guard_idx');
            $table->index('expires_at',       'login_otps_expiry_idx');
        });

        // ── level_course_charges ──────────────────────────────────────────────
        // (UNIQUE (franchise_level_id, course_id) already exists)
        Schema::table('level_course_charges', function (Blueprint $table) {
            $table->index(['institute_id', 'franchise_level_id', 'status'], 'lcc_scope_status_idx');
        });

        // ── franchise_course_charges ──────────────────────────────────────────
        // (UNIQUE (franchise_id, course_id) and (franchise_id, institute_id) already exist)
        Schema::table('franchise_course_charges', function (Blueprint $table) {
            $table->index(['franchise_id', 'enabled'], 'fcc_enabled_idx');
        });

        // ── student_transactions ──────────────────────────────────────────────
        // (user_id, institute_id, scope, ref indexes already exist)
        Schema::table('student_transactions', function (Blueprint $table) {
            $table->index(['institute_id', 'date'], 'student_txn_date_idx');
            $table->index(['user_id', 'date'],      'student_txn_user_date_idx');
        });

        // ── institute_student_transactions ────────────────────────────────────
        // (institute_id and (institute_id, franchise_id) already exist)
        Schema::table('institute_student_transactions', function (Blueprint $table) {
            $table->index(['institute_id', 'type'], 'inst_student_txn_type_idx');
            $table->index(['institute_id', 'date'], 'inst_student_txn_date_idx');
        });
    }

    public function down(): void
    {
        Schema::table('institute_subscriptions',         fn($t) => $t->dropIndex(['inst_subs_status_idx',                    'inst_subs_expiry_idx']));
        Schema::table('institute_transactions',          fn($t) => $t->dropIndex(['inst_txn_type_idx',                       'inst_txn_date_idx']));
        Schema::table('course_types',                    fn($t) => $t->dropIndex('course_types_status_idx'));
        Schema::table('course_details',                  fn($t) => $t->dropIndex(['course_details_status_idx',               'course_details_type_idx']));
        Schema::table('batch_details',                   fn($t) => $t->dropIndex(['batch_details_status_idx',                'batch_details_franchise_idx']));
        Schema::table('subjects',                        fn($t) => $t->dropIndex('subjects_status_idx'));
        Schema::table('fee_types',                       fn($t) => $t->dropIndex('fee_types_active_idx'));
        Schema::table('payment_plan_types',              fn($t) => $t->dropIndex('payment_plan_types_active_idx'));
        Schema::table('course_books',                    fn($t) => $t->dropIndex(['course_books_status_idx',                 'course_books_course_idx', 'course_books_session_status_idx', 'course_books_user_status_idx']));
        Schema::table('enrollment_fee_snapshots',        fn($t) => $t->dropIndex('enroll_snapshots_inst_idx'));
        Schema::table('fee_collect_details',             fn($t) => $t->dropIndex(['fee_collect_date_idx',                   'fee_collect_cancel_idx']));
        Schema::table('student_certificates',            fn($t) => $t->dropIndex(['student_certs_user_idx',                 'student_certs_status_idx']));
        Schema::table('attendance_students',             fn($t) => $t->dropIndex(['attend_students_date_idx',               'attend_students_batch_date_idx']));
        Schema::table('attendance_staffs',               fn($t) => $t->dropIndex('attend_staffs_date_idx'));
        Schema::table('institute_sessions',              fn($t) => $t->dropIndex('inst_sessions_active_idx'));
        Schema::table('user_profiles',                   fn($t) => $t->dropIndex(['user_profiles_reg_no_idx',               'user_profiles_admission_idx']));
        Schema::table('enquiries',                       fn($t) => $t->dropIndex(['enquiries_followup_idx',                 'enquiries_source_idx']));
        Schema::table('enquiry_followups',               fn($t) => $t->dropIndex(['enq_followups_date_idx',                 'enq_followups_scope_idx']));
        Schema::table('franchise_levels',                fn($t) => $t->dropIndex('franchise_levels_status_idx'));
        Schema::table('franchise_transactions',          fn($t) => $t->dropIndex(['frn_txn_inst_date_idx',                  'frn_txn_scope_idx']));
        Schema::table('franchise_fee_collections',       fn($t) => $t->dropIndex(['frn_fee_col_cancel_idx',                 'frn_fee_col_scope_idx']));
        Schema::table('franchise_institute_transactions',fn($t) => $t->dropIndex(['fi_txn_frn_date_idx',                    'fi_txn_inst_date_idx', 'fi_txn_type_idx']));
        Schema::table('franchise_pay_details',           fn($t) => $t->dropIndex(['frn_pay_date_idx',                       'frn_pay_cancel_idx']));
        Schema::table('franchise_fee_structures',        fn($t) => $t->dropIndex('frn_fee_structs_inst_idx'));
        Schema::table('districts',                       fn($t) => $t->dropIndex('districts_state_name_idx'));
        Schema::table('staff_roles',                     fn($t) => $t->dropIndex('staff_roles_status_idx'));
        Schema::table('salary_records',                  fn($t) => $t->dropIndex(['salary_records_status_idx',              'salary_records_staff_idx']));
        Schema::table('salary_transactions',             fn($t) => $t->dropIndex('salary_txn_date_idx'));
        Schema::table('login_otps',                      fn($t) => $t->dropIndex(['login_otps_guard_idx',                   'login_otps_expiry_idx']));
        Schema::table('level_course_charges',            fn($t) => $t->dropIndex('lcc_scope_status_idx'));
        Schema::table('franchise_course_charges',        fn($t) => $t->dropIndex('fcc_enabled_idx'));
        Schema::table('student_transactions',            fn($t) => $t->dropIndex(['student_txn_date_idx',                   'student_txn_user_date_idx']));
        Schema::table('institute_student_transactions',  fn($t) => $t->dropIndex(['inst_student_txn_type_idx',              'inst_student_txn_date_idx']));
    }
};
