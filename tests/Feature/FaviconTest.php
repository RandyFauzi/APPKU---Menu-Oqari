<?php

namespace Tests\Feature;

use App\Models\Shop;
use App\Models\User;
use Database\Factories\OrderFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class FaviconTest extends TestCase
{
    use RefreshDatabase;

    private function assertFavicon(TestResponse $response, string $expectedUrl): void
    {
        $response->assertOk();
        preg_match_all('/<link\b[^>]*\brel="icon"[^>]*>/i', $response->getContent(), $icons);
        $this->assertCount(1, $icons[0]);
        $this->assertStringContainsString('href="'.e($expectedUrl).'"', $icons[0][0]);
        $this->assertStringNotContainsString('type="image/png"', $icons[0][0]);
    }

    #[TestWith(['admin.dashboard'])]
    #[TestWith(['admin.pos.index'])]
    #[TestWith(['admin.kitchen.index'])]
    #[TestWith(['admin.hpp.index'])]
    #[TestWith(['admin.register.index'])]
    #[TestWith(['admin.attendance.index'])]
    #[TestWith(['admin.users.index'])]
    #[TestWith(['admin.users.create'])]
    #[TestWith(['admin.reports.index'])]
    #[TestWith(['admin.my-schedule'])]
    #[TestWith(['profile.edit'])]
    public function test_each_operational_layout_uses_the_authenticated_cafe_logo(string $route): void
    {
        $shop = Shop::factory()->create(['onboarding_status' => 'completed', 'logo_path' => 'logos/cafe.webp']);
        $owner = User::factory()->create(['shop_id' => $shop->id, 'role' => 'owner']);
        $this->actingAs($owner);

        $this->assertFavicon($this->get(route($route)), $shop->logo_url);
        $this->assertFavicon($this->get(route('admin.attendance.index', ['tab' => 'team'])), $shop->logo_url);
    }

    public function test_customer_pages_use_the_viewed_cafe_logo_even_when_another_owner_is_logged_in(): void
    {
        $viewerShop = Shop::factory()->create(['onboarding_status' => 'completed', 'logo_path' => 'logos/viewer.webp']);
        $owner = User::factory()->create(['shop_id' => $viewerShop->id, 'role' => 'owner']);
        $shop = Shop::factory()->create(['status' => 'active', 'logo_path' => 'logos/customer-cafe.webp']);
        $this->actingAs($owner);

        foreach (['shop.menu', 'shop.cart', 'shop.tracking'] as $route) {
            $this->assertFavicon($this->get(route($route, $shop->slug)), $shop->logo_url);
        }
    }

    public function test_missing_logo_falls_back_to_platform_icon_for_guests_and_staff(): void
    {
        $shop = Shop::factory()->create(['onboarding_status' => 'completed', 'status' => 'active', 'logo_path' => null]);
        $this->assertFavicon($this->get(route('shop.menu', $shop->slug)), asset('logo-oqari.webp'));
        $cashier = User::factory()->create(['shop_id' => $shop->id, 'role' => 'cashier']);
        $this->assertFavicon($this->actingAs($cashier)->get(route('admin.register.index')), asset('logo-oqari.webp'));
    }

    public function test_platform_pages_keep_platform_identity_when_a_cafe_owner_is_logged_in(): void
    {
        $shop = Shop::factory()->create(['onboarding_status' => 'completed', 'logo_path' => 'logos/private-cafe.webp']);
        $owner = User::factory()->create(['shop_id' => $shop->id, 'role' => 'owner']);
        $this->actingAs($owner);

        foreach (['/', '/harga'] as $path) {
            $this->assertFavicon($this->get($path), asset('logo-oqari.webp'));
        }
    }

    public function test_public_receipt_uses_the_order_cafe_logo_without_a_login(): void
    {
        $shop = Shop::factory()->create(['status' => 'active', 'logo_path' => 'logos/receipt-cafe.webp']);
        $order = OrderFactory::new()->create(['shop_id' => $shop->id]);

        $this->assertFavicon($this->get(URL::signedRoute('receipt.web', $order)), $shop->logo_url);
    }

    public function test_uploading_a_replacement_logo_changes_the_favicon_url_on_following_pages(): void
    {
        Storage::fake('public');
        $shop = Shop::factory()->create(['onboarding_status' => 'completed', 'logo_path' => 'logos/old.webp']);
        $owner = User::factory()->create(['shop_id' => $shop->id, 'role' => 'owner']);
        $oldUrl = $shop->logo_url;
        $this->actingAs($owner);

        $response = $this->post('/admin/api/settings', ['name' => $shop->name, 'logo' => UploadedFile::fake()->image('new-logo.png')], ['Accept' => 'application/json'])->assertOk();
        $shop->refresh();
        $this->assertNotSame($oldUrl, $shop->logo_url);
        $response->assertJsonPath('shop.logo_url', $shop->logo_url);
        $owner->unsetRelation('shop');
        $this->assertFavicon($this->get(route('admin.register.index')), $shop->logo_url);
        $this->assertFavicon($this->get(route('admin.dashboard')), $shop->logo_url);
    }
}
