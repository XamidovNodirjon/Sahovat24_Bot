<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add owner_verified to products table
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('owner_verified')->default(false)->after('longitude');
        });

        // 2. Insert Dorilar category if not exists (before Boshqa)
        $exists = DB::table('categories')->where('name_uz', 'like', '%Dorilar%')->exists();
        if (!$exists) {
            DB::table('categories')->insert([
                'name_uz'    => '💊 Dorilar',
                'name_ru'    => '💊 Лекарства',
                'name_en'    => '💊 Medicines',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('owner_verified');
        });

        DB::table('categories')->where('name_uz', 'like', '%Dorilar%')->delete();
    }
};
