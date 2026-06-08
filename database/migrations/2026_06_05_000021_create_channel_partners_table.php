<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('channel_partners')) {
            Schema::create('channel_partners', function (Blueprint $table) {
                $table->id();
                $table->foreignId('institute_id')->constrained()->cascadeOnDelete();
                $table->string('name', 150);
                $table->string('mobile', 15);
                $table->string('email', 100)->nullable();
                $table->string('whatsapp_no', 15)->nullable();
                $table->string('alternate_mobile', 15)->nullable();
                $table->string('father_name', 150)->nullable();
                $table->date('dob')->nullable();
                $table->enum('gender', ['Male', 'Female', 'Other'])->nullable();
                $table->string('occupation', 120)->nullable();
                $table->string('aadhar_no', 16)->nullable();
                $table->string('pan_no', 10)->nullable();
                $table->text('address')->nullable();
                $table->string('state', 100)->nullable();
                $table->string('district', 100)->nullable();
                $table->string('city', 100)->nullable();
                $table->string('pin_code', 10)->nullable();
                $table->string('notes', 255)->nullable();
                $table->enum('status', ['active', 'inactive'])->default('active');
                $table->timestamps();

                $table->index(['institute_id', 'status']);
                $table->unique(['institute_id', 'mobile']);
            });
        }

        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'channel_partner_id')) {
                $table->foreignId('channel_partner_id')
                    ->nullable()
                    ->after('franchise_id')
                    ->constrained('channel_partners')
                    ->nullOnDelete();
            }
        });

        Schema::table('course_books', function (Blueprint $table) {
            if (!Schema::hasColumn('course_books', 'channel_partner_id')) {
                $table->foreignId('channel_partner_id')
                    ->nullable()
                    ->after('franchise_id')
                    ->constrained('channel_partners')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('course_books', function (Blueprint $table) {
            if (Schema::hasColumn('course_books', 'channel_partner_id')) {
                $table->dropConstrainedForeignId('channel_partner_id');
            }
        });

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'channel_partner_id')) {
                $table->dropConstrainedForeignId('channel_partner_id');
            }
        });

        Schema::dropIfExists('channel_partners');
    }
};
