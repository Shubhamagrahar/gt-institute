<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // User Profiles
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name', 100);
            $table->string('photo', 300)->nullable();
            $table->string('father_name', 100)->nullable();
            $table->string('mother_name', 100)->nullable();
            $table->string('guardian_name', 100)->nullable();
            $table->string('guardian_relation', 50)->nullable();
            $table->string('guardian_mobile', 15)->nullable();
            $table->string('guardian_occupation', 80)->nullable();
            $table->date('dob')->nullable();
            $table->enum('gender', ['Male','Female','Other'])->nullable();
            $table->string('category', 20)->nullable();   // Gen/OBC/SC-ST/EWS
            $table->string('religion', 50)->nullable();
            $table->string('nationality', 50)->nullable()->default('Indian');
            $table->string('whatsapp_no', 15)->nullable();
            $table->string('alternate_mobile', 15)->nullable();
            $table->string('aadhar_no', 16)->nullable();
            $table->string('pan_no', 10)->nullable();
            $table->string('blood_group', 5)->nullable();
            $table->enum('employment_status', ['Employed','Unemployed'])->nullable();
            $table->enum('computer_literacy', ['Yes','No'])->nullable();
            $table->string('qualification', 80)->nullable();
            $table->text('address')->nullable();
            $table->text('permanent_address')->nullable();
            $table->string('state', 60)->nullable();
            $table->string('district', 60)->nullable();
            $table->string('pin_code', 10)->nullable();
            $table->timestamps();
        });

        // User Education
        Schema::create('user_education', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('examination', 80);     // 10th, 12th, Graduation
            $table->string('board_university', 150)->nullable();
            $table->string('passing_year', 10)->nullable();
            $table->string('marks_percentage', 10)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_education');
        Schema::dropIfExists('user_profiles');
    }
};