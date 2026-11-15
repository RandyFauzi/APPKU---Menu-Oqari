<?php

namespace Tests\Feature;

use App\Models\CrewShift;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReplicateShiftsTest extends TestCase
{
    use RefreshDatabase;

    private function member(string $role = 'owner', ?Shop $shop = null): User
    {
        return User::factory()->create([
            'shop_id' => ($shop ?? Shop::factory()->create(['onboarding_status' => 'completed']))->id,
            'role' => $role,
            'is_active' => true,
        ]);
    }

    private function createShift(User $user, string $date, string $start = '08:00', string $end = '16:00', ?string $position = 'Barista'): CrewShift
    {
        return CrewShift::create([
            'shop_id' => $user->shop_id,
            'user_id' => $user->id,
            'date' => $date,
            'start_time' => $start,
            'end_time' => $end,
            'position' => $position,
            'notes' => 'Regular shift',
        ]);
    }

    public function test_guest_and_unauthorized_crew_cannot_replicate_shifts(): void
    {
        $this->postJson(route('admin.shifts.replicate'), [
            'source_month' => '2026-11',
            'target_months' => ['2026-12'],
        ])->assertUnauthorized();

        $crew = $this->member('crew');
        $this->actingAs($crew)->postJson(route('admin.shifts.replicate'), [
            'source_month' => '2026-11',
            'target_months' => ['2026-12'],
        ])->assertForbidden();
    }

    public function test_validation_fails_for_empty_targets_same_month_or_empty_source(): void
    {
        $owner = $this->member('owner');
        $this->actingAs($owner);

        // Target months required
        $this->postJson(route('admin.shifts.replicate'), [
            'source_month' => '2026-11',
            'target_months' => [],
        ])->assertUnprocessable()->assertJsonValidationErrors('target_months');

        // Target month cannot be the same as source month
        $this->postJson(route('admin.shifts.replicate'), [
            'source_month' => '2026-11',
            'target_months' => ['2026-11'],
        ])->assertUnprocessable()->assertJsonValidationErrors('target_months.0');

        // Empty source month shifts
        $this->postJson(route('admin.shifts.replicate'), [
            'source_month' => '2026-11',
            'target_months' => ['2026-12'],
        ])->assertUnprocessable()->assertJsonValidationErrors('source_month');
    }

    public function test_replicates_shifts_by_date_strategy_across_multiple_target_months(): void
    {
        $owner = $this->member('owner');
        $barista = $this->member('crew', $owner->shop);

        // Source month shifts in 2026-11 (30 days)
        $this->createShift($barista, '2026-11-01', '08:00', '16:00');
        $this->createShift($barista, '2026-11-15', '10:00', '18:00');
        $this->createShift($barista, '2026-11-30', '15:00', '23:00');

        $this->actingAs($owner);
        $response = $this->postJson(route('admin.shifts.replicate'), [
            'source_month' => '2026-11',
            'target_months' => ['2026-12', '2027-01'],
            'strategy' => 'date',
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('created_count', 6);

        // Verify target shifts exist in December 2026
        $this->assertTrue(CrewShift::where('shop_id', $owner->shop_id)
            ->where('user_id', $barista->id)
            ->whereDate('date', '2026-12-01')
            ->where('start_time', '08:00')
            ->exists());

        $this->assertTrue(CrewShift::where('shop_id', $owner->shop_id)
            ->where('user_id', $barista->id)
            ->whereDate('date', '2026-12-15')
            ->where('start_time', '10:00')
            ->exists());

        $this->assertTrue(CrewShift::where('shop_id', $owner->shop_id)
            ->where('user_id', $barista->id)
            ->whereDate('date', '2026-12-30')
            ->where('start_time', '15:00')
            ->exists());

        // Verify target shifts exist in January 2027
        $this->assertTrue(CrewShift::where('shop_id', $owner->shop_id)
            ->where('user_id', $barista->id)
            ->whereDate('date', '2027-01-01')
            ->where('start_time', '08:00')
            ->exists());

        // Verify audit log
        $this->assertDatabaseHas('activity_logs', [
            'shop_id' => $owner->shop_id,
            'user_id' => $owner->id,
            'action' => 'replicate_shifts',
        ]);
    }

    public function test_handles_different_month_lengths_safely_when_replicating_to_february(): void
    {
        $owner = $this->member('owner');
        $barista = $this->member('crew', $owner->shop);

        // January has 31 days (2027-01)
        $this->createShift($barista, '2027-01-10', '08:00', '16:00');
        $this->createShift($barista, '2027-01-31', '08:00', '16:00'); // Feb 2027 only has 28 days

        $this->actingAs($owner);
        $response = $this->postJson(route('admin.shifts.replicate'), [
            'source_month' => '2027-01',
            'target_months' => ['2027-02'],
            'strategy' => 'date',
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('created_count', 1)
            ->assertJsonPath('skipped_count', 1);

        $this->assertTrue(CrewShift::where('shop_id', $owner->shop_id)
            ->where('user_id', $barista->id)
            ->whereDate('date', '2027-02-10')
            ->exists());

        // Ensure no invalid Feb 31 was created
        $this->assertFalse(CrewShift::where('shop_id', $owner->shop_id)
            ->where('user_id', $barista->id)
            ->whereDate('date', '2027-02-31')
            ->exists());
    }

    public function test_conflict_handling_skips_when_overwrite_false_and_replaces_when_overwrite_true(): void
    {
        $owner = $this->member('owner');
        $barista = $this->member('crew', $owner->shop);

        // Source month 2026-11
        $this->createShift($barista, '2026-11-05', '08:00', '16:00');

        // Existing target month shift 2026-12-05 with conflicting hours
        $existing = $this->createShift($barista, '2026-12-05', '09:00', '17:00', 'Cashier');

        $this->actingAs($owner);

        // Case 1: overwrite = false -> skip
        $resSkip = $this->postJson(route('admin.shifts.replicate'), [
            'source_month' => '2026-11',
            'target_months' => ['2026-12'],
            'strategy' => 'date',
            'overwrite' => false,
        ]);

        $resSkip->assertOk()
            ->assertJsonPath('created_count', 0)
            ->assertJsonPath('skipped_count', 1)
            ->assertJsonPath('all_skipped', true)
            ->assertJsonPath('message', 'Tidak ada shift baru yang diterapkan (1 shift dilewati karena jadwal pada bulan target sudah ada). Aktifkan opsi \'Gantikan / Timpa jadwal yang sudah ada (Overwrite)\' jika ingin memperbarui jadwal lama.');

        // Original shift preserved
        $this->assertDatabaseHas('crew_shifts', [
            'id' => $existing->id,
            'position' => 'Cashier',
        ]);

        // Case 2: overwrite = true -> replaces existing
        $resOverwrite = $this->postJson(route('admin.shifts.replicate'), [
            'source_month' => '2026-11',
            'target_months' => ['2026-12'],
            'strategy' => 'date',
            'overwrite' => true,
        ]);

        $resOverwrite->assertOk()
            ->assertJsonPath('created_count', 1);

        $this->assertDatabaseMissing('crew_shifts', ['id' => $existing->id]);
        $this->assertTrue(CrewShift::where('shop_id', $owner->shop_id)
            ->where('user_id', $barista->id)
            ->whereDate('date', '2026-12-05')
            ->where('start_time', '08:00')
            ->where('position', 'Barista')
            ->exists());
    }

    public function test_replicates_shifts_by_day_of_week_pattern(): void
    {
        $owner = $this->member('owner');
        $barista = $this->member('crew', $owner->shop);

        // In 2026-11:
        // 2026-11-02 is a Monday (dayOfWeekIso = 1)
        $this->createShift($barista, '2026-11-02', '07:00', '15:00', 'Head Barista');

        $this->actingAs($owner);
        $response = $this->postJson(route('admin.shifts.replicate'), [
            'source_month' => '2026-11',
            'target_months' => ['2026-12'],
            'strategy' => 'day_of_week',
        ]);

        $response->assertOk()->assertJsonPath('success', true);

        // In December 2026, Mondays are: 7, 14, 21, 28 (4 Mondays)
        $mondaysInDec = ['2026-12-07', '2026-12-14', '2026-12-21', '2026-12-28'];
        foreach ($mondaysInDec as $mon) {
            $this->assertTrue(CrewShift::where('shop_id', $owner->shop_id)
                ->where('user_id', $barista->id)
                ->whereDate('date', $mon)
                ->where('start_time', '07:00')
                ->where('position', 'Head Barista')
                ->exists());
        }
    }

    public function test_cannot_replicate_shifts_for_other_cafe_staff(): void
    {
        $owner = $this->member('owner');
        $foreignShop = Shop::factory()->create(['onboarding_status' => 'completed']);
        $foreignUser = $this->member('crew', $foreignShop);

        $this->createShift($foreignUser, '2026-11-01');

        $this->actingAs($owner);
        $this->postJson(route('admin.shifts.replicate'), [
            'source_month' => '2026-11',
            'target_months' => ['2026-12'],
            'user_ids' => [$foreignUser->id],
        ])->assertUnprocessable()->assertJsonValidationErrors('user_ids.0');
    }
}
