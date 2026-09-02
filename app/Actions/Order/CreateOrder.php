<?php

namespace App\Actions\Order;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Shop;
use App\Models\Table;
use Exception;
use Illuminate\Support\Facades\DB;

/**
 * Persists a resolved cart (from CalculateCart + ApplyDiscount) as a new Order.
 * Handles table resolution and order_items bulk creation inside a DB transaction.
 * Does NOT handle payment — that is ProcessPayment's responsibility.
 */
class CreateOrder
{
    /**
     * @param  array  $cart  Output of ApplyDiscount::execute()
     * @param  array  $data  Customer data: table, name, payment_method, fulfillment_type
     *
     * @throws Exception
     */
    public function execute(Shop $shop, array $cart, array $data): Order
    {
        return DB::transaction(function () use ($shop, $cart, $data) {

            // 1. Resolve or create table
            $tableModel = null;
            if (! empty($data['table'])) {
                $tableModel = Table::where('shop_id', $shop->id)
                    ->where('name', $data['table'])
                    ->first()
                    ?? Table::create(['shop_id' => $shop->id, 'name' => $data['table']]);
            }

            // 2. Apply shop-level tax/service_charge config
            $taxRate = (float) ($shop->tax_rate ?? 0);
            $serviceChargeRate = (float) ($shop->service_charge_rate ?? 0);
            $netSales = $cart['net_sales'] ?? $cart['subtotal'];
            $taxAmount = round($netSales * $taxRate / 100, 2);
            $serviceAmount = round($netSales * $serviceChargeRate / 100, 2);
            $grandTotal = $netSales + $taxAmount + $serviceAmount;

            // 3. Create Order header
            $order = Order::create([
                'shop_id' => $shop->id,
                'table_id' => $tableModel?->id,
                'customer_name' => $data['customer_name'] ?? null,
                'customer_email' => $data['customer_email'] ?? null,
                'customer_phone' => $data['customer_phone'] ?? null,
                'payment_method' => $data['payment_method'],
                'subtotal' => $cart['subtotal'],
                'discount_amount' => $cart['discount_amount'] ?? 0,
                'tax_amount' => $taxAmount,
                'service_charge_amount' => $serviceAmount,
                'grand_total' => $grandTotal,
                'total_cogs' => $cart['total_cogs'],
                'order_status' => 'CONFIRMED',
                'payment_status' => 'UNPAID',
                'fulfillment_type' => $data['fulfillment_type'] ?? 'DINE_IN',
            ]);

            // 4. Bulk-insert order items
            $now = now();
            $rows = array_map(fn ($item) => array_merge($item, [
                'order_id' => $order->id,
                'modifiers' => $item['modifiers'] ? json_encode($item['modifiers']) : null,
                'created_at' => $now,
                'updated_at' => $now,
            ]), $cart['items']);

            OrderItem::insert($rows);

            $order->load('items');

            return $order;
        });
    }
}
