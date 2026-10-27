<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('materials', function (Blueprint $table) {
            $table->string('package_unit', 30)->nullable()->after('unit');
            $table->decimal('package_weight', 10, 2)->nullable()->after('package_unit');
            $table->decimal('package_price', 12, 2)->nullable()->after('package_weight');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('materials', function (Blueprint $table) {
            $table->dropColumn(['package_unit', 'package_weight', 'package_price']);
        });
    }
};
