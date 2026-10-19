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
        Schema::create('saved_recipes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('recipe_configuration_id')->nullable()->constrained('recipe_configurations')->nullOnDelete();
            $table->string('name', 100);
            $table->decimal('meat_weight', 10, 3);
            $table->enum('start_type', ['start_from_zero', 'own_meat']);
            $table->string('preference', 30);
            $table->json('recipe_details');
            $table->decimal('estimated_total', 12, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('saved_recipes');
    }
};
