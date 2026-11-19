<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Shop;
use App\Models\User;
use App\Services\MediaService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ShopBrandingTest extends TestCase
{
    use RefreshDatabase;

    public function test_shop_branding_settings_can_be_saved(): void
    {
        $shop = Shop::factory()->create([
            'onboarding_status' => 'completed',
            'theme_style' => 'list',
        ]);
        $owner = User::factory()->create(['shop_id' => $shop->id, 'role' => 'owner']);

        $product1 = Product::factory()->create(['shop_id' => $shop->id, 'name' => 'Kopi Tubruk', 'is_best_seller' => false]);
        $product2 = Product::factory()->create(['shop_id' => $shop->id, 'name' => 'Caramel Macchiato', 'is_best_seller' => false]);

        $payload = [
            'name' => 'Kedai Kopi Sparta',
            'slug' => $shop->slug,
            'primary_color' => '#8B0000',
            'theme_style' => 'cards',
            'font_family' => 'playfair',
            'title_font_family' => 'playfair',
            'tiktok_link' => 'kopisparta',
            'instagram_link' => 'kopi.sparta',
            'whatsapp_number' => '081234567890',
            'maps_link' => 'https://maps.google.com/?q=sparta',
            'social_settings' => [
                'show_tiktok' => true,
                'show_instagram' => true,
                'show_whatsapp' => false,
                'show_maps' => true,
            ],
            'background_type' => 'pattern',
            'background_pattern' => 'cafe',
            'background_color' => '#fbf8f2',
            'background_overlay_opacity' => 90,
            'best_seller_ids' => [$product2->id],
        ];

        $response = $this->actingAs($owner)->postJson('/admin/api/settings', $payload);

        $response->assertOk()->assertJson(['success' => true]);

        $shop->refresh();
        $this->assertSame('cards', $shop->theme_style);
        $this->assertSame('#8B0000', $shop->primary_color);
        $this->assertSame('kopisparta', $shop->tiktok_link);
        $this->assertSame('cafe', $shop->background_pattern);
        $this->assertSame(90, $shop->background_overlay_opacity);
        $this->assertTrue($shop->social_settings['show_tiktok']);
        $this->assertFalse($shop->social_settings['show_whatsapp']);

        $this->assertFalse($product1->fresh()->is_best_seller);
        $this->assertTrue($product2->fresh()->is_best_seller);
    }

    public function test_active_social_links_helper(): void
    {
        $shop = Shop::factory()->create([
            'tiktok_link' => 'tokokita',
            'instagram_link' => 'tokokita.id',
            'whatsapp_number' => '081299998888',
            'maps_link' => 'https://maps.app.goo.gl/xyz',
            'social_settings' => [
                'show_tiktok' => true,
                'show_instagram' => false,
                'show_whatsapp' => true,
                'show_maps' => true,
            ],
        ]);

        $links = $shop->activeSocialLinks();

        $this->assertArrayHasKey('tiktok', $links);
        $this->assertArrayNotHasKey('instagram', $links); // Disabled
        $this->assertArrayHasKey('whatsapp', $links);
        $this->assertArrayHasKey('maps', $links);

        $this->assertSame('https://www.tiktok.com/@tokokita', $links['tiktok']['url']);
        $this->assertSame('https://wa.me/6281299998888', $links['whatsapp']['url']);
    }

    public function test_customer_menu_renders_with_branding_and_social_links(): void
    {
        $shop = Shop::factory()->create([
            'status' => 'active',
            'primary_color' => '#8B0000',
            'background_type' => 'pattern',
            'background_pattern' => 'cafe',
            'tiktok_link' => 'coffeelovers',
            'social_settings' => [
                'show_tiktok' => true,
                'show_instagram' => true,
                'show_whatsapp' => true,
                'show_maps' => false,
            ],
        ]);

        $product = Product::factory()->create([
            'shop_id' => $shop->id,
            'name' => 'Signature Blend',
            'is_best_seller' => true,
        ]);

        $response = $this->get(route('shop.menu', $shop->slug));

        $response->assertOk();
        $response->assertSee('bestseller-shelf-container', false);
        $response->assertSee('https://www.tiktok.com/@coffeelovers', false);
    }

    public function test_existing_banner_is_preserved_when_sent_as_full_url_or_path(): void
    {
        Storage::fake('public');

        $shop = Shop::factory()->create([
            'onboarding_status' => 'completed',
            'banner_paths' => ['media/shops/1/branding/banner_0_test.webp'],
        ]);
        $owner = User::factory()->create(['shop_id' => $shop->id, 'role' => 'owner']);

        Storage::disk('public')->put('media/shops/1/branding/banner_0_test.webp', 'fake-image-bytes');

        // Simulate Alpine.js sending full URL for existing banner
        $payload = [
            'name' => $shop->name,
            'slug' => $shop->slug,
            'existing_banner_0' => 'https://appku.site/uploads/media/shops/1/branding/banner_0_test.webp',
        ];

        $response = $this->actingAs($owner)->postJson('/admin/api/settings', $payload);

        $response->assertOk()->assertJson(['success' => true]);

        // Verify the file was NOT deleted by orphan cleanup
        Storage::disk('public')->assertExists('media/shops/1/branding/banner_0_test.webp');

        // Verify the stored banner path is clean relative path
        $shop->refresh();
        $this->assertSame(['media/shops/1/branding/banner_0_test.webp'], $shop->banner_paths);

        // Test with /uploads/ prefix
        $payload2 = [
            'name' => $shop->name,
            'slug' => $shop->slug,
            'existing_banner_0' => '/uploads/media/shops/1/branding/banner_0_test.webp',
        ];
        $this->actingAs($owner)->postJson('/admin/api/settings', $payload2)->assertOk();
        Storage::disk('public')->assertExists('media/shops/1/branding/banner_0_test.webp');
        $this->assertSame(['media/shops/1/branding/banner_0_test.webp'], $shop->fresh()->banner_paths);
    }

    public function test_media_service_resolves_and_auto_heals_internal_urls(): void
    {
        $service = app(MediaService::class);

        // Standard relative path
        $this->assertStringEndsWith('/uploads/media/shops/1/test.webp', $service->url('media/shops/1/test.webp'));

        // Path with leading slash
        $this->assertStringEndsWith('/uploads/media/shops/1/test.webp', $service->url('/uploads/media/shops/1/test.webp'));

        // Auto-heal dirty absolute URL from previous environment/host
        $this->assertStringEndsWith('/uploads/media/shops/1/test.webp', $service->url('http://localhost:8000/uploads/media/shops/1/test.webp'));
        $this->assertStringEndsWith('/uploads/media/shops/1/test.webp', $service->url('https://appku.site/uploads/media/shops/1/test.webp'));

        // External URL should remain intact
        $this->assertSame('https://images.unsplash.com/photo-123', $service->url('https://images.unsplash.com/photo-123'));
    }

    public function test_can_save_up_to_five_banners(): void
    {
        Storage::fake('public');

        $shop = Shop::factory()->create([
            'onboarding_status' => 'completed',
            'banner_paths' => [],
        ]);
        $owner = User::factory()->create(['shop_id' => $shop->id, 'role' => 'owner']);

        $payload = [
            'name' => $shop->name,
            'slug' => $shop->slug,
            'is_banner_active' => true,
            'banner_0' => UploadedFile::fake()->image('banner0.jpg'),
            'banner_1' => UploadedFile::fake()->image('banner1.jpg'),
            'banner_2' => UploadedFile::fake()->image('banner2.jpg'),
            'banner_3' => UploadedFile::fake()->image('banner3.jpg'),
            'banner_4' => UploadedFile::fake()->image('banner4.jpg'),
        ];

        $response = $this->actingAs($owner)->post('/admin/api/settings', $payload, ['Accept' => 'application/json']);

        $response->assertOk()->assertJson(['success' => true]);

        $shop->refresh();
        $this->assertCount(5, $shop->banner_paths);
        $this->assertCount(5, $shop->banners);
    }

    public function test_shop_branding_can_save_sticker_and_bistro_theme_styles(): void
    {
        $shop = Shop::factory()->create([
            'onboarding_status' => 'completed',
            'theme_style' => 'list',
        ]);
        $owner = User::factory()->create(['shop_id' => $shop->id, 'role' => 'owner']);

        // Test saving 'sticker'
        $response = $this->actingAs($owner)->postJson('/admin/api/settings', [
            'name' => $shop->name,
            'slug' => $shop->slug,
            'theme_style' => 'sticker',
        ]);
        $response->assertOk()->assertJson(['success' => true]);
        $this->assertSame('sticker', $shop->fresh()->theme_style);

        // Test saving 'bistro'
        $response = $this->actingAs($owner)->postJson('/admin/api/settings', [
            'name' => $shop->name,
            'slug' => $shop->slug,
            'theme_style' => 'bistro',
        ]);
        $response->assertOk()->assertJson(['success' => true]);
        $this->assertSame('bistro', $shop->fresh()->theme_style);

        // Test invalid theme_style is rejected
        $response = $this->actingAs($owner)->postJson('/admin/api/settings', [
            'name' => $shop->name,
            'slug' => $shop->slug,
            'theme_style' => 'invalid_theme',
        ]);
        $response->assertStatus(422)->assertJsonValidationErrors(['theme_style']);
    }
}
