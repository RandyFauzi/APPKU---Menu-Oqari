<?php

namespace Tests\Feature;

use App\Models\Shop;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SettingsPageAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_accessing_settings_pages_when_onboarding_pending(): void
    {
        $shop = Shop::factory()->create([
            'name' => 'Warung Lokal',
            'onboarding_status' => 'pending',
        ]);

        $owner = User::factory()->create([
            'name' => 'Owner Warung Lokal',
            'role' => 'owner',
            'shop_id' => $shop->id,
        ]);

        $routes = [
            'admin.tables.index',
            'admin.settings.index',
            'admin.operating-hours.index',
            'admin.receipt-settings.index',
            'admin.hpp.index',
        ];

        foreach ($routes as $route) {
            $response = $this->actingAs($owner)->get(route($route));
            $response->assertStatus(200);
        }

        // Dashboard specifically redirects to onboarding when setup is pending
        $dashboardResponse = $this->actingAs($owner)->get(route('admin.dashboard'));
        $dashboardResponse->assertRedirect(route('admin.onboarding'));
    }

    public function test_owner_accessing_settings_pages_when_onboarding_completed(): void
    {
        $shop = Shop::factory()->create([
            'name' => 'Warung Lokal',
            'onboarding_status' => 'completed',
        ]);

        $owner = User::factory()->create([
            'name' => 'Owner Warung Lokal',
            'role' => 'owner',
            'shop_id' => $shop->id,
        ]);

        $routes = [
            'admin.tables.index',
            'admin.settings.index',
            'admin.operating-hours.index',
            'admin.receipt-settings.index',
            'admin.hpp.index',
        ];

        foreach ($routes as $route) {
            $response = $this->actingAs($owner)->get(route($route));
            $response->assertStatus(200);
        }

        $dashboardResponse = $this->actingAs($owner)->get(route('admin.dashboard'));
        $dashboardResponse->assertStatus(200);
    }

    public function test_non_permitted_roles_cannot_access_settings(): void
    {
        $shop = Shop::factory()->create([
            'onboarding_status' => 'completed',
        ]);

        $cashier = User::factory()->create([
            'role' => 'cashier',
            'shop_id' => $shop->id,
        ]);

        $this->actingAs($cashier)->get(route('admin.settings.index'))->assertForbidden();
        $this->actingAs($cashier)->get(route('admin.tables.index'))->assertForbidden();
        $this->actingAs($cashier)->get(route('admin.operating-hours.index'))->assertForbidden();
        $this->actingAs($cashier)->get(route('admin.receipt-settings.index'))->assertForbidden();
        $this->actingAs($cashier)->get(route('admin.hpp.index'))->assertForbidden();
    }
}
