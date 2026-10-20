<?php

namespace App\Actions\POS;

use App\Actions\Order\CreateOrder;
use App\Actions\Payment\ProcessPayment;
use App\Events\OrderCreated;
use App\Models\Order;
use App\Models\Shop;
use App\Models\User;
use App\Services\InventoryService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SubmitPosOrder
{
    public function __construct(
        private CalculateCart $calculateCart,
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

            if (! empty($data['recalled_order_id'])) {
                $order = Order::where('shop_id', $shop->id)
                    ->whereKey($data['recalled_order_id'])->lockForUpdate()->firstOrFail();
                if ($order->order_status !== 'HOLD' || $order->payment_status !== 'UNPAID') {
                    throw ValidationException::withMessages(['recalled_order_id' => 'Pesanan sudah diproses.']);
                }

                $order->items()->delete();
                foreach ($cart['items'] as $item) {
                    $order->items()->create($item);
                }
                $order->update([
                    'subtotal' => $cart['subtotal'],
                    'grand_total' => $cart['subtotal'],
                    'total_cogs' => $cart['total_cogs'],
                    'discount_amount' => 0,
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
            $this->inventory->deductStockForOrder($order);
            OrderCreated::dispatch($order);

            return $order;
        });
    }
}
