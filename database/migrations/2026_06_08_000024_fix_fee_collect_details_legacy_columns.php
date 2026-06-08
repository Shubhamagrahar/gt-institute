<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('fee_collect_details')) {
            return;
        }

        if (Schema::hasColumn('fee_collect_details', 'by_rcv')) {
            DB::statement('ALTER TABLE fee_collect_details MODIFY by_rcv BIGINT NULL DEFAULT NULL');
        }

        if (Schema::hasColumn('fee_collect_details', 'received_by')) {
            DB::statement('ALTER TABLE fee_collect_details MODIFY received_by BIGINT NULL DEFAULT NULL');
        }
    }

    public function down(): void {}
};
