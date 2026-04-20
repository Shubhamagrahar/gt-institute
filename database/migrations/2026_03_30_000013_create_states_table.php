<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('states', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
        });

        DB::table('states')->insert([
            ['id' => 1, 'name' => 'Andaman and Nicobar Islands'],
            ['id' => 2, 'name' => 'Andhra Pradesh'],
            ['id' => 3, 'name' => 'Arunachal Pradesh'],
            ['id' => 4, 'name' => 'Assam'],
            ['id' => 5, 'name' => 'Bihar'],
            ['id' => 6, 'name' => 'Chandighar'],
            ['id' => 7, 'name' => 'Chhattisgarh'],
            ['id' => 8, 'name' => 'Dadra and Nagar Haveli'],
            ['id' => 9, 'name' => 'Daman and Diu'],
            ['id' => 10, 'name' => 'Goa'],
            ['id' => 11, 'name' => 'Gujarat'],
            ['id' => 12, 'name' => 'Haryana'],
            ['id' => 13, 'name' => 'Himachal Pradesh'],
            ['id' => 14, 'name' => 'Jammu and Kashmir'],
            ['id' => 15, 'name' => 'Jharkhand'],
            ['id' => 16, 'name' => 'Karnataka'],
            ['id' => 17, 'name' => 'Kerala'],
            ['id' => 18, 'name' => 'Lakshadweep'],
            ['id' => 19, 'name' => 'Madhya Pradesh'],
            ['id' => 20, 'name' => 'Maharashtra'],
            ['id' => 21, 'name' => 'Manipur'],
            ['id' => 22, 'name' => 'Meghalaya'],
            ['id' => 23, 'name' => 'Mizoram'],
            ['id' => 24, 'name' => 'Nagaland'],
            ['id' => 25, 'name' => 'NCT of Delhi'],
            ['id' => 26, 'name' => 'Odisha'],
            ['id' => 27, 'name' => 'Puducherry'],
            ['id' => 28, 'name' => 'Punjab'],
            ['id' => 29, 'name' => 'Rajasthan'],
            ['id' => 30, 'name' => 'Sikkim'],
            ['id' => 31, 'name' => 'Tamil Nadu'],
            ['id' => 32, 'name' => 'Telangana'],
            ['id' => 33, 'name' => 'Tripura'],
            ['id' => 34, 'name' => 'Uttar Pradesh'],
            ['id' => 35, 'name' => 'Uttarakhand'],
            ['id' => 36, 'name' => 'West Bengal'],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('states');
    }
};
