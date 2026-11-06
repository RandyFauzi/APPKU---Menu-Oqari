<?php

namespace Tests\Feature;

use App\Actions\Orders\CreateOrderAction;
use App\Actions\POS\CalculateCart;
use App\Models\Category;
use App\Models\Product;
use App\Models\Shop;
use App\Models\Table;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductDiscountPriceTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_model_calculates_effective_price_and_discount_percentage(): void
    {
        $shop = Shop::factory()->create(['onboarding_status' => 'completed']);

        // Case 1: Active discount
        $product = Product::create([
            'shop_id' => $shop->id,
            'name' => 'Kopi Susu Gula Aren',
            'price' => 25000,
            'discount_price' => 20000,
            'is_discount_active' => true,
        ]);

        $this->assertTrue($product->has_active_discount);
        $this->assertEquals(20000.0, $product->effective_price);
        $this->assertEquals(20, $product->discount_percentage);

        // Case 2: Inactive discount
        $product->update(['is_discount_active' => false]);
        $product->refresh();

        $this->assertFalse($product->has_active_discount);
        $this->assertEquals(25000.0, $product->effective_price);
        $this->assertNull($product->discount_percentage);

        // Case 3: Invalid discount price (greater than or equal to regular price)
        $product->update([
            'is_discount_active' => true,
            'discount_price' => 30000,
        ]);
        $product->refresh();

        $this->assertFalse($product->has_active_discount);
        $this->assertEquals(25000.0, $product->effective_price);
        $this->assertNull($product->discount_percentage);
    }

    public function test_toggle_menu_discount_endpoint_toggles_discount_status(): void
    {
        $shop = Shop::factory()->create(['onboarding_status' => 'completed']);
        $owner = User::factory()->create(['shop_id' => $shop->id, 'role' => 'owner']);

        $product = Product::create([
            'shop_id' => $shop->id,
            'name' => 'Caramel Macchiato',
            'price' => 30000,
            'discount_price' => 24000,
            'is_discount_active' => false,
        ]);

        $response = $this->actingAs($owner)->postJson("/admin/api/menu/{$product->id}/toggle-discount");

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'is_discount_active' => true,
                'effective_price' => 24000,
                'discount_percentage' => 20,
                'has_active_discount' => true,
            ]);

        $this->assertTrue((bool) $product->fresh()->is_discount_active);

        // Toggle back
        $response2 = $this->actingAs($owner)->postJson("/admin/api/menu/{$product->id}/toggle-discount");
        $response2->assertOk()
            ->assertJson([
                'success' => true,
                'is_discount_active' => false,
                'effective_price' => 30000,
                'discount_percentage' => null,
                'has_active_discount' => false,
            ]);

        $this->assertFalse((bool) $product->fresh()->is_discount_active);
    }

    public function test_save_menu_bulk_persists_discount_price_and_active_flag(): void
    {
        $shop = Shop::factory()->create(['onboarding_status' => 'completed']);
        $owner = User::factory()->create(['shop_id' => $shop->id, 'role' => 'owner']);
        $category = Category::create(['shop_id' => $shop->id, 'name' => 'Coffee']);

        $response = $this->actingAs($owner)->postJson('/admin/api/menu/bulk', [
            'items' => [
                [
                    'name' => 'Espresso Promo',
                    'price' => 20000,
                    'discount_price' => 15000,
                    'is_discount_active' => '1',
                    'category_id' => $category->id,
                ],
                [
                    'name' => 'Americano Normal',
                    'price' => 22000,
                    'discount_price' => '',
                    'is_discount_active' => '0',
                    'category_id' => $category->id,
                ],
            ],
        ]);

        $response->assertOk()->assertJson(['success' => true]);

        $promoItem = Product::where('shop_id', $shop->id)->where('name', 'Espresso Promo')->first();
        $this->assertNotNull($promoItem);
        $this->assertTrue($promoItem->is_discount_active);
        $this->assertEquals(15000.0, $promoItem->discount_price);
        $this->assertEquals(15000.0, $promoItem->effective_price);
        $this->assertEquals(25, $promoItem->discount_percentage);

        $normalItem = Product::where('shop_id', $shop->id)->where('name', 'Americano Normal')->first();
        $this->assertNotNull($normalItem);
        $this->assertFalse($normalItem->is_discount_active);
        $this->assertEquals(22000.0, $normalItem->effective_price);
    }

    public function test_pos_calculate_cart_uses_effective_price_for_discounted_items(): void
    {
        $shop = Shop::factory()->create(['onboarding_status' => 'completed']);

        $promoProduct = Product::create([
            'shop_id' => $shop->id,
            'name' => 'Matcha Latte Promo',
            'price' => 28000,
            'discount_price' => 21000,
            'is_discount_active' => true,
            'is_sold_out' => false,
        ]);

        $normalProduct = Product::create([
            'shop_id' => $shop->id,
            'name' => 'Croissant',
            'price' => 18000,
            'discount_price' => null,
            'is_discount_active' => false,
            'is_sold_out' => false,
        ]);

        $action = new CalculateCart;
        $result = $action->execute($shop, [
            ['id' => $promoProduct->id, 'qty' => 2],
            ['id' => $normalProduct->id, 'qty' => 1],
        ]);

        // 2 * 21,000 + 1 * 18,000 = 42,000 + 18,000 = 60,000
        $this->assertEquals(60000.0, $result['subtotal']);
        $this->assertEquals(21000.0, $result['items'][0]['unit_price']);
        $this->assertEquals(42000.0, $result['items'][0]['subtotal']);
        $this->assertEquals(18000.0, $result['items'][1]['unit_price']);
    }

    public function test_customer_order_creation_charges_discounted_price(): void
    {
        $shop = Shop::factory()->create(['onboarding_status' => 'completed']);
        $table = Table::create(['shop_id' => $shop->id, 'name' => 'Meja 5']);

        $product = Product::create([
            'shop_id' => $shop->id,
            'name' => 'Iced Vanilla Latte',
            'price' => 32000,
            'discount_price' => 25000,
            'is_discount_active' => true,
            'is_sold_out' => false,
        ]);

        $action = app(CreateOrderAction::class);
        $order = $action->execute($shop, [
            'table_id' => $table->name,
            'customer_name' => 'Rian',
            'customer_phone' => '08123456789',
            'payment_method' => 'CASH',
            'items' => [
                ['id' => $product->id, 'qty' => 2],
            ],
            'fulfillment_type' => 'DINE_IN',
        ]);

        // 2 * 25,000 = 50,000
        $this->assertEquals(50000.0, (float) $order->grand_total);
        $this->assertEquals(25000.0, (float) $order->items->first()->unit_price);
        $this->assertEquals(50000.0, (float) $order->items->first()->subtotal);
    }
}
