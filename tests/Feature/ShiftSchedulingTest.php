<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\CrewShift;
use App\Models\Shop;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class ShiftSchedulingTest extends TestCase
{
    use RefreshDatabase;

    private function member(string $role = 'owner', ?Shop $shop = null): User
    {
        return User::factory()->create(['shop_id' => ($shop ?? Shop::factory()->create(['onboarding_status' => 'completed']))->id, 'role' => $role]);
    }

    private function payload(User $user, array $overrides = []): array
    {
        return array_merge(['user_id' => $user->id, 'date' => '2026-11-04', 'start_time' => '08:00', 'end_time' => '12:00', 'position' => 'Kasir', 'notes' => 'Shift pagi'], $overrides);
    }

    private function shift(User $user, array $overrides = []): CrewShift
    {
        return CrewShift::create($this->payload($user, $overrides) + ['shop_id' => $user->shop_id]);
    }

    #[TestWith(['/admin/shifts'])]
    #[TestWith(['/admin/api/shifts'])]
    public function test_both_endpoints_create_validated_overnight_shifts(string $endpoint): void
    {
        $owner = $this->member();
        $this->actingAs($owner)->postJson($endpoint, $this->payload($owner, ['start_time' => '22:00', 'end_time' => '06:30']))->assertCreated();
        $shift = CrewShift::sole();
        $this->assertSame(510, $shift->durationMinutes());
        $this->assertSame('2026-11-05', $shift->endsAt()->toDateString());
        $this->assertSame($owner->shop_id, $shift->shop_id);
    }

    public function test_overlapping_shifts_are_rejected_including_across_midnight_but_adjacent_shifts_are_allowed(): void
    {
        $owner = $this->member();
        $this->shift($owner, ['start_time' => '22:00', 'end_time' => '06:00']);
        $this->actingAs($owner)->postJson('/admin/api/shifts', $this->payload($owner, ['date' => '2026-11-05', 'start_time' => '05:00', 'end_time' => '08:00']))
            ->assertUnprocessable()->assertJsonValidationErrors('start_time');
        $this->assertDatabaseCount('crew_shifts', 1);
        $this->postJson('/admin/api/shifts', $this->payload($owner, ['date' => '2026-11-05', 'start_time' => '06:00', 'end_time' => '08:00']))->assertCreated();
    }

    public function test_update_excludes_itself_from_conflicts_and_cannot_modify_foreign_shift(): void
    {
        $owner = $this->member();
        $own = $this->shift($owner);
        $foreign = $this->shift($this->member());
        $this->actingAs($owner)->postJson('/admin/api/shifts', $this->payload($owner, ['id' => $own->id, 'notes' => 'Updated note']))->assertOk();
        $this->assertDatabaseHas('crew_shifts', ['id' => $own->id, 'notes' => 'Updated note']);
        $this->postJson('/admin/api/shifts', $this->payload($owner, ['id' => $foreign->id]))->assertNotFound();
        $this->deleteJson(route('admin.shifts.destroy', $foreign))->assertNotFound();
    }

    public function test_only_active_own_cafe_staff_can_be_assigned(): void
    {
        $owner = $this->member();
        $foreign = $this->member('crew');
        $inactive = $this->member('crew', $owner->shop);
        $inactive->is_active = false;
        $inactive->save();
        $archived = $this->member('crew', $owner->shop);
        $archived->archived_at = now();
        $archived->save();
        $this->actingAs($owner);
        foreach ([$foreign, $inactive, $archived] as $staff) {
            $this->postJson('/admin/api/shifts', $this->payload($staff))->assertUnprocessable()->assertJsonValidationErrors('user_id');
        }
        $this->assertDatabaseCount('crew_shifts', 0);
        $this->getJson(route('admin.shifts.data', ['date' => '2026-11-04']))->assertOk()->assertJsonCount(1, 'employees');
    }

    public function test_management_list_is_cafe_scoped_and_limited_to_selected_seven_days(): void
    {
        $owner = $this->member();
        $own = $this->shift($owner);
        $this->shift($owner, ['date' => '2026-11-11']);
        $this->shift($this->member());
        $this->actingAs($owner)->getJson(route('admin.shifts.data', ['date' => '2026-11-04']))
            ->assertOk()->assertJsonCount(1, 'data')->assertJsonPath('data.0.id', $own->id);
        $this->getJson(route('admin.shifts.data', ['date' => 'invalid']))->assertUnprocessable()->assertJsonValidationErrors('date');
    }

    public function test_personal_schedule_uses_attendance_timezone_and_keeps_multiple_shifts_without_other_users_data(): void
    {
        config(['attendance.timezone' => 'Asia/Makassar']);
        $this->travelTo(CarbonImmutable::parse('2026-11-04 18:00:00', 'UTC'));
        $staff = $this->member('crew');
        $colleague = $this->member('crew', $staff->shop);
        $this->shift($staff, ['date' => '2026-11-05', 'notes' => 'Morning personal']);
        $this->shift($staff, ['date' => '2026-11-05', 'start_time' => '13:00', 'end_time' => '16:30', 'notes' => 'Afternoon personal']);
        $this->shift($colleague, ['date' => '2026-11-05', 'notes' => 'Private colleague']);
        $this->shift($this->member(), ['date' => '2026-11-05', 'notes' => 'Private cafe']);
        $this->actingAs($staff)->get(route('admin.my-schedule'))->assertOk()
            ->assertSee('Morning personal')->assertSee('Afternoon personal')->assertDontSee('Private colleague')->assertDontSee('Private cafe')
            ->assertViewHas('shiftCount', 2)->assertViewHas('totalMinutes', 450)
            ->assertViewHas('today', fn ($today) => $today->toDateString() === '2026-11-05');
    }

    #[TestWith(['cashier'])]
    #[TestWith(['barista'])]
    #[TestWith(['kitchen'])]
    #[TestWith(['crew'])]
    public function test_staff_can_see_own_schedule_but_cannot_manage_team(string $role): void
    {
        $staff = $this->member($role);
        $this->actingAs($staff)->get(route('admin.my-schedule'))->assertOk();
        $this->getJson(route('admin.shifts.data'))->assertForbidden();
        $this->postJson('/admin/api/shifts', $this->payload($staff))->assertForbidden();
    }

    public function test_invalid_time_and_missing_authentication_are_rejected(): void
    {
        $this->getJson(route('admin.shifts.data'))->assertUnauthorized();
        $owner = $this->member();
        $this->actingAs($owner)->postJson('/admin/api/shifts', $this->payload($owner, ['end_time' => '08:00', 'date' => 'invalid']))
            ->assertUnprocessable()->assertJsonValidationErrors(['end_time', 'date']);
    }

    public function test_deleting_a_schedule_keeps_recorded_attendance_and_its_time_snapshot(): void
    {
        $owner = $this->member();
        $shift = $this->shift($owner);
        $attendance = Attendance::factory()->create(['shop_id' => $owner->shop_id, 'user_id' => $owner->id,
            'crew_shift_id' => $shift->id, 'scheduled_start_at' => '2026-11-04 00:00:00']);

        $this->actingAs($owner)->deleteJson(route('admin.shifts.destroy', $shift))->assertOk();
        $this->assertModelMissing($shift);
        $this->assertModelExists($attendance);
        $this->assertNull($attendance->fresh()->crew_shift_id);
        $this->assertSame('2026-11-04 00:00:00', $attendance->fresh()->scheduled_start_at->format('Y-m-d H:i:s'));
    }
}
