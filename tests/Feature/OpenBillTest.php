<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use App\Models\Shop;
use App\Models\Table;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OpenBillTest extends TestCase
{
    use RefreshDatabase;

    public function test_cashier_can_create_open_bill_with_table_and_customer(): void
    {
        $shop = Shop::factory()->create(['onboarding_status' => 'completed']);
        $cashier = User::factory()->create(['shop_id' => $shop->id, 'role' => 'cashier']);
        $table = Table::create(['shop_id' => $shop->id, 'name' => 'Meja 08']);
        $product = Product::factory()->create(['shop_id' => $shop->id, 'price' => 30000]);

        $payload = [
            'table' => 'Meja 08',
            'customer_name' => 'Pak Joko',
            'fulfillment_type' => 'DINE_IN',
            'items' => [
                ['id' => $product->id, 'qty' => 2, 'notes' => 'Hangat'],
            ],
        ];

        $response = $this->actingAs($cashier)->postJson(route('admin.pos.orders.hold'), $payload);

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'table' => 'Meja 08',
                'customer_name' => 'Pak Joko',
                'total' => 60000,
                'updated' => false,
            ]);

        $order = Order::findOrFail($response->json('order_id'));
        $this->assertSame('HOLD', $order->order_status);
        $this->assertSame('UNPAID', $order->payment_status);
        $this->assertSame($table->id, $order->table_id);
        $this->assertSame('Pak Joko', $order->customer_name);
    }

    public function test_cashier_can_recall_and_update_existing_open_bill(): void
    {
        $shop = Shop::factory()->create(['onboarding_status' => 'completed']);
        $cashier = User::factory()->create(['shop_id' => $shop->id, 'role' => 'cashier']);
        $product1 = Product::factory()->create(['shop_id' => $shop->id, 'price' => 25000]);
        $product2 = Product::factory()->create(['shop_id' => $shop->id, 'price' => 15000]);

        // Step 1: Open initial bill
        $initialPayload = [
            'table' => 'Meja 03',
            'customer_name' => 'Andi',
            'fulfillment_type' => 'DINE_IN',
            'items' => [
                ['id' => $product1->id, 'qty' => 1],
            ],
        ];
        $initialRes = $this->actingAs($cashier)->postJson(route('admin.pos.orders.hold'), $initialPayload)->assertOk();
        $orderId = $initialRes->json('order_id');

        // Step 2: Recall bill
        $recallRes = $this->actingAs($cashier)->postJson("/admin/pos/orders/{$orderId}/recall")->assertOk();
        $this->assertSame($orderId, $recallRes->json('order.id'));

        // Step 3: Add new product and update the open bill
        $updatePayload = [
            'recalled_order_id' => $orderId,
            'table' => 'Meja 03',
            'customer_name' => 'Andi',
            'fulfillment_type' => 'DINE_IN',
            'items' => [
                ['id' => $product1->id, 'qty' => 1],
                ['id' => $product2->id, 'qty' => 2],
            ],
        ];

        $updateRes = $this->actingAs($cashier)->postJson(route('admin.pos.orders.hold'), $updatePayload);

        $updateRes->assertOk()->assertJson([
            'success' => true,
            'order_id' => $orderId,
            'total' => 55000,
            'updated' => true,
        ]);

        $order = Order::findOrFail($orderId);
        $this->assertSame('HOLD', $order->order_status);
        $this->assertSame(55000.0, (float) $order->grand_total);
        $this->assertCount(2, $order->items);
    }

    public function test_cashier_can_cancel_open_bill(): void
    {
        $shop = Shop::factory()->create(['onboarding_status' => 'completed']);
        $cashier = User::factory()->create(['shop_id' => $shop->id, 'role' => 'cashier']);
        $product = Product::factory()->create(['shop_id' => $shop->id, 'price' => 20000]);

        $payload = [
            'table' => 'Meja 12',
            'items' => [['id' => $product->id, 'qty' => 1]],
        ];
        $res = $this->actingAs($cashier)->postJson(route('admin.pos.orders.hold'), $payload)->assertOk();
        $orderId = $res->json('order_id');

        $cancelRes = $this->actingAs($cashier)->deleteJson("/admin/pos/orders/{$orderId}/hold");
        $cancelRes->assertOk()->assertJson(['success' => true]);

        $order = Order::findOrFail($orderId);
        $this->assertSame('CANCELLED', $order->order_status);
    }

    public function test_open_bill_prints_proforma_unpaid_watermark(): void
    {
        $shop = Shop::factory()->create(['onboarding_status' => 'completed', 'name' => 'Coffee Lounge']);
        $cashier = User::factory()->create(['shop_id' => $shop->id, 'role' => 'cashier']);
        $product = Product::factory()->create(['shop_id' => $shop->id, 'price' => 20000]);

        $payload = [
            'table' => 'Meja VIP',
            'customer_name' => 'Bpk Ronald',
            'items' => [['id' => $product->id, 'qty' => 1]],
        ];
        $res = $this->actingAs($cashier)->postJson(route('admin.pos.orders.hold'), $payload)->assertOk();
        $orderId = $res->json('order_id');

        $owner = User::factory()->create(['shop_id' => $shop->id, 'role' => 'owner']);
        $printRes = $this->actingAs($owner)->get("/admin/api/orders/{$orderId}/print?type=customer");

        $printRes->assertOk();
        $printRes->assertSee('TAGIHAN SEMENTARA (OPEN BILL)');
        $printRes->assertSee('Belum Lunas');
    }
}
