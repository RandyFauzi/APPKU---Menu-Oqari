<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use App\Models\RawMaterial;
use App\Models\Shop;
use App\Models\User;
use App\Services\InventoryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class HppCalculatorTest extends TestCase
{
    use RefreshDatabase;

    public function test_simulation_calculates_margin_and_markup_without_writing(): void
    {
        [$user, $material, $product, $data] = $this->scenario();
        $this->actingAs($user)->get(route('admin.hpp.index', ['product_id' => $product->id]))->assertOk()->assertSee('Kalkulator HPP');
        $this->postJson(route('admin.hpp.calculate'), $data)->assertOk()
            ->assertJsonPath('result.hpp', 10000)->assertJsonPath('result.gross_profit', 15000)
            ->assertJsonPath('result.margin_percent', 60)->assertJsonPath('result.markup_percent', 150)
            ->assertJsonPath('result.suggested_price', 25000);
        $this->assertEquals(5000, $product->fresh()->cogs);
        $this->assertDatabaseCount('product_recipes', 0);
        $this->assertDatabaseCount('activity_logs', 0);
    }

    public function test_save_updates_recipe_and_cost_without_changing_price_or_stock(): void
    {
        [$user, $material, $product, $data] = $this->scenario();
        $data['selling_price'] = 40000;
        $this->actingAs($user)->putJson(route('admin.hpp.update', $product->id), $data)->assertOk();
        $this->assertDatabaseHas('products', ['id' => $product->id, 'cogs' => 10000, 'price' => 25000, 'hpp_overhead_cost' => 1000, 'hpp_target_margin' => 60]);
        $this->assertDatabaseHas('product_recipes', ['product_id' => $product->id, 'raw_material_id' => $material->id, 'amount' => 2]);
        $this->assertEquals(100, $material->fresh()->current_stock);
        $this->assertDatabaseHas('activity_logs', ['shop_id' => $user->shop_id, 'action' => 'product.hpp_updated']);
        $this->assertEquals(10000, $product->fresh()->calculateHppFromRecipes());
    }

    public function test_unknown_material_cost_blocks_save_and_preserves_existing_recipe(): void
    {
        [$user, $material, $product, $data] = $this->scenario();
        $product->recipes()->create(['raw_material_id' => $material->id, 'amount' => 1]);
        $material->update(['cost_per_unit' => 0]);
        $this->actingAs($user)->postJson(route('admin.hpp.calculate'), $data)->assertOk()
            ->assertJsonPath('result.is_complete', false)->assertJsonPath('result.hpp', null)->assertJsonPath('result.gross_profit', null);
        $this->putJson(route('admin.hpp.update', $product->id), $data)->assertUnprocessable()->assertJsonValidationErrors('recipes');
        $this->assertDatabaseHas('product_recipes', ['product_id' => $product->id, 'amount' => 1]);
        $this->assertEquals(5000, $product->fresh()->cogs);
        $this->assertDatabaseCount('activity_logs', 0);
    }

    public function test_cafe_cannot_use_another_cafes_material_or_product(): void
    {
        [$user, $material, $product, $data] = $this->scenario();
        $other = Shop::factory()->create();
        $material->update(['shop_id' => $other->id]);
        $product->update(['shop_id' => $other->id]);
        $this->actingAs($user)->postJson(route('admin.hpp.calculate'), $data)->assertUnprocessable()->assertJsonValidationErrors('recipes.0.raw_material_id');
        $this->get(route('admin.hpp.index', ['product_id' => $product->id]))->assertNotFound();
        $material->update(['shop_id' => $user->shop_id]);
        $this->putJson(route('admin.hpp.update', $product->id), $data)->assertNotFound();
        $this->assertEquals(5000, $product->fresh()->cogs);
    }

    public function test_deleted_material_and_duplicate_recipe_are_rejected(): void
    {
        [$user, $material, $product, $data] = $this->scenario();
        $this->actingAs($user);
        $data['recipes'][] = $data['recipes'][0];
        $this->postJson(route('admin.hpp.calculate'), $data)->assertUnprocessable()->assertJsonValidationErrors('recipes.0.raw_material_id');
        array_pop($data['recipes']);
        $material->delete();
        $this->postJson(route('admin.hpp.calculate'), $data)->assertUnprocessable()->assertJsonValidationErrors('recipes.0.raw_material_id');
    }

    #[TestWith(['cashier'])]
    #[TestWith(['barista'])]
    #[TestWith(['crew'])]
    public function test_staff_without_menu_permission_cannot_access_costs(string $role): void
    {
        [$user, $material, $product, $data] = $this->scenario();
        $user->update(['role' => $role]);
        $this->actingAs($user)->get(route('admin.hpp.index'))->assertForbidden();
        $this->postJson(route('admin.hpp.calculate'), $data)->assertForbidden();
        $this->putJson(route('admin.hpp.update', $product->id), $data)->assertForbidden();
    }

    #[TestWith(['target_margin', 100])]
    #[TestWith(['overhead_cost', -1])]
    #[TestWith(['selling_price', 100000000])]
    #[TestWith(['recipes.0.amount', 0])]
    #[TestWith(['recipes.0.amount', 0.0000001])]
    #[TestWith(['cogs', 1])]
    #[TestWith(['shop_id', 1])]
    public function test_invalid_financial_inputs_are_rejected(string $field, mixed $value): void
    {
        [$user, $material, $product, $data] = $this->scenario();
        data_set($data, $field, $value);
        $this->actingAs($user)->postJson(route('admin.hpp.calculate'), $data)->assertUnprocessable()->assertJsonValidationErrors($field);
    }

    public function test_zero_price_has_no_margin_and_below_cost_price_shows_loss(): void
    {
        [$user, $material, $product, $data] = $this->scenario();
        $data['selling_price'] = 0;
        $data['target_margin'] = null;
        $this->actingAs($user)->postJson(route('admin.hpp.calculate'), $data)->assertOk()
            ->assertJsonPath('result.margin_percent', null)->assertJsonPath('result.suggested_price', null);
        $data['selling_price'] = 5000;
        $this->postJson(route('admin.hpp.calculate'), $data)->assertOk()->assertJsonPath('result.gross_profit', -5000)->assertJsonPath('result.margin_percent', -100);
    }

    public function test_save_recalculates_using_latest_material_price_and_replaces_old_recipe(): void
    {
        [$user, $material, $product, $data] = $this->scenario();
        $old = RawMaterial::create(['shop_id' => $user->shop_id, 'name' => 'Old', 'unit' => 'pcs', 'cost_per_unit' => 1, 'current_stock' => 10, 'minimum_stock' => 0]);
        $product->recipes()->create(['raw_material_id' => $old->id, 'amount' => 1]);
        $this->actingAs($user)->postJson(route('admin.hpp.calculate'), $data)->assertOk()->assertJsonPath('result.hpp', 10000);
        $material->update(['cost_per_unit' => 5000]);
        $this->putJson(route('admin.hpp.update', $product->id), $data)->assertOk()->assertJsonPath('result.hpp', 11000);
        $this->assertDatabaseMissing('product_recipes', ['raw_material_id' => $old->id]);
        $this->assertDatabaseCount('product_recipes', 1);
    }

    public function test_reference_batch_includes_precise_ingredient_cost_and_percentage_overhead(): void
    {
        [$user, $milk, $product, $data] = $this->scenario();
        $milk->update(['cost_per_unit' => 28.5, 'unit' => 'ml']);
        $syrup = RawMaterial::create(['shop_id' => $user->shop_id, 'name' => 'Caramel', 'unit' => 'ml', 'cost_per_unit' => 120]);
        $cup = RawMaterial::create(['shop_id' => $user->shop_id, 'name' => 'Cup', 'unit' => 'pcs', 'cost_per_unit' => 1140]);
        $data = array_merge($data, ['recipes' => [
            ['raw_material_id' => $milk->id, 'amount' => 200, 'notes' => 'Cold'],
            ['raw_material_id' => $syrup->id, 'amount' => 80],
            ['raw_material_id' => $cup->id, 'amount' => 1],
        ], 'production_quantity' => 200, 'production_unit' => 'gram', 'overhead_mode' => 'percentage', 'overhead_percent' => 54, 'labor_cost' => 0, 'pricing_method' => 'markup', 'target_markup' => 25]);
        $this->actingAs($user)->postJson(route('admin.hpp.calculate'), $data)->assertOk()
            ->assertJsonPath('result.ingredients_cost', 16440)->assertJsonPath('result.overhead_cost', 8877.6)
            ->assertJsonPath('result.total_hpp', 25317.6)->assertJsonPath('result.hpp', 126.59)->assertJsonPath('result.suggested_price', 158.24);
        $this->putJson(route('admin.hpp.update', $product->id), $data)->assertOk();
        $this->assertDatabaseHas('product_recipes', ['product_id' => $product->id, 'raw_material_id' => $milk->id, 'amount' => 1, 'batch_amount' => 200, 'notes' => 'Cold']);
        $this->assertEquals(126.59, $product->fresh()->calculateHppFromRecipes());
        $this->get(route('admin.hpp.index', ['product_id' => $product->id]))->assertOk()->assertViewHas('initial', fn (array $initial): bool => (float) $initial['production_quantity'] === 200.0 && (float) $initial['recipes'][0]['amount'] === 200.0);
    }

    public function test_labor_and_overhead_are_divided_by_output_without_changing_selling_price(): void
    {
        [$user, $material, $product, $data] = $this->scenario();
        $data = array_merge($data, ['production_quantity' => 10, 'labor_cost' => 1000, 'overhead_mode' => 'percentage', 'overhead_percent' => 10]);
        $this->actingAs($user)->putJson(route('admin.hpp.update', $product->id), $data)->assertOk()->assertJsonPath('result.total_hpp', 11000)->assertJsonPath('result.hpp', 1100);
        $this->assertEquals(1100, $product->fresh()->calculateHppFromRecipes());
        $this->postJson('/admin/api/inventory/material', ['id' => $material->id, 'name' => $material->name, 'unit' => $material->unit, 'current_stock' => 100, 'minimum_stock' => 0, 'cost_per_unit' => 5000])->assertOk();
        $this->assertEquals(1210, $product->fresh()->cogs);
        $this->assertEquals(1210, $product->fresh()->calculateHppFromRecipes());
        $this->assertEquals(25000, $product->fresh()->price);
    }

    public function test_purchase_price_is_calculated_on_server_and_creates_no_stock(): void
    {
        [$user] = $this->scenario();
        $response = $this->actingAs($user)->postJson(route('admin.hpp.materials.store'), ['name' => 'Ultra Milk', 'purchase_total' => 28500, 'purchase_quantity' => 1000, 'unit' => 'ml'])->assertCreated()->assertJsonPath('material.cost_per_unit', '28.500000');
        $this->assertDatabaseHas('raw_materials', ['id' => $response->json('material.id'), 'shop_id' => $user->shop_id, 'current_stock' => 0]);
        $this->assertDatabaseCount('stock_mutations', 0);
        $this->assertDatabaseHas('activity_logs', ['action' => 'material.hpp_created']);
        $this->postJson(route('admin.hpp.materials.store'), ['name' => 'Bad', 'purchase_total' => 100, 'purchase_quantity' => 0, 'unit' => 'ml'])->assertUnprocessable()->assertJsonValidationErrors('purchase_quantity');
    }

    public function test_recipe_converts_only_compatible_units(): void
    {
        [$user, $material, $product, $data] = $this->scenario();
        $material->update(['unit' => 'gram', 'cost_per_unit' => 1]);
        $data['recipes'][0] = ['raw_material_id' => $material->id, 'amount' => 0.5, 'unit' => 'kg'];
        $data['overhead_cost'] = 0;
        $this->actingAs($user)->postJson(route('admin.hpp.calculate'), $data)->assertOk()->assertJsonPath('result.hpp', 500);
        $data['recipes'][0]['unit'] = 'ml';
        $this->postJson(route('admin.hpp.calculate'), $data)->assertUnprocessable()->assertJsonValidationErrors('recipes');
    }

    #[TestWith(['exclusive', 10, 27500, 2500, 15000])]
    #[TestWith(['inclusive', 10, 25000, 2272.73, 12727.27])]
    #[TestWith(['exclusive', 0, 25000, 0, 15000])]
    public function test_tax_simulation_uses_correct_base(string $mode, int $rate, float $final, float $tax, float $profit): void
    {
        [$user, $material, $product, $data] = $this->scenario();
        $data = array_merge($data, ['tax_mode' => $mode, 'tax_rate' => $rate]);
        $response = $this->actingAs($user)->postJson(route('admin.hpp.calculate'), $data)->assertOk();
        $this->assertEquals($final, $response->json('result.final_price'));
        $this->assertEquals($tax, $response->json('result.tax_amount'));
        $this->assertEquals($profit, $response->json('result.profit_after_tax'));
        $this->assertEquals(25000, $product->fresh()->price);
    }

    public function test_small_batch_recipe_remains_precise_when_deducting_stock(): void
    {
        [$user, $material, $product, $data] = $this->scenario();
        $material->update(['current_stock' => 1]);
        $data['recipes'][0]['amount'] = 1;
        $data['production_quantity'] = 200;
        $this->actingAs($user)->putJson(route('admin.hpp.update', $product->id), $data)->assertOk();
        $this->assertDatabaseHas('product_recipes', ['product_id' => $product->id, 'amount' => 0.005]);
        $order = Order::create(['shop_id' => $user->shop_id, 'order_status' => 'CONFIRMED']);
        $order->items()->create(['product_name' => $product->name, 'product_id' => $product->id, 'quantity' => 1, 'unit_price' => 25000, 'subtotal' => 25000]);
        app(InventoryService::class)->deductStockForOrder($order);
        $this->assertSame('0.995000', $material->fresh()->current_stock);
        $this->assertDatabaseHas('stock_mutations', ['raw_material_id' => $material->id, 'amount' => -0.005]);
    }

    #[TestWith(['production_quantity', 0])]
    #[TestWith(['overhead_percent', 101])]
    #[TestWith(['labor_cost', -1])]
    #[TestWith(['tax_rate', 101])]
    #[TestWith(['target_markup', 1001])]
    public function test_new_cost_settings_are_validated(string $field, mixed $value): void
    {
        [$user, $material, $product, $data] = $this->scenario();
        $data[$field] = $value;
        $this->actingAs($user)->postJson(route('admin.hpp.calculate'), $data)->assertUnprocessable()->assertJsonValidationErrors($field);
    }

    public function test_material_creation_rejects_forged_shop_and_staff_without_inventory_access(): void
    {
        [$user] = $this->scenario();
        $payload = ['name' => 'Milk', 'purchase_total' => 28500, 'purchase_quantity' => 1000, 'unit' => 'ml'];
        $this->actingAs($user)->postJson(route('admin.hpp.materials.store'), $payload + ['shop_id' => 999])->assertUnprocessable()->assertJsonValidationErrors('shop_id');
        $user->update(['role' => 'cashier']);
        $this->postJson(route('admin.hpp.materials.store'), $payload)->assertForbidden();
        $this->assertDatabaseCount('raw_materials', 1);
    }

    public function test_too_small_normalized_recipe_rolls_back_existing_recipe(): void
    {
        [$user, $material, $product, $data] = $this->scenario();
        $product->recipes()->create(['raw_material_id' => $material->id, 'amount' => 1]);
        $data['recipes'][0]['amount'] = 0.01;
        $data['production_quantity'] = 999999;
        $this->actingAs($user)->putJson(route('admin.hpp.update', $product->id), $data)->assertUnprocessable()->assertJsonValidationErrors('recipes');
        $this->assertDatabaseHas('product_recipes', ['product_id' => $product->id, 'amount' => 1]);
        $this->assertEquals(5000, $product->fresh()->cogs);
        $this->assertDatabaseCount('activity_logs', 0);
    }

    /** @return array{User, RawMaterial, Product, array<string, mixed>} */
    private function scenario(): array
    {
        $shop = Shop::factory()->create(['onboarding_status' => 'completed']);
        $user = User::factory()->create(['shop_id' => $shop->id, 'role' => 'owner']);
        $material = RawMaterial::create(['shop_id' => $shop->id, 'name' => 'Coffee', 'unit' => 'gram', 'cost_per_unit' => 4500, 'current_stock' => 100, 'minimum_stock' => 0]);
        $product = Product::factory()->create(['shop_id' => $shop->id, 'price' => 25000, 'cogs' => 5000]);

        return [$user, $material, $product, ['recipes' => [['raw_material_id' => $material->id, 'amount' => 2]], 'overhead_cost' => 1000, 'selling_price' => 25000, 'target_margin' => 60]];
    }
}
