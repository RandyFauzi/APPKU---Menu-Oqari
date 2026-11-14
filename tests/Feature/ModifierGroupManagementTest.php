<?php

namespace Tests\Feature;

use App\Actions\POS\CalculateCart;
use App\Models\CashRegister;
use App\Models\CashRegisterSession;
use App\Models\Modifier;
use App\Models\ModifierGroup;
use App\Models\Order;
use App\Models\Product;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class ModifierGroupManagementTest extends TestCase
{
    use RefreshDatabase;

    private function createShopUser(string $role = 'owner'): array
    {
        $shop = Shop::factory()->create(['onboarding_status' => 'completed']);
        $user = User::factory()->create([
            'shop_id' => $shop->id,
            'role' => $role,
        ]);

        return [$shop, $user];
    }

    public function test_user_can_access_addons_page_and_fetch_addons_api(): void
    {
        [$shop, $user] = $this->createShopUser();

        $group = ModifierGroup::create([
            'shop_id' => $shop->id,
            'name' => 'Tingkat Kemanisan',
            'selection_type' => 'SINGLE',
            'is_required' => true,
            'is_active' => true,
        ]);

        Modifier::create([
            'modifier_group_id' => $group->id,
            'name' => 'Normal Sugar',
            'price_adjustment' => 0,
            'is_active' => true,
        ]);

        // Access page
        $this->actingAs($user)
            ->get(route('admin.addons.index'))
            ->assertOk()
            ->assertSee('Kelola Add-ons')
            ->assertSee('Tingkat Kemanisan');

        // Fetch API
        $response = $this->actingAs($user)
            ->getJson(route('admin.addons.api.index'))
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonCount(1, 'groups')
            ->assertJsonPath('groups.0.name', 'Tingkat Kemanisan')
            ->assertJsonPath('groups.0.selection_type', 'SINGLE')
            ->assertJsonPath('groups.0.modifiers.0.name', 'Normal Sugar');
    }

    public function test_user_can_create_modifier_group_with_items_and_products(): void
    {
        [$shop, $user] = $this->createShopUser();

        $product1 = Product::factory()->create(['shop_id' => $shop->id, 'name' => 'Americano']);
        $product2 = Product::factory()->create(['shop_id' => $shop->id, 'name' => 'Latte']);

        $payload = [
            'name' => 'Level Es (Ice Level)',
            'selection_type' => 'SINGLE',
            'is_required' => true,
            'is_active' => true,
            'product_ids' => [$product1->id, $product2->id],
            'items' => [
                ['name' => 'Normal Ice', 'price_adjustment' => 0, 'cogs_adjustment' => 0],
                ['name' => 'Less Ice', 'price_adjustment' => 0, 'cogs_adjustment' => 0],
                ['name' => 'No Ice', 'price_adjustment' => 0, 'cogs_adjustment' => 0],
            ],
        ];

        $response = $this->actingAs($user)
            ->postJson(route('admin.addons.store'), $payload)
            ->assertOk()
            ->assertJsonPath('success', true);

        $groupId = $response->json('group.id');

        $this->assertDatabaseHas('modifier_groups', [
            'id' => $groupId,
            'shop_id' => $shop->id,
            'name' => 'Level Es (Ice Level)',
            'selection_type' => 'SINGLE',
            'is_required' => true,
        ]);

        $this->assertDatabaseHas('modifiers', [
            'modifier_group_id' => $groupId,
            'name' => 'Less Ice',
            'price_adjustment' => 0,
        ]);

        $this->assertDatabaseHas('product_modifier_group', [
            'modifier_group_id' => $groupId,
            'product_id' => $product1->id,
        ]);
        $this->assertDatabaseHas('product_modifier_group', [
            'modifier_group_id' => $groupId,
            'product_id' => $product2->id,
        ]);
    }

    public function test_create_validation_fails_on_missing_required_fields(): void
    {
        [$shop, $user] = $this->createShopUser();

        $this->actingAs($user)
            ->postJson(route('admin.addons.store'), [
                'name' => '',
                'selection_type' => 'INVALID_TYPE',
                'items' => [],
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'selection_type', 'items']);
    }

    public function test_user_can_update_modifier_group(): void
    {
        [$shop, $user] = $this->createShopUser();
        $product = Product::factory()->create(['shop_id' => $shop->id]);

        $group = ModifierGroup::create([
            'shop_id' => $shop->id,
            'name' => 'Topping Lama',
            'selection_type' => 'SINGLE',
            'is_required' => false,
            'is_active' => true,
        ]);
        $mod = Modifier::create([
            'modifier_group_id' => $group->id,
            'name' => 'Boba',
            'price_adjustment' => 3000,
            'is_active' => true,
        ]);

        $updatePayload = [
            'name' => 'Aneka Topping',
            'selection_type' => 'MULTIPLE',
            'is_required' => false,
            'is_active' => true,
            'product_ids' => [$product->id],
            'items' => [
                ['id' => $mod->id, 'name' => 'Boba Brown Sugar', 'price_adjustment' => 4000],
                ['name' => 'Egg Pudding', 'price_adjustment' => 5000],
            ],
        ];

        $this->actingAs($user)
            ->putJson(route('admin.addons.update', $group->id), $updatePayload)
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('modifier_groups', [
            'id' => $group->id,
            'name' => 'Aneka Topping',
            'selection_type' => 'MULTIPLE',
        ]);

        $this->assertDatabaseHas('modifiers', [
            'id' => $mod->id,
            'name' => 'Boba Brown Sugar',
            'price_adjustment' => 4000,
        ]);

        $this->assertDatabaseHas('modifiers', [
            'modifier_group_id' => $group->id,
            'name' => 'Egg Pudding',
            'price_adjustment' => 5000,
        ]);

        $this->assertDatabaseHas('product_modifier_group', [
            'modifier_group_id' => $group->id,
            'product_id' => $product->id,
        ]);
    }

    public function test_user_can_toggle_addon_group_status(): void
    {
        [$shop, $user] = $this->createShopUser();

        $group = ModifierGroup::create([
            'shop_id' => $shop->id,
            'name' => 'Gula',
            'selection_type' => 'SINGLE',
            'is_required' => true,
            'is_active' => true,
        ]);

        $this->actingAs($user)
            ->patchJson(route('admin.addons.toggle', $group->id))
            ->assertOk()
            ->assertJsonPath('is_active', false);

        $this->assertDatabaseHas('modifier_groups', [
            'id' => $group->id,
            'is_active' => false,
        ]);

        // Toggle back
        $this->actingAs($user)
            ->patchJson(route('admin.addons.toggle', $group->id))
            ->assertOk()
            ->assertJsonPath('is_active', true);

        $this->assertDatabaseHas('modifier_groups', [
            'id' => $group->id,
            'is_active' => true,
        ]);
    }

    public function test_user_can_delete_modifier_group(): void
    {
        [$shop, $user] = $this->createShopUser();
        $product = Product::factory()->create(['shop_id' => $shop->id]);

        $group = ModifierGroup::create([
            'shop_id' => $shop->id,
            'name' => 'Topping Hapus',
            'selection_type' => 'MULTIPLE',
            'is_active' => true,
        ]);
        $mod = Modifier::create([
            'modifier_group_id' => $group->id,
            'name' => 'Jelly',
            'price_adjustment' => 2000,
        ]);
        $group->products()->attach($product->id);

        $this->actingAs($user)
            ->deleteJson(route('admin.addons.destroy', $group->id))
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->assertDatabaseMissing('modifier_groups', ['id' => $group->id]);
        $this->assertDatabaseMissing('modifiers', ['id' => $mod->id]);
        $this->assertDatabaseMissing('product_modifier_group', [
            'modifier_group_id' => $group->id,
            'product_id' => $product->id,
        ]);
    }

    public function test_tenant_isolation_user_cannot_access_or_modify_other_shop_groups(): void
    {
        [$shopA, $userA] = $this->createShopUser();
        [$shopB, $userB] = $this->createShopUser();

        $groupB = ModifierGroup::create([
            'shop_id' => $shopB->id,
            'name' => 'Group Rahasia Toko B',
            'selection_type' => 'SINGLE',
            'is_active' => true,
        ]);

        // User A cannot update Group B
        $this->actingAs($userA)
            ->putJson(route('admin.addons.update', $groupB->id), [
                'name' => 'Hacked Group',
                'selection_type' => 'SINGLE',
                'items' => [['name' => 'Bad Item', 'price_adjustment' => 0]],
            ])
            ->assertStatus(404);

        // User A cannot toggle Group B
        $this->actingAs($userA)
            ->patchJson(route('admin.addons.toggle', $groupB->id))
            ->assertStatus(404);

        // User A cannot delete Group B
        $this->actingAs($userA)
            ->deleteJson(route('admin.addons.destroy', $groupB->id))
            ->assertStatus(404);

        // Group B is not returned in User A's API list
        $response = $this->actingAs($userA)
            ->getJson(route('admin.addons.api.index'))
            ->assertOk();

        $this->assertEmpty($response->json('groups'));
    }

    public function test_calculate_cart_calculates_single_and_multiple_choice_modifiers(): void
    {
        [$shop, $user] = $this->createShopUser();

        $product = Product::factory()->create([
            'shop_id' => $shop->id,
            'name' => 'Kopi Susu Gula Aren',
            'price' => 20000,
            'cogs' => 8000,
        ]);

        // Single Choice Group: Ice Level
        $iceGroup = ModifierGroup::create([
            'shop_id' => $shop->id,
            'name' => 'Level Es',
            'selection_type' => 'SINGLE',
            'is_required' => true,
            'is_active' => true,
        ]);
        $lessIce = Modifier::create([
            'modifier_group_id' => $iceGroup->id,
            'name' => 'Less Ice',
            'price_adjustment' => 0,
            'cogs_adjustment' => 0,
            'is_active' => true,
        ]);

        // Multiple Choice Group: Extra Topping
        $toppingGroup = ModifierGroup::create([
            'shop_id' => $shop->id,
            'name' => 'Topping Tambahan',
            'selection_type' => 'MULTIPLE',
            'is_required' => false,
            'is_active' => true,
        ]);
        $boba = Modifier::create([
            'modifier_group_id' => $toppingGroup->id,
            'name' => 'Boba',
            'price_adjustment' => 4000,
            'cogs_adjustment' => 1500,
            'is_active' => true,
        ]);
        $shot = Modifier::create([
            'modifier_group_id' => $toppingGroup->id,
            'name' => 'Extra Espresso Shot',
            'price_adjustment' => 5000,
            'cogs_adjustment' => 2000,
            'is_active' => true,
        ]);

        // Attach both groups to product
        $product->modifierGroups()->attach([$iceGroup->id, $toppingGroup->id]);

        $calculateAction = app(CalculateCart::class);

        // 1 item with Less Ice (0) + Boba (4000) + Extra Shot (5000) -> Unit Price: 20000 + 9000 = 29000
        $items = [
            [
                'id' => $product->id,
                'qty' => 2,
                'modifiers' => [$lessIce->id, $boba->id, $shot->id],
            ],
        ];

        $result = $calculateAction->execute($shop, $items);

        $this->assertEquals(58000, $result['subtotal']); // 29.000 * 2 = 58.000
        $this->assertEquals(23000, $result['total_cogs']); // (8000 + 0 + 1500 + 2000) * 2 = 11500 * 2 = 23000
        $this->assertCount(1, $result['items']);
        $this->assertEquals(29000, $result['items'][0]['unit_price']);
        $this->assertCount(3, $result['items'][0]['modifiers']);
    }

    public function test_pos_order_checkout_records_modifiers_accurately(): void
    {
        [$shop, $cashier] = $this->createShopUser('cashier');

        // Open Register
        $register = CashRegister::create([
            'shop_id' => $shop->id,
            'name' => 'Kasir 1',
            'is_active' => true,
        ]);
        CashRegisterSession::create([
            'cash_register_id' => $register->id,
            'user_id' => $cashier->id,
            'opened_at' => now(),
            'opening_cash' => 50000,
            'status' => 'OPEN',
        ]);

        $product = Product::factory()->create([
            'shop_id' => $shop->id,
            'name' => 'Ice Taro Latte',
            'price' => 22000,
        ]);

        $group = ModifierGroup::create([
            'shop_id' => $shop->id,
            'name' => 'Pilihan Es',
            'selection_type' => 'SINGLE',
            'is_active' => true,
        ]);
        $lessIce = Modifier::create([
            'modifier_group_id' => $group->id,
            'name' => 'Less Ice',
            'price_adjustment' => 0,
            'is_active' => true,
        ]);
        $product->modifierGroups()->attach($group->id);

        $items = [
            [
                'id' => $product->id,
                'qty' => 1,
                'modifiers' => [$lessIce->id],
                'notes' => 'Tolong sedotan kertas',
            ],
        ];

        $payload = [
            'items' => $items,
            'request_id' => (string) Str::uuid(),
            'payment_method' => 'CASH',
            'amount_paid' => 50000,
        ];

        $response = $this->actingAs($cashier)
            ->postJson(route('admin.pos.orders.submit'), $payload)
            ->assertOk();

        $orderId = $response->json('order_id');
        $order = Order::with('items')->findOrFail($orderId);

        $this->assertEquals(22000, $order->grand_total);
        $this->assertCount(1, $order->items);

        $orderItem = $order->items->first();
        $this->assertEquals(22000, $orderItem->unit_price);
        $this->assertEquals(22000, $orderItem->subtotal);
        $this->assertNotNull($orderItem->modifiers);

        $modifiers = is_string($orderItem->modifiers)
            ? json_decode($orderItem->modifiers, true)
            : $orderItem->modifiers;

        $this->assertCount(1, $modifiers);
        $this->assertEquals('Less Ice', $modifiers[0]['name']);
        $this->assertEquals(0, $modifiers[0]['price_adjustment']);
    }
}
