<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Add separate index on batch_id first so FK constraint still has a supporting index
        \DB::statement('ALTER TABLE attendance_students ADD INDEX idx_att_batch_id (batch_id)');
        \DB::statement('ALTER TABLE attendance_students DROP INDEX attendance_students_user_id_date_batch_id_unique');
        \DB::statement('
            ALTER TABLE attendance_students
                ADD COLUMN course_id BIGINT UNSIGNED NULL AFTER batch_id,
                ADD COLUMN session_id BIGINT UNSIGNED NULL AFTER course_id,
                ADD COLUMN course_book_id BIGINT UNSIGNED NULL AFTER session_id,
                ADD UNIQUE INDEX att_student_course_date_unique (user_id, course_id, date)
        ');
    }

    public function down(): void
    {
        \DB::statement('SET FOREIGN_KEY_CHECKS=0');
        \DB::statement('ALTER TABLE attendance_students DROP INDEX att_student_course_date_unique');
        \DB::statement('
            ALTER TABLE attendance_students
                DROP COLUMN course_id,
                DROP COLUMN session_id,
                DROP COLUMN course_book_id,
                ADD UNIQUE INDEX attendance_students_user_id_date_batch_id_unique (user_id, date, batch_id)
        ');
        \DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
