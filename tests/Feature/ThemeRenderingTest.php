<?php

namespace Tests\Feature;

use App\Models\Shop;
use App\Models\User;
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
            ->assertDontSee('cdn.tailwindcss.com', false)->assertDontSee('alpinejs@', false);
    }

    public function test_operational_pages_render_for_owner_with_shop_identity_intact(): void
    {
        $shop = Shop::factory()->create(['onboarding_status' => 'completed', 'name' => 'Cafe Sparta']);
        $owner = User::factory()->create(['shop_id' => $shop->id, 'role' => 'owner']);
        $this->actingAs($owner);
        foreach (['admin.dashboard', 'admin.pos.index', 'admin.kitchen.index', 'admin.hpp.index', 'admin.register.index', 'admin.attendance.index'] as $route) {
            $this->get(route($route))->assertOk()->assertSee('name="theme-color"', false)->assertSee('/build/assets/', false);
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
}
