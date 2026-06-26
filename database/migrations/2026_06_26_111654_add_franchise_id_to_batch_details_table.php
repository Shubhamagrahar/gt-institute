<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('batch_details', function (Blueprint $table) {
            // NULL = institute batch | filled = franchise batch (isolation by franchise_id)
            $table->unsignedBigInteger('franchise_id')->nullable()->after('institute_id');
        });
    }

    public function down(): void
    {
        Schema::table('batch_details', function (Blueprint $table) {
            $table->dropColumn('franchise_id');
        });
    }
};
