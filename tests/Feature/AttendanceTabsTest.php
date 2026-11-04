<?php

namespace Tests\Feature;

use App\Models\Shop;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class AttendanceTabsTest extends TestCase
{
    use RefreshDatabase;

    #[TestWith(['admin'])]
    #[TestWith(['manager'])]
    #[TestWith(['cashier'])]
    #[TestWith(['barista'])]
    #[TestWith(['kitchen'])]
    #[TestWith(['crew'])]
    public function test_non_owner_only_sees_personal_tab_and_cannot_open_team_or_legacy_report(string $role): void
    {
        $user = User::factory()->create(['shop_id' => Shop::factory()->create(['onboarding_status' => 'completed'])->id, 'role' => $role]);

        $this->actingAs($user)->get(route('admin.attendance.index'))
            ->assertOk()->assertSee('Absensi Saya')->assertDontSee('Rekap Tim')->assertDontSee('Rekap Absensi');
        $this->getJson(route('admin.attendance.index', ['tab' => 'team']))->assertForbidden();
        $this->getJson(route('admin.attendance.report'))->assertForbidden();
    }

    public function test_owner_has_both_tabs_and_legacy_report_redirects_to_unified_page(): void
    {
        $owner = User::factory()->create(['shop_id' => Shop::factory()->create(['onboarding_status' => 'completed'])->id, 'role' => 'owner']);

        $this->actingAs($owner)->get(route('admin.attendance.index'))
            ->assertOk()->assertSee('Absensi Saya')->assertSee('Rekap Tim')->assertDontSee('Rekap Absensi');
        $this->get(route('admin.attendance.report', ['month' => '2026-10', 'status' => 'late']))
            ->assertRedirect(route('admin.attendance.index', ['tab' => 'team', 'month' => '2026-10', 'status' => 'late']));
        $this->get(route('admin.attendance.index', ['tab' => 'team']))
            ->assertOk()->assertSee('name="tab" value="team"', false)->assertDontSee('id="record-title"', false);
    }
}
