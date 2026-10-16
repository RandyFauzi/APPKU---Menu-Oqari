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
        // 1. Raw Materials (Bahan Baku)
        if (! Schema::hasTable('raw_materials')) {
            Schema::create('raw_materials', function (Blueprint $table) {
                $table->id();
                $table->foreignId('shop_id')->constrained()->cascadeOnDelete();
                $table->string('name');
                $table->string('sku')->nullable();
                $table->string('unit')->default('pcs')->comment('gram, ml, pcs, shot, oz, etc');
                $table->decimal('current_stock', 12, 2)->default(0);
                $table->decimal('minimum_stock', 12, 2)->default(0);
                $table->decimal('cost_per_unit', 12, 2)->default(0)->comment('Harga modal / HPP beli per satuan unit');
                $table->timestamps();
                $table->softDeletes();

                $table->index(['shop_id', 'name']);
            });
        }

        // 2. Product Recipes (BOM / Resep Bahan Baku untuk Menu)
        if (! Schema::hasTable('product_recipes')) {
            Schema::create('product_recipes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained()->cascadeOnDelete();
                $table->foreignId('raw_material_id')->constrained('raw_materials')->cascadeOnDelete();
                $table->decimal('amount', 12, 2)->default(1)->comment('Takaran bahan per 1 porsi menu');
                $table->timestamps();

                $table->unique(['product_id', 'raw_material_id']);
            });
        }

        // 3. Stock Mutations (Riwayat Mutasi Stok - Masuk, Terpakai Order, Opname, Rusak/Waste)
        if (! Schema::hasTable('stock_mutations')) {
            Schema::create('stock_mutations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('shop_id')->constrained()->cascadeOnDelete();
                $table->foreignId('raw_material_id')->constrained('raw_materials')->cascadeOnDelete();
                $table->enum('type', ['in', 'out_order', 'adjustment', 'waste'])->default('in');
                $table->decimal('amount', 12, 2)->comment('Jumlah mutasi: positif / negatif');
                $table->decimal('balance_after', 12, 2)->comment('Sisa stok setelah mutasi');
                $table->string('reference_type')->nullable()->comment('Order, StockOpname, Purchase, etc');
                $table->unsignedBigInteger('reference_id')->nullable();
                $table->string('notes')->nullable();
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
                $table->timestamps();

                $table->index(['shop_id', 'raw_material_id']);
                $table->index(['shop_id', 'created_at']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_mutations');
        Schema::dropIfExists('product_recipes');
        Schema::dropIfExists('raw_materials');
    }
};
