<?php

namespace Tests\Feature;

use App\Models\CashRegisterSession;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductRecipe;
use App\Models\RawMaterial;
use App\Models\Shop;
use App\Models\Table;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class AuditRegressionTest extends TestCase
{
    use RefreshDatabase;

    public function test_suspended_owner_cannot_reactivate_shop_or_access_dashboard(): void
    {
        $shop = Shop::factory()->create(['status' => 'suspended']);
        $owner = User::factory()->create(['shop_id' => $shop->id, 'role' => 'owner']);
        $this->actingAs($owner)->postJson('/admin/onboarding/complete')->assertForbidden();
        $this->getJson('/admin/dashboard')->assertForbidden();
        $this->assertDatabaseHas('shops', ['id' => $shop->id, 'status' => 'suspended']);
    }

    public function test_suspended_staff_cannot_login(): void
    {
        $shop = Shop::factory()->create(['status' => 'suspended']);
        $user = User::factory()->create(['shop_id' => $shop->id, 'role' => 'cashier']);
        $this->postJson('/admin/login', ['email' => $user->email, 'password' => 'password'])
            ->assertUnprocessable()->assertJsonValidationErrors('email');
        $this->assertGuest();
    }

    public function test_cashier_cannot_change_onboarding_or_complete_it(): void
    {
        $cashier = $this->cashier();
        $this->actingAs($cashier)->postJson('/admin/onboarding/step', [
            'step' => 'business_profile', 'data' => ['name' => 'Changed', 'business_type' => 'Cafe'],
        ])->assertForbidden();
        $this->postJson('/admin/onboarding/complete')->assertForbidden();
        $this->assertDatabaseMissing('shops', ['id' => $cashier->shop_id, 'name' => 'Changed']);
    }

    public function test_owner_completes_onboarding_and_keeps_trial_status(): void
    {
        $shop = Shop::factory()->create(['status' => 'trial']);
        $owner = User::factory()->create(['shop_id' => $shop->id, 'role' => 'owner']);
        $this->actingAs($owner)->postJson('/admin/onboarding/complete')->assertOk();
        $this->assertDatabaseHas('shops', ['id' => $shop->id, 'status' => 'trial', 'onboarding_status' => 'completed']);
    }

    public function test_suspended_shop_is_not_available_to_customers(): void
    {
        $shop = Shop::factory()->create(['status' => 'suspended']);
        $this->get(route('shop.menu', $shop->slug))->assertNotFound();
    }

    public function test_barista_can_update_own_kitchen_order_and_not_another_shops_order(): void
    {
        $shop = Shop::factory()->create();
        $barista = User::factory()->create(['shop_id' => $shop->id, 'role' => 'barista']);
        $order = Order::create(['shop_id' => $shop->id, 'order_status' => 'CONFIRMED']);
        $otherShop = Shop::factory()->create();
        $otherOrder = Order::create(['shop_id' => $otherShop->id, 'order_status' => 'CONFIRMED']);
        $this->actingAs($barista)->postJson('/admin/kitchen/orders/'.$order->id.'/status', ['status' => 'READY'])
            ->assertOk()->assertJsonPath('status', 'READY');
        $this->postJson('/admin/kitchen/orders/'.$otherOrder->id.'/status', ['status' => 'READY'])->assertNotFound();
        $this->assertDatabaseHas('orders', ['id' => $otherOrder->id, 'order_status' => 'CONFIRMED']);
    }

    public function test_owner_can_schedule_staff_and_view_shift_management(): void
    {
        $shop = Shop::factory()->create();
        $owner = User::factory()->create(['shop_id' => $shop->id, 'role' => 'owner']);
        $staff = User::factory()->create(['shop_id' => $shop->id, 'role' => 'cashier']);
        $this->actingAs($owner)->postJson('/admin/shifts', [
            'user_id' => $staff->id, 'date' => '2026-10-20', 'start_time' => '08:00', 'end_time' => '16:00',
        ])->assertCreated();
        $this->get('/admin/shifts')->assertRedirect(route('admin.dashboard', ['tab' => 'shifts']));
        $this->assertDatabaseHas('crew_shifts', ['shop_id' => $shop->id, 'user_id' => $staff->id]);
    }

    public function test_repeated_payment_creates_one_order_and_one_payment_in_correct_shift(): void
    {
        $cashier = $this->cashier();
        $product = Product::factory()->create(['shop_id' => $cashier->shop_id, 'price' => 30000]);
        $this->actingAs($cashier)->postJson('/shift/open', ['opening_cash' => 100000])->assertOk();
        $ownSession = CashRegisterSession::where('user_id', $cashier->id)->firstOrFail();
        $other = User::factory()->create(['shop_id' => $cashier->shop_id, 'role' => 'cashier']);
        $this->actingAs($other)->postJson('/shift/open', ['opening_cash' => 50000])->assertOk();
        $material = RawMaterial::create(['shop_id' => $cashier->shop_id, 'name' => 'Beans', 'unit' => 'g', 'current_stock' => 1000]);
        ProductRecipe::create(['product_id' => $product->id, 'raw_material_id' => $material->id, 'amount' => 20]);
        $payload = $this->paymentPayload($product);
        $response = $this->actingAs($cashier)->postJson('/admin/pos/orders', $payload)->assertOk();
        $this->postJson('/admin/pos/orders', $payload)->assertOk()->assertJsonPath('order_id', $response->json('order_id'));
        $this->assertDatabaseCount('orders', 1);
        $this->assertDatabaseCount('payments', 1);
        $this->assertDatabaseHas('raw_materials', ['id' => $material->id, 'current_stock' => 960]);
        $this->assertDatabaseCount('stock_mutations', 1);
        $this->assertDatabaseHas('cash_register_sessions', ['id' => $ownSession->id, 'total_cash_sales' => 60000]);
        $this->assertDatabaseHas('cash_register_sessions', ['user_id' => $other->id, 'total_cash_sales' => 0]);
        $this->assertDatabaseHas('orders', ['id' => $response->json('order_id'), 'amount_paid' => 100000, 'change_amount' => 40000]);
    }

    public function test_recalled_order_is_paid_in_place_and_cannot_be_paid_again_with_new_request_id(): void
    {
        $cashier = $this->cashier();
        $product = Product::factory()->create(['shop_id' => $cashier->shop_id, 'price' => 30000]);
        $this->actingAs($cashier)->postJson('/shift/open', ['opening_cash' => 0])->assertOk();
        $held = $this->postJson('/admin/pos/orders/hold', ['items' => [['id' => $product->id, 'qty' => 1]]])->assertOk();
        $orderId = $held->json('order_id');
        $this->postJson('/admin/pos/orders/'.$orderId.'/recall')->assertOk();
        $this->assertDatabaseHas('orders', ['id' => $orderId, 'order_status' => 'HOLD']);
        $payload = $this->paymentPayload($product);
        $payload['recalled_order_id'] = $orderId;
        $this->postJson('/admin/pos/orders', $payload)->assertOk()->assertJsonPath('order_id', $orderId);
        $payload['request_id'] = (string) Str::uuid();
        $this->postJson('/admin/pos/orders', $payload)->assertUnprocessable()->assertJsonValidationErrors('recalled_order_id');
        $this->assertDatabaseCount('orders', 1);
        $this->assertDatabaseCount('payments', 1);
        $this->assertDatabaseCount('order_items', 1);
        $this->assertDatabaseHas('orders', ['id' => $orderId, 'payment_status' => 'PAID', 'grand_total' => 60000]);
    }

    public function test_checkout_rejects_a_recalled_order_from_another_shop(): void
    {
        $cashier = $this->cashier();
        $product = Product::factory()->create(['shop_id' => $cashier->shop_id, 'price' => 30000]);
        $otherShop = Shop::factory()->create();
        $otherOrder = Order::create(['shop_id' => $otherShop->id, 'order_status' => 'HOLD']);
        $payload = $this->paymentPayload($product);
        $payload['recalled_order_id'] = $otherOrder->id;
        $this->actingAs($cashier)->postJson('/admin/pos/orders', $payload)->assertNotFound();
        $this->assertDatabaseCount('payments', 0);
        $this->assertDatabaseHas('orders', ['id' => $otherOrder->id, 'order_status' => 'HOLD']);
    }

    public function test_payment_without_own_open_shift_returns_422_without_creating_order(): void
    {
        $cashier = $this->cashier();
        $product = Product::factory()->create(['shop_id' => $cashier->shop_id, 'price' => 30000]);
        $this->actingAs($cashier)->postJson('/admin/pos/orders', $this->paymentPayload($product))
            ->assertUnprocessable()->assertJsonValidationErrors('amount_paid');
        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseCount('payments', 0);
    }

    public function test_insufficient_payment_rolls_back_order_and_ledger(): void
    {
        $cashier = $this->cashier();
        $product = Product::factory()->create(['shop_id' => $cashier->shop_id, 'price' => 30000]);
        $this->actingAs($cashier)->postJson('/shift/open', ['opening_cash' => 100000])->assertOk();
        $payload = $this->paymentPayload($product);
        $payload['amount_paid'] = 1;
        $this->postJson('/admin/pos/orders', $payload)->assertUnprocessable()->assertJsonValidationErrors('amount_paid');
        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseCount('payments', 0);
    }

    public function test_cash_close_includes_cash_sales_but_excludes_qris(): void
    {
        $cashier = $this->cashier();
        $product = Product::factory()->create(['shop_id' => $cashier->shop_id, 'price' => 30000]);
        $this->actingAs($cashier)->postJson('/shift/open', ['opening_cash' => 100000])->assertOk();
        $this->postJson('/admin/pos/orders', $this->paymentPayload($product))->assertOk();
        $payload = $this->paymentPayload($product);
        $payload['payment_method'] = 'QRIS';
        $payload['amount_paid'] = 60000;
        $this->postJson('/admin/pos/orders', $payload)->assertOk();
        $this->postJson('/shift/close', ['actual_cash' => 160000, 'notes' => 'Reconciled'])->assertOk();
        $this->assertDatabaseHas('cash_register_sessions', [
            'user_id' => $cashier->id, 'status' => 'CLOSED', 'expected_cash' => 160000,
            'actual_cash' => 160000, 'difference' => 0, 'total_qris_sales' => 60000, 'notes' => 'Reconciled',
        ]);
    }

    public function test_checkout_requires_uuid_request_id_before_creating_order(): void
    {
        $cashier = $this->cashier();
        $product = Product::factory()->create(['shop_id' => $cashier->shop_id]);
        $payload = $this->paymentPayload($product);
        $payload['request_id'] = 'invalid';
        $this->actingAs($cashier)->postJson('/admin/pos/orders', $payload)
            ->assertUnprocessable()->assertJsonValidationErrors('request_id');
        $this->assertDatabaseCount('orders', 0);
    }

    public function test_checkout_rejects_products_from_another_shop(): void
    {
        $cashier = $this->cashier();
        $product = Product::factory()->create();
        $this->actingAs($cashier)->postJson('/admin/pos/orders', $this->paymentPayload($product))
            ->assertUnprocessable()->assertJsonValidationErrors('items');
        $this->assertDatabaseCount('orders', 0);
    }

    public function test_suspended_staff_can_still_logout(): void
    {
        $shop = Shop::factory()->create(['status' => 'suspended']);
        $staff = User::factory()->create(['shop_id' => $shop->id, 'role' => 'cashier']);
        $this->actingAs($staff)->post('/logout')->assertRedirect('/');
        $this->assertGuest();
    }

    public function test_customer_checkout_total_matches_server_subtotal_and_supports_new_tables(): void
    {
        $shop = Shop::factory()->create();
        $product = Product::factory()->create(['shop_id' => $shop->id, 'price' => 30000]);
        $this->postJson(route('shop.order.submit', $shop->slug), [
            'table_id' => 'New Table', 'customer_name' => 'Guest', 'payment_method' => 'CASH',
            'items' => [['id' => $product->id, 'qty' => 2]],
        ])->assertOk()->assertJsonPath('success', true);
        $this->assertDatabaseHas('orders', ['shop_id' => $shop->id, 'subtotal' => 60000, 'grand_total' => 60000, 'tax_amount' => 0]);
        $this->assertDatabaseHas('order_items', ['unit_price' => 30000, 'subtotal' => 60000]);
        $this->assertNotEmpty(Table::where('name', 'New Table')->firstOrFail()->public_token);
    }

    public function test_shift_dashboard_api_lists_and_deletes_own_shifts_only(): void
    {
        $shop = Shop::factory()->create();
        $owner = User::factory()->create(['shop_id' => $shop->id, 'role' => 'owner']);
        $staff = User::factory()->create(['shop_id' => $shop->id, 'role' => 'cashier']);
        $this->actingAs($owner)->postJson('/admin/api/shifts', [
            'user_id' => $staff->id, 'date' => '2026-10-20', 'start_time' => '08:00', 'end_time' => '16:00',
        ])->assertOk();
        $response = $this->getJson('/admin/api/shifts')->assertOk()->assertJsonCount(1);
        $shiftId = $response->json('0.id');
        $this->deleteJson('/admin/api/shifts/'.$shiftId)->assertOk();
        $this->assertDatabaseMissing('crew_shifts', ['id' => $shiftId]);
        $this->actingAs($staff)->getJson('/admin/api/shifts')->assertForbidden();
    }

    public function test_suspended_superadmin_can_still_manage_platform(): void
    {
        $shop = Shop::factory()->create(['status' => 'suspended']);
        $admin = User::factory()->create(['shop_id' => $shop->id, 'role' => 'superadmin']);
        $this->postJson('/admin/login', ['email' => $admin->email, 'password' => 'password'])->assertRedirect();
        $this->assertAuthenticatedAs($admin);
    }

    public function test_guest_checkout_is_forbidden_and_staff_cannot_open_register(): void
    {
        $this->postJson('/admin/pos/orders')->assertUnauthorized();
        $shop = Shop::factory()->create();
        $staff = User::factory()->create(['shop_id' => $shop->id, 'role' => 'barista']);
        $this->actingAs($staff)->postJson('/shift/open', ['opening_cash' => 0])->assertForbidden();
        $this->assertDatabaseCount('cash_register_sessions', 0);
    }

    public function test_invalid_variant_is_rejected_before_payment(): void
    {
        $cashier = $this->cashier();
        $product = Product::factory()->create(['shop_id' => $cashier->shop_id]);
        $payload = $this->paymentPayload($product);
        $payload['items'][0]['variant_id'] = 99999;
        $this->actingAs($cashier)->postJson('/admin/pos/orders', $payload)
            ->assertUnprocessable()->assertJsonValidationErrors('items');
        $this->assertDatabaseCount('payments', 0);
    }

    private function cashier(): User
    {
        $shop = Shop::factory()->create(['onboarding_status' => 'completed']);

        return User::factory()->create(['shop_id' => $shop->id, 'role' => 'cashier']);
    }

    /** @return array<string, mixed> */
    private function paymentPayload(Product $product): array
    {
        return [
            'request_id' => (string) Str::uuid(),
            'items' => [['id' => $product->id, 'qty' => 2]],
            'payment_method' => 'CASH', 'amount_paid' => 100000, 'fulfillment_type' => 'DINE_IN',
        ];
    }
}
