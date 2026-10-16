<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use App\Models\StockMutation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InventoryService
{
    /**
     * Pastikan tabel inventori sudah ada di database shared-hosting / server.
     */
    public function ensureTablesExist(): void
    {
        try {
            if (! DB::getSchemaBuilder()->hasTable('raw_materials')) {
                DB::statement("
                    CREATE TABLE IF NOT EXISTS `raw_materials` (
                        `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                        `shop_id` bigint(20) unsigned NOT NULL,
                        `name` varchar(255) NOT NULL,
                        `sku` varchar(255) DEFAULT NULL,
                        `unit` varchar(50) NOT NULL DEFAULT 'pcs',
                        `current_stock` decimal(12,2) NOT NULL DEFAULT '0.00',
                        `minimum_stock` decimal(12,2) NOT NULL DEFAULT '0.00',
                        `cost_per_unit` decimal(12,2) NOT NULL DEFAULT '0.00',
                        `created_at` timestamp NULL DEFAULT NULL,
                        `updated_at` timestamp NULL DEFAULT NULL,
                        `deleted_at` timestamp NULL DEFAULT NULL,
                        PRIMARY KEY (`id`),
                        KEY `raw_materials_shop_id_name_index` (`shop_id`,`name`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
                ");
            }

            if (! DB::getSchemaBuilder()->hasTable('product_recipes')) {
                DB::statement("
                    CREATE TABLE IF NOT EXISTS `product_recipes` (
                        `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                        `product_id` bigint(20) unsigned NOT NULL,
                        `raw_material_id` bigint(20) unsigned NOT NULL,
                        `amount` decimal(12,2) NOT NULL DEFAULT '1.00',
                        `created_at` timestamp NULL DEFAULT NULL,
                        `updated_at` timestamp NULL DEFAULT NULL,
                        PRIMARY KEY (`id`),
                        UNIQUE KEY `product_recipes_product_id_raw_material_id_unique` (`product_id`,`raw_material_id`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
                ");
            }

            if (! DB::getSchemaBuilder()->hasTable('stock_mutations')) {
                DB::statement("
                    CREATE TABLE IF NOT EXISTS `stock_mutations` (
                        `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                        `shop_id` bigint(20) unsigned NOT NULL,
                        `raw_material_id` bigint(20) unsigned NOT NULL,
                        `type` enum('in','out_order','adjustment','waste') NOT NULL DEFAULT 'in',
                        `amount` decimal(12,2) NOT NULL,
                        `balance_after` decimal(12,2) NOT NULL,
                        `reference_type` varchar(255) DEFAULT NULL,
                        `reference_id` bigint(20) unsigned DEFAULT NULL,
                        `notes` varchar(255) DEFAULT NULL,
                        `user_id` bigint(20) unsigned DEFAULT NULL,
                        `created_at` timestamp NULL DEFAULT NULL,
                        `updated_at` timestamp NULL DEFAULT NULL,
                        PRIMARY KEY (`id`),
                        KEY `stock_mutations_shop_id_raw_material_id_index` (`shop_id`,`raw_material_id`),
                        KEY `stock_mutations_shop_id_created_at_index` (`shop_id`,`created_at`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
                ");
            }
        } catch (\Throwable $e) {
            Log::error('InventoryService::ensureTablesExist failed: '.$e->getMessage());
        }
    }

    /**
     * Kurangi stok bahan baku berdasarkan resep menu yang dipesan.
     */
    public function deductStockForOrder(Order $order): void
    {
        try {
            $this->ensureTablesExist();

            DB::transaction(function () use ($order) {
                $order->loadMissing('items.product.recipes.rawMaterial');

                foreach ($order->items as $item) {
                    $product = $item->product;
                    if (! $product || $product->recipes->isEmpty()) {
                        continue;
                    }

                    $orderQty = (float) $item->quantity;

                    foreach ($product->recipes as $recipe) {
                        $material = $recipe->rawMaterial;
                        if (! $material) {
                            continue;
                        }

                        $amountToDeduct = (float) $recipe->amount * $orderQty;
                        $oldStock = (float) $material->current_stock;
                        $newStock = $oldStock - $amountToDeduct;

                        // Perbarui stok
                        $material->update([
                            'current_stock' => $newStock,
                        ]);

                        // Catat mutasi stok
                        StockMutation::create([
                            'shop_id' => $order->shop_id,
                            'raw_material_id' => $material->id,
                            'type' => 'out_order',
                            'amount' => -$amountToDeduct,
                            'balance_after' => $newStock,
                            'reference_type' => 'Order',
                            'reference_id' => $order->id,
                            'notes' => "Pesanan #{$order->id} ({$item->product_name} x{$orderQty})",
                            'user_id' => auth()->id() ?? null,
                        ]);
                    }
                }
            });
        } catch (\Throwable $e) {
            Log::error("Failed deducting stock for order #{$order->id}: ".$e->getMessage());
        }
    }

    /**
     * Hitung & sinkronkan ulang HPP produk dari daftar resepnya.
     */
    public function syncProductHpp(Product $product): float
    {
        $calculatedHpp = $product->calculateHppFromRecipes();
        $product->update(['cogs' => $calculatedHpp]);

        return $calculatedHpp;
    }
}
