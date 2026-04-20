<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('admission_form_fields')) {
            DB::statement("ALTER TABLE admission_form_fields MODIFY field_type ENUM('text','number','email','date','select','textarea','file') NOT NULL DEFAULT 'text'");
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('admission_form_fields')) {
            DB::statement("UPDATE admission_form_fields SET field_type = 'text' WHERE field_type = 'email'");
            DB::statement("ALTER TABLE admission_form_fields MODIFY field_type ENUM('text','number','date','select','textarea','file') NOT NULL DEFAULT 'text'");
        }
    }
};
