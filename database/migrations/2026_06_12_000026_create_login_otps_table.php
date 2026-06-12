<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('login_otps', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            $table->string('guard', 20); // 'web' or 'institute'
            $table->string('otp');       // bcrypt-hashed 6-digit code
            $table->timestamp('expires_at');
            $table->timestamps();

            $table->index('email');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('login_otps');
    }
};
