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
}
