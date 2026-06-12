<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fee_collect_details', function (Blueprint $table) {
            $table->timestamp('cancelled_at')->nullable()->after('note');
            $table->text('cancel_reason')->nullable()->after('cancelled_at');
            $table->unsignedBigInteger('cancelled_by')->nullable()->after('cancel_reason');
        });
    }

    public function down(): void
    {
        Schema::table('fee_collect_details', function (Blueprint $table) {
            $table->dropColumn(['cancelled_at', 'cancel_reason', 'cancelled_by']);
        });
    }
};
