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
        Schema::create('recipe_formulas', function (Blueprint $table) {
            $table->id();
            $table->string('code', 30)->unique();
            $table->string('name', 100);
            $table->decimal('meat_ratio', 8, 4)->default(1.0000);
            $table->decimal('flour_ratio', 8, 4)->default(0.2000);
            $table->decimal('spice_ratio', 8, 4)->default(0.0500);
            $table->boolean('is_simulation')->default(true);
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recipe_formulas');
    }
};
