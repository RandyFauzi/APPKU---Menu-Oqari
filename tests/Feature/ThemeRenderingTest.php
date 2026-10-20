<?php

namespace Tests\Feature;

use App\Models\Shop;
use App\Models\User;
use Database\Factories\OrderFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class ThemeRenderingTest extends TestCase
{
    use RefreshDatabase;

    #[TestWith(['/'])]
    #[TestWith(['/harga'])]
    #[TestWith(['/admin/login'])]
    #[TestWith(['/admin/register'])]
    #[TestWith(['/forgot-password'])]
    public function test_guest_pages_render_with_compiled_assets_without_duplicate_frameworks(string $path): void
    {
        $this->get($path)->assertOk()->assertSee('name="theme-color"', false)->assertSee('/build/assets/', false)
            ->assertDontSee('cdn.tailwindcss.com', false)->assertDontSee('alpinejs@', false)->assertDontSee('data-cafe-sidebar', false);
    }

    public function test_operational_pages_render_for_owner_with_shop_identity_intact(): void
    {
        $shop = Shop::factory()->create(['onboarding_status' => 'completed', 'name' => 'Cafe Sparta']);
        $owner = User::factory()->create(['shop_id' => $shop->id, 'role' => 'owner']);
        $this->actingAs($owner);
        foreach (['admin.dashboard', 'admin.pos.index', 'admin.kitchen.index', 'admin.hpp.index', 'admin.register.index', 'admin.attendance.index', 'admin.attendance.report', 'admin.users.index', 'admin.users.create', 'admin.reports.index', 'admin.my-schedule', 'profile.edit'] as $route) {
            $this->get(route($route))->assertOk()->assertSee('name="theme-color"', false)->assertSee('/build/assets/', false)->assertSee('data-cafe-sidebar', false);
        }
        $this->assertSame('Cafe Sparta', $shop->fresh()->name);
    }

    #[TestWith(['#1E5A7A', '#1e5a7a'])]
    #[TestWith(['#276749', '#276749'])]
    public function test_customer_pages_use_palette_without_overwriting_saved_branding(string $stored, string $effective): void
    {
        $shop = Shop::factory()->create(['status' => 'active', 'primary_color' => $stored]);
        foreach (['shop.menu', 'shop.cart', 'shop.tracking'] as $route) {
            $this->get(route($route, $shop->slug))->assertOk()->assertSee('content="'.$effective.'"', false)->assertSee($shop->name);
        }
        $this->assertSame($stored, $shop->fresh()->primary_color);
    }

    public function test_onboarding_and_superadmin_receive_same_asset_pipeline(): void
    {
        $shop = Shop::factory()->create(['onboarding_status' => 'pending']);
        $owner = User::factory()->create(['shop_id' => $shop->id, 'role' => 'owner']);
        $this->actingAs($owner)->get(route('admin.onboarding'))->assertOk()->assertSee('/build/assets/', false)->assertDontSee('cdn.tailwindcss.com', false);
        $superadmin = User::factory()->create(['role' => 'superadmin', 'shop_id' => null]);
        $this->actingAs($superadmin)->get(route('superadmin.dashboard'))->assertOk()->assertSee('/build/assets/', false)->assertSee('name="theme-color"', false);
    }

    public function test_navigation_is_filtered_and_consistent_for_cashier_on_each_allowed_page(): void
    {
        $shop = Shop::factory()->create(['onboarding_status' => 'completed']);
        $cashier = User::factory()->create(['shop_id' => $shop->id, 'role' => 'cashier']);
        $this->actingAs($cashier);
        foreach (['admin.pos.index', 'admin.register.index', 'admin.attendance.index', 'profile.edit'] as $route) {
            $response = $this->get(route($route))->assertOk()->assertSee('data-cafe-sidebar', false)
                ->assertSee('aria-label="POS (Kasir)"', false)->assertSee('aria-label="Rekonsiliasi Kas"', false)
                ->assertDontSee('aria-label="User &amp; Hak Akses"', false)->assertDontSee('aria-label="Kalkulator HPP"', false);
            $this->assertSame(1, substr_count($response->getContent(), 'data-cafe-sidebar'));
        }
    }

    public function test_report_renders_saved_costs_and_scopes_totals_to_cafe(): void
    {
        $shop = Shop::factory()->create(['onboarding_status' => 'completed']);
        $owner = User::factory()->create(['shop_id' => $shop->id, 'role' => 'owner']);
        OrderFactory::new()->create(['shop_id' => $shop->id, 'subtotal' => 100000, 'discount_amount' => 10000, 'grand_total' => 90000, 'total_cogs' => 30000, 'payment_status' => 'PAID', 'created_at' => now()]);
        OrderFactory::new()->create(['shop_id' => Shop::factory()->create()->id, 'subtotal' => 900000, 'payment_status' => 'PAID', 'created_at' => now()]);
        $this->actingAs($owner)->get(route('admin.reports.index'))->assertOk()
            ->assertViewHas('totalCogs', 30000)->assertViewHas('netSales', 90000)->assertViewHas('grossProfit', 60000)->assertViewHas('margin', 66.67);
    }
}
