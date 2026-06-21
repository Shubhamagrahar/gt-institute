<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('staff_profiles', function (Blueprint $table) {
            // Only adding columns that don't already exist
            // Already exist: id, user_id, institute_id, staff_role_id, custom_permissions,
            //                name, photo, designation, father_name, dob, gender,
            //                blood_group, qualification, joining_date, salary, address

            $table->unsignedTinyInteger('experience_years')->default(0)->after('qualification');
            $table->string('department', 80)->nullable()->after('experience_years');
            $table->string('whatsapp', 15)->nullable()->after('designation');
            $table->string('city', 80)->nullable()->after('address');
            $table->string('state', 80)->nullable()->after('city');
            $table->string('pin', 10)->nullable()->after('state');
            $table->enum('salary_type', ['monthly','daily','hourly'])->default('monthly')->after('salary');
            $table->string('aadhar_no', 12)->nullable()->after('salary_type');
            $table->string('pan_no', 10)->nullable()->after('aadhar_no');
            $table->string('bank_name', 100)->nullable()->after('pan_no');
            $table->string('account_no', 30)->nullable()->after('bank_name');
            $table->string('ifsc', 15)->nullable()->after('account_no');
            $table->string('branch_name', 100)->nullable()->after('ifsc');
            $table->string('emergency_name', 100)->nullable()->after('branch_name');
            $table->string('emergency_phone', 15)->nullable()->after('emergency_name');
            $table->string('emergency_relation', 50)->nullable()->after('emergency_phone');
            $table->text('notes')->nullable()->after('emergency_relation');
        });
    }

    public function down(): void
    {
        Schema::table('staff_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'experience_years','department','whatsapp',
                'city','state','pin','salary_type',
                'aadhar_no','pan_no','bank_name','account_no','ifsc','branch_name',
                'emergency_name','emergency_phone','emergency_relation','notes',
            ]);
        });
    }
};
