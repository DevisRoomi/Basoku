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
        Schema::create('recipe_configurations', function (Blueprint $table) {
            $table->id();
            $table->string('name', 30)->unique();
            $table->decimal('meat_ratio', 8, 4);
            $table->decimal('flour_ratio', 8, 4);
            $table->decimal('seasoning_ratio', 8, 4);
            $table->decimal('grinding_fee', 12, 2)->default(0.00);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recipe_configurations');
    }
};
