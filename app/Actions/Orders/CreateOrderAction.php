<?php

namespace App\Actions\Orders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Modifier;
use App\Models\Table;
use App\Models\Shop;
use App\Events\OrderCreated;
use Illuminate\Support\Facades\DB;
use Exception;

class CreateOrderAction
{
    /**
     * Mengeksekusi pembuatan pesanan dengan segala rules domainnya.
     */
    public function execute(Shop $shop, array $data, $fulfillmentType = 'DINE_IN'): Order
    {
        return DB::transaction(function () use ($shop, $data, $fulfillmentType) {
            
            // 1. Resolve Table
            $tableModel = Table::where('shop_id', $shop->id)->where('name', $data['table_id'])->first();
            if (!$tableModel) {
                $tableModel = Table::create(['shop_id' => $shop->id, 'name' => $data['table_id']]);
            }

            // 2. Validate & Calculate Items
            $total = 0;
            $totalCogs = 0;
            $orderItems = [];

            foreach ($data['items'] as $item) {
                $product = Product::where('id', $item['id'])
                    ->where('shop_id', $shop->id)
                    ->where('is_sold_out', false)
                    ->first();

                if (!$product) {
                    throw new Exception("Produk tidak tersedia: " . $item['id']);
                }

                $price = $product->price;
                $cogs = $product->cogs;
                
                $variantId = $item['variant_id'] ?? null;
                $variantName = null;
                
                if ($variantId) {
                    $variant = ProductVariant::where('id', $variantId)
                        ->where('product_id', $product->id)
                        ->where('is_active', true)
                        ->first();
                        
                    if (!$variant) {
                        throw new Exception("Varian produk tidak valid.");
                    }
                    
                    $variantName = $variant->name;
                    $price += $variant->price_adjustment;
                    $cogs += $variant->cogs_adjustment;
                }
                
                $modifiersSnapshot = [];
                if (!empty($item['modifiers']) && is_array($item['modifiers'])) {
                    foreach ($item['modifiers'] as $modId) {
                        $modifier = Modifier::where('id', $modId)
                            ->whereHas('group', function($q) use ($product) {
                                $q->where('product_id', $product->id);
                            })
                            ->where('is_active', true)
                            ->first();
                            
                        if ($modifier) {
                            $price += $modifier->price_adjustment;
                            $cogs += $modifier->cogs_adjustment;
                            $modifiersSnapshot[] = [
                                'id' => $modifier->id,
                                'name' => $modifier->name,
                                'price_adjustment' => $modifier->price_adjustment
                            ];
                        }
                    }
                }

                $subtotal = $price * $item['qty'];
                $itemCogs = $cogs * $item['qty'];
                
                $total += $subtotal;
                $totalCogs += $itemCogs;

                $orderItems[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'variant_id' => $variantId,
                    'variant_name' => $variantName,
                    'modifiers' => empty($modifiersSnapshot) ? null : json_encode($modifiersSnapshot),
                    'quantity' => $item['qty'],
                    'price' => $price,
                    'subtotal' => $subtotal,
                    'total_cogs' => $itemCogs,
                    'notes' => $item['notes'] ?? null,
                ];
            }

            if (empty($orderItems)) {
                throw new Exception("Keranjang pesanan tidak valid.");
            }

            // 3. Create the Order Parent
            $order = Order::create([
                'shop_id' => $shop->id,
                'table_id' => $tableModel->id,
                'customer_name' => $data['customer_name'],
                'customer_email' => $data['customer_email'] ?? null,
                'customer_phone' => $data['customer_phone'] ?? null,
                'payment_method' => $data['payment_method'],
                'subtotal' => $total,
                'grand_total' => $total,
                'total_cogs' => $totalCogs,
                'order_status' => 'CONFIRMED',
                'payment_status' => 'UNPAID',
                'fulfillment_type' => $fulfillmentType,
            ]);

            // 4. Attach Items
            foreach ($orderItems as $orderItem) {
                $orderItem['order_id'] = $order->id;
                OrderItem::create($orderItem);
            }

            $order->load('items.product');
            
            return $order;
        });
    }
}
