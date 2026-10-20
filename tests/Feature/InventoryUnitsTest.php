<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\RawMaterial;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class InventoryUnitsTest extends TestCase
{
    use RefreshDatabase;

    #[TestWith(['Pcs', 'pcs'])]
    #[TestWith(['Dus', 'dus'])]
    #[TestWith(['Botol', 'botol'])]
    #[TestWith(['Pack', 'pack'])]
    public function test_units_are_canonical_and_visible_on_remaining_stock_and_history(string $label, string $unit): void
    {
        $user = $this->owner();
        $response = $this->actingAs($user)->postJson('/admin/api/inventory/material', $this->materialData($label))->assertOk()->assertJsonPath('material.unit', $unit)->assertJsonPath('material.unit_label', $label);
        $id = $response->json('material.id');
        $this->getJson('/admin/api/inventory')->assertOk()->assertJsonPath('materials.0.current_stock', '10.000000')->assertJsonPath('materials.0.unit_label', $label)->assertJsonPath('materials.0.unit_locked', true)->assertJsonPath('mutations.0.unit', $unit)->assertJsonPath('mutations.0.unit_label', $label)->assertJsonPath('units.'.$unit, $label);
        $this->assertDatabaseHas('stock_mutations', ['raw_material_id' => $id, 'unit' => $unit, 'amount' => 10, 'balance_after' => 10]);
        $this->get(route('admin.dashboard', ['tab' => 'inventory']))->assertOk()->assertSee('Satuan Dasar');
    }

    public function test_fractional_pack_restock_and_zero_opname_preserve_unit_and_exact_balance(): void
    {
        $user = $this->owner();
        $id = $this->actingAs($user)->postJson('/admin/api/inventory/material', $this->materialData('pack'))->json('material.id');
        $this->postJson('/admin/api/inventory/adjust', ['raw_material_id' => $id, 'type' => 'in', 'amount' => 0.000001])->assertOk()->assertJsonPath('material.current_stock', '10.000001');
        $this->postJson('/admin/api/inventory/adjust', ['raw_material_id' => $id, 'type' => 'adjustment', 'amount' => 0])->assertOk()->assertJsonPath('material.current_stock', '0.000000');
        $this->assertDatabaseHas('stock_mutations', ['raw_material_id' => $id, 'type' => 'adjustment', 'unit' => 'pack', 'amount' => -10.000001, 'balance_after' => 0]);
        $this->assertDatabaseHas('stock_mutations', ['raw_material_id' => $id, 'type' => 'in', 'unit' => 'pack', 'amount' => 0.000001]);
    }

    public function test_waste_cannot_exceed_stock_and_does_not_write_false_history(): void
    {
        $user = $this->owner();
        $id = $this->actingAs($user)->postJson('/admin/api/inventory/material', $this->materialData('botol'))->json('material.id');
        $this->postJson('/admin/api/inventory/adjust', ['raw_material_id' => $id, 'type' => 'waste', 'amount' => 11])->assertUnprocessable()->assertJsonValidationErrors('amount');
        $this->assertDatabaseHas('raw_materials', ['id' => $id, 'current_stock' => 10]);
        $this->assertDatabaseCount('stock_mutations', 1);
        $this->postJson('/admin/api/inventory/adjust', ['raw_material_id' => $id, 'type' => 'waste', 'amount' => 2])->assertOk();
        $this->assertDatabaseHas('stock_mutations', ['raw_material_id' => $id, 'unit' => 'botol', 'amount' => -2, 'balance_after' => 8]);
    }

    public function test_used_unit_cannot_change_even_after_balance_reaches_zero(): void
    {
        $user = $this->owner();
        $id = $this->actingAs($user)->postJson('/admin/api/inventory/material', $this->materialData('dus'))->json('material.id');
        $this->postJson('/admin/api/inventory/adjust', ['raw_material_id' => $id, 'type' => 'adjustment', 'amount' => 0])->assertOk();
        $this->postJson('/admin/api/inventory/material', ['id' => $id] + $this->materialData('pcs'))->assertUnprocessable()->assertJsonValidationErrors('unit');
        $this->assertDatabaseHas('raw_materials', ['id' => $id, 'unit' => 'dus']);
        $this->assertDatabaseMissing('stock_mutations', ['raw_material_id' => $id, 'unit' => 'pcs']);
    }

    public function test_unused_empty_material_can_change_unit_but_recipe_link_locks_it(): void
    {
        $user = $this->owner();
        $data = $this->materialData('pcs');
        $data['current_stock'] = 0;
        $id = $this->actingAs($user)->postJson('/admin/api/inventory/material', $data)->json('material.id');
        $this->postJson('/admin/api/inventory/material', ['id' => $id] + $this->materialData('pack'))->assertOk();
        $this->assertDatabaseHas('raw_materials', ['id' => $id, 'unit' => 'pack', 'current_stock' => 0]);
        $product = Product::factory()->create(['shop_id' => $user->shop_id]);
        $product->recipes()->create(['raw_material_id' => $id, 'amount' => 1]);
        $this->postJson('/admin/api/inventory/material', ['id' => $id] + $this->materialData('pcs'))->assertUnprocessable()->assertJsonValidationErrors('unit');
    }

    public function test_editing_material_does_not_overwrite_stock_with_stale_form_balance(): void
    {
        $user = $this->owner();
        $id = $this->actingAs($user)->postJson('/admin/api/inventory/material', $this->materialData('pack'))->json('material.id');
        $this->postJson('/admin/api/inventory/adjust', ['raw_material_id' => $id, 'type' => 'in', 'amount' => 5])->assertOk();
        $this->postJson('/admin/api/inventory/material', ['id' => $id] + $this->materialData('pack'))->assertOk()->assertJsonPath('material.current_stock', '15.000000');
        $this->assertDatabaseCount('stock_mutations', 2);
    }

    public function test_new_unknown_units_are_rejected_but_existing_custom_units_remain_editable(): void
    {
        $user = $this->owner();
        $this->actingAs($user)->postJson('/admin/api/inventory/material', $this->materialData('karung'))->assertUnprocessable()->assertJsonValidationErrors('unit');
        $material = RawMaterial::create(['shop_id' => $user->shop_id, 'name' => 'Legacy', 'unit' => 'karung', 'current_stock' => 5]);
        $this->postJson('/admin/api/inventory/material', ['id' => $material->id] + $this->materialData('karung'))->assertOk()->assertJsonPath('material.unit', 'karung');
    }

    public function test_history_keeps_unit_and_material_name_after_soft_delete(): void
    {
        $user = $this->owner();
        $id = $this->actingAs($user)->postJson('/admin/api/inventory/material', $this->materialData('botol'))->json('material.id');
        RawMaterial::findOrFail($id)->delete();
        $this->getJson('/admin/api/inventory')->assertOk()->assertJsonCount(0, 'materials')->assertJsonPath('mutations.0.unit_label', 'Botol')->assertJsonPath('mutations.0.raw_material.name', 'Test stock');
    }

    public function test_foreign_material_cannot_be_changed_or_adjusted(): void
    {
        $user = $this->owner();
        $other = $this->owner();
        $material = RawMaterial::create(['shop_id' => $other->shop_id, 'name' => 'Secret', 'unit' => 'pcs', 'current_stock' => 10]);
        $this->actingAs($user)->postJson('/admin/api/inventory/material', ['id' => $material->id] + $this->materialData('pack'))->assertNotFound();
        $this->postJson('/admin/api/inventory/adjust', ['raw_material_id' => $material->id, 'type' => 'in', 'amount' => 1])->assertNotFound();
        $this->getJson('/admin/api/inventory')->assertOk()->assertJsonCount(0, 'materials');
        $this->assertDatabaseCount('stock_mutations', 0);
    }

    public function test_stock_unit_cannot_be_forged_through_adjustment_and_permissions_are_required(): void
    {
        $this->postJson('/admin/api/inventory/material', $this->materialData('pcs'))->assertUnauthorized();
        $user = $this->owner();
        $id = $this->actingAs($user)->postJson('/admin/api/inventory/material', $this->materialData('pcs'))->json('material.id');
        $this->postJson('/admin/api/inventory/adjust', ['raw_material_id' => $id, 'type' => 'in', 'amount' => 1, 'unit' => 'dus'])->assertUnprocessable()->assertJsonValidationErrors('unit');
        $user->update(['role' => 'cashier']);
        $this->postJson('/admin/api/inventory/material', $this->materialData('pcs'))->assertForbidden();
        $this->postJson('/admin/api/inventory/adjust', ['raw_material_id' => $id, 'type' => 'in', 'amount' => 1])->assertForbidden();
    }

    public function test_legacy_capitalization_can_be_normalized_without_changing_meaning(): void
    {
        $user = $this->owner();
        $material = RawMaterial::create(['shop_id' => $user->shop_id, 'name' => 'Old stock', 'unit' => 'Pcs', 'current_stock' => 5]);
        $this->actingAs($user)->postJson('/admin/api/inventory/material', ['id' => $material->id] + $this->materialData('pcs'))->assertOk()->assertJsonPath('material.unit', 'pcs')->assertJsonPath('material.current_stock', '5.000000');
    }

    private function owner(): User
    {
        return User::factory()->create(['shop_id' => Shop::factory()->create(['onboarding_status' => 'completed'])->id, 'role' => 'owner']);
    }

    /** @return array<string, mixed> */
    private function materialData(string $unit): array
    {
        return ['name' => 'Test stock', 'unit' => $unit, 'current_stock' => 10, 'minimum_stock' => 2, 'cost_per_unit' => 1000];
    }
}
