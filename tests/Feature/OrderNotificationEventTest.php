<?php

namespace Tests\Feature;

use App\Events\OrderCreated;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Shop;
use App\Models\Table;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderNotificationEventTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_created_broadcast_payload_contains_correct_totals_and_details()
    {
        $shop = Shop::factory()->create();
        $table = Table::create(['shop_id' => $shop->id, 'name' => '05']);

        $order = Order::create([
            'shop_id' => $shop->id,
            'table_id' => $table->id,
            'customer_name' => 'Budi Santoso',
            'grand_total' => 125000,
            'subtotal' => 125000,
            'fulfillment_type' => 'DINE_IN',
            'order_status' => 'CONFIRMED',
            'payment_status' => 'PAID',
        ]);

        $product = Product::factory()->create(['shop_id' => $shop->id, 'name' => 'Mont Blanc']);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_name' => 'Mont Blanc',
            'quantity' => 2,
            'unit_price' => 35000,
            'subtotal' => 70000,
        ]);

        $event = new OrderCreated($order);
        $payload = $event->broadcastWith();

        $this->assertEquals($order->id, $payload['id']);
        $this->assertEquals('#'.$order->id, $payload['order_number']);
        $this->assertEquals('Budi Santoso', $payload['customer_name']);
        $this->assertEquals('Meja 05', $payload['table']);
        $this->assertEquals('DINE_IN', $payload['fulfillment_type']);
        $this->assertEquals(125000, $payload['total']);
        $this->assertEquals(125000, $payload['total_amount']);
        $this->assertEquals(1, $payload['items_count']);
        $this->assertCount(1, $payload['items']);
        $this->assertEquals('Mont Blanc', $payload['items'][0]['name']);
        $this->assertEquals(2, $payload['items'][0]['quantity']);
        $this->assertEquals(35000, $payload['items'][0]['price']);
        $this->assertEquals('order.created', $event->broadcastAs());
    }

    public function test_order_created_handles_takeaway_without_table_and_default_customer()
    {
        $shop = Shop::factory()->create();

        $order = Order::create([
            'shop_id' => $shop->id,
            'customer_name' => null,
            'grand_total' => 45000,
            'subtotal' => 45000,
            'fulfillment_type' => 'TAKEAWAY',
            'order_status' => 'CONFIRMED',
            'payment_status' => 'PAID',
        ]);

        $event = new OrderCreated($order);
        $payload = $event->broadcastWith();

        $this->assertEquals('Walk-in Customer', $payload['customer_name']);
        $this->assertEquals('Takeaway', $payload['table']);
        $this->assertEquals('TAKEAWAY', $payload['fulfillment_type']);
        $this->assertEquals(45000, $payload['total']);
        $this->assertEquals(45000, $payload['total_amount']);
    }
}
