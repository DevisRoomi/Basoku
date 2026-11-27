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
        Schema::table('orders', function (Blueprint $table) {
            if (! Schema::hasColumn('orders', 'cancelled_by')) {
                $table->string('cancelled_by', 20)->nullable()->after('status');
            }
            if (! Schema::hasColumn('orders', 'cancelled_at')) {
                $table->timestamp('cancelled_at')->nullable()->after('cancelled_by');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $columns = [];
            if (Schema::hasColumn('orders', 'cancelled_by')) {
                $columns[] = 'cancelled_by';
            }
            if (Schema::hasColumn('orders', 'cancelled_at')) {
                $columns[] = 'cancelled_at';
            }
            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }
};
