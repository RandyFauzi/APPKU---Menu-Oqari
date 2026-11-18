<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Modifier;
use App\Models\ModifierGroup;
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

    public function test_pos_products_endpoint_returns_category_and_sold_count_for_open_bill_picker(): void
    {
        $shop = Shop::factory()->create(['onboarding_status' => 'completed']);
        $cashier = User::factory()->create(['shop_id' => $shop->id, 'role' => 'cashier']);
        $category = Category::create(['shop_id' => $shop->id, 'name' => 'Main Course', 'slug' => 'main-course', 'sort_order' => 1]);
        $product = Product::factory()->create([
            'shop_id' => $shop->id,
            'category_id' => $category->id,
            'name' => 'Beef Burger',
            'price' => 68000,
            'is_sold_out' => false,
            'is_best_seller' => true,
        ]);

        $res = $this->actingAs($cashier)->getJson(route('admin.pos.products'));
        $res->assertOk();
        $res->assertJsonFragment([
            'id' => $product->id,
            'name' => 'Beef Burger',
            'category_name' => 'Main Course',
            'sold_count' => 0,
        ]);

        $bestSellerRes = $this->actingAs($cashier)->getJson(route('admin.pos.products', ['category_id' => 'best_seller']));
        $bestSellerRes->assertOk();
        $this->assertCount(1, $bestSellerRes->json());
    }

    public function test_open_bill_supports_mixed_dine_in_and_takeaway_items(): void
    {
        $shop = Shop::factory()->create(['onboarding_status' => 'completed']);
        $cashier = User::factory()->create(['shop_id' => $shop->id, 'role' => 'cashier']);
        $p1 = Product::factory()->create(['shop_id' => $shop->id, 'name' => 'Es Kopi Susu', 'price' => 28000]);
        $p2 = Product::factory()->create(['shop_id' => $shop->id, 'name' => 'Chicken Katsu', 'price' => 48000]);

        $payload = [
            'table' => 'Meja 04',
            'customer_name' => 'Nadia',
            'items' => [
                ['id' => $p1->id, 'qty' => 2, 'serving_type' => 'DINE_IN', 'notes' => 'tanpa gula, es sedikit'],
                ['id' => $p2->id, 'qty' => 1, 'serving_type' => 'TAKEAWAY', 'notes' => 'sambal dipisah'],
            ],
        ];

        $res = $this->actingAs($cashier)->postJson(route('admin.pos.orders.hold'), $payload)->assertOk();
        $orderId = $res->json('order_id');

        $order = Order::with('items')->findOrFail($orderId);
        $this->assertCount(2, $order->items);

        $dineInItem = $order->items->firstWhere('product_id', $p1->id);
        $takeawayItem = $order->items->firstWhere('product_id', $p2->id);

        $this->assertSame('DINE_IN', $dineInItem->serving_type);
        $this->assertSame('TAKEAWAY', $takeawayItem->serving_type);

        // Test Recall Order returns serving_type
        $recallRes = $this->actingAs($cashier)->postJson("/admin/pos/orders/{$orderId}/recall", [])->assertOk();
        $recalledItems = $recallRes->json('order.items');
        $this->assertSame('DINE_IN', $recalledItems[0]['serving_type']);
        $this->assertSame('TAKEAWAY', $recalledItems[1]['serving_type']);

        // Test Print Thermal Separates Dine In and Takeaway
        $printRes = $this->actingAs($cashier)->get("/admin/api/orders/{$orderId}/print?type=customer")->assertOk();
        $printRes->assertSee('DINE IN / MAKAN DI TEMPAT');
        $printRes->assertSee('TAKEAWAY / BUNGKUS');
        $printRes->assertSee('Es Kopi Susu');
        $printRes->assertSee('Chicken Katsu');
    }

    public function test_cashier_can_update_open_bill_with_walk_in_customer_and_modifiers(): void
    {
        $shop = Shop::factory()->create(['onboarding_status' => 'completed']);
        $cashier = User::factory()->create(['shop_id' => $shop->id, 'role' => 'cashier']);
        $product = Product::factory()->create(['shop_id' => $shop->id, 'name' => 'Kopi Tubruk', 'price' => 15000]);
        $modGroup = ModifierGroup::create(['shop_id' => $shop->id, 'name' => 'Gula', 'selection_type' => 'SINGLE', 'is_active' => true]);
        $mod = Modifier::create(['modifier_group_id' => $modGroup->id, 'name' => 'Less Sugar', 'price_adjustment' => 0, 'is_active' => true]);
        $product->modifierGroups()->attach($modGroup->id);

        // Initial Hold: Walk-in without table
        $initialRes = $this->actingAs($cashier)->postJson(route('admin.pos.orders.hold'), [
            'fulfillment_type' => 'DINE_IN',
            'customer_name' => 'Walk-in Customer',
            'items' => [['id' => $product->id, 'qty' => 1]],
        ])->assertOk();
        $orderId = $initialRes->json('order_id');

        // Update Hold with modifier
        $updateRes = $this->actingAs($cashier)->postJson(route('admin.pos.orders.hold'), [
            'recalled_order_id' => $orderId,
            'fulfillment_type' => 'DINE_IN',
            'customer_name' => 'Walk-in Customer',
            'items' => [
                ['id' => $product->id, 'qty' => 2, 'modifiers' => [$mod->id]],
            ],
        ])->assertOk()->assertJson(['success' => true, 'updated' => true, 'order_id' => $orderId]);

        $order = Order::with('items')->findOrFail($orderId);
        $this->assertEquals(30000, $order->grand_total);
        $this->assertCount(1, $order->items);
        $this->assertEquals(2, $order->items[0]->quantity);
        $this->assertNotNull($order->items[0]->modifiers);
    }

    public function test_cashier_can_append_sub_order_batch_to_open_bill_without_resetting_previous_completed_items(): void
    {
        $shop = Shop::factory()->create(['onboarding_status' => 'completed']);
        $cashier = User::factory()->create(['shop_id' => $shop->id, 'role' => 'cashier']);
        $table = Table::create(['shop_id' => $shop->id, 'name' => '02']);

        $americano = Product::factory()->create(['shop_id' => $shop->id, 'name' => 'Americano', 'price' => 25000]);
        $montBlanc = Product::factory()->create(['shop_id' => $shop->id, 'name' => 'Mont Blanc', 'price' => 35000]);
        $blueOcean = Product::factory()->create(['shop_id' => $shop->id, 'name' => 'Blue Ocean', 'price' => 45000]);
        $nasiGoreng = Product::factory()->create(['shop_id' => $shop->id, 'name' => 'Nasi Goreng', 'price' => 40000]);

        // Round 1: Order 3 items (Americano, Mont Blanc, Blue Ocean)
        $r1Payload = [
            'table' => '02',
            'customer_name' => 'Budi',
            'fulfillment_type' => 'DINE_IN',
            'items' => [
                ['id' => $americano->id, 'qty' => 1],
                ['id' => $montBlanc->id, 'qty' => 1],
                ['id' => $blueOcean->id, 'qty' => 1],
            ],
        ];

        $r1Res = $this->actingAs($cashier)->postJson(route('admin.pos.orders.hold'), $r1Payload)->assertOk();
        $orderId = $r1Res->json('order_id');
        $this->assertEquals(105000, $r1Res->json('total'));

        // Advance Round 1 items to 'completed' (Selesai dikerjakan)
        $batchStatusRes = $this->actingAs($cashier)->postJson("/admin/api/orders/{$orderId}/batch-status", [
            'batch_no' => 1,
            'status' => 'completed',
        ])->assertOk();
        $this->assertSame('completed', $batchStatusRes->json('batch_status'));

        // Verify Round 1 items are all 'completed' and batch_no = 1
        $order = Order::with('items')->findOrFail($orderId);
        $this->assertCount(3, $order->items);
        foreach ($order->items as $item) {
            $this->assertEquals(1, $item->batch_no);
            $this->assertSame('completed', $item->status);
        }

        // Round 2: Customer orders additional item: Nasi Goreng x1 with append_batch = true
        $r2Payload = [
            'recalled_order_id' => $orderId,
            'append_batch' => true,
            'table' => '02',
            'customer_name' => 'Budi',
            'fulfillment_type' => 'DINE_IN',
            'items' => [
                ['id' => $nasiGoreng->id, 'qty' => 1],
            ],
        ];

        $r2Res = $this->actingAs($cashier)->postJson(route('admin.pos.orders.hold'), $r2Payload)->assertOk();
        $r2Res->assertJson([
            'success' => true,
            'order_id' => $orderId,
            'total' => 145000,
            'updated' => true,
            'batch_no' => 2,
        ]);

        // Reload order: Must have 4 items total!
        // Round 1 items MUST retain batch_no = 1 and status = 'completed'!
        // Round 2 item (Nasi Goreng) MUST have batch_no = 2 and status = 'new'!
        $order->refresh();
        $order->load('items');

        $this->assertCount(4, $order->items);
        $this->assertEquals(145000, (float) $order->grand_total);
        $this->assertSame('UNPAID', $order->payment_status);

        $round1Items = $order->items->where('batch_no', 1);
        $this->assertCount(3, $round1Items);
        foreach ($round1Items as $item) {
            $this->assertSame('completed', $item->status, 'Round 1 items must stay completed!');
        }

        $round2Items = $order->items->where('batch_no', 2);
        $this->assertCount(1, $round2Items);
        $this->assertSame('new', $round2Items->first()->status, 'Round 2 item must have status new!');
        $this->assertSame('Nasi Goreng', $round2Items->first()->product_name);
    }

    public function test_cashier_can_transition_batch_statuses_independently(): void
    {
        $shop = Shop::factory()->create(['onboarding_status' => 'completed']);
        $cashier = User::factory()->create(['shop_id' => $shop->id, 'role' => 'cashier']);
        $table = Table::create(['shop_id' => $shop->id, 'name' => '05']);

        $tea = Product::factory()->create(['shop_id' => $shop->id, 'name' => 'Lemon Tea', 'price' => 18000]);
        $burger = Product::factory()->create(['shop_id' => $shop->id, 'name' => 'Beef Burger', 'price' => 45000]);

        // Batch 1: Lemon Tea
        $r1 = $this->actingAs($cashier)->postJson(route('admin.pos.orders.hold'), [
            'table' => '05',
            'customer_name' => 'Siti',
            'fulfillment_type' => 'DINE_IN',
            'items' => [['id' => $tea->id, 'qty' => 1]],
        ])->assertOk();
        $orderId = $r1->json('order_id');

        // Mark Batch 1 as ready
        $this->actingAs($cashier)->postJson("/admin/api/orders/{$orderId}/batch-status", [
            'batch_no' => 1,
            'status' => 'ready',
        ])->assertOk()->assertJson(['batch_status' => 'ready']);

        // Batch 2: Beef Burger
        $r2 = $this->actingAs($cashier)->postJson(route('admin.pos.orders.hold'), [
            'recalled_order_id' => $orderId,
            'append_batch' => true,
            'table' => '05',
            'customer_name' => 'Siti',
            'fulfillment_type' => 'DINE_IN',
            'items' => [['id' => $burger->id, 'qty' => 1]],
        ])->assertOk();

        // Advance Batch 2 to cooking
        $this->actingAs($cashier)->postJson("/admin/api/orders/{$orderId}/batch-status", [
            'batch_no' => 2,
            'status' => 'cooking',
        ])->assertOk()->assertJson(['batch_status' => 'cooking']);

        $order = Order::with('items')->findOrFail($orderId);
        $this->assertSame('ready', $order->items->where('batch_no', 1)->first()->status);
        $this->assertSame('cooking', $order->items->where('batch_no', 2)->first()->status);
    }

    public function test_cashier_can_append_batch_gracefully_when_table_schema_is_checked(): void
    {
        $shop = Shop::factory()->create(['onboarding_status' => 'completed']);
        $cashier = User::factory()->create(['shop_id' => $shop->id, 'role' => 'cashier']);
        $product = Product::factory()->create(['shop_id' => $shop->id, 'price' => 20000]);

        $r1 = $this->actingAs($cashier)->postJson(route('admin.pos.orders.hold'), [
            'table' => '10',
            'customer_name' => 'Doni',
            'items' => [['id' => $product->id, 'qty' => 1]],
        ])->assertOk();
        $orderId = $r1->json('order_id');

        $r2 = $this->actingAs($cashier)->postJson(route('admin.pos.orders.hold'), [
            'recalled_order_id' => $orderId,
            'append_batch' => true,
            'items' => [['id' => $product->id, 'qty' => 2]],
        ]);

        $r2->assertOk()->assertJson(['success' => true, 'updated' => true]);
        $order = Order::with('items')->findOrFail($orderId);
        $this->assertEquals(60000, (float) $order->grand_total);
    }

    public function test_cashier_can_submit_delivery_order_with_delivery_serving_type(): void
    {
        $shop = Shop::factory()->create(['onboarding_status' => 'completed']);
        $cashier = User::factory()->create(['shop_id' => $shop->id, 'role' => 'cashier']);
        $register = \App\Models\CashRegister::create(['shop_id' => $shop->id, 'name' => 'Main Register', 'is_active' => true]);
        \App\Models\CashRegisterSession::create([
            'cash_register_id' => $register->id,
            'user_id' => $cashier->id,
            'opened_at' => now(),
            'opening_cash' => 100000,
            'status' => 'OPEN',
        ]);
        $product = Product::factory()->create(['shop_id' => $shop->id, 'price' => 35000]);

        $payload = [
            'request_id' => (string) \Illuminate\Support\Str::uuid(),
            'customer_name' => 'Budi Delivery',
            'fulfillment_type' => 'DELIVERY',
            'payment_method' => 'CASH',
            'amount_paid' => 35000,
            'items' => [
                [
                    'id' => $product->id,
                    'qty' => 1,
                    'serving_type' => 'DELIVERY',
                    'notes' => 'Tolong antar cepat ya',
                ],
            ],
        ];

        $response = $this->actingAs($cashier)->postJson(route('admin.pos.orders.submit'), $payload);

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'total' => 35000,
            ]);

        $order = Order::findOrFail($response->json('order_id'));
        $this->assertSame('CONFIRMED', $order->order_status);
        $this->assertSame('PAID', $order->payment_status);
        $this->assertSame('DELIVERY', $order->fulfillment_type);
        $this->assertSame('Budi Delivery', $order->customer_name);
        $this->assertNull($order->table_id);
        $this->assertSame('DELIVERY', $order->items->first()->serving_type);
    }

    public function test_cashier_can_hold_and_recall_delivery_order_with_delivery_serving_type(): void
    {
        $shop = Shop::factory()->create(['onboarding_status' => 'completed']);
        $cashier = User::factory()->create(['shop_id' => $shop->id, 'role' => 'cashier']);
        $product = Product::factory()->create(['shop_id' => $shop->id, 'price' => 35000]);

        $holdPayload = [
            'customer_name' => 'Siti Delivery',
            'fulfillment_type' => 'DELIVERY',
            'items' => [
                [
                    'id' => $product->id,
                    'qty' => 1,
                    'serving_type' => 'DELIVERY',
                ],
            ],
        ];

        $holdRes = $this->actingAs($cashier)->postJson(route('admin.pos.orders.hold'), $holdPayload);
        $holdRes->assertOk()->assertJson(['success' => true]);
        $orderId = $holdRes->json('order_id');

        $order = Order::findOrFail($orderId);
        $this->assertSame('HOLD', $order->order_status);
        $this->assertSame('DELIVERY', $order->fulfillment_type);
        $this->assertSame('DELIVERY', $order->items->first()->serving_type);

        $recallRes = $this->actingAs($cashier)->postJson("/admin/pos/orders/{$orderId}/recall");
        $recallRes->assertOk()
            ->assertJsonPath('order.fulfillment_type', 'DELIVERY')
            ->assertJsonPath('order.items.0.serving_type', 'DELIVERY');
    }
}
