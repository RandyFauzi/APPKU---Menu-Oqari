<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GoBizWebhookController extends Controller
{
    public function handle(Request $request)
    {
        Log::info('GoBiz Webhook Received', $request->all());

        // Validate basic payload structure from GoBiz
        $payload = $request->all();

        if (! isset($payload['notification_type'])) {
            return response()->json(['message' => 'Invalid payload'], 400);
        }

        if ($payload['notification_type'] === 'ORDER_CREATED') {
            $this->processNewOrder($payload);
        }

        return response()->json(['success' => true]);
    }

    protected function processNewOrder(array $payload)
    {
        $outletId = $payload['outlet_id'] ?? null;
        if (! $outletId) {
            return;
        }

        $shop = Shop::where('gobiz_outlet_id', $outletId)->first();
        if (! $shop) {
            Log::warning("GoBiz Webhook: Shop not found for outlet {$outletId}");

            return;
        }

        $orderData = $payload['order'] ?? [];
        if (empty($orderData)) {
            return;
        }

        $goBizOrderId = $orderData['order_id'];

        // Prevent duplicate
        $existingOrder = Order::where('shop_id', $shop->id)
            ->where('customer_name', 'LIKE', "GoFood - {$goBizOrderId}")
            ->first();

        if ($existingOrder) {
            return;
        }

        $order = Order::create([
            'shop_id' => $shop->id,
            'table_id' => null, // Delivery order
            'customer_name' => "GoFood - {$goBizOrderId}",
            'customer_phone' => null,
            'status' => 'PENDING',
            'total_amount' => $orderData['total_price'] ?? 0,
        ]);

        $items = $orderData['items'] ?? [];
        foreach ($items as $item) {
            // partner_item_id is our product ID
            $productId = $item['partner_item_id'] ?? null;
            $product = Product::find($productId);

            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $productId,
                'qty' => $item['quantity'] ?? 1,
                'subtotal' => $item['total_price'] ?? 0,
            ]);
        }

        // Ensure Live Order websocket is triggered here if you have an Event,
        // e.g. broadcast(new \App\Events\OrderCreated($order));
    }
}
