<?php

namespace Tests\Feature;

use App\Models\CashRegister;
use App\Models\CashRegisterSession;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class RegisterReconciliationTest extends TestCase
{
    use RefreshDatabase;

    #[TestWith([150000, 'BALANCED', 0])]
    #[TestWith([140000, 'SHORT', -10000])]
    #[TestWith([160000, 'OVER', 10000])]
    public function test_closing_stores_correct_status_and_audited_snapshot(int $actual, string $status, int $difference): void
    {
        [$cashier, $session] = $this->scenario();
        $response = $this->actingAs($cashier)->postJson(route('shift.close'), ['session_id' => $session->id, 'actual_cash' => $actual, 'notes' => 'Hitung ulang selesai']);
        $response->assertOk()->assertJsonPath('session.reconciliation_status', $status);
        $this->assertDatabaseHas('cash_register_sessions', ['id' => $session->id, 'status' => 'CLOSED', 'expected_cash' => 150000, 'actual_cash' => $actual, 'difference' => $difference]);
        $this->assertNotNull($session->fresh()->closed_at);
        $this->assertDatabaseHas('activity_logs', ['action' => 'register.closed', 'entity_id' => $session->id, 'shop_id' => $cashier->shop_id]);
        $this->assertSame($status, json_decode(DB::table('activity_logs')->sole()->new_values, true)['reconciliation_status']);
    }

    public function test_active_shift_is_uncounted_and_preview_does_not_close_it(): void
    {
        [$cashier, $session] = $this->scenario();
        $this->assertSame('UNCOUNTED', $session->reconciliation_status);
        $this->actingAs($cashier)->get(route('admin.register.index'))->assertOk()->assertSee('Kas seharusnya')->assertSee('150.000,00')->assertSee('Belum dihitung');
        $this->assertSame('OPEN', $session->fresh()->status);
        $this->assertDatabaseCount('activity_logs', 0);
    }

    public function test_cash_movements_include_only_own_shop_and_uncancelled_amounts(): void
    {
        [$cashier, $session] = $this->scenario();
        $this->movement($cashier, $session, 'CASH_IN', 25000);
        $this->movement($cashier, $session, 'CASH_OUT', 10000);
        $this->movement($cashier, $session, 'CASH_OUT', 50000, true);
        $other = Shop::factory()->create();
        DB::table('cash_drawer_transactions')->insert(['shop_id' => $other->id, 'user_id' => $cashier->id, 'cash_register_session_id' => $session->id, 'type' => 'CASH_IN', 'amount' => 999, 'description' => 'Foreign record']);
        $this->actingAs($cashier)->postJson(route('shift.close'), ['actual_cash' => 165000])->assertOk()->assertJsonPath('session.reconciliation_status', 'BALANCED');
        $this->assertEquals(165000, $session->fresh()->expected_cash);
        $audit = json_decode(DB::table('activity_logs')->first()->new_values, true);
        $this->assertEquals(25000, $audit['cash_in']);
        $this->assertEquals(10000, $audit['cash_out']);
    }

    public function test_decimal_cash_is_preserved_without_false_discrepancy(): void
    {
        [$cashier, $session] = $this->scenario();
        $session->update(['opening_cash' => 100.10, 'total_cash_sales' => 0.20]);
        $this->actingAs($cashier)->postJson(route('shift.close'), ['actual_cash' => 100.30])->assertOk()->assertJsonPath('session.reconciliation_status', 'BALANCED');
        $this->assertSame('100.30', $session->fresh()->actual_cash);
        $this->assertSame('0.00', $session->fresh()->difference);
    }

    #[TestWith([140000, null])]
    #[TestWith([160000, '   '])]
    public function test_discrepancy_requires_explanation_and_rolls_back(int $actual, ?string $notes): void
    {
        [$cashier, $session] = $this->scenario();
        $this->actingAs($cashier)->postJson(route('shift.close'), ['actual_cash' => $actual, 'notes' => $notes])->assertUnprocessable()->assertJsonValidationErrors('notes');
        $this->assertSame('OPEN', $session->fresh()->status);
        $this->assertDatabaseCount('activity_logs', 0);
    }

    public function test_closed_shift_cannot_be_overwritten_by_repeat_request(): void
    {
        [$cashier, $session] = $this->scenario();
        $this->actingAs($cashier)->postJson(route('shift.close'), ['actual_cash' => 150000])->assertOk();
        $this->postJson(route('shift.close'), ['actual_cash' => 1, 'notes' => 'Overwrite'])->assertUnprocessable();
        $this->assertSame('150000.00', $session->fresh()->actual_cash);
        $this->assertDatabaseCount('activity_logs', 1);
    }

    public function test_old_page_cannot_close_a_different_active_session(): void
    {
        [$cashier, $session] = $this->scenario();
        $this->actingAs($cashier)->postJson(route('shift.close'), ['actual_cash' => 150000])->assertOk();
        $this->postJson(route('shift.open'), ['opening_cash' => 100])->assertOk();
        $this->postJson(route('shift.close'), ['session_id' => $session->id, 'actual_cash' => 100])->assertUnprocessable()->assertJsonValidationErrors('session_id');
        $this->assertDatabaseHas('cash_register_sessions', ['user_id' => $cashier->id, 'status' => 'OPEN']);
    }

    public function test_cash_changed_after_preview_requires_recount(): void
    {
        [$cashier, $session] = $this->scenario();
        $session->increment('total_cash_sales', 1000);
        $this->actingAs($cashier)->postJson(route('shift.close'), ['actual_cash' => 150000, 'expected_cash_confirmation' => 150000, 'notes' => 'Counted'])->assertUnprocessable()->assertJsonValidationErrors('actual_cash');
        $this->assertSame('OPEN', $session->fresh()->status);
        $this->assertDatabaseCount('activity_logs', 0);
    }

    public function test_cashier_sees_only_own_history_and_cannot_close_colleagues_session(): void
    {
        [$cashier, $session] = $this->scenario();
        $colleague = User::factory()->create(['shop_id' => $cashier->shop_id, 'role' => 'cashier']);
        $other = CashRegisterSession::create(['cash_register_id' => $session->cash_register_id, 'user_id' => $colleague->id, 'status' => 'CLOSED', 'opening_cash' => 0, 'actual_cash' => 0, 'expected_cash' => 0, 'difference' => 0, 'notes' => 'Colleague private']);
        $this->actingAs($cashier)->get(route('admin.register.index'))->assertOk()->assertDontSee('Colleague private');
        $this->postJson(route('shift.close'), ['session_id' => $other->id, 'actual_cash' => 150000])->assertUnprocessable();
        $this->assertSame('OPEN', $session->fresh()->status);
    }

    public function test_manager_sees_own_cafe_history_and_filters_status_and_dates(): void
    {
        [$cashier, $session] = $this->scenario();
        $session->update(['status' => 'CLOSED', 'opened_at' => '2026-10-20 08:00:00', 'closed_at' => '2026-10-20 16:00:00', 'expected_cash' => 150000, 'actual_cash' => 140000, 'difference' => -10000, 'notes' => '<script>Counted</script>']);
        [$foreign, $foreignSession] = $this->scenario();
        $foreignSession->update(['notes' => 'Foreign secret']);
        $manager = User::factory()->create(['shop_id' => $cashier->shop_id, 'role' => 'manager']);
        $this->actingAs($manager)->get(route('admin.register.index', ['status' => 'SHORT', 'from' => '2026-10-20', 'to' => '2026-10-20']))->assertOk()->assertSee('<script>Counted</script>')->assertDontSee('<script>Counted</script>', false)->assertDontSee('Foreign secret');
        $this->get(route('admin.register.index', ['status' => 'BALANCED']))->assertOk()->assertDontSee('Counted');
        $this->get(route('admin.register.index', ['to' => '2026-10-20']))->assertOk();
    }

    #[TestWith(['actual_cash', -1])]
    #[TestWith(['actual_cash', 1.001])]
    #[TestWith(['actual_cash', 1000000000])]
    #[TestWith(['difference', 0])]
    #[TestWith(['expected_cash', 150000])]
    #[TestWith(['reconciliation_status', 'BALANCED'])]
    public function test_invalid_and_forged_financial_values_are_rejected(string $field, mixed $value): void
    {
        [$cashier, $session] = $this->scenario();
        $payload = ['actual_cash' => 150000];
        $payload[$field] = $value;
        $this->actingAs($cashier)->postJson(route('shift.close'), $payload)->assertUnprocessable()->assertJsonValidationErrors($field);
        $this->assertSame('OPEN', $session->fresh()->status);
    }

    #[TestWith([100.29, 'SHORT', '-0.01'])]
    #[TestWith([100.31, 'OVER', '0.01'])]
    public function test_single_cent_discrepancy_is_not_rounded_away(float $actual, string $status, string $difference): void
    {
        [$cashier, $session] = $this->scenario();
        $session->update(['opening_cash' => 100.10, 'total_cash_sales' => 0.20]);
        $this->actingAs($cashier)->postJson(route('shift.close'), ['actual_cash' => $actual, 'notes' => 'Counted twice'])->assertOk()->assertJsonPath('session.reconciliation_status', $status);
        $this->assertSame($difference, $session->fresh()->difference);
    }

    public function test_invalid_history_filters_are_rejected(): void
    {
        [$cashier, $session] = $this->scenario();
        $this->actingAs($cashier)->getJson(route('admin.register.index', ['status' => 'UNKNOWN']))->assertUnprocessable()->assertJsonValidationErrors('status');
        $this->getJson(route('admin.register.index', ['from' => '2026-10-21', 'to' => '2026-10-20']))->assertUnprocessable()->assertJsonValidationErrors('to');
    }

    public function test_access_requires_permission_and_shop(): void
    {
        $this->getJson(route('admin.register.index'))->assertUnauthorized();
        [$cashier, $session] = $this->scenario();
        $cashier->update(['role' => 'barista']);
        $this->actingAs($cashier)->get(route('admin.register.index'))->assertForbidden();
        $this->postJson(route('shift.close'), ['actual_cash' => 150000])->assertForbidden();
        $cashier->update(['role' => 'owner', 'shop_id' => null]);
        $this->get(route('admin.register.index'))->assertForbidden();
    }

    public function test_html_closing_redirects_to_saved_result(): void
    {
        [$cashier, $session] = $this->scenario();
        $this->actingAs($cashier)->post(route('shift.close'), ['actual_cash' => 150000])->assertRedirect(route('admin.register.index'))->assertSessionHas('success');
        $this->get(route('admin.register.index'))->assertOk()->assertSee('Shift berhasil ditutup')->assertSee('Kas sesuai');
    }

    /** @return array{User, CashRegisterSession} */
    private function scenario(): array
    {
        $shop = Shop::factory()->create(['onboarding_status' => 'completed']);
        $cashier = User::factory()->create(['shop_id' => $shop->id, 'role' => 'cashier']);
        $register = CashRegister::create(['shop_id' => $shop->id, 'name' => 'Register', 'is_active' => true]);
        $session = CashRegisterSession::create(['cash_register_id' => $register->id, 'user_id' => $cashier->id, 'opened_at' => now(), 'status' => 'OPEN', 'opening_cash' => 100000, 'total_cash_sales' => 50000, 'total_qris_sales' => 70000, 'total_other_sales' => 30000, 'actual_cash' => 0, 'difference' => 0]);

        return [$cashier, $session];
    }

    private function movement(User $cashier, CashRegisterSession $session, string $type, float $amount, bool $cancelled = false): void
    {
        DB::table('cash_drawer_transactions')->insert(['shop_id' => $cashier->shop_id, 'user_id' => $cashier->id, 'cash_register_session_id' => $session->id, 'type' => $type, 'amount' => $amount, 'description' => 'Test', 'cancelled_at' => $cancelled ? now() : null]);
    }
}
