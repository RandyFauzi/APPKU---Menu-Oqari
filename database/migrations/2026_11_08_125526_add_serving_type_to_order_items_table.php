<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('order_items') && ! Schema::hasColumn('order_items', 'serving_type')) {
            Schema::table('order_items', function (Blueprint $table): void {
                $table->string('serving_type', 20)->default('DINE_IN')->nullable()->after('notes');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('order_items') && Schema::hasColumn('order_items', 'serving_type')) {
            Schema::table('order_items', function (Blueprint $table): void {
                $table->dropColumn('serving_type');
            });
        }
    }
};
