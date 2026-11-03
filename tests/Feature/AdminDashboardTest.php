<?php

namespace Tests\Feature;

use App\Models\CashRegister;
use App\Models\CashRegisterSession;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function test_admin_can_access_dashboard()
    {
        $shop = Shop::factory()->create(['onboarding_status' => 'completed']);
        $user = User::factory()->create(['role' => 'owner', 'shop_id' => $shop->id]);

        $response = $this->actingAs($user)->get('/admin/dashboard');

        $response->assertStatus(200);
        $response->assertViewIs('Admin.Dashboard.dashboard');
    }

    public function test_admin_can_create_menu()
    {
        $shop = Shop::factory()->create(['onboarding_status' => 'completed']);
        $user = User::factory()->create(['role' => 'owner', 'shop_id' => $shop->id]);

        $this->actingAs($user)->get('/admin/dashboard');

        $response = $this->actingAs($user)->postJson('/admin/api/menu', [
            'name' => 'Kopi Test',
            'price' => 15000,
            'categoryId' => 'beverages',
            'desc' => 'Kopi testing',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('products', [
            'name' => 'Kopi Test',
            'price' => 15000,
        ]);
    }

    public function test_user_can_register()
    {
        $response = $this->post('/admin/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        if ($response->isRedirect() && session()->has('errors')) {
            dump(session('errors')->getBag('default')->getMessages());
        }

        $response->assertRedirect('/admin/dashboard');
        $this->assertAuthenticated();
    }

    public function test_dashboard_loads_active_session_through_register_and_excludes_other_cafes(): void
    {
        $shop = Shop::factory()->create(['onboarding_status' => 'completed']);
        $otherShop = Shop::factory()->create();
        $owner = User::factory()->create(['role' => 'owner', 'shop_id' => $shop->id]);
        $otherRegister = CashRegister::create(['shop_id' => $otherShop->id, 'name' => 'Cafe Lain']);
        $ownRegister = CashRegister::create(['shop_id' => $shop->id, 'name' => 'Cafe Sendiri']);
        CashRegisterSession::create(['cash_register_id' => $otherRegister->id, 'user_id' => $owner->id, 'status' => 'OPEN', 'opening_cash' => 0, 'opened_at' => now()]);
        $session = CashRegisterSession::create(['cash_register_id' => $ownRegister->id, 'user_id' => $owner->id, 'status' => 'OPEN', 'opening_cash' => 100000, 'opened_at' => now()]);
        $this->actingAs($owner)->get('/admin/dashboard')->assertOk()
            ->assertViewHas('activeSession', fn ($active): bool => $active?->id === $session->id);
        $session->update(['status' => 'CLOSED']);
        $this->get('/admin/dashboard')->assertOk()->assertViewHas('activeSession', null);
    }

    public function test_owner_can_save_operating_hours_via_api(): void
    {
        $shop = Shop::factory()->create(['onboarding_status' => 'completed']);
        $owner = User::factory()->create(['role' => 'owner', 'shop_id' => $shop->id]);

        $payload = [
            'operating_hours' => [
                'weekly' => [
                    'monday' => ['open' => '08:00', 'close' => '22:00', 'is_closed' => false],
                    'tuesday' => ['open' => '08:00', 'close' => '22:00', 'is_closed' => false],
                    'wednesday' => ['open' => '08:00', 'close' => '22:00', 'is_closed' => false],
                    'thursday' => ['open' => '08:00', 'close' => '21:00', 'is_closed' => false],
                    'friday' => ['open' => '08:00', 'close' => '23:00', 'is_closed' => false],
                    'saturday' => ['open' => '08:00', 'close' => '23:00', 'is_closed' => false],
                    'sunday' => ['open' => '08:00', 'close' => '22:00', 'is_closed' => true],
                ],
                'holidays' => [
                    ['date' => '2026-08-17', 'name' => 'Hari Kemerdekaan', 'is_closed' => true],
                ],
                'timezone' => 'Asia/Jakarta',
                'auto_close' => true,
                'allow_preorder' => false,
            ],
        ];

        $response = $this->actingAs($owner)->postJson('/admin/api/operating-hours', $payload);

        $response->assertOk()
            ->assertJsonPath('success', true);

        $shop->refresh();
        $this->assertIsArray($shop->operating_hours);
        $this->assertEquals('Asia/Jakarta', $shop->operating_hours['timezone']);
        $this->assertTrue($shop->operating_hours['auto_close']);
        $this->assertCount(1, $shop->operating_hours['holidays']);
    }

    public function test_shop_operating_hours_helpers(): void
    {
        $shop = Shop::factory()->create([
            'is_open' => true,
            'operating_hours' => [
                'weekly' => [
                    'monday' => ['open' => '08:00', 'close' => '22:00', 'is_closed' => false],
                    'sunday' => ['open' => '08:00', 'close' => '22:00', 'is_closed' => true],
                ],
                'holidays' => [
                    ['date' => now()->format('Y-m-d'), 'name' => 'Hari Ini Libur', 'is_closed' => true],
                ],
                'timezone' => 'Asia/Jakarta',
                'auto_close' => true,
                'allow_preorder' => false,
            ],
        ]);

        $this->assertFalse($shop->isOpenNow());

        // When auto_close is false, isOpenNow reflects is_open
        $shop->operating_hours = array_merge($shop->operating_hours, ['auto_close' => false]);
        $this->assertTrue($shop->isOpenNow());
    }
}
