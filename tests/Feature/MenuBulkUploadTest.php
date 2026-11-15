<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MenuBulkUploadTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_download_excel_menu_template(): void
    {
        $shop = Shop::factory()->create(['onboarding_status' => 'completed']);
        $user = User::factory()->create(['role' => 'owner', 'shop_id' => $shop->id]);

        $this->actingAs($user)->get('/admin/dashboard');

        $response = $this->actingAs($user)->get(route('admin.menu.template.excel'));

        $response->assertStatus(200);
        $response->assertHeader('content-disposition', 'attachment; filename=template_menu_oqari.xlsx');
    }

    public function test_admin_can_download_csv_menu_template(): void
    {
        $shop = Shop::factory()->create(['onboarding_status' => 'completed']);
        $user = User::factory()->create(['role' => 'owner', 'shop_id' => $shop->id]);

        $this->actingAs($user)->get('/admin/dashboard');

        $response = $this->actingAs($user)->get(route('admin.menu.template.csv'));

        $response->assertStatus(200);
        $response->assertHeader('content-disposition', 'attachment; filename=template_menu_oqari.csv');
    }

    public function test_admin_can_save_bulk_menus_and_auto_create_categories(): void
    {
        $shop = Shop::factory()->create(['onboarding_status' => 'completed']);
        $user = User::factory()->create(['role' => 'owner', 'shop_id' => $shop->id]);

        $this->actingAs($user)->get('/admin/dashboard');

        $payload = [
            'items' => [
                [
                    'name' => 'Americano Special',
                    'price' => 22000,
                    'category_name' => 'Signature Coffee',
                    'description' => 'Espresso dengan air panas murni',
                ],
                [
                    'name' => 'Butter Croissant Premium',
                    'price' => 24000,
                    'category_name' => 'French Pastry',
                    'description' => 'Pastry renyah dengan butter impor',
                ],
            ],
        ];

        $response = $this->actingAs($user)->postJson('/admin/api/menu/bulk', $payload);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        $this->assertDatabaseHas('products', [
            'shop_id' => $shop->id,
            'name' => 'Americano Special',
            'price' => 22000,
            'category_name' => 'Signature Coffee',
            'description' => 'Espresso dengan air panas murni',
        ]);

        $this->assertDatabaseHas('products', [
            'shop_id' => $shop->id,
            'name' => 'Butter Croissant Premium',
            'price' => 24000,
            'category_name' => 'French Pastry',
            'description' => 'Pastry renyah dengan butter impor',
        ]);

        $this->assertDatabaseHas('categories', [
            'shop_id' => $shop->id,
            'name' => 'Signature Coffee',
        ]);

        $this->assertDatabaseHas('categories', [
            'shop_id' => $shop->id,
            'name' => 'French Pastry',
        ]);
    }

    public function test_bulk_menu_save_validates_empty_items(): void
    {
        $shop = Shop::factory()->create(['onboarding_status' => 'completed']);
        $user = User::factory()->create(['role' => 'owner', 'shop_id' => $shop->id]);

        $this->actingAs($user)->get('/admin/dashboard');

        $response = $this->actingAs($user)->postJson('/admin/api/menu/bulk', [
            'items' => [],
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
        ]);
    }

    public function test_bulk_menu_save_updates_existing_menu_item_when_id_is_provided(): void
    {
        $shop = Shop::factory()->create(['onboarding_status' => 'completed']);
        $user = User::factory()->create(['role' => 'owner', 'shop_id' => $shop->id]);

        $category = Category::factory()->create(['shop_id' => $shop->id, 'name' => 'Coffee']);
        $existingProduct = Product::factory()->create([
            'shop_id' => $shop->id,
            'category_id' => $category->id,
            'category_name' => 'Coffee',
            'name' => 'Old Americano',
            'price' => 20000,
        ]);

        $this->actingAs($user)->get('/admin/dashboard');

        $payload = [
            'items' => [
                [
                    'id' => $existingProduct->id,
                    'name' => 'Updated Americano',
                    'price' => 25000,
                    'category_id' => $category->id,
                    'description' => 'Updated recipe',
                ],
            ],
        ];

        $response = $this->actingAs($user)->postJson('/admin/api/menu/bulk', $payload);

        $response->assertStatus(200);
        $this->assertDatabaseHas('products', [
            'id' => $existingProduct->id,
            'name' => 'Updated Americano',
            'price' => 25000,
            'description' => 'Updated recipe',
        ]);
    }
}
