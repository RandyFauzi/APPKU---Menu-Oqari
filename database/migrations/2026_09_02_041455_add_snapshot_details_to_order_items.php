<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->string('product_name')->nullable()->after('product_id');
            $table->integer('discount_amount')->default(0)->after('price');
            $table->integer('tax_amount')->default(0)->after('discount_amount');
            $table->integer('subtotal')->default(0)->after('tax_amount');
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn(['product_name', 'discount_amount', 'tax_amount', 'subtotal']);
        });
    }
};
