<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('franchise_fee_structures', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('franchise_id');
            $table->unsignedBigInteger('institute_id');
            $table->unsignedBigInteger('course_id');
            $table->unsignedBigInteger('fee_type_id')->nullable();
            $table->string('fee_type_name', 100);
            $table->decimal('amount', 10, 2)->default(0);
            $table->boolean('enabled')->default(true);
            $table->unsignedTinyInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['franchise_id', 'course_id', 'fee_type_id'], 'uniq_frn_crs_feetype');
            $table->index(['franchise_id', 'course_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('franchise_fee_structures');
    }
};
