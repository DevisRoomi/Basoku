<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('materials', function (Blueprint $table) {
            $table->string('selling_mode', 20)->default('kemasan')->after('category');
        });

        // Tetapkan mode jual realistis pasar tradisional untuk data eksisting
        DB::table('materials')
            ->whereIn('name', ['Daging Sapi', 'Daging Ayam', 'Bawang Putih Kupas', 'Garam Kasar', 'Garam Halus'])
            ->update(['selling_mode' => 'curah']);

        DB::table('materials')
            ->whereIn('name', ['Telur Ayam', 'Merica Bubuk Sachet', 'Pengenyal Sobaso', 'Penyedap Masako'])
            ->update(['selling_mode' => 'satuan']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('materials', function (Blueprint $table) {
            $table->dropColumn('selling_mode');
        });
    }
};
