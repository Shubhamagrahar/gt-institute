<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('admission_form_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institute_id')->constrained()->cascadeOnDelete();
            $table->string('field_key', 60);       // matches user_profiles column
            $table->string('field_label', 100);
            $table->enum('field_type', ['text','number','email','date','select','textarea','file'])
                  ->default('text');
            $table->text('options')->nullable();   // comma separated for select
            $table->boolean('is_required')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['institute_id', 'field_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admission_form_fields');
    }
};
