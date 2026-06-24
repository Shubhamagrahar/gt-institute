<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE course_books MODIFY COLUMN status ENUM('OPEN','RUN','CLOSE','CANCEL','EXPIRED','CONVERTED') NOT NULL DEFAULT 'OPEN'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE course_books MODIFY COLUMN status ENUM('OPEN','RUN','CLOSE','CANCEL') NOT NULL DEFAULT 'OPEN'");
    }
};
