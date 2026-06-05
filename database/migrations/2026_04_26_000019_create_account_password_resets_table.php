<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('account_password_resets', function (Blueprint $table) {
            $table->string('account_type', 20);
            $table->string('email', 100);
            $table->string('token');
            $table->timestamp('created_at')->nullable();

            $table->unique(['account_type', 'email'], 'account_password_resets_unique');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('account_password_resets');
    }
};
