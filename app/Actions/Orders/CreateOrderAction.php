<?php

namespace App\Actions\Orders;

use App\Models\ActivityLog;
use App\Models\CashRegisterSession;
use App\Models\Modifier;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Shop;
use App\Models\Table;
use App\Models\Voucher;
use App\Services\InventoryService;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateOrderAction
{
    /**
     * Mengeksekusi pembuatan pesanan dengan segala rules domainnya.
     */
    public function execute(Shop $shop, array $data, $fulfillmentType = 'DINE_IN'): Order
    {
        return DB::transaction(function () use ($shop, $data, $fulfillmentType) {

            // 1. Resolve Table (Only for Dine In or if table is provided)
            $tableModel = null;
            if (! empty($data['table_id'])) {
                $tableModel = Table::where('shop_id', $shop->id)->where('name', $data['table_id'])->first();
                if (! $tableModel) {
                    $tableModel = Table::create(['shop_id' => $shop->id, 'name' => $data['table_id']]);
                }
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

                if (! $product) {
                    throw new Exception('Produk tidak tersedia: '.$item['id']);
                }

                $price = (float) $product->effective_price;
                $cogs = $product->cogs;

                $variantId = $item['variant_id'] ?? null;
                $variantName = null;

                if ($variantId) {
                    $variant = ProductVariant::where('id', $variantId)
                        ->where('product_id', $product->id)
                        ->where('is_active', true)
                        ->first();

                    if (! $variant) {
                        throw new Exception('Varian produk tidak valid.');
                    }

                    $variantName = $variant->name;
                    $price += $variant->price_adjustment;
                    $cogs += $variant->cogs_adjustment;
                }

                $modifiersSnapshot = [];
                if (! empty($item['modifiers']) && is_array($item['modifiers'])) {
                    foreach ($item['modifiers'] as $modId) {
                        $modifier = Modifier::where('id', $modId)
                            ->whereHas('group', function ($q) use ($product) {
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
                                'price_adjustment' => $modifier->price_adjustment,
                            ];
                        }
                    }
                }

                $qty = (int) ($item['qty'] ?? $item['quantity'] ?? 1);
                $subtotal = $price * $qty;
                $itemCogs = $cogs * $qty;

                $total += $subtotal;
                $totalCogs += $itemCogs;

                $orderItems[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'variant_id' => $variantId,
                    'variant_name' => $variantName,
                    'modifiers' => empty($modifiersSnapshot) ? null : $modifiersSnapshot,
                    'quantity' => $qty,
                    'unit_price' => $price,
                    'subtotal' => $subtotal,
                    'cogs_total' => $itemCogs,
                    'notes' => $item['notes'] ?? null,
                    'serving_type' => $item['serving_type'] ?? $fulfillmentType,
                ];
            }

            if (empty($orderItems)) {
                throw new Exception('Keranjang pesanan tidak valid.');
            }

            // 3. Resolve optional voucher / promo code
            $voucherCode = ! empty($data['voucher_code']) ? trim(strtoupper($data['voucher_code'])) : null;
            $discountAmount = 0;
            $voucher = null;

            if ($voucherCode) {
                $voucher = Voucher::where('shop_id', $shop->id)
                    ->where('code', $voucherCode)
                    ->first();

                if ($voucher) {
                    $check = $voucher->validateForSubtotal($total);
                    if ($check['valid']) {
                        $discountAmount = $voucher->calculateDiscount($total);
                        $voucher->increment('used_count');
                    }
                }
            }

            $rawPaymentMethod = strtoupper(trim((string) ($data['payment_method'] ?? 'QRIS')));
            $isPaidOnline = in_array($rawPaymentMethod, ['QRIS', 'ONLINE', 'MIDTRANS', 'XENDIT', 'TRANSFER', 'E-WALLET'], true);
            $grandTotal = max(0, $total - $discountAmount);

            // 4. Create the Order Parent
            $order = Order::create([
                'shop_id' => $shop->id,
                'table_id' => $tableModel?->id,
                'customer_name' => $data['customer_name'],
                'customer_email' => $data['customer_email'] ?? null,
                'customer_phone' => $data['customer_phone'] ?? null,
                'payment_method' => $rawPaymentMethod,
                'subtotal' => $total,
                'discount_amount' => $discountAmount,
                'voucher_code' => $voucher?->code,
                'discount_reason' => $voucher?->name,
                'grand_total' => $grandTotal,
                'amount_paid' => $isPaidOnline ? $grandTotal : 0,
                'change_amount' => 0,
                'paid_at' => $isPaidOnline ? now() : null,
                'total_cogs' => $totalCogs,
                'order_status' => 'CONFIRMED',
                'payment_status' => $isPaidOnline ? 'PAID' : 'UNPAID',
                'fulfillment_type' => $fulfillmentType,
            ]);

            // 5. If paid online (e.g. QRIS/E-Wallet), record Payment ledger and link to open shift
            if ($isPaidOnline) {
                Payment::create([
                    'shop_id' => $shop->id,
                    'order_id' => $order->id,
                    'payment_method' => $rawPaymentMethod,
                    'amount' => $grandTotal,
                    'reference_number' => 'QR-'.strtoupper(uniqid()),
                ]);

                // Increment sales on open session if one exists for the shop
                $session = CashRegisterSession::whereHas('register', fn ($query) => $query->where('shop_id', $shop->id))
                    ->where('status', 'OPEN')
                    ->first();
                if ($session) {
                    if ($rawPaymentMethod === 'QRIS') {
                        $session->increment('total_qris_sales', $grandTotal);
                    } else {
                        $session->increment('total_other_sales', $grandTotal);
                    }
                }
            }

            // 4. Attach Items
            $hasPriceColumn = Schema::hasColumn('order_items', 'price');
            $hasBatchNoColumn = Schema::hasColumn('order_items', 'batch_no');
            $hasStatusColumn = Schema::hasColumn('order_items', 'status');
            $hasServingTypeColumn = Schema::hasColumn('order_items', 'serving_type');
            foreach ($orderItems as $orderItem) {
                $orderItem['order_id'] = $order->id;
                if ($hasPriceColumn && ! isset($orderItem['price'])) {
                    $orderItem['price'] = $orderItem['unit_price'] ?? 0;
                }
                if ($hasBatchNoColumn && ! isset($orderItem['batch_no'])) {
                    $orderItem['batch_no'] = 1;
                }
                if ($hasStatusColumn && ! isset($orderItem['status'])) {
                    $orderItem['status'] = 'new';
                }
                if (! $hasServingTypeColumn) {
                    unset($orderItem['serving_type']);
                }
                OrderItem::create($orderItem);
            }

            $order->load('items.product');

            // Deduct inventory stock based on recipes
            app(InventoryService::class)->deductStockForOrder($order);

            if (class_exists(ActivityLog::class)) {
                $tableLabel = $tableModel ? 'Meja '.$tableModel->name : ($order->fulfillment_type === 'TAKEAWAY' ? 'Takeaway' : 'Tanpa Meja');
                $statusLabel = $isPaidOnline ? 'Lunas ('.$rawPaymentMethod.')' : 'Belum Lunas (Bayar di Kasir)';
                $isKiosk = ($data['source'] ?? '') === 'kiosk';
                $channelLabel = $isKiosk ? 'Kiosk Mandiri' : 'QR Menu';
                ActivityLog::create([
                    'shop_id' => $shop->id,
                    'user_id' => auth()->id(),
                    'action' => $isKiosk ? 'order.kiosk_placed' : 'order.qr_placed',
                    'entity_type' => Order::class,
                    'entity_id' => $order->id,
                    'description' => 'Pesanan baru masuk via '.$channelLabel.' #'.$order->id.' ('.$tableLabel.' · '.($order->customer_name ?: 'Pelanggan').') - Total: Rp '.number_format((float) $order->grand_total, 0, ',', '.').' ['.$statusLabel.']',
                    'ip_address' => request()->ip(),
                    'user_agent' => mb_substr((string) request()->userAgent(), 0, 255),
                ]);
            }

            return $order;
        });
    }
}
