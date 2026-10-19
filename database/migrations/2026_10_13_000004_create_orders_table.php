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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('recipe_configuration_id')->nullable()->constrained('recipe_configurations')->nullOnDelete();
            $table->string('customer_name', 100);
            $table->dateTime('pickup_at');
            $table->enum('start_type', ['start_from_zero', 'own_meat']);
            $table->decimal('meat_weight', 10, 3);
            $table->string('preference', 30);
            $table->decimal('grinding_fee', 12, 2)->default(0.00);
            $table->decimal('total_price', 12, 2);
            $table->string('status', 50)->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
