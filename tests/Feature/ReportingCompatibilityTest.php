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
            ->assertJsonPath('topProduct.image', $product->image_url)->assertJsonPath('hourlySales.1', 1)
            ->assertJsonPath('topProducts.0.name', 'Latte')
            ->assertJsonPath('busyHours.peakHour', '10:00')
            ->assertJsonPath('busyHours.peakVisitors', 1)
            ->assertJsonPath('monthlyTrend.hasData', true);
    }

    public function test_analytics_returns_safe_zero_defaults_when_shop_has_no_orders(): void
    {
        $shop = Shop::factory()->create(['onboarding_status' => 'completed']);
        $owner = User::factory()->create(['shop_id' => $shop->id, 'role' => 'owner']);

        $this->actingAs($owner)->getJson('/admin/api/dashboard/analytics')->assertOk()
            ->assertJsonPath('orders', 0)
            ->assertJsonPath('ordersYesterday', 0)
            ->assertJsonPath('ordersChange', 0)
            ->assertJsonPath('revenue', 0)
            ->assertJsonPath('revenueYesterday', 0)
            ->assertJsonPath('revenueChange', 0)
            ->assertJsonPath('topProduct.name', 'Belum ada')
            ->assertJsonPath('topProduct.sold', 0)
            ->assertJsonPath('topProducts', [])
            ->assertJsonPath('newCount', 0)
            ->assertJsonPath('returningCount', 0)
            ->assertJsonPath('newCustomersPct', 0)
            ->assertJsonPath('returningCustomers', 0)
            ->assertJsonPath('busyHours.peakHour', '—')
            ->assertJsonPath('busyHours.peakVisitors', 0)
            ->assertJsonPath('monthlyTrend.hasData', false);
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

    public function test_analytics_returns_top_products_by_period_for_today_week_and_month(): void
    {
        $this->travelTo(now()->setDate(2026, 11, 12)->setTime(12, 0, 0));

        $shop = Shop::factory()->create(['onboarding_status' => 'completed']);
        $owner = User::factory()->create(['shop_id' => $shop->id, 'role' => 'owner']);

        $productToday = Product::factory()->create(['shop_id' => $shop->id, 'name' => 'Latte']);
        $productWeek = Product::factory()->create(['shop_id' => $shop->id, 'name' => 'Espresso']);
        $productMonth = Product::factory()->create(['shop_id' => $shop->id, 'name' => 'Cappuccino']);

        // Order today (2026-11-12)
        $orderToday = OrderFactory::new()->create([
            'shop_id' => $shop->id,
            'created_at' => now(),
            'payment_status' => 'PAID',
            'order_status' => 'Selesai',
            'grand_total' => 30000,
        ]);
        $orderToday->items()->create([
            'product_id' => $productToday->id,
            'product_name' => 'Latte',
            'quantity' => 3,
            'unit_price' => 10000,
            'subtotal' => 30000,
        ]);

        // Order this week, but not today (2026-11-10 Tuesday)
        $orderWeek = OrderFactory::new()->create([
            'shop_id' => $shop->id,
            'created_at' => now()->setDate(2026, 11, 10)->setTime(10, 0, 0),
            'payment_status' => 'PAID',
            'order_status' => 'Selesai',
            'grand_total' => 40000,
        ]);
        $orderWeek->items()->create([
            'product_id' => $productWeek->id,
            'product_name' => 'Espresso',
            'quantity' => 2,
            'unit_price' => 20000,
            'subtotal' => 40000,
        ]);

        // Order this month, but last week (2026-11-05 Thursday)
        $orderMonth = OrderFactory::new()->create([
            'shop_id' => $shop->id,
            'created_at' => now()->setDate(2026, 11, 5)->setTime(10, 0, 0),
            'payment_status' => 'PAID',
            'order_status' => 'Selesai',
            'grand_total' => 50000,
        ]);
        $orderMonth->items()->create([
            'product_id' => $productMonth->id,
            'product_name' => 'Cappuccino',
            'quantity' => 5,
            'unit_price' => 10000,
            'subtotal' => 50000,
        ]);

        $response = $this->actingAs($owner)->getJson('/admin/api/dashboard/analytics')->assertOk();

        // Check today's list
        $response->assertJsonCount(1, 'topProductsByPeriod.today');
        $response->assertJsonPath('topProductsByPeriod.today.0.name', 'Latte');
        $response->assertJsonPath('topProductsByPeriod.today.0.sold', 3);

        // Check week's list (Latte and Espresso)
        $response->assertJsonCount(2, 'topProductsByPeriod.week');
        $weekNames = collect($response->json('topProductsByPeriod.week'))->pluck('name')->all();
        $this->assertContains('Latte', $weekNames);
        $this->assertContains('Espresso', $weekNames);
        $this->assertNotContains('Cappuccino', $weekNames);

        // Check month's list (Cappuccino, Latte, Espresso)
        $response->assertJsonCount(3, 'topProductsByPeriod.month');
        $monthNames = collect($response->json('topProductsByPeriod.month'))->pluck('name')->all();
        $this->assertContains('Cappuccino', $monthNames);
        $this->assertContains('Latte', $monthNames);
        $this->assertContains('Espresso', $monthNames);
    }
}
