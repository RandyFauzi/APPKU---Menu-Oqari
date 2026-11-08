<?php

namespace App\Actions\POS;

use App\Actions\Order\CreateOrder;
use App\Actions\Payment\ProcessPayment;
use App\Events\OrderCreated;
use App\Models\ActivityLog;
use App\Models\Order;
use App\Models\Shop;
use App\Models\Table;
use App\Models\User;
use App\Models\Voucher;
use App\Services\InventoryService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;

class SubmitPosOrder
{
    public function __construct(
        private CalculateCart $calculateCart,
        private ApplyDiscount $applyDiscount,
        private CreateOrder $createOrder,
        private ProcessPayment $processPayment,
        private InventoryService $inventory,
    ) {}

    /** @param array<string, mixed> $data */
    public function execute(User $cashier, array $data): Order
    {
        return DB::transaction(function () use ($cashier, $data): Order {
            $shop = Shop::whereKey($cashier->shop_id)->lockForUpdate()->firstOrFail();
            abort_if($shop->isSuspended(), 403, 'Toko dinonaktifkan.');

            $existingOrder = Order::where('shop_id', $shop->id)
                ->where('checkout_request_id', $data['request_id'])->first();
            if ($existingOrder) {
                return $existingOrder;
            }

            $cart = $this->calculateCart->execute($shop, $data['items']);
            $cart = $this->applyDiscount->execute($shop, $cart, $data);

            if (! empty($data['recalled_order_id'])) {
                $order = Order::where('shop_id', $shop->id)
                    ->whereKey($data['recalled_order_id'])->lockForUpdate()->firstOrFail();
                if ($order->payment_status !== 'UNPAID' || in_array($order->order_status, ['COMPLETED', 'CANCELLED'], true)) {
                    throw ValidationException::withMessages(['recalled_order_id' => 'Pesanan sudah diproses atau lunas.']);
                }

                $order->items()->delete();
                $hasPriceColumn = Schema::hasColumn('order_items', 'price');
                foreach ($cart['items'] as $item) {
                    if ($hasPriceColumn && ! isset($item['price'])) {
                        $item['price'] = $item['unit_price'] ?? 0;
                    }
                    $order->items()->create($item);
                }
                $tableId = $order->table_id;
                if (! empty($data['table'])) {
                    $tableModel = Table::where('shop_id', $shop->id)
                        ->where('name', $data['table'])
                        ->first()
                        ?? Table::create(['shop_id' => $shop->id, 'name' => $data['table']]);
                    $tableId = $tableModel->id;
                }

                $order->update([
                    'table_id' => $tableId,
                    'customer_name' => $data['customer_name'] ?? $order->customer_name,
                    'subtotal' => $cart['subtotal'],
                    'grand_total' => $cart['net_sales'] ?? $cart['subtotal'],
                    'total_cogs' => $cart['total_cogs'],
                    'discount_amount' => $cart['discount_amount'] ?? 0,
                    'voucher_code' => $cart['voucher_code'] ?? null,
                    'discount_reason' => $cart['discount_reason'] ?? null,
                    'tax_amount' => 0,
                    'service_charge_amount' => 0,
                    'order_status' => 'CONFIRMED',
                    'fulfillment_type' => $data['fulfillment_type'] ?? 'DINE_IN',
                ]);
                $order->unsetRelation('items');
            } else {
                $order = $this->createOrder->execute($shop, $cart, $data);
            }

            $order->update(['checkout_request_id' => $data['request_id']]);
            $this->processPayment->execute($order, $data['payment_method'], (float) $data['amount_paid']);
            $order->refresh();

            if (! empty($cart['voucher'])) {
                $cart['voucher']->increment('used_count');
            } elseif (! empty($order->voucher_code)) {
                Voucher::where('shop_id', $shop->id)
                    ->where('code', $order->voucher_code)
                    ->increment('used_count');
            }

            $this->inventory->deductStockForOrder($order);
            OrderCreated::dispatch($order);

            if (class_exists(ActivityLog::class)) {
                $fulfillment = match ($order->fulfillment_type) {
                    'TAKEAWAY' => 'Takeaway',
                    'DELIVERY' => 'Delivery',
                    default => ($order->table?->name ? 'Meja '.$order->table->name : 'Dine In'),
                };
                $itemsCount = (int) $order->items()->sum('quantity');
                ActivityLog::create([
                    'shop_id' => $shop->id,
                    'user_id' => $cashier->id,
                    'action' => 'pos.order_completed',
                    'entity_type' => Order::class,
                    'entity_id' => $order->id,
                    'description' => 'Transaksi kasir selesai untuk pesanan #'.$order->id.' ('.$fulfillment.' · '.$itemsCount.' item) - Metode: '.$data['payment_method'].' - Total: Rp '.number_format((float) $order->grand_total, 0, ',', '.'),
                    'ip_address' => request()->ip(),
                    'user_agent' => mb_substr((string) request()->userAgent(), 0, 255),
                ]);
            }

            return $order;
        });
    }
}
