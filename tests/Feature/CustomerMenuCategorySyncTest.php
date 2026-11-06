<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Shop;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerMenuCategorySyncTest extends TestCase
{
    use RefreshDatabase;

    public function test_deleted_category_does_not_appear_on_customer_ordering_page(): void
    {
        $shop = Shop::factory()->create(['onboarding_status' => 'completed', 'slug' => 'sparta-cafe']);

        $coffeeCategory = Category::create([
            'shop_id' => $shop->id,
            'name' => 'Coffee',
            'slug' => 'coffee',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $solensinCategory = Category::create([
            'shop_id' => $shop->id,
            'name' => 'Solensin',
            'slug' => 'solensin',
            'sort_order' => 2,
            'is_active' => true,
        ]);

        $product1 = Product::create([
            'shop_id' => $shop->id,
            'category_id' => $coffeeCategory->id,
            'category_name' => 'Coffee',
            'name' => 'Americano',
            'price' => 25000,
            'is_sold_out' => false,
        ]);

        $product2 = Product::create([
            'shop_id' => $shop->id,
            'category_id' => $solensinCategory->id,
            'category_name' => 'Solensin',
            'name' => 'Solensin Special',
            'price' => 35000,
            'is_sold_out' => false,
        ]);

        // When Solensin is deleted from the database
        $solensinCategory->delete();

        $this->assertDatabaseMissing('categories', [
            'id' => $solensinCategory->id,
            'name' => 'Solensin',
        ]);

        // Visit customer ordering page
        $response = $this->get(route('shop.menu', $shop->slug));

        $response->assertOk();

        // Must see Coffee category
        $response->assertSee('Coffee');

        // Must NOT see Solensin as a category in window.SHOP_CATEGORIES
        $this->assertStringNotContainsString('"name":"Solensin"', $response->getContent());

        // The product itself should still be available in menu data (under All Menu)
        $response->assertSee('Solensin Special');
    }

    public function test_orphaned_legacy_product_category_name_is_sanitized_and_does_not_resurrect_deleted_category(): void
    {
        $shop = Shop::factory()->create(['onboarding_status' => 'completed', 'slug' => 'sparta-cafe']);

        $beveragesCategory = Category::create([
            'shop_id' => $shop->id,
            'name' => 'Minuman',
            'slug' => 'minuman',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        // Product with orphaned category_name from a category that no longer exists in DB
        $product = Product::create([
            'shop_id' => $shop->id,
            'category_id' => null,
            'category_name' => 'GhostCategory',
            'name' => 'Secret Recipe',
            'price' => 20000,
            'is_sold_out' => false,
        ]);

        // Visit customer ordering page
        $response = $this->get(route('shop.menu', $shop->slug));

        $response->assertOk();

        // Must NOT see GhostCategory in window.SHOP_CATEGORIES
        $this->assertStringNotContainsString('"name":"GhostCategory"', $response->getContent());

        // The orphaned product category fields should be sanitized in database
        $product->refresh();
        $this->assertNull($product->category_id);
        $this->assertNull($product->category_name);
    }

    public function test_deleting_category_model_automatically_reassigns_or_clears_product_category(): void
    {
        $shop = Shop::factory()->create(['onboarding_status' => 'completed']);

        $cat1 = Category::create([
            'shop_id' => $shop->id,
            'name' => 'Makanan Utama',
            'slug' => 'makanan-utama',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $cat2 = Category::create([
            'shop_id' => $shop->id,
            'name' => 'Camilan',
            'slug' => 'camilan',
            'sort_order' => 2,
            'is_active' => true,
        ]);

        $product = Product::create([
            'shop_id' => $shop->id,
            'category_id' => $cat2->id,
            'category_name' => 'Camilan',
            'name' => 'Kentang Goreng',
            'price' => 15000,
        ]);

        // Delete Camilan -> should fallback to Makanan Utama
        $cat2->delete();

        $product->refresh();
        $this->assertEquals($cat1->id, $product->category_id);
        $this->assertEquals('Makanan Utama', $product->category_name);

        // Delete Makanan Utama -> no more fallback, should be set to null
        $cat1->delete();

        $product->refresh();
        $this->assertNull($product->category_id);
        $this->assertNull($product->category_name);
    }

    public function test_inactive_categories_are_not_shown_on_customer_ordering_page(): void
    {
        $shop = Shop::factory()->create(['onboarding_status' => 'completed', 'slug' => 'sparta-cafe']);

        Category::create([
            'shop_id' => $shop->id,
            'name' => 'Kategori Nonaktif',
            'slug' => 'kategori-nonaktif',
            'sort_order' => 1,
            'is_active' => false,
        ]);

        $response = $this->get(route('shop.menu', $shop->slug));

        $response->assertOk();
        $this->assertStringNotContainsString('"name":"Kategori Nonaktif"', $response->getContent());
    }
}
