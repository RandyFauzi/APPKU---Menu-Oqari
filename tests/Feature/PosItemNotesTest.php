<?php

namespace Tests\Feature;

use App\Models\CashRegister;
use App\Models\CashRegisterSession;
use App\Models\Order;
use App\Models\Product;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class PosItemNotesTest extends TestCase
{
    use RefreshDatabase;

    public function test_same_product_keeps_distinct_notes_and_quantities_through_hold_and_recall(): void
    {
        [$cashier, $product] = $this->scenario();
        $items = [['id' => $product->id, 'qty' => 2, 'notes' => 'Tanpa gula'], ['id' => $product->id, 'qty' => 1, 'notes' => "Es sedikit\nBungkus terpisah"]];
        $response = $this->actingAs($cashier)->postJson(route('admin.pos.orders.hold'), ['items' => $items])->assertOk();
        $order = Order::findOrFail($response->json('order_id'));
        $this->assertEquals(75000, $order->grand_total);
        $this->assertDatabaseHas('order_items', ['order_id' => $order->id, 'quantity' => 2, 'notes' => 'Tanpa gula']);
        $this->postJson(route('admin.pos.orders.recall', $order->id))->assertOk()
            ->assertJsonPath('order.items.0.notes', 'Tanpa gula')->assertJsonPath('order.items.1.notes', "Es sedikit\nBungkus terpisah");
        $this->getJson(route('admin.pos.orders.held'))->assertOk()->assertJsonPath('0.items.0.notes', 'Tanpa gula');
        $this->get(route('admin.pos.index'))->assertOk()->assertSee('Catatan customer');
        $this->assertDatabaseCount('payments', 0);
    }

    public function test_checkout_preserves_notes_and_recall_can_edit_or_clear_them(): void
    {
        [$cashier, $product] = $this->scenario();
        $this->openRegister($cashier);
        $items = [['id' => $product->id, 'qty' => 1, 'notes' => 'Tanpa gula'], ['id' => $product->id, 'qty' => 1, 'notes' => 'Es sedikit']];
        $response = $this->actingAs($cashier)->postJson(route('admin.pos.orders.hold'), ['items' => $items])->assertOk();
        $id = $response->json('order_id');
        $items[0]['notes'] = '  Panas saja  ';
        $items[1]['notes'] = '';
        $payload = $this->payment($items) + ['recalled_order_id' => $id];
        $this->postJson(route('admin.pos.orders.submit'), $payload)->assertOk()->assertJsonPath('order_id', $id);
        $this->assertDatabaseHas('order_items', ['order_id' => $id, 'notes' => 'Panas saja']);
        $this->assertDatabaseHas('order_items', ['order_id' => $id, 'notes' => null]);
        $this->assertDatabaseCount('order_items', 2);
        $this->assertDatabaseHas('orders', ['id' => $id, 'payment_status' => 'PAID', 'grand_total' => 50000]);
        $this->postJson(route('admin.pos.orders.submit'), $payload)->assertOk();
        $this->assertDatabaseCount('payments', 1);
    }

    public function test_direct_checkout_sends_plain_text_notes_to_kitchen_and_escaped_receipt(): void
    {
        [$cashier, $product] = $this->scenario();
        $this->openRegister($cashier);
        $note = '<img src=x onerror=alert(1)>';
        $response = $this->actingAs($cashier)->postJson(route('admin.pos.orders.submit'), $this->payment([['id' => $product->id, 'qty' => 1, 'notes' => $note]]))->assertOk();
        $id = $response->json('order_id');
        $this->assertDatabaseHas('order_items', ['order_id' => $id, 'notes' => $note]);
        $this->get('/admin/api/orders/'.$id.'/print')->assertOk()->assertSee($note)->assertDontSee($note, false);
        $this->getJson('/admin/api/orders/live')->assertOk()->assertJsonFragment(['notes' => $note]);
        $barista = User::factory()->create(['shop_id' => $cashier->shop_id, 'role' => 'barista']);
        $this->actingAs($barista)->get(route('admin.kitchen.index'))->assertOk()->assertDontSee($note, false);
        $this->getJson(route('admin.kitchen.orders'))->assertOk()->assertJsonPath('0.items.0.notes', $note);
    }

    #[TestWith(['admin.pos.orders.hold'])]
    #[TestWith(['admin.pos.orders.submit'])]
    public function test_note_limit_is_enforced_before_any_order_is_written(string $route): void
    {
        [$cashier, $product] = $this->scenario();
        $payload = $this->payment([['id' => $product->id, 'qty' => 1, 'notes' => str_repeat('a', 256)]]);
        $this->actingAs($cashier)->postJson(route($route), $payload)->assertUnprocessable()
            ->assertJsonValidationErrors('items.0.notes')->assertJsonFragment(['Catatan produk maksimal 255 karakter.']);
        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseCount('order_items', 0);
    }

    #[TestWith(['admin.pos.orders.hold'])]
    #[TestWith(['admin.pos.orders.submit'])]
    public function test_notes_must_be_text(string $route): void
    {
        [$cashier, $product] = $this->scenario();
        $payload = $this->payment([['id' => $product->id, 'qty' => 1, 'notes' => ['unexpected']]]);
        $this->actingAs($cashier)->postJson(route($route), $payload)->assertUnprocessable()->assertJsonValidationErrors('items.0.notes');
        $this->assertDatabaseCount('orders', 0);
    }

    public function test_maximum_length_and_empty_notes_are_supported(): void
    {
        [$cashier, $product] = $this->scenario();
        $items = [['id' => $product->id, 'qty' => 1, 'notes' => str_repeat('é', 255)], ['id' => $product->id, 'qty' => 1, 'notes' => '   ']];
        $response = $this->actingAs($cashier)->postJson(route('admin.pos.orders.hold'), ['items' => $items])->assertOk();
        $this->assertDatabaseHas('order_items', ['order_id' => $response->json('order_id'), 'notes' => str_repeat('é', 255)]);
        $this->assertDatabaseHas('order_items', ['order_id' => $response->json('order_id'), 'notes' => null]);
    }

    public function test_notes_cannot_be_used_to_access_another_cafes_order(): void
    {
        [$cashier, $product] = $this->scenario();
        $other = Shop::factory()->create();
        $order = Order::create(['shop_id' => $other->id, 'order_status' => 'HOLD']);
        $this->actingAs($cashier)->postJson(route('admin.pos.orders.recall', $order->id))->assertNotFound();
        $product->update(['shop_id' => $other->id]);
        $this->postJson(route('admin.pos.orders.hold'), ['items' => [['id' => $product->id, 'qty' => 1, 'notes' => 'Secret']]])->assertUnprocessable();
        $this->assertDatabaseCount('order_items', 0);
    }

    public function test_hold_notes_require_authenticated_pos_permission(): void
    {
        $this->postJson(route('admin.pos.orders.hold'), [])->assertUnauthorized();
        [$cashier, $product] = $this->scenario();
        $cashier->update(['role' => 'barista']);
        $this->actingAs($cashier)->postJson(route('admin.pos.orders.hold'), ['items' => [['id' => $product->id, 'qty' => 1, 'notes' => 'Hidden']]])->assertForbidden();
        $this->assertDatabaseCount('orders', 0);
    }

    /** @return array{User, Product} */
    private function scenario(): array
    {
        $shop = Shop::factory()->create(['onboarding_status' => 'completed']);
        $cashier = User::factory()->create(['shop_id' => $shop->id, 'role' => 'cashier']);
        $product = Product::factory()->create(['shop_id' => $shop->id, 'price' => 25000]);

        return [$cashier, $product];
    }

    private function openRegister(User $cashier): void
    {
        $register = CashRegister::create(['shop_id' => $cashier->shop_id, 'name' => 'Test register', 'is_active' => true]);
        CashRegisterSession::create(['cash_register_id' => $register->id, 'user_id' => $cashier->id, 'opened_at' => now(), 'opening_cash' => 0, 'status' => 'OPEN']);
    }

    /** @param array<int, array<string, mixed>> $items
     * @return array<string, mixed>
     */
    private function payment(array $items): array
    {
        return ['items' => $items, 'request_id' => (string) Str::uuid(), 'payment_method' => 'CASH', 'amount_paid' => 100000];
    }
}
