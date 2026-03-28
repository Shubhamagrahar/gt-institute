<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Student Profiles
        Schema::create('student_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('institute_id')->constrained()->cascadeOnDelete();
            $table->string('reg_no', 50)->nullable();
            $table->string('father_name', 100)->nullable();
            $table->string('mother_name', 100)->nullable();
            $table->string('father_mobile', 15)->nullable();
            $table->date('dob')->nullable();
            $table->enum('gender', ['Male', 'Female', 'Other'])->nullable();
            $table->string('w_mob', 15)->nullable();
            $table->string('qualification', 50)->nullable();
            $table->string('state', 60)->nullable();
            $table->string('pin_code', 10)->nullable();
            $table->text('full_add')->nullable();
            $table->text('full_add_permanent')->nullable();
            $table->string('photo', 300)->default('images/user.png');
            $table->enum('fee_collect_type', ['MONTHLY', 'PART', 'OTP'])->default('OTP');
            $table->decimal('monthly_fee', 11, 2)->default(0.00);
            $table->decimal('daily_late_fee', 11, 2)->default(0.00);
            $table->integer('late_fee_count_after')->default(0);
            $table->date('next_fee_date')->nullable();
            $table->date('issue_date')->nullable();
            $table->date('valid_till_date')->nullable();
            $table->date('r_date')->nullable();
            $table->timestamps();
        });

        // Staff Profiles
        Schema::create('staff_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('institute_id')->constrained()->cascadeOnDelete();
            $table->string('designation', 80)->nullable();
            $table->date('joining_date')->nullable();
            $table->decimal('salary', 11, 2)->default(0.00);
            $table->string('photo', 300)->default('images/user.png');
            $table->timestamps();
        });

        // Course Types
        Schema::create('course_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institute_id')->constrained()->cascadeOnDelete();
            $table->string('name', 100);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });

        // Course Details
        Schema::create('course_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institute_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_type_id')->nullable()->constrained('course_types')->nullOnDelete();
            $table->string('name', 150);
            $table->string('short_name', 50)->nullable();
            $table->integer('duration_months')->default(6);
            $table->decimal('fee', 11, 2)->default(0.00);
            $table->text('description')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });

        // Batches
        Schema::create('batch_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institute_id')->constrained()->cascadeOnDelete();
            $table->string('name', 100);
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });

        // Course Enrollment (course_book)
        Schema::create('course_books', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institute_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_id')->constrained('course_details')->cascadeOnDelete();
            $table->foreignId('batch_id')->nullable()->constrained('batch_details')->nullOnDelete();
            $table->decimal('fee', 11, 2);
            $table->date('book_date');
            $table->date('start_date')->nullable();
            $table->date('complete_date')->nullable();
            $table->enum('status', ['OPEN', 'RUN', 'CLOSE', 'CANCEL'])->default('OPEN');
            $table->timestamps();
        });

        // Wallets (per student/user)
        Schema::create('wallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete()->unique();
            $table->decimal('main_b', 11, 2)->default(0.00);
            $table->timestamps();
        });

        // Transactions (student ledger)
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('institute_id')->constrained()->cascadeOnDelete();
            $table->text('des');
            $table->decimal('credit', 11, 2)->default(0.00);
            $table->decimal('debit', 11, 2)->default(0.00);
            $table->tinyInteger('type')->comment('1=Fee, 2=Direct, 3=Refund');
            $table->date('date');
            $table->dateTime('c_date');
            $table->decimal('op_bal', 11, 2);
            $table->decimal('cl_bal', 11, 2);
            $table->unsignedBigInteger('by_userid');
            $table->timestamps();
        });

        // Fee Collect Details
        Schema::create('fee_collect_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('institute_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_book_id')->nullable()->constrained('course_books')->nullOnDelete();
            $table->string('invoice_no', 30)->nullable();
            $table->enum('payment_mode', ['CASH', 'UPI', 'NEFT', 'IMPS', 'CHEQUE'])->default('CASH');
            $table->string('utr', 80)->nullable();
            $table->decimal('amt', 11, 2);
            $table->date('date');
            $table->unsignedBigInteger('by_rcv');
            $table->timestamps();
        });

        // Attendance Student
        Schema::create('attendance_students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institute_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('batch_id')->nullable()->constrained('batch_details')->nullOnDelete();
            $table->date('date');
            $table->time('in_time')->nullable();
            $table->time('out_time')->nullable();
            $table->enum('status', ['P', 'A'])->default('A');
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
        });

        // Attendance Staff
        Schema::create('attendance_staffs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institute_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->time('in_time')->nullable();
            $table->time('out_time')->nullable();
            $table->enum('status', ['P', 'A'])->default('A');
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
        });

        // Enquiry Details
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
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enquiry_details');
        Schema::dropIfExists('attendance_staffs');
        Schema::dropIfExists('attendance_students');
        Schema::dropIfExists('fee_collect_details');
        Schema::dropIfExists('transactions');
        Schema::dropIfExists('wallets');
        Schema::dropIfExists('course_books');
        Schema::dropIfExists('batch_details');
        Schema::dropIfExists('course_details');
        Schema::dropIfExists('course_types');
        Schema::dropIfExists('staff_profiles');
        Schema::dropIfExists('student_profiles');
    }
};
