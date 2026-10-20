<?php

namespace Tests\Feature;

use App\Models\Payment;
use App\Models\Shop;
use App\Models\User;
use App\Services\SchemaCompatibility;
use Database\Factories\OrderFactory;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\RefreshDatabaseState;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DatabaseCompatibilityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        RefreshDatabaseState::$migrated = false;
        parent::setUp();
    }

    public function test_schema_check_passes_after_all_migrations(): void
    {
        $this->assertSame([], (new SchemaCompatibility)->missing());
        $this->artisan('app:check-database-schema')->expectsOutputToContain('Database schema compatible.')->assertSuccessful();
    }

    public function test_missing_feature_columns_are_detected_before_writing_and_other_pages_still_work(): void
    {
        $shop = Shop::factory()->create(['onboarding_status' => 'completed']);
        $owner = User::factory()->create(['shop_id' => $shop->id, 'role' => 'owner']);
        Schema::table('products', fn (Blueprint $table) => $table->dropColumn('hpp_settings'));
        $this->artisan('app:check-database-schema')->expectsOutputToContain('hpp_settings')->assertFailed();
        $this->actingAs($owner)->getJson(route('admin.hpp.index'))->assertStatus(503)->assertJsonPath('message', 'Database perlu diperbarui sebelum fitur ini digunakan. Administrator perlu menjalankan php artisan migrate --force. Data belum diubah.');
        $this->putJson(route('admin.hpp.update', 1), [])->assertStatus(503);
        $this->assertDatabaseCount('products', 0);
        $this->get(route('admin.dashboard'))->assertOk();
    }

    public function test_inventory_request_does_not_create_tables_in_an_old_database(): void
    {
        $shop = Shop::factory()->create();
        $owner = User::factory()->create(['shop_id' => $shop->id, 'role' => 'owner']);
        Schema::dropIfExists('stock_mutations');
        Schema::dropIfExists('product_recipes');
        Schema::dropIfExists('raw_materials');
        $this->actingAs($owner)->getJson('/admin/api/inventory')->assertStatus(503);
        $this->assertFalse(Schema::hasTable('raw_materials'));
        $this->assertFalse(Schema::hasTable('stock_mutations'));
    }

    public function test_checkout_rejects_unmigrated_schema_without_creating_order_or_payment(): void
    {
        $shop = Shop::factory()->create();
        $cashier = User::factory()->create(['shop_id' => $shop->id, 'role' => 'cashier']);
        Schema::table('order_items', fn (Blueprint $table) => $table->dropConstrainedForeignId('variant_id'));
        $this->actingAs($cashier)->postJson(route('admin.pos.orders.submit'), [])->assertStatus(503);
        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseCount('payments', 0);
    }

    public function test_legacy_users_can_register_and_read_summary_without_access_columns(): void
    {
        Schema::table('users', fn (Blueprint $table) => $table->dropColumn(['permissions', 'is_active', 'account_version']));
        $this->post('/admin/register', ['name' => 'Legacy Owner', 'email' => 'legacy@example.test', 'password' => 'Password1234', 'password_confirmation' => 'Password1234'])->assertSessionHasNoErrors()->assertRedirect('/admin/dashboard');
        $this->get('/admin/dashboard');
        $this->getJson('/admin/api/dashboard/summary')->assertOk();
        $this->getJson(route('admin.users.index'))->assertStatus(503);
        $this->assertAuthenticated();
    }

    public function test_forward_repair_preserves_existing_values_and_is_repeatable(): void
    {
        $shop = Shop::factory()->create();
        $user = User::factory()->create(['shop_id' => $shop->id, 'role' => 'owner']);
        $user->is_active = false;
        $user->save();
        Schema::table('products', fn (Blueprint $table) => $table->dropColumn('hpp_settings'));
        Schema::table('product_variants', fn (Blueprint $table) => $table->dropColumn('cogs_adjustment'));
        Schema::table('modifiers', fn (Blueprint $table) => $table->dropColumn('cogs_adjustment'));
        Schema::table('shops', fn (Blueprint $table) => $table->renameColumn('logo_path', 'logo_url'));
        DB::table('shops')->where('id', $shop->id)->update(['logo_url' => 'old/logo.png']);
        $migration = require database_path('migrations/2026_10_20_065451_repair_legacy_database_columns.php');
        $migration->up();
        $migration->up();
        $this->assertSame([], (new SchemaCompatibility)->missing());
        $this->assertDatabaseHas('shops', ['id' => $shop->id, 'logo_path' => 'old/logo.png', 'logo_url' => 'old/logo.png']);
        $this->assertDatabaseHas('users', ['id' => $user->id, 'email' => $user->email, 'is_active' => false]);
    }

    public function test_repair_backfills_legacy_payment_scope_from_order_without_changing_amounts(): void
    {
        $firstShop = Shop::factory()->create();
        $secondShop = Shop::factory()->create();
        $firstOrder = OrderFactory::new()->create(['shop_id' => $firstShop->id]);
        $secondOrder = OrderFactory::new()->create(['shop_id' => $secondShop->id]);
        $first = Payment::create(['shop_id' => $firstShop->id, 'order_id' => $firstOrder->id, 'payment_method' => 'CASH', 'amount' => 15000.25]);
        $second = Payment::create(['shop_id' => $secondShop->id, 'order_id' => $secondOrder->id, 'payment_method' => 'QRIS', 'amount' => 28000.75, 'cancelled_at' => now()]);
        Schema::table('payments', fn (Blueprint $table) => $table->dropForeign(['shop_id']));
        Schema::table('payments', fn (Blueprint $table) => $table->dropIndex(['shop_id', 'created_at']));
        Schema::table('payments', fn (Blueprint $table) => $table->dropColumn('shop_id'));
        $migration = require database_path('migrations/2026_10_20_065451_repair_legacy_database_columns.php');
        $migration->up();
        $migration->up();
        $this->assertDatabaseHas('payments', ['id' => $first->id, 'shop_id' => $firstShop->id, 'amount' => 15000.25]);
        $this->assertDatabaseHas('payments', ['id' => $second->id, 'shop_id' => $secondShop->id, 'amount' => 28000.75]);
        $this->assertNotNull($second->fresh()->cancelled_at);
        $this->assertDatabaseCount('payments', 2);
    }

    public function test_all_model_write_attributes_exist_after_upgrade(): void
    {
        foreach (glob(app_path('Models/*.php')) as $file) {
            $class = 'App\\Models\\'.basename($file, '.php');
            $model = new $class;
            $this->assertSame([], array_values(array_diff($model->getFillable(), Schema::getColumnListing($model->getTable()))), $class);
        }
    }
}
