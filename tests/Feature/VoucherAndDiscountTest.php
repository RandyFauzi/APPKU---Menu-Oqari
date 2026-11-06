<?php

namespace Tests\Feature;

use App\Models\CashRegister;
use App\Models\CashRegisterSession;
use App\Models\Order;
use App\Models\Product;
use App\Models\Shop;
use App\Models\Table;
use App\Models\User;
use App\Models\Voucher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class VoucherAndDiscountTest extends TestCase
{
    use RefreshDatabase;

    public function test_cashier_can_validate_active_voucher_successfully(): void
    {
        $shop = Shop::factory()->create(['onboarding_status' => 'completed']);
        $cashier = User::factory()->create(['shop_id' => $shop->id, 'role' => 'cashier']);

        $voucher = Voucher::create([
            'shop_id' => $shop->id,
            'code' => 'HEMAT20',
            'name' => 'Diskon 20% Promo',
            'discount_type' => 'percentage',
            'discount_value' => 20,
            'min_spend' => 50000,
            'max_discount' => 25000,
            'is_active' => true,
        ]);

        $response = $this->actingAs($cashier)->postJson(route('admin.pos.vouchers.validate'), [
            'code' => 'hemat20', // case-insensitive input
            'subtotal' => 100000,
        ]);

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'discount_amount' => 20000, // 20% of 100,000 = 20,000 <= 25,000
                'net_total' => 80000,
                'voucher' => [
                    'code' => 'HEMAT20',
                    'name' => 'Diskon 20% Promo',
                ],
            ]);
    }

    public function test_voucher_validation_fails_when_subtotal_is_below_min_spend(): void
    {
        $shop = Shop::factory()->create(['onboarding_status' => 'completed']);
        $cashier = User::factory()->create(['shop_id' => $shop->id, 'role' => 'cashier']);

        Voucher::create([
            'shop_id' => $shop->id,
            'code' => 'MIN100K',
            'name' => 'Min 100k',
            'discount_type' => 'fixed',
            'discount_value' => 15000,
            'min_spend' => 100000,
            'is_active' => true,
        ]);

        $response = $this->actingAs($cashier)->postJson(route('admin.pos.vouchers.validate'), [
            'code' => 'MIN100K',
            'subtotal' => 50000,
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
            ]);
    }

    public function test_voucher_validation_fails_when_expired_or_inactive(): void
    {
        $shop = Shop::factory()->create(['onboarding_status' => 'completed']);
        $cashier = User::factory()->create(['shop_id' => $shop->id, 'role' => 'cashier']);

        Voucher::create([
            'shop_id' => $shop->id,
            'code' => 'EXPIRED',
            'name' => 'Expired Coupon',
            'discount_type' => 'percentage',
            'discount_value' => 10,
            'expires_at' => now()->subDay(),
            'is_active' => true,
        ]);

        $response = $this->actingAs($cashier)->postJson(route('admin.pos.vouchers.validate'), [
            'code' => 'EXPIRED',
            'subtotal' => 100000,
        ]);

        $response->assertStatus(422);
    }

    public function test_voucher_validation_fails_when_usage_limit_reached(): void
    {
        $shop = Shop::factory()->create(['onboarding_status' => 'completed']);
        $cashier = User::factory()->create(['shop_id' => $shop->id, 'role' => 'cashier']);

        Voucher::create([
            'shop_id' => $shop->id,
            'code' => 'LIMITED',
            'name' => 'Limited Coupon',
            'discount_type' => 'fixed',
            'discount_value' => 10000,
            'usage_limit' => 5,
            'used_count' => 5,
            'is_active' => true,
        ]);

        $response = $this->actingAs($cashier)->postJson(route('admin.pos.vouchers.validate'), [
            'code' => 'LIMITED',
            'subtotal' => 100000,
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Batas kuota penggunaan voucher telah habis.',
            ]);
    }

    public function test_cashier_can_submit_order_with_voucher_and_it_increments_used_count(): void
    {
        $shop = Shop::factory()->create(['onboarding_status' => 'completed']);
        $cashier = User::factory()->create(['shop_id' => $shop->id, 'role' => 'cashier']);
        $register = CashRegister::create(['shop_id' => $shop->id, 'name' => 'Main', 'is_active' => true]);
        CashRegisterSession::create(['cash_register_id' => $register->id, 'user_id' => $cashier->id, 'opened_at' => now(), 'opening_cash' => 0, 'status' => 'OPEN']);
        $table = Table::create(['shop_id' => $shop->id, 'name' => 'Meja 01']);
        $product = Product::factory()->create(['shop_id' => $shop->id, 'price' => 50000]);

        $voucher = Voucher::create([
            'shop_id' => $shop->id,
            'code' => 'CAPTEST',
            'name' => 'Promo Diskon 50%',
            'discount_type' => 'percentage',
            'discount_value' => 50,
            'max_discount' => 20000, // Caps at 20,000 even though 50% of 100,000 is 50,000
            'used_count' => 0,
            'is_active' => true,
        ]);

        $payload = [
            'request_id' => (string) Str::uuid(),
            'table' => 'Meja 01',
            'payment_method' => 'CASH',
            'amount_paid' => 80000,
            'items' => [
                ['id' => $product->id, 'qty' => 2], // 100,000
            ],
            'voucher_code' => 'CAPTEST',
        ];

        $response = $this->actingAs($cashier)->postJson(route('admin.pos.orders.submit'), $payload);

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'total' => 80000, // 100,000 - 20,000 = 80,000
            ]);

        $order = Order::findOrFail($response->json('order_id'));
        $this->assertEquals(100000, $order->subtotal);
        $this->assertEquals(20000, $order->discount_amount);
        $this->assertEquals('CAPTEST', $order->voucher_code);
        $this->assertEquals(80000, $order->grand_total);

        $voucher->refresh();
        $this->assertSame(1, $voucher->used_count);
    }

    public function test_cashier_can_submit_order_with_manual_discount(): void
    {
        $shop = Shop::factory()->create(['onboarding_status' => 'completed']);
        $cashier = User::factory()->create(['shop_id' => $shop->id, 'role' => 'cashier']);
        $register = CashRegister::create(['shop_id' => $shop->id, 'name' => 'Main', 'is_active' => true]);
        CashRegisterSession::create(['cash_register_id' => $register->id, 'user_id' => $cashier->id, 'opened_at' => now(), 'opening_cash' => 0, 'status' => 'OPEN']);
        $product = Product::factory()->create(['shop_id' => $shop->id, 'price' => 40000]);

        $payload = [
            'request_id' => (string) Str::uuid(),
            'payment_method' => 'CASH',
            'amount_paid' => 30000,
            'items' => [
                ['id' => $product->id, 'qty' => 1],
            ],
            'discount_type' => 'fixed',
            'discount_value' => 10000,
            'discount_reason' => 'Teman Owner',
        ];

        $response = $this->actingAs($cashier)->postJson(route('admin.pos.orders.submit'), $payload);

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'total' => 30000,
            ]);

        $order = Order::findOrFail($response->json('order_id'));
        $this->assertEquals(10000, $order->discount_amount);
        $this->assertEquals('Teman Owner', $order->discount_reason);
        $this->assertEquals(30000, $order->grand_total);
    }

    public function test_cashier_can_create_open_bill_with_discount(): void
    {
        $shop = Shop::factory()->create(['onboarding_status' => 'completed']);
        $cashier = User::factory()->create(['shop_id' => $shop->id, 'role' => 'cashier']);
        $product = Product::factory()->create(['shop_id' => $shop->id, 'price' => 50000]);

        $payload = [
            'table' => 'Meja 05',
            'customer_name' => 'Pak Budi',
            'items' => [
                ['id' => $product->id, 'qty' => 2], // 100,000
            ],
            'discount_type' => 'percentage',
            'discount_value' => 10,
            'discount_reason' => 'Diskon 10%',
        ];

        $response = $this->actingAs($cashier)->postJson(route('admin.pos.orders.hold'), $payload);

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'total' => 90000,
            ]);

        $order = Order::findOrFail($response->json('order_id'));
        $this->assertEquals(10000, $order->discount_amount);
        $this->assertEquals(90000, $order->grand_total);
        $this->assertEquals('Diskon 10%', $order->discount_reason);
        $this->assertSame('HOLD', $order->order_status);
    }

    public function test_owner_can_manage_vouchers_crud(): void
    {
        $shop = Shop::factory()->create(['onboarding_status' => 'completed']);
        $owner = User::factory()->create(['shop_id' => $shop->id, 'role' => 'owner']);

        // 1. Create Voucher
        $createRes = $this->actingAs($owner)->postJson(route('admin.vouchers.store'), [
            'code' => 'newpromo',
            'name' => 'Promo Spesial',
            'discount_type' => 'percentage',
            'discount_value' => 15,
            'min_spend' => 30000,
            'is_active' => true,
        ]);

        $createRes->assertOk()
            ->assertJson([
                'success' => true,
                'voucher' => [
                    'code' => 'NEWPROMO',
                    'name' => 'Promo Spesial',
                    'discount_value' => 15,
                ],
            ]);

        $voucherId = $createRes->json('voucher.id');
        $voucher = Voucher::findOrFail($voucherId);
        $this->assertSame('NEWPROMO', $voucher->code);

        // 2. Toggle Voucher
        $toggleRes = $this->actingAs($owner)->patchJson(route('admin.vouchers.toggle', $voucher));
        $toggleRes->assertOk();
        $this->assertFalse($voucher->fresh()->is_active);

        // 3. Update Voucher
        $updateRes = $this->actingAs($owner)->putJson(route('admin.vouchers.update', $voucher), [
            'code' => 'NEWPROMO_V2',
            'name' => 'Promo Spesial V2',
            'discount_type' => 'fixed',
            'discount_value' => 20000,
            'min_spend' => 50000,
            'is_active' => true,
        ]);

        $updateRes->assertOk();
        $this->assertSame('NEWPROMO_V2', $voucher->fresh()->code);
        $this->assertSame(20000.0, $voucher->fresh()->discount_value);

        // 4. Delete Voucher
        $deleteRes = $this->actingAs($owner)->deleteJson(route('admin.vouchers.destroy', $voucher));
        $deleteRes->assertOk();
        $this->assertNull(Voucher::find($voucherId));
    }

    public function test_owner_can_save_discount_settings(): void
    {
        $shop = Shop::factory()->create(['onboarding_status' => 'completed']);
        $owner = User::factory()->create(['shop_id' => $shop->id, 'role' => 'owner']);

        $settingsPayload = [
            'name' => $shop->name,
            'slug' => $shop->slug,
            'discount_settings' => json_encode([
                'is_enabled' => true,
                'allow_custom' => false,
                'presets' => [
                    ['id' => 'p1', 'name' => 'VIP 15%', 'type' => 'percentage', 'value' => 15],
                    ['id' => 'p2', 'name' => 'Owner Rp 20k', 'type' => 'fixed', 'value' => 20000],
                ],
            ]),
        ];

        $response = $this->actingAs($owner)->postJson('/admin/api/settings', $settingsPayload);

        $response->assertOk()
            ->assertJson(['success' => true]);

        $shop->refresh();
        $this->assertIsArray($shop->discount_settings);
        $this->assertTrue($shop->discount_settings['is_enabled']);
        $this->assertFalse($shop->discount_settings['allow_custom']);
        $this->assertCount(2, $shop->discount_settings['presets']);
        $this->assertSame('VIP 15%', $shop->discount_settings['presets'][0]['name']);
    }
}
