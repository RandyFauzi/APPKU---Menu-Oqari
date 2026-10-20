<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\CashRegister;
use App\Models\CashRegisterSession;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class CafeUserManagementTest extends TestCase
{
    use RefreshDatabase;

    private function member(Shop $shop, string $role = 'crew', ?array $permissions = null): User
    {
        $user = User::factory()->create(['shop_id' => $shop->id, 'role' => $role]);
        $user->permissions = $permissions;
        $user->save();

        return $user;
    }

    private function payload(User $user, array $overrides = []): array
    {
        return array_merge(['name' => $user->name, 'email' => $user->email, 'role' => $user->role], $overrides);
    }

    public function test_owner_only_sees_users_from_their_cafe_and_forms_render(): void
    {
        $shop = Shop::factory()->create(['onboarding_status' => 'completed']);
        $owner = $this->member($shop, 'owner');
        $member = $this->member($shop);
        $foreign = $this->member(Shop::factory()->create());
        $this->actingAs($owner)->get(route('admin.users.index'))->assertOk()->assertSee($member->email)->assertDontSee($foreign->email);
        $this->get(route('admin.users.create'))->assertOk()->assertSee('Hak Akses');
        $this->get(route('admin.users.edit', $member))->assertOk()->assertSee($member->email);
    }

    public function test_creation_hashes_password_scopes_shop_and_records_safe_audit(): void
    {
        $shop = Shop::factory()->create();
        $owner = $this->member($shop, 'owner');
        $this->actingAs($owner)->postJson(route('admin.users.store'), [
            'name' => 'Kasir Baru', 'email' => 'new@example.test', 'role' => 'cashier',
            'password' => 'SecurePassword123', 'password_confirmation' => 'SecurePassword123', 'permission_mode' => 'role',
        ])->assertOk()->assertJsonPath('success', true)->assertJsonMissingPath('user.password');
        $created = User::where('email', 'new@example.test')->firstOrFail();
        $this->assertSame($shop->id, $created->shop_id);
        $this->assertTrue(Hash::check('SecurePassword123', $created->password));
        $this->assertNull($created->permissions);
        $this->assertTrue($created->can('manage-payments'));
        $log = ActivityLog::where('action', 'user.created')->firstOrFail();
        $this->assertSame($shop->id, $log->shop_id);
        $this->assertArrayNotHasKey('password', $log->new_values);
        $this->assertStringNotContainsString('SecurePassword123', $log->toJson());
    }

    public function test_custom_permissions_replace_role_and_empty_list_denies_all(): void
    {
        $shop = Shop::factory()->create();
        $owner = $this->member($shop, 'owner');
        $member = $this->member($shop, 'cashier');
        $this->actingAs($owner)->putJson(route('admin.users.update', $member), $this->payload($member, ['permission_mode' => 'custom', 'permissions' => ['record-attendance']]))->assertOk();
        $this->assertSame(['record-attendance'], $member->fresh()->effectivePermissions());
        $this->putJson(route('admin.users.update', $member), $this->payload($member, ['permission_mode' => 'custom']))->assertOk();
        $this->assertSame([], $member->fresh()->effectivePermissions());
        $this->putJson(route('admin.users.update', $member), $this->payload($member, ['permission_mode' => 'role']))->assertOk();
        $this->assertTrue($member->fresh()->can('access-pos'));
    }

    public function test_cross_cafe_ids_are_rejected_on_all_management_routes(): void
    {
        $owner = $this->member(Shop::factory()->create(), 'owner');
        $foreign = $this->member(Shop::factory()->create());
        $this->actingAs($owner)->get(route('admin.users.edit', $foreign))->assertNotFound();
        $this->putJson(route('admin.users.update', $foreign), $this->payload($foreign))->assertNotFound();
        $this->patchJson(route('admin.users.deactivate', $foreign))->assertNotFound();
        $this->putJson('/admin/api/crew/'.$foreign->id, $this->payload($foreign))->assertNotFound();
        $this->deleteJson('/admin/api/crew/'.$foreign->id)->assertNotFound();
        $this->assertTrue($foreign->fresh()->is_active);
    }

    public function test_owner_and_self_accounts_cannot_be_modified(): void
    {
        $shop = Shop::factory()->create();
        $owner = $this->member($shop, 'owner');
        $otherOwner = $this->member($shop, 'owner');
        $this->actingAs($owner)->patchJson(route('admin.users.deactivate', $owner))->assertForbidden();
        $this->putJson(route('admin.users.update', $otherOwner), $this->payload($otherOwner, ['role' => 'crew']))->assertForbidden();
        $this->deleteJson('/admin/api/crew/'.$otherOwner->id)->assertForbidden();
    }

    public function test_role_escalation_and_client_supplied_shop_are_rejected(): void
    {
        $shop = Shop::factory()->create();
        $owner = $this->member($shop, 'owner');
        $member = $this->member($shop);
        $this->actingAs($owner);
        foreach (['owner', 'superadmin'] as $role) {
            $this->putJson(route('admin.users.update', $member), $this->payload($member, ['role' => $role]))->assertUnprocessable()->assertJsonValidationErrors('role');
        }
        $this->putJson(route('admin.users.update', $member), $this->payload($member, ['shop_id' => Shop::factory()->create()->id, 'account_version' => 0]))->assertUnprocessable()->assertJsonValidationErrors(['shop_id', 'account_version']);
        $this->putJson(route('admin.users.update', $member), $this->payload($member, ['permissions' => ['unknown-ability']]))->assertUnprocessable()->assertJsonValidationErrors('permissions.0');
    }

    public function test_delegated_manager_cannot_escalate_delegate_or_edit_privileged_accounts(): void
    {
        $shop = Shop::factory()->create();
        $manager = $this->member($shop, 'manager', ['manage-users', 'record-attendance']);
        $member = $this->member($shop);
        $peer = $this->member($shop, 'manager');
        $this->actingAs($manager);
        $this->putJson(route('admin.users.update', $member), $this->payload($member, ['permissions' => ['view-reports']]))->assertUnprocessable();
        $this->putJson(route('admin.users.update', $member), $this->payload($member, ['permissions' => ['manage-users']]))->assertUnprocessable();
        $this->putJson(route('admin.users.update', $member), $this->payload($member, ['role' => 'admin']))->assertUnprocessable();
        $this->patchJson(route('admin.users.deactivate', $peer))->assertForbidden();
        $this->putJson(route('admin.users.update', $member), $this->payload($member, ['permissions' => ['record-attendance']]))->assertOk();
    }

    public function test_owner_can_delegate_management_only_to_admin_or_manager(): void
    {
        $shop = Shop::factory()->create();
        $owner = $this->member($shop, 'owner');
        $member = $this->member($shop);
        $this->actingAs($owner)->putJson(route('admin.users.update', $member), $this->payload($member, ['permissions' => ['manage-users']]))->assertUnprocessable();
        $this->putJson(route('admin.users.update', $member), $this->payload($member, ['role' => 'manager', 'permissions' => ['manage-users', 'record-attendance']]))->assertOk();
        $this->assertTrue($member->fresh()->can('manage-users'));
    }

    public function test_access_dependencies_are_validated(): void
    {
        $shop = Shop::factory()->create();
        $owner = $this->member($shop, 'owner');
        $member = $this->member($shop);
        $this->actingAs($owner)->putJson(route('admin.users.update', $member), $this->payload($member, ['permissions' => ['manage-payments']]))->assertUnprocessable()->assertJsonValidationErrors('permissions');
        $this->putJson(route('admin.users.update', $member), $this->payload($member, ['permissions' => ['access-pos', 'view-orders', 'manage-register', 'manage-payments']]))->assertOk();
    }

    public function test_scheduling_permission_does_not_allow_account_management_or_legacy_crud(): void
    {
        $shop = Shop::factory()->create();
        $manager = $this->member($shop, 'manager');
        $member = $this->member($shop);
        $this->actingAs($manager)->get(route('admin.users.index'))->assertForbidden();
        $this->get(route('admin.users.create'))->assertForbidden();
        $this->postJson('/admin/api/crew', [])->assertForbidden();
        $this->putJson('/admin/api/crew/'.$member->id, $this->payload($member))->assertForbidden();
        $this->deleteJson('/admin/api/crew/'.$member->id)->assertForbidden();
    }

    public function test_deactivation_preserves_user_and_blocks_login(): void
    {
        $shop = Shop::factory()->create();
        $owner = $this->member($shop, 'owner');
        $member = $this->member($shop);
        $this->actingAs($owner)->patchJson(route('admin.users.deactivate', $member))->assertOk();
        $this->assertDatabaseHas('users', ['id' => $member->id, 'is_active' => false]);
        auth()->logout();
        $this->post('/admin/login', ['email' => $member->email, 'password' => 'password'])->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_permission_change_invalidates_old_session_and_fresh_login_restores_allowed_access(): void
    {
        $shop = Shop::factory()->create();
        $owner = $this->member($shop, 'owner');
        $member = $this->member($shop, 'cashier');
        $this->actingAs($owner)->putJson(route('admin.users.update', $member), $this->payload($member, ['permissions' => ['record-attendance']]))->assertOk();
        $member->refresh();
        $this->assertSame(1, $member->account_version);
        $this->actingAs($member)->withSession(['account_version' => 0])->get(route('admin.attendance.index'))->assertForbidden();
        $this->assertGuest();
        $this->post('/admin/login', ['email' => $member->email, 'password' => 'password'])->assertRedirect('/admin/attendance')->assertSessionHas('account_version', 1);
        $this->get(route('admin.attendance.index'))->assertOk();
        $this->get(route('admin.pos.index'))->assertForbidden();
    }

    public function test_inventory_access_is_independent_and_other_modules_are_denied(): void
    {
        $shop = Shop::factory()->create(['onboarding_status' => 'completed']);
        $member = $this->member($shop, 'crew', ['manage-inventory']);
        $this->actingAs($member)->get(route('admin.dashboard'))->assertOk();
        $this->getJson('/admin/api/inventory')->assertOk();
        $this->getJson('/admin/api/dashboard/products')->assertForbidden();
        $this->getJson('/admin/api/dashboard/orders')->assertForbidden();
        $this->getJson('/admin/api/dashboard/analytics')->assertForbidden();
        $this->getJson('/admin/api/dashboard/summary')->assertOk()->assertJsonPath('users', [])->assertJsonPath('categories', [])->assertJsonPath('tables', []);
        $this->get(route('admin.users.index'))->assertForbidden();
    }

    public function test_pos_access_without_payment_permission_cannot_submit_payment(): void
    {
        $member = $this->member(Shop::factory()->create(), 'cashier', ['access-pos', 'view-orders']);
        $this->actingAs($member)->postJson('/admin/pos/orders', [])->assertForbidden();
    }

    public function test_legacy_users_can_login_before_access_columns_have_been_migrated(): void
    {
        $shop = Shop::factory()->create(['onboarding_status' => 'completed']);
        $member = $this->member($shop, 'owner');
        Schema::table('users', fn (Blueprint $table) => $table->dropColumn(['is_active', 'permissions', 'account_version']));
        $this->post('/admin/login', ['email' => $member->email, 'password' => 'password'])->assertSessionHasNoErrors()->assertRedirect('/admin/dashboard');
        $this->assertAuthenticatedAs($member);
        $this->get(route('admin.dashboard'))->assertOk();
        $this->getJson('/admin/api/dashboard/products')->assertOk();
    }

    public function test_open_cash_shift_blocks_access_changes_without_losing_history(): void
    {
        $shop = Shop::factory()->create();
        $owner = $this->member($shop, 'owner');
        $member = $this->member($shop, 'cashier');
        $register = CashRegister::create(['shop_id' => $shop->id, 'name' => 'Kasir Utama', 'is_active' => true]);
        $session = CashRegisterSession::create(['shop_id' => $shop->id, 'cash_register_id' => $register->id, 'user_id' => $member->id, 'status' => 'OPEN', 'opening_cash' => 0, 'opened_at' => now()]);
        $this->actingAs($owner)->patchJson(route('admin.users.deactivate', $member))->assertUnprocessable()->assertJsonValidationErrors('is_active');
        $this->assertTrue($member->fresh()->is_active);
        $this->assertSame(0, $member->fresh()->account_version);
        $this->assertSame('OPEN', $session->fresh()->status);
        $this->assertDatabaseCount('activity_logs', 0);
        $this->putJson(route('admin.users.update', $member), $this->payload($member, ['name' => 'Nama Diperbarui']))->assertOk();
    }

    public function test_explicit_inactive_status_is_never_treated_as_legacy_active(): void
    {
        $user = $this->member(Shop::factory()->create(), 'owner');
        $user->is_active = false;
        $user->save();
        $this->assertFalse($user->fresh()->isAccountActive());
        $this->post('/admin/login', ['email' => $user->email, 'password' => 'password'])->assertSessionHasErrors('email');
        $this->assertGuest();
        $this->actingAs($user)->get(route('admin.dashboard'))->assertForbidden();
        $this->assertGuest();
    }

    public function test_filter_search_cannot_escape_tenant_scope(): void
    {
        $owner = $this->member(Shop::factory()->create(), 'owner');
        $foreign = $this->member(Shop::factory()->create());
        $this->actingAs($owner)->get(route('admin.users.index', ['q' => $foreign->email]))->assertOk()->assertDontSee($foreign->name)->assertSee('Tidak ada user');
    }
}
