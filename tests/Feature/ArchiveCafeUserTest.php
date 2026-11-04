<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\Attendance;
use App\Models\CashRegister;
use App\Models\CashRegisterSession;
use App\Models\Shop;
use App\Models\User;
use App\Policies\CafeUserPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class ArchiveCafeUserTest extends TestCase
{
    use RefreshDatabase;

    private function member(Shop $shop, string $role = 'crew'): User
    {
        return User::factory()->create(['shop_id' => $shop->id, 'role' => $role]);
    }

    public function test_owner_can_edit_identity_password_role_and_reactivate_staff(): void
    {
        $shop = Shop::factory()->create(['onboarding_status' => 'completed']);
        $owner = $this->member($shop, 'owner');
        $staff = $this->member($shop);
        $staff->is_active = false;
        $staff->save();

        $this->actingAs($owner)->putJson(route('admin.users.update', $staff), [
            'name' => 'Kasir Diperbarui', 'email' => 'updated@example.test', 'role' => 'cashier', 'is_active' => true,
            'permission_mode' => 'role', 'password' => 'NewPassword123', 'password_confirmation' => 'NewPassword123',
        ])->assertOk();
        $staff->refresh();
        $this->assertSame('Kasir Diperbarui', $staff->name);
        $this->assertSame('updated@example.test', $staff->email);
        $this->assertTrue($staff->can('manage-payments'));
        $this->assertFalse($staff->can('manage-users'));
        $this->assertTrue($staff->isAccountActive());
        $this->assertTrue(Hash::check('NewPassword123', $staff->password));
    }

    public function test_archiving_preserves_history_removes_staff_from_list_and_revokes_access(): void
    {
        $shop = Shop::factory()->create(['onboarding_status' => 'completed']);
        $owner = $this->member($shop, 'owner');
        $staff = $this->member($shop);
        $attendance = Attendance::factory()->create(['shop_id' => $shop->id, 'user_id' => $staff->id, 'checked_out_at' => now()]);

        $this->actingAs($owner)->deleteJson(route('admin.users.destroy', $staff))->assertOk()->assertJsonPath('success', true);
        $staff->refresh();
        $this->assertNotNull($staff->archived_at);
        $this->assertFalse($staff->isAccountActive());
        $this->assertSame(1, $staff->account_version);
        $this->assertSame($staff->id, $attendance->fresh()->user_id);
        $this->get(route('admin.users.index'))->assertDontSee($staff->email);
        $this->get(route('admin.users.edit', $staff))->assertNotFound();
        $this->patchJson(route('admin.users.deactivate', $staff))->assertNotFound();
        $this->assertDatabaseHas('activity_logs', ['action' => 'user.archived', 'entity_id' => $staff->id, 'shop_id' => $shop->id]);
        $this->assertArrayNotHasKey('password', ActivityLog::where('action', 'user.archived')->firstOrFail()->new_values);
        auth()->logout();
        $this->post('/admin/login', ['email' => $staff->email, 'password' => 'password'])->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    #[TestWith(['admin'])]
    #[TestWith(['manager'])]
    #[TestWith(['cashier'])]
    #[TestWith(['barista'])]
    #[TestWith(['kitchen'])]
    #[TestWith(['crew'])]
    public function test_non_owners_cannot_archive_even_with_delegated_management(string $role): void
    {
        $shop = Shop::factory()->create();
        $actor = $this->member($shop, $role);
        $actor->permissions = ['manage-users'];
        $actor->save();
        $staff = $this->member($shop);

        $this->assertFalse((new CafeUserPolicy)->delete($actor, $staff));
        $this->actingAs($actor)->deleteJson(route('admin.users.destroy', $staff))->assertForbidden();
        $this->assertNull($staff->fresh()->archived_at);
        $this->assertDatabaseCount('activity_logs', 0);
    }

    public function test_owner_cannot_archive_self_other_owner_or_foreign_staff(): void
    {
        $shop = Shop::factory()->create();
        $owner = $this->member($shop, 'owner');
        $peer = $this->member($shop, 'owner');
        $foreign = $this->member(Shop::factory()->create());

        $this->actingAs($owner)->deleteJson(route('admin.users.destroy', $owner))->assertForbidden();
        $this->deleteJson(route('admin.users.destroy', $peer))->assertForbidden();
        $this->deleteJson(route('admin.users.destroy', $foreign))->assertNotFound();
        $this->assertDatabaseCount('activity_logs', 0);
    }

    #[TestWith(['attendance'])]
    #[TestWith(['register'])]
    public function test_open_work_sessions_prevent_archiving_without_changing_access(string $sessionType): void
    {
        $shop = Shop::factory()->create();
        $owner = $this->member($shop, 'owner');
        $staff = $this->member($shop, 'cashier');
        if ($sessionType === 'attendance') {
            Attendance::factory()->create(['shop_id' => $shop->id, 'user_id' => $staff->id]);
        } else {
            $register = CashRegister::create(['shop_id' => $shop->id, 'name' => 'Kasir', 'is_active' => true]);
            CashRegisterSession::create(['shop_id' => $shop->id, 'cash_register_id' => $register->id, 'user_id' => $staff->id, 'status' => 'OPEN', 'opening_cash' => 0, 'opened_at' => now()]);
        }

        $this->actingAs($owner)->deleteJson(route('admin.users.destroy', $staff))->assertUnprocessable()
            ->assertJsonValidationErrors('user')
            ->assertJsonPath('errors.user.0', 'Tutup shift kasir dan selesaikan absensi staf sebelum menghapus akun.');
        $this->assertNull($staff->fresh()->archived_at);
        $this->assertTrue($staff->fresh()->isAccountActive());
        $this->assertDatabaseCount('activity_logs', 0);
    }

    public function test_guest_cannot_archive_staff(): void
    {
        $staff = $this->member(Shop::factory()->create());

        $this->deleteJson(route('admin.users.destroy', $staff))->assertUnauthorized();
    }

    public function test_open_attendance_prevents_deactivation_and_password_changes(): void
    {
        $shop = Shop::factory()->create();
        $owner = $this->member($shop, 'owner');
        $staff = $this->member($shop);
        Attendance::factory()->create(['shop_id' => $shop->id, 'user_id' => $staff->id]);

        $this->actingAs($owner)->patchJson(route('admin.users.deactivate', $staff))
            ->assertUnprocessable()->assertJsonValidationErrors('is_active');
        $this->putJson(route('admin.users.update', $staff), [
            'name' => $staff->name, 'email' => $staff->email, 'role' => $staff->role,
            'password' => 'ChangedPassword123', 'password_confirmation' => 'ChangedPassword123',
        ])->assertUnprocessable()->assertJsonValidationErrors('is_active');
        $this->assertTrue($staff->fresh()->isAccountActive());
        $this->assertDatabaseCount('activity_logs', 0);
    }
}
