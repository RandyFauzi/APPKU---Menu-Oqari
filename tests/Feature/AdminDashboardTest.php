<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_admin_can_access_dashboard()
    {
        $user = \App\Models\User::factory()->create();
        
        $response = $this->actingAs($user)->get('/admin/dashboard');

        $response->assertStatus(200);
        $response->assertViewIs('Admin.Dashboard.dashboard');
    }

    public function test_admin_can_create_menu()
    {
        $user = \App\Models\User::factory()->create();
        
        // Ensure a shop is created via the dashboard
        $this->actingAs($user)->get('/admin/dashboard');

        $response = $this->actingAs($user)->postJson('/admin/api/menu', [
            'name' => 'Kopi Test',
            'price' => 15000,
            'categoryId' => 'beverages',
            'desc' => 'Kopi testing'
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('products', [
            'name' => 'Kopi Test',
            'price' => 15000
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
}
