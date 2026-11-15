<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PosDiscountSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_cashier_can_save_discount_presets_from_pos(): void
    {
        $shop = Shop::factory()->create([
            'discount_settings' => [
                'is_enabled' => true,
                'allow_custom' => true,
                'presets' => [
                    ['id' => 'p1', 'name' => '5%', 'type' => 'percentage', 'value' => 5],
                ],
            ],
        ]);

        $user = User::factory()->create([
            'shop_id' => $shop->id,
            'role' => 'admin',
        ]);

        $newPresets = [
            ['id' => 'p1', 'name' => '5%', 'type' => 'percentage', 'value' => 5],
            ['id' => 'p2', 'name' => 'VIP 25%', 'type' => 'percentage', 'value' => 25],
            ['id' => 'p3', 'name' => 'Kupon Rp 10.000', 'type' => 'fixed', 'value' => 10000],
        ];

        $response = $this->actingAs($user)
            ->postJson(route('admin.pos.discount-settings.save'), [
                'presets' => $newPresets,
            ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('discount_settings.presets.1.name', 'VIP 25%')
            ->assertJsonPath('discount_settings.presets.2.value', 10000);

        $shop->refresh();
        $this->assertCount(3, $shop->discount_settings['presets']);
        $this->assertEquals('VIP 25%', $shop->discount_settings['presets'][1]['name']);
    }

    public function test_pos_page_and_products_api_include_sold_out_products(): void
    {
        $shop = Shop::factory()->create();
        $user = User::factory()->create([
            'shop_id' => $shop->id,
            'role' => 'admin',
        ]);

        $soldOut = Product::create([
            'shop_id' => $shop->id,
            'name' => 'Kopi Robusta Habis',
            'price' => 15000,
            'is_sold_out' => true,
        ]);

        $inStock = Product::create([
            'shop_id' => $shop->id,
            'name' => 'Kopi Arabika Ada',
            'price' => 20000,
            'is_sold_out' => false,
        ]);

        // 1. POS View
        $response = $this->actingAs($user)->get(route('admin.pos.index'));
        $response->assertOk();
        $response->assertSee('Kopi Robusta Habis');
        $response->assertSee('Sold Out (Habis)');
        $response->assertSee('Kopi Arabika Ada');

        // 2. POS Products API
        $apiResponse = $this->actingAs($user)->getJson(route('admin.pos.products'));
        $apiResponse->assertOk();
        $items = collect($apiResponse->json());
        $this->assertTrue($items->contains('name', 'Kopi Robusta Habis'));
        $this->assertTrue($items->firstWhere('name', 'Kopi Robusta Habis')['is_sold_out']);
        $this->assertTrue($items->contains('name', 'Kopi Arabika Ada'));
        $this->assertFalse($items->firstWhere('name', 'Kopi Arabika Ada')['is_sold_out']);
    }
}
