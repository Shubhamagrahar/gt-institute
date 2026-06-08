<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('course_books')) {
            return;
        }

        if (Schema::hasColumn('course_books', 'fee')) {
            DB::statement('ALTER TABLE course_books MODIFY fee DECIMAL(11, 2) NOT NULL DEFAULT 0.00');
        }

        if (Schema::hasColumn('course_books', 'book_date')) {
            DB::statement('ALTER TABLE course_books MODIFY book_date DATE NULL');
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('course_books')) {
            return;
        }

        if (Schema::hasColumn('course_books', 'fee')) {
            DB::statement('ALTER TABLE course_books MODIFY fee DECIMAL(11, 2) NOT NULL');
        }

        if (Schema::hasColumn('course_books', 'book_date')) {
            DB::statement('ALTER TABLE course_books MODIFY book_date DATE NOT NULL');
        }
    }
};
