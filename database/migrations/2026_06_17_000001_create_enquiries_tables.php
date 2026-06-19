<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('enquiries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institute_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_id')->nullable()->constrained('course_details')->nullOnDelete();
            $table->foreignId('converted_to_course_book_id')->nullable()->constrained('course_books')->nullOnDelete();
            $table->string('name', 150);
            $table->string('mobile', 15);
            $table->string('email', 150)->nullable();
            $table->enum('source', ['WALK_IN', 'PHONE', 'ONLINE', 'REFERENCE'])->default('WALK_IN');
            $table->enum('status', ['OPEN', 'CONVERTED', 'LOST'])->default('OPEN');
            $table->text('notes')->nullable();
            $table->date('next_followup_date')->nullable();
            $table->string('lost_reason', 255)->nullable();
            $table->timestamps();

            $table->index(['institute_id', 'status']);
            $table->index(['institute_id', 'mobile']);
        });

        Schema::create('enquiry_followups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enquiry_id')->constrained()->cascadeOnDelete();
            $table->text('notes');
            $table->enum('outcome', ['INTERESTED', 'NOT_INTERESTED', 'CALLBACK', 'NO_RESPONSE'])->default('CALLBACK');
            $table->date('next_followup_date')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enquiry_followups');
        Schema::dropIfExists('enquiries');
    }
};
