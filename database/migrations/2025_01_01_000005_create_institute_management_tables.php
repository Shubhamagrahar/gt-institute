<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // ── 1. STUDENT PROFILES (personal info) ──────────────────────────────
        Schema::create('student_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('institute_id')->constrained()->cascadeOnDelete();
            $table->string('reg_no', 50)->nullable();            // e.g. TVSI/2025/001
            $table->string('name', 100);                         // full name
            $table->string('photo', 300)->default('images/user.png');
            $table->date('dob')->nullable();
            $table->enum('gender', ['Male', 'Female', 'Other'])->nullable();
            $table->string('father_name', 100)->nullable();
            $table->string('mother_name', 100)->nullable();
            $table->string('father_mobile', 15)->nullable();
            $table->string('w_mob', 15)->nullable();             // whatsapp number
            $table->string('state', 60)->nullable();
            $table->string('pin_code', 10)->nullable();
            $table->text('address')->nullable();                 // current address
            $table->text('permanent_address')->nullable();
            // Fee settings
            $table->enum('fee_collect_type', ['MONTHLY', 'PART', 'OTP'])->default('OTP');
            $table->decimal('monthly_fee', 11, 2)->default(0.00);
            $table->decimal('daily_late_fee', 11, 2)->default(0.00);
            $table->integer('late_fee_count_after')->default(0);
            $table->date('next_fee_date')->nullable();
            $table->date('issue_date')->nullable();
            $table->date('valid_till_date')->nullable();
            $table->date('r_date')->nullable();                  // registration date
            $table->timestamps();

            $table->index('institute_id');
        });

        // ── 2. STUDENT EDUCATION (academic background) ────────────────────────
        Schema::create('student_education', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('level', 50);                         // e.g. 10th, 12th, Graduation
            $table->string('board_university', 100)->nullable();
            $table->string('passing_year', 10)->nullable();
            $table->string('percentage', 10)->nullable();
            $table->string('subjects', 200)->nullable();
            $table->timestamps();
        });

        // ── 3. STAFF PROFILES ─────────────────────────────────────────────────
        Schema::create('staff_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('institute_id')->constrained()->cascadeOnDelete();
            $table->string('name', 100);
            $table->string('photo', 300)->default('images/user.png');
            $table->string('designation', 80)->nullable();
            $table->string('qualification', 100)->nullable();
            $table->date('joining_date')->nullable();
            $table->decimal('salary', 11, 2)->default(0.00);
            $table->string('address', 300)->nullable();
            $table->timestamps();

            $table->index('institute_id');
        });

        // ── 4. COURSE TYPES ───────────────────────────────────────────────────
        Schema::create('course_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institute_id')->constrained()->cascadeOnDelete();
            $table->string('name', 100);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });

        // ── 5. COURSE DETAILS ─────────────────────────────────────────────────
        Schema::create('course_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institute_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_type_id')->nullable()->constrained('course_types')->nullOnDelete();
            $table->string('course_code', 20)->nullable();
            $table->string('name', 150);
            $table->string('short_name', 50)->nullable();
            $table->integer('duration_months')->default(6);
            
            $table->text('description')->nullable();
            $table->string('img', 300)->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();

            $table->index('institute_id');
        });

        // ── 6. BATCHES ────────────────────────────────────────────────────────
        Schema::create('batch_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institute_id')->constrained()->cascadeOnDelete();
            $table->string('name', 100);
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });

        // ── 7. COURSE ENROLLMENT (course_books) ──────────────────────────────
        Schema::create('course_books', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institute_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_id')->constrained('course_details')->cascadeOnDelete();
            $table->foreignId('batch_id')->nullable()->constrained('batch_details')->nullOnDelete();
            $table->string('enrollment_no', 30)->nullable();     // e.g. BIMT0001
            $table->decimal('fee', 11, 2);                       // fee for this enrollment
            $table->date('book_date');
            $table->date('start_date')->nullable();
            $table->date('complete_date')->nullable();
            $table->enum('status', ['OPEN', 'RUN', 'CLOSE', 'CANCEL'])->default('OPEN');
            $table->timestamps();

            $table->index('institute_id');
            $table->index('user_id');
        });

        // ── 8. STUDENT WALLETS ────────────────────────────────────────────────
        Schema::create('wallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete()->unique();
            $table->decimal('main_b', 11, 2)->default(0.00);    // advance/wallet balance
            $table->timestamps();
        });

        // ── 9. TRANSACTIONS (student ledger) ──────────────────────────────────
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('institute_id')->constrained()->cascadeOnDelete();
            $table->text('des');
            $table->decimal('credit', 11, 2)->default(0.00);
            $table->decimal('debit', 11, 2)->default(0.00);
            $table->tinyInteger('type')->comment('1=Fee, 2=Direct, 3=Refund, 4=Wallet');
            $table->date('date');
            $table->dateTime('c_date');
            $table->decimal('op_bal', 11, 2)->default(0.00);
            $table->decimal('cl_bal', 11, 2)->default(0.00);
            $table->string('invoice_no', 30)->nullable();
            $table->unsignedBigInteger('by_userid');
            $table->timestamps();

            $table->index('institute_id');
            $table->index('user_id');
        });

        // ── 10. FEE COLLECTION ────────────────────────────────────────────────
        Schema::create('fee_collect_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('institute_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_book_id')->nullable()->constrained('course_books')->nullOnDelete();
            $table->string('invoice_no', 30)->unique()->nullable();
            $table->enum('payment_mode', ['CASH', 'UPI', 'NEFT', 'IMPS', 'CHEQUE'])->default('CASH');
            $table->string('utr', 80)->nullable();               // transaction ref
            $table->decimal('amt', 11, 2);
            $table->date('date');
            $table->text('note')->nullable();
            $table->unsignedBigInteger('by_rcv');               // received by staff id
            $table->timestamps();

            $table->index('institute_id');
            $table->index('user_id');
        });

        // ── 11. STUDENT CERTIFICATES ──────────────────────────────────────────
        Schema::create('student_certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institute_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_id')->constrained('course_details')->cascadeOnDelete();
            $table->string('certificate_no', 50)->unique();
            $table->string('enrollment_no', 50)->nullable();
            $table->string('reg_no', 50)->nullable();
            $table->string('photo', 300)->nullable();
            $table->date('start_date');
            $table->date('complete_date');
            $table->date('issue_date');
            $table->enum('status', ['DONE', 'VERIFY'])->default('DONE');
            $table->unsignedBigInteger('created_by');
            $table->timestamps();

            $table->index('institute_id');
        });

        // ── 12. ATTENDANCE STUDENT ────────────────────────────────────────────
        Schema::create('attendance_students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institute_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('batch_id')->nullable()->constrained('batch_details')->nullOnDelete();
            $table->date('date');
            $table->time('in_time')->nullable();
            $table->time('out_time')->nullable();
            $table->enum('status', ['P', 'A', 'L'])->default('A'); // Present/Absent/Leave
            $table->unsignedBigInteger('created_by');
            $table->timestamps();

            $table->unique(['user_id', 'date', 'batch_id']);
            $table->index('institute_id');
        });

        // ── 13. ATTENDANCE STAFF ──────────────────────────────────────────────
        Schema::create('attendance_staffs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institute_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->time('in_time')->nullable();
            $table->time('out_time')->nullable();
            $table->enum('status', ['P', 'A', 'L'])->default('A');
            $table->unsignedBigInteger('created_by');
            $table->timestamps();

            $table->unique(['user_id', 'date']);
            $table->index('institute_id');
        });

        // ── 14. ENQUIRY ───────────────────────────────────────────────────────
        Schema::create('enquiry_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institute_id')->constrained()->cascadeOnDelete();
            $table->string('name', 100);
            $table->string('mobile', 15);
            $table->string('email', 100)->nullable();
            $table->string('course_interest', 150)->nullable();
            $table->text('note')->nullable();
            $table->enum('status', ['new', 'follow_up', 'converted', 'not_interested'])->default('new');
            $table->date('enquiry_date');
            $table->date('follow_up_date')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();

            $table->index('institute_id');
        });

        // ── 15. SESSION DETAILS (Jan-June, July-Dec etc) ──────────────────────
        Schema::create('session_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institute_id')->constrained()->cascadeOnDelete();
            $table->string('name', 50);                          // e.g. JAN-JUNE (2025-26)
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();

            $table->index('institute_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('session_details');
        Schema::dropIfExists('enquiry_details');
        Schema::dropIfExists('attendance_staffs');
        Schema::dropIfExists('attendance_students');
        Schema::dropIfExists('student_certificates');
        Schema::dropIfExists('fee_collect_details');
        Schema::dropIfExists('transactions');
        Schema::dropIfExists('wallets');
        Schema::dropIfExists('course_books');
        Schema::dropIfExists('batch_details');
        Schema::dropIfExists('course_details');
        Schema::dropIfExists('course_types');
        Schema::dropIfExists('staff_profiles');
        Schema::dropIfExists('student_education');
        Schema::dropIfExists('student_profiles');
    }
};
