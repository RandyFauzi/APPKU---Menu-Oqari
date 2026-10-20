<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table): void {
            $table->json('hpp_settings')->nullable();
        });
        Schema::table('product_recipes', function (Blueprint $table): void {
            $table->decimal('amount', 18, 6)->default(1)->change();
            $table->decimal('batch_amount', 18, 6)->nullable();
            $table->string('notes', 500)->nullable();
        });
        Schema::table('raw_materials', function (Blueprint $table): void {
            $table->decimal('cost_per_unit', 18, 6)->default(0)->change();
            $table->decimal('current_stock', 18, 6)->default(0)->change();
            $table->decimal('minimum_stock', 18, 6)->default(0)->change();
        });
        Schema::table('stock_mutations', function (Blueprint $table): void {
            $table->decimal('amount', 18, 6)->change();
            $table->decimal('balance_after', 18, 6)->change();
        });
    }

    public function down(): void
    {
        Schema::table('products', fn (Blueprint $table) => $table->dropColumn('hpp_settings'));
        Schema::table('product_recipes', fn (Blueprint $table) => $table->dropColumn(['batch_amount', 'notes']));
    }
};
