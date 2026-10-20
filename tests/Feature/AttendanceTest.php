<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\CrewShift;
use App\Models\Shop;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class AttendanceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['attendance.timezone' => 'Asia/Jakarta', 'attendance.grace_minutes' => 0]);
    }

    public function test_employee_checks_in_with_server_time_and_scheduled_lateness(): void
    {
        $employee = $this->employee();
        $this->shift($employee);
        $this->travelTo(Carbon::parse('2026-10-20 08:10:00', 'Asia/Jakarta'));
        $response = $this->actingAs($employee)->postJson(route('admin.attendance.store'), ['type' => 'check_in', 'note' => 'Bus terlambat']);
        $response->assertOk()->assertJsonPath('attendance.late_minutes', 10);
        $this->assertDatabaseHas('attendances', [
            'user_id' => $employee->id, 'shop_id' => $employee->shop_id,
            'checked_in_at' => '2026-10-20 01:10:00',
            'late_minutes' => 10, 'check_in_note' => 'Bus terlambat',
        ]);
        $this->assertDatabaseHas('activity_logs', ['user_id' => $employee->id, 'action' => 'attendance.check_in']);
    }

    public function test_duplicate_check_in_is_rejected_without_overwriting_original(): void
    {
        $employee = $this->employee();
        $this->travelTo(Carbon::parse('2026-10-20 08:00:00', 'Asia/Jakarta'));
        $this->actingAs($employee)->postJson(route('admin.attendance.store'), ['type' => 'check_in'])->assertOk();
        $this->travel(15)->minutes();
        $this->postJson(route('admin.attendance.store'), ['type' => 'check_in'])->assertUnprocessable()->assertJsonValidationErrors('type');
        $this->assertDatabaseCount('attendances', 1);
        $this->assertDatabaseHas('attendances', ['checked_in_at' => '2026-10-20 01:00:00']);
        $this->assertDatabaseCount('activity_logs', 1);
    }

    public function test_checkout_records_duration_early_leave_and_audit_log(): void
    {
        $employee = $this->employee();
        $this->shift($employee);
        $this->travelTo(Carbon::parse('2026-10-20 08:00:00', 'Asia/Jakarta'));
        $this->actingAs($employee)->postJson(route('admin.attendance.store'), ['type' => 'check_in'])->assertOk();
        $this->travel(7)->hours();
        $this->postJson(route('admin.attendance.store'), ['type' => 'check_out', 'note' => 'Izin pulang awal'])->assertOk()
            ->assertJsonPath('attendance.worked_minutes', 420)->assertJsonPath('attendance.early_leave_minutes', 60);
        $this->assertDatabaseHas('attendances', ['checked_out_at' => '2026-10-20 08:00:00', 'check_out_note' => 'Izin pulang awal']);
        $this->assertDatabaseHas('activity_logs', ['action' => 'attendance.check_out']);
        $this->postJson(route('admin.attendance.store'), ['type' => 'check_out'])->assertUnprocessable();
        $this->assertDatabaseCount('activity_logs', 2);
    }

    public function test_completed_day_cannot_be_reopened(): void
    {
        $employee = $this->employee();
        $this->travelTo(Carbon::parse('2026-10-20 08:00:00', 'Asia/Jakarta'));
        $this->actingAs($employee)->postJson(route('admin.attendance.store'), ['type' => 'check_in'])->assertOk();
        $this->travel(8)->hours();
        $this->postJson(route('admin.attendance.store'), ['type' => 'check_out'])->assertOk();
        $this->postJson(route('admin.attendance.store'), ['type' => 'check_in'])->assertUnprocessable();
        $this->assertDatabaseCount('attendances', 1);
    }

    public function test_checkout_without_check_in_is_rejected(): void
    {
        $this->actingAs($this->employee())->postJson(route('admin.attendance.store'), ['type' => 'check_out'])
            ->assertUnprocessable()->assertJsonValidationErrors('type');
        $this->assertDatabaseCount('attendances', 0);
    }

    public function test_overnight_shift_keeps_start_work_date_and_can_checkout_next_day(): void
    {
        $employee = $this->employee();
        $this->shift($employee, '22:00', '06:00', '2026-10-19');
        $this->travelTo(Carbon::parse('2026-10-20 00:15:00', 'Asia/Jakarta'));
        $this->actingAs($employee)->postJson(route('admin.attendance.store'), ['type' => 'check_in'])->assertOk();
        $this->assertDatabaseHas('attendances', ['late_minutes' => 135]);
        $this->assertSame('2026-10-19', Attendance::firstOrFail()->work_date->toDateString());
        $this->travelTo(Carbon::parse('2026-10-20 06:00:00', 'Asia/Jakarta'));
        $this->postJson(route('admin.attendance.store'), ['type' => 'check_out'])->assertOk()
            ->assertJsonPath('attendance.worked_minutes', 345)->assertJsonPath('attendance.early_leave_minutes', 0);
    }

    public function test_open_session_from_previous_day_blocks_new_check_in_but_allows_checkout(): void
    {
        $employee = $this->employee();
        $this->travelTo(Carbon::parse('2026-10-19 23:00:00', 'Asia/Jakarta'));
        $this->actingAs($employee)->postJson(route('admin.attendance.store'), ['type' => 'check_in'])->assertOk();
        $this->travel(2)->hours();
        $this->postJson(route('admin.attendance.store'), ['type' => 'check_in'])->assertUnprocessable();
        $this->postJson(route('admin.attendance.store'), ['type' => 'check_out'])->assertOk()->assertJsonPath('attendance.worked_minutes', 120);
    }

    public function test_missing_schedule_is_not_marked_late(): void
    {
        $employee = $this->employee();
        $this->travelTo(Carbon::parse('2026-10-20 14:00:00', 'Asia/Jakarta'));
        $this->actingAs($employee)->postJson(route('admin.attendance.store'), ['type' => 'check_in'])
            ->assertOk()->assertJsonPath('attendance.late_minutes', 0)->assertJsonPath('attendance.crew_shift_id', null);
        $this->get(route('admin.attendance.index'))->assertOk()->assertSee('Tanpa jadwal');
    }

    #[TestWith(['crew'])]
    #[TestWith(['cashier'])]
    #[TestWith(['barista'])]
    #[TestWith(['kitchen'])]
    public function test_staff_can_record_own_attendance_but_cannot_view_report(string $role): void
    {
        $employee = $this->employee($role);
        $this->actingAs($employee)->get(route('admin.attendance.index'))->assertOk();
        $this->postJson(route('admin.attendance.store'), ['type' => 'check_in'])->assertOk();
        $this->getJson(route('admin.attendance.report'))->assertForbidden();
    }

    public function test_identity_and_timestamps_cannot_be_supplied_by_client(): void
    {
        $employee = $this->employee();
        $this->actingAs($employee)->postJson(route('admin.attendance.store'), [
            'type' => 'check_in', 'user_id' => 999, 'shop_id' => 999,
            'checked_in_at' => '2000-01-01 00:00:00', 'work_date' => '2000-01-01',
        ])->assertUnprocessable()->assertJsonValidationErrors(['user_id', 'shop_id', 'checked_in_at', 'work_date']);
        $this->assertDatabaseCount('attendances', 0);
    }

    public function test_personal_history_never_returns_other_employees_or_other_cafes(): void
    {
        $employee = $this->employee();
        $colleague = User::factory()->create(['shop_id' => $employee->shop_id, 'role' => 'crew']);
        $other = $this->employee();
        Attendance::factory()->create(['shop_id' => $employee->shop_id, 'user_id' => $employee->id, 'check_in_note' => 'Own record']);
        Attendance::factory()->create(['shop_id' => $employee->shop_id, 'user_id' => $colleague->id, 'check_in_note' => 'Private colleague record']);
        Attendance::factory()->create(['shop_id' => $other->shop_id, 'user_id' => $other->id, 'check_in_note' => 'Other cafe record']);
        $this->actingAs($employee)->get(route('admin.attendance.index', ['user_id' => $other->id]))->assertOk()
            ->assertSee('Own record')->assertDontSee('Private colleague record')->assertDontSee('Other cafe record');
    }

    #[TestWith(['owner'])]
    #[TestWith(['manager'])]
    public function test_management_report_is_scoped_and_filters_by_employee_month_and_status(string $role): void
    {
        $manager = $this->employee($role);
        $employee = User::factory()->create(['shop_id' => $manager->shop_id, 'role' => 'crew']);
        $other = $this->employee();
        Attendance::factory()->create(['shop_id' => $manager->shop_id, 'user_id' => $employee->id,
            'work_date' => '2026-10-20', 'late_minutes' => 15, 'check_in_note' => 'Late selected']);
        Attendance::factory()->create(['shop_id' => $manager->shop_id, 'user_id' => $manager->id,
            'work_date' => '2026-10-20', 'check_in_note' => 'Manager private']);
        Attendance::factory()->create(['shop_id' => $other->shop_id, 'user_id' => $other->id,
            'work_date' => '2026-10-20', 'check_in_note' => 'Other cafe private']);
        $this->actingAs($manager)->get(route('admin.attendance.report', ['month' => '2026-10', 'user_id' => $employee->id, 'status' => 'late']))
            ->assertOk()->assertSee('Late selected')->assertDontSee('Manager private')->assertDontSee('Other cafe private');
        $this->getJson(route('admin.attendance.report', ['user_id' => $other->id]))->assertUnprocessable()->assertJsonValidationErrors('user_id');
    }

    public function test_guest_unassigned_user_unknown_role_and_suspended_employee_cannot_record(): void
    {
        $this->postJson(route('admin.attendance.store'), ['type' => 'check_in'])->assertUnauthorized();
        $user = User::factory()->create(['shop_id' => null, 'role' => 'crew']);
        $this->actingAs($user)->postJson(route('admin.attendance.store'), ['type' => 'check_in'])->assertForbidden();
        $user = $this->employee('unknown');
        $this->actingAs($user)->postJson(route('admin.attendance.store'), ['type' => 'check_in'])->assertForbidden();
        $user = $this->employee();
        $user->shop->update(['status' => 'suspended']);
        $this->actingAs($user)->postJson(route('admin.attendance.store'), ['type' => 'check_in'])->assertForbidden();
        $this->assertDatabaseCount('attendances', 0);
    }

    public function test_notes_are_validated_and_escaped_in_history(): void
    {
        $employee = $this->employee();
        $this->actingAs($employee)->postJson(route('admin.attendance.store'), ['type' => 'check_in', 'note' => str_repeat('x', 501)])
            ->assertUnprocessable()->assertJsonValidationErrors('note');
        $this->postJson(route('admin.attendance.store'), ['type' => 'check_in', 'note' => '<script>alert(1)</script>'])->assertOk();
        $this->get(route('admin.attendance.index'))->assertOk()->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false)->assertDontSee('<script>alert(1)</script>', false);
    }

    public function test_grace_period_and_schedule_snapshots_are_preserved(): void
    {
        config(['attendance.grace_minutes' => 5]);
        $employee = $this->employee();
        $shift = $this->shift($employee);
        $this->travelTo(Carbon::parse('2026-10-20 08:04:00', 'Asia/Jakarta'));
        $this->actingAs($employee)->postJson(route('admin.attendance.store'), ['type' => 'check_in'])->assertOk()
            ->assertJsonPath('attendance.late_minutes', 0);
        $shift->update(['start_time' => '09:00', 'end_time' => '17:00']);
        $this->travelTo(Carbon::parse('2026-10-20 16:00:00', 'Asia/Jakarta'));
        $this->postJson(route('admin.attendance.store'), ['type' => 'check_out'])->assertOk()->assertJsonPath('attendance.early_leave_minutes', 0);
        $this->assertDatabaseHas('attendances', ['scheduled_start_at' => '2026-10-20 01:00:00', 'scheduled_end_at' => '2026-10-20 09:00:00']);
    }

    public function test_invalid_filter_and_action_are_rejected(): void
    {
        $this->actingAs($this->employee('owner'))->getJson(route('admin.attendance.report', ['month' => 'invalid', 'status' => 'invalid']))
            ->assertUnprocessable()->assertJsonValidationErrors(['month', 'status']);
        $this->postJson(route('admin.attendance.store'), ['type' => 'edit'])->assertUnprocessable()->assertJsonValidationErrors('type');
    }

    public function test_report_includes_last_day_of_month_and_retains_pagination_filter(): void
    {
        $manager = $this->employee('owner');
        Attendance::factory()->create(['shop_id' => $manager->shop_id, 'user_id' => $manager->id,
            'work_date' => '2026-10-31', 'check_in_note' => 'Last day record']);
        $this->actingAs($manager)->get(route('admin.attendance.report', ['month' => '2026-10']))
            ->assertOk()->assertSee('Last day record');
        $this->get(route('admin.attendance.report', ['month' => '2026-11']))->assertOk()->assertDontSee('Last day record');
    }

    public function test_employee_can_record_next_day_after_previous_session_is_closed(): void
    {
        $employee = $this->employee();
        $this->travelTo(Carbon::parse('2026-10-19 08:00:00', 'Asia/Jakarta'));
        $this->actingAs($employee)->postJson(route('admin.attendance.store'), ['type' => 'check_in'])->assertOk();
        $this->travel(8)->hours();
        $this->postJson(route('admin.attendance.store'), ['type' => 'check_out'])->assertOk();
        $this->travel(16)->hours();
        $this->postJson(route('admin.attendance.store'), ['type' => 'check_in'])->assertOk();
        $this->assertDatabaseCount('attendances', 2);
    }

    public function test_checkout_does_not_close_colleagues_open_session(): void
    {
        $employee = $this->employee();
        $colleague = User::factory()->create(['shop_id' => $employee->shop_id, 'role' => 'crew']);
        $attendance = Attendance::factory()->create(['shop_id' => $employee->shop_id, 'user_id' => $colleague->id]);
        $this->actingAs($employee)->postJson(route('admin.attendance.store'), ['type' => 'check_out'])->assertUnprocessable();
        $this->assertNull($attendance->fresh()->checked_out_at);
    }

    public function test_report_retains_employee_name_after_account_is_deleted(): void
    {
        $manager = $this->employee('owner');
        $employee = User::factory()->create(['shop_id' => $manager->shop_id, 'name' => 'Historical Employee', 'role' => 'crew']);
        $attendance = Attendance::factory()->create(['shop_id' => $manager->shop_id, 'user_id' => $employee->id]);
        $employee->delete();
        $this->actingAs($manager)->get(route('admin.attendance.report'))->assertOk()->assertSee('Historical Employee')->assertSee('Akun telah dihapus');
        $this->assertNull($attendance->fresh()->user_id);
    }

    private function employee(string $role = 'crew'): User
    {
        $shop = Shop::factory()->create(['onboarding_status' => 'completed']);

        return User::factory()->create(['shop_id' => $shop->id, 'role' => $role]);
    }

    private function shift(User $employee, string $start = '08:00', string $end = '16:00', string $date = '2026-10-20'): CrewShift
    {
        return CrewShift::create([
            'shop_id' => $employee->shop_id, 'user_id' => $employee->id, 'date' => $date,
            'start_time' => $start, 'end_time' => $end,
        ]);
    }
}
