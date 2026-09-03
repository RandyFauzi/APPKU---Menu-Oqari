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
            $table->index(['shop_id', 'created_at']);
            $table->index(['shop_id', 'order_status']);
            $table->index(['shop_id', 'payment_status']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->index(['shop_id', 'category_id']);
            $table->index(['shop_id', 'is_sold_out']);
        });

        Schema::table('tables', function (Blueprint $table) {
            $table->index(['shop_id']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->index(['shop_id', 'role']);
        });

        Schema::table('activity_logs', function (Blueprint $table) {
            $table->index(['shop_id', 'created_at']);
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->index(['shop_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['shop_id', 'created_at']);
            $table->dropIndex(['shop_id', 'order_status']);
            $table->dropIndex(['shop_id', 'payment_status']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['shop_id', 'category_id']);
            $table->dropIndex(['shop_id', 'is_sold_out']);
        });

        Schema::table('tables', function (Blueprint $table) {
            $table->dropIndex(['shop_id']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['shop_id', 'role']);
        });

        Schema::table('activity_logs', function (Blueprint $table) {
            $table->dropIndex(['shop_id', 'created_at']);
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex(['shop_id', 'created_at']);
        });
    }
};
