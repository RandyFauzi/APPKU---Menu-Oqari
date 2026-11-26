<?php

namespace Tests\Feature;

use App\Events\OrderCreated;
use App\Events\PosCustomerAction;
use App\Events\PosDisplayUpdated;
use App\Models\Order;
use App\Models\Product;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class PosCustomerDisplayTest extends TestCase
{
    use RefreshDatabase;

    public function test_pos_user_can_view_customer_display(): void
    {
        $shop = Shop::factory()->create();
        $user = User::factory()->create([
            'shop_id' => $shop->id,
            'role' => 'admin',
        ]);

        $response = $this->actingAs($user)
            ->get(route('admin.pos.customer-display'));

        $response->assertOk()
            ->assertSee('Layar Informasi Pelanggan');
    }

    public function test_pos_user_can_sync_customer_display_and_dispatch_event(): void
    {
        Event::fake([PosDisplayUpdated::class]);

        $shop = Shop::factory()->create();
        $user = User::factory()->create([
            'shop_id' => $shop->id,
            'role' => 'admin',
        ]);

        $response = $this->actingAs($user)
            ->postJson(route('admin.pos.customer-display.sync'), [
                'status' => 'ordering',
                'items' => [
                    [
                        'name' => 'Kopi Susu Gula Aren',
                        'variant' => 'Ice',
                        'modifiers' => ['Less Sugar'],
                        'qty' => 2,
                        'price' => 25000,
                        'subtotal' => 50000,
                    ],
                ],
                'subtotal' => 50000,
                'discount_amount' => 5000,
                'discount_label' => 'Promo 10%',
                'grand_total' => 45000,
                'customer_name' => 'Budi',
                'table_name' => 'Meja 03',
                'fulfillment_type' => 'DINE_IN',
                'payment_method' => 'CASH',
            ]);

        $response->assertOk()
            ->assertJsonPath('success', true);

        Event::assertDispatched(PosDisplayUpdated::class, function (PosDisplayUpdated $event) use ($shop): bool {
            return $event->shopId === $shop->id
                && $event->payload['status'] === 'ordering'
                && (int) $event->payload['grand_total'] === 45000
                && count($event->payload['items']) === 1;
        });
    }

    public function test_sync_customer_display_validates_status(): void
    {
        $shop = Shop::factory()->create();
        $user = User::factory()->create([
            'shop_id' => $shop->id,
            'role' => 'admin',
        ]);

        $response = $this->actingAs($user)
            ->postJson(route('admin.pos.customer-display.sync'), [
                'status' => 'invalid_state',
                'subtotal' => 0,
                'grand_total' => 0,
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['status']);
    }

    public function test_pos_user_can_view_customer_kiosk_touchscreen_mode(): void
    {
        $shop = Shop::factory()->create();
        $user = User::factory()->create([
            'shop_id' => $shop->id,
            'role' => 'admin',
        ]);

        $response = $this->actingAs($user)
            ->get(route('admin.pos.customer-kiosk'));

        $response->assertOk()
            ->assertSee('Pilih Menu Mandiri (Touchscreen)');
    }

    public function test_customer_kiosk_action_dispatches_event(): void
    {
        Event::fake([PosCustomerAction::class]);

        $shop = Shop::factory()->create();
        $user = User::factory()->create([
            'shop_id' => $shop->id,
            'role' => 'admin',
        ]);

        $response = $this->actingAs($user)
            ->postJson(route('admin.pos.customer-kiosk.action'), [
                'action' => 'add_item',
                'item' => [
                    'id' => 1,
                    'name' => 'Americano',
                    'qty' => 1,
                    'price' => 20000,
                    'subtotal' => 20000,
                ],
            ]);

        $response->assertOk()
            ->assertJsonPath('success', true);

        Event::assertDispatched(PosCustomerAction::class, function (PosCustomerAction $event) use ($shop): bool {
            return $event->shopId === $shop->id
                && $event->payload['action'] === 'add_item';
        });
    }

    public function test_customer_kiosk_renders_payment_method_selection_and_qris_flow(): void
    {
        $shop = Shop::factory()->create();
        $user = User::factory()->create([
            'shop_id' => $shop->id,
            'role' => 'admin',
        ]);

        $response = $this->actingAs($user)
            ->get(route('admin.pos.customer-kiosk'));

        $response->assertOk()
            ->assertSee('Pilih Pembayaran')
            ->assertSee('QRIS (Bayar Langsung)')
            ->assertSee('Bayar di Kasir (Tunai)')
            ->assertSee('Kartu Debit / Kredit (EDC)')
            ->assertSee('Konfirmasi Sudah Bayar');
    }

    public function test_customer_kiosk_checkout_with_qris_creates_paid_order_and_payment(): void
    {
        Event::fake([OrderCreated::class, PosDisplayUpdated::class]);

        $shop = Shop::factory()->create();
        $user = User::factory()->create([
            'shop_id' => $shop->id,
            'role' => 'admin',
        ]);
        $product = Product::factory()->create([
            'shop_id' => $shop->id,
            'price' => 35000,
            'is_sold_out' => false,
        ]);

        $response = $this->actingAs($user)
            ->postJson(route('admin.pos.customer-kiosk.checkout'), [
                'customer_name' => 'Andi Kiosk',
                'fulfillment_type' => 'TAKEAWAY',
                'payment_method' => 'QRIS',
                'items' => [
                    [
                        'id' => $product->id,
                        'qty' => 2,
                        'serving_type' => 'TAKEAWAY',
                    ],
                ],
            ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('order.payment_status', 'PAID')
            ->assertJsonPath('order.payment_method', 'QRIS');

        $orderId = $response->json('order.id');
        $this->assertDatabaseHas('orders', [
            'id' => $orderId,
            'shop_id' => $shop->id,
            'customer_name' => 'Andi Kiosk',
            'payment_method' => 'QRIS',
            'payment_status' => 'PAID',
            'grand_total' => 70000,
            'amount_paid' => 70000,
        ]);

        $this->assertDatabaseHas('payments', [
            'shop_id' => $shop->id,
            'order_id' => $orderId,
            'payment_method' => 'QRIS',
            'amount' => 70000,
        ]);

        Event::assertDispatched(OrderCreated::class);
    }

    public function test_customer_kiosk_checkout_with_cash_creates_unpaid_order_for_cashier(): void
    {
        Event::fake([OrderCreated::class, PosDisplayUpdated::class]);

        $shop = Shop::factory()->create();
        $user = User::factory()->create([
            'shop_id' => $shop->id,
            'role' => 'admin',
        ]);
        $product = Product::factory()->create([
            'shop_id' => $shop->id,
            'price' => 20000,
            'is_sold_out' => false,
        ]);

        $response = $this->actingAs($user)
            ->postJson(route('admin.pos.customer-kiosk.checkout'), [
                'customer_name' => 'Budi Tunai',
                'table_id' => '05',
                'fulfillment_type' => 'DINE_IN',
                'payment_method' => 'CASH',
                'items' => [
                    [
                        'id' => $product->id,
                        'qty' => 1,
                        'serving_type' => 'DINE_IN',
                    ],
                ],
            ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('order.payment_status', 'UNPAID')
            ->assertJsonPath('order.payment_method', 'CASH');

        $order = Order::findOrFail($response->json('order.id'));
        $this->assertNull($order->paid_at);
        $this->assertDatabaseMissing('payments', [
            'order_id' => $order->id,
        ]);

        Event::assertDispatched(OrderCreated::class);
    }
}
