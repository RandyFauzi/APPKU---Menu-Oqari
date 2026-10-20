<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Shop;
use Database\Factories\OrderFactory;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\RefreshDatabaseState;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class LegacyUpgradeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        RefreshDatabaseState::$migrated = false;
        parent::setUp();
    }

    public function test_upgrade_reconciles_existing_variant_and_preserves_legacy_price_and_shift_columns(): void
    {
        $shop = Shop::factory()->create();
        $product = Product::factory()->create(['shop_id' => $shop->id]);
        $variant = $product->variants()->create(['name' => 'Large', 'price_adjustment' => 5000]);
        $order = OrderFactory::new()->create(['shop_id' => $shop->id]);
        $item = $order->items()->create(['product_id' => $product->id, 'product_name' => 'Latte', 'variant_id' => $variant->id, 'quantity' => 2, 'unit_price' => 28500.25, 'subtotal' => 57000.50, 'cogs_total' => 10000]);
        Schema::table('order_items', fn (Blueprint $table) => $table->renameColumn('unit_price', 'price'));
        Schema::table('order_items', fn (Blueprint $table) => $table->dropColumn('cogs_total'));
        Schema::table('crew_shifts', fn (Blueprint $table) => $table->dropConstrainedForeignId('shift_template_id'));
        Schema::table('crew_shifts', fn (Blueprint $table) => $table->dropColumn('position'));
        Schema::dropIfExists('attendances');
        Schema::table('users', fn (Blueprint $table) => $table->dropColumn(['permissions', 'is_active', 'account_version']));
        Schema::table('products', fn (Blueprint $table) => $table->dropColumn(['hpp_overhead_cost', 'hpp_target_margin', 'hpp_settings']));
        Schema::table('product_recipes', fn (Blueprint $table) => $table->dropColumn(['batch_amount', 'notes']));
        Schema::table('stock_mutations', fn (Blueprint $table) => $table->dropColumn('unit'));
        DB::table('migrations')->where('migration', '>=', '2026_10_16_030932_add_variant_id_to_order_items_table')->delete();
        $this->artisan('app:upgrade-legacy-database', ['--force' => true])->assertSuccessful();
        $this->assertDatabaseHas('order_items', ['id' => $item->id, 'variant_id' => $variant->id, 'price' => 28500.25, 'unit_price' => 28500.25, 'subtotal' => 57000.50]);
        $this->assertNull(DB::table('order_items')->where('id', $item->id)->value('cogs_total'));
        $this->assertTrue(Schema::hasColumns('crew_shifts', ['position', 'shift_template_id']));
        $this->artisan('app:upgrade-legacy-database', ['--force' => true])->assertSuccessful();
    }

    public function test_upgrade_requires_explicit_force(): void
    {
        $this->artisan('app:upgrade-legacy-database')->assertFailed();
    }
}
