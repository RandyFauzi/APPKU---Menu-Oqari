<?php

namespace Tests\Feature;

use App\Models\Shop;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class StaffSchemaCompatibilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_list_create_and_edit_staff_before_archive_migration(): void
    {
        $shop = Shop::factory()->create(['onboarding_status' => 'completed']);
        $owner = User::factory()->create(['shop_id' => $shop->id, 'role' => 'owner']);
        $staff = User::factory()->create(['shop_id' => $shop->id, 'role' => 'crew']);
        Schema::table('users', fn (Blueprint $table) => $table->dropColumn('archived_at'));

        $this->actingAs($owner)->get(route('admin.users.index'))
            ->assertOk()->assertSee($staff->email)->assertSee('Edit Staf')->assertDontSee('Hapus Staf');
        $this->get(route('admin.users.create'))->assertOk();
        $this->get(route('admin.users.edit', $staff))->assertOk()->assertSee($staff->email);
        $this->putJson(route('admin.users.update', $staff), [
            'name' => 'Nama Baru', 'email' => $staff->email, 'role' => 'cashier', 'permission_mode' => 'role',
        ])->assertOk();
        $this->assertDatabaseHas('users', ['id' => $staff->id, 'name' => 'Nama Baru', 'role' => 'cashier']);
        $this->postJson(route('admin.users.store'), [
            'name' => 'Staf Baru', 'email' => 'new-staff@example.test', 'role' => 'crew',
            'password' => 'PasswordBaru123', 'password_confirmation' => 'PasswordBaru123',
        ])->assertOk();
        $this->assertDatabaseHas('users', ['shop_id' => $shop->id, 'email' => 'new-staff@example.test']);
    }

    public function test_only_archive_action_waits_for_its_migration_without_changing_data(): void
    {
        $shop = Shop::factory()->create(['onboarding_status' => 'completed']);
        $owner = User::factory()->create(['shop_id' => $shop->id, 'role' => 'owner']);
        $staff = User::factory()->create(['shop_id' => $shop->id, 'role' => 'crew']);
        Schema::table('users', fn (Blueprint $table) => $table->dropColumn('archived_at'));

        $this->actingAs($owner)->deleteJson(route('admin.users.destroy', $staff))->assertServiceUnavailable();
        $this->assertDatabaseHas('users', ['id' => $staff->id, 'is_active' => true]);
        $this->assertDatabaseCount('activity_logs', 0);
    }
}
