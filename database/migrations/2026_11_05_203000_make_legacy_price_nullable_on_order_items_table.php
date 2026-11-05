<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('order_items') && Schema::hasColumn('order_items', 'price')) {
            Schema::table('order_items', function (Blueprint $table) {
                $table->decimal('price', 12, 2)->nullable()->default(null)->change();
            });
        }
    }

    public function down(): void
    {
        // Forward-only migration to ensure historical schema compatibility
    }
};
