<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('districts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('state_id')->nullable()->constrained('states')->nullOnDelete();
            $table->string('name', 255);
        });

        $this->importDistrictsFromUploadedDump();
    }

    public function down(): void
    {
        Schema::dropIfExists('districts');
    }

    private function importDistrictsFromUploadedDump(): void
    {
        $dumpPath = 'C:\\Users\\shubh\\Downloads\\fms_db.sql';
        if (! is_file($dumpPath)) {
            return;
        }

        $sql = file_get_contents($dumpPath);
        if ($sql === false) {
            return;
        }

        if (! preg_match('/INSERT INTO `district` \(`id`, `state_id`, `name`\) VALUES\s*(.*?);/s', $sql, $matches)) {
            return;
        }

        $insertSql = "INSERT INTO `districts` (`id`, `state_id`, `name`) VALUES\n" . trim($matches[1]) . ';';
        DB::unprepared($insertSql);
    }
};
