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
        if (Schema::hasTable('cash_drawer_transactions') && ! Schema::hasColumn('cash_drawer_transactions', 'shop_id')) {
            Schema::table('cash_drawer_transactions', function (Blueprint $table) {
                $table->foreignId('shop_id')->nullable()->after('id')->constrained('shops')->cascadeOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('cash_drawer_transactions') && Schema::hasColumn('cash_drawer_transactions', 'shop_id')) {
            Schema::table('cash_drawer_transactions', function (Blueprint $table) {
                $table->dropConstrainedForeignId('shop_id');
            });
        }
    }
};
