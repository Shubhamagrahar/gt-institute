<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('staff_roles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('institute_id');
            $table->string('name', 80);
            $table->string('color', 20)->default('#6c5dd3'); // UI badge color
            $table->text('description')->nullable();
            $table->json('permissions')->nullable();          // array of permission keys
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();

            $table->index('institute_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff_roles');
    }
};
