<?php

namespace Tests\Feature;

use App\Models\Payment;
use App\Models\Product;
use App\Models\Shop;
use App\Models\User;
use Database\Factories\OrderFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportingCompatibilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_analytics_uses_stored_media_path_and_database_supported_hour_query(): void
    {
        $this->freezeTime();
        $shop = Shop::factory()->create(['onboarding_status' => 'completed']);
        $owner = User::factory()->create(['shop_id' => $shop->id, 'role' => 'owner']);
        $product = Product::factory()->create(['shop_id' => $shop->id, 'name' => 'Latte', 'image_path' => 'products/latte.png']);
        $order = OrderFactory::new()->create(['shop_id' => $shop->id, 'created_at' => now()->setTime(10, 30), 'payment_status' => 'PAID', 'grand_total' => 50000]);
        $order->items()->create(['product_id' => $product->id, 'product_name' => 'Latte', 'quantity' => 2, 'unit_price' => 25000, 'subtotal' => 50000]);
        OrderFactory::new()->create(['shop_id' => Shop::factory()->create()->id, 'created_at' => now()->setTime(10, 30), 'payment_status' => 'PAID', 'grand_total' => 999999]);
        $this->actingAs($owner)->getJson('/admin/api/dashboard/analytics')->assertOk()
            ->assertJsonPath('revenue', 50000)->assertJsonPath('topProduct.name', 'Latte')->assertJsonPath('topProduct.sold', 2)
            ->assertJsonPath('topProduct.image', $product->image_url)->assertJsonPath('hourlySales.1', 1);
    }

    public function test_payment_report_uses_ledger_cancellation_and_paid_order_instead_of_nonexistent_status(): void
    {
        $shop = Shop::factory()->create(['onboarding_status' => 'completed']);
        $owner = User::factory()->create(['shop_id' => $shop->id, 'role' => 'owner']);
        $order = OrderFactory::new()->create(['shop_id' => $shop->id, 'payment_status' => 'PAID', 'created_at' => now()]);
        $base = ['shop_id' => $shop->id, 'order_id' => $order->id, 'payment_method' => 'CASH'];
        Payment::create($base + ['amount' => 20000]);
        Payment::create($base + ['amount' => 99999, 'cancelled_at' => now()]);
        $otherShop = Shop::factory()->create();
        $otherOrder = OrderFactory::new()->create(['shop_id' => $otherShop->id, 'payment_status' => 'PAID', 'created_at' => now()]);
        Payment::create(['shop_id' => $otherShop->id, 'order_id' => $otherOrder->id, 'payment_method' => 'CASH', 'amount' => 50000]);
        $this->actingAs($owner)->get(route('admin.reports.index'))->assertOk()->assertViewHas('payments', fn ($payments): bool => (float) $payments->get('CASH') === 20000.0);
    }
}
