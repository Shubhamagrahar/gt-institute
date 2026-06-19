<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('franchise_joining_wallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('franchise_id')->unique()->constrained()->cascadeOnDelete();
            $table->foreignId('institute_id')->constrained()->cascadeOnDelete();
            $table->decimal('total_due', 12, 2)->default(0)
                ->comment('Level fee at time of franchise creation (snapshot)');
            $table->decimal('total_paid', 12, 2)->default(0)
                ->comment('Sum of all non-cancelled payments collected');
            $table->decimal('balance', 12, 2)->default(0)
                ->comment('Outstanding: total_due - total_paid');
            $table->timestamps();

            $table->index('institute_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('franchise_joining_wallets');
    }
};
