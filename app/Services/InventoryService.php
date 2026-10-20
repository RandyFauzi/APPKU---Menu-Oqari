<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use App\Models\RawMaterial;
use App\Models\StockMutation;
use Illuminate\Support\Facades\DB;

class InventoryService
{
    /**
     * Kurangi stok bahan baku berdasarkan resep menu yang dipesan.
     */
    public function deductStockForOrder(Order $order): void
    {
        DB::transaction(function () use ($order): void {
            $order->loadMissing('items.product.recipes');

            foreach ($order->items as $item) {
                foreach ($item->product?->recipes ?? [] as $recipe) {
                    $material = RawMaterial::where('shop_id', $order->shop_id)
                        ->whereKey($recipe->raw_material_id)->lockForUpdate()->firstOrFail();
                    $amountToDeduct = (float) $recipe->amount * (float) $item->quantity;
                    $newStock = (float) $material->current_stock - $amountToDeduct;
                    $material->update(['current_stock' => $newStock]);
                    StockMutation::create([
                        'shop_id' => $order->shop_id,
                        'raw_material_id' => $material->id,
                        'unit' => $material->unit,
                        'type' => 'out_order',
                        'amount' => -$amountToDeduct,
                        'balance_after' => $newStock,
                        'reference_type' => 'Order',
                        'reference_id' => $order->id,
                        'notes' => "Pesanan #{$order->id}",
                        'user_id' => auth()->id(),
                    ]);
                }
            }
        });
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
