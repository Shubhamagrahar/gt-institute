<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('franchise_levels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institute_id')->constrained()->cascadeOnDelete();
            $table->string('name', 100);
            $table->decimal('commission_percent', 5, 2)->default(0.00);
            $table->text('notes')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });

        Schema::table('franchises', function (Blueprint $table) {
            $table->foreignId('franchise_level_id')->nullable()->after('institute_id')->constrained('franchise_levels')->nullOnDelete();
            $table->decimal('commission_percent', 5, 2)->default(0.00)->after('website');
            $table->boolean('wallet_enabled')->default(true)->after('commission_percent');
            $table->decimal('low_wallet_alert', 11, 2)->default(1000.00)->after('wallet_enabled');
            $table->boolean('has_sub_franchise')->default(false)->after('low_wallet_alert');
        });
    }

    public function down(): void
    {
        Schema::table('franchises', function (Blueprint $table) {
            $table->dropConstrainedForeignId('franchise_level_id');
            $table->dropColumn(['commission_percent', 'wallet_enabled', 'low_wallet_alert', 'has_sub_franchise']);
        });

        Schema::dropIfExists('franchise_levels');
    }
};
