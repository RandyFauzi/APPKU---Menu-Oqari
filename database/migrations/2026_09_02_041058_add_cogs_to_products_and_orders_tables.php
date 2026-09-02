<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->integer('cogs')->default(0)->after('price'); // Cost of Goods Sold
        });
        
        Schema::table('product_variants', function (Blueprint $table) {
            $table->integer('cogs_adjustment')->default(0)->after('price_adjustment');
        });
        
        Schema::table('modifiers', function (Blueprint $table) {
            $table->integer('cogs_adjustment')->default(0)->after('price_adjustment');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->integer('total_cogs')->default(0)->after('amount_paid');
        });
        
        Schema::table('order_items', function (Blueprint $table) {
            $table->integer('total_cogs')->default(0)->after('price'); // Unit COGS * quantity
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('cogs');
        });
        
        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropColumn('cogs_adjustment');
        });
        
        Schema::table('modifiers', function (Blueprint $table) {
            $table->dropColumn('cogs_adjustment');
        });
        
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('total_cogs');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn('total_cogs');
        });
    }
};
