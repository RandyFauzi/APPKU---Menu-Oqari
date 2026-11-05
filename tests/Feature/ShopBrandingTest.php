<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
}
