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
        if (Schema::hasColumn('orders', 'recipe_configuration_id')) {
            Schema::table('orders', function (Blueprint $table): void {
                $table->dropConstrainedForeignId('recipe_configuration_id');
            });
        }

        if (Schema::hasColumn('saved_recipes', 'recipe_configuration_id')) {
            Schema::table('saved_recipes', function (Blueprint $table): void {
                $table->dropConstrainedForeignId('recipe_configuration_id');
            });
        }

        Schema::dropIfExists('recipe_configurations');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('recipe_configurations')) {
            Schema::create('recipe_configurations', function (Blueprint $table): void {
                $table->id();
                $table->string('name', 100);
                $table->text('description')->nullable();
                $table->decimal('meat_ratio', 5, 2);
                $table->decimal('flour_ratio', 5, 2);
                $table->decimal('garlic_ratio', 5, 2);
                $table->decimal('salt_ratio', 5, 2);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (! Schema::hasColumn('saved_recipes', 'recipe_configuration_id')) {
            Schema::table('saved_recipes', function (Blueprint $table): void {
                $table->foreignId('recipe_configuration_id')
                    ->nullable()
                    ->after('user_id')
                    ->constrained('recipe_configurations')
                    ->nullOnDelete();
            });
        }

        if (! Schema::hasColumn('orders', 'recipe_configuration_id')) {
            Schema::table('orders', function (Blueprint $table): void {
                $table->foreignId('recipe_configuration_id')
                    ->nullable()
                    ->after('user_id')
                    ->constrained('recipe_configurations')
                    ->nullOnDelete();
            });
        }
    }
};
