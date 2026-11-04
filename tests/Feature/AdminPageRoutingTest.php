<?php

namespace Tests\Feature;

use App\Models\Shop;
use App\Models\User;
use App\Providers\AppServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class AdminPageRoutingTest extends TestCase
{
    use RefreshDatabase;

    private function owner(): User
    {
        return User::factory()->create(['role' => 'owner', 'shop_id' => Shop::factory()->create(['onboarding_status' => 'completed'])->id]);
    }

    public function test_every_feature_has_a_canonical_page_with_only_its_selected_view_and_no_dashboard_tab_links(): void
    {
        $this->actingAs($this->owner());
        foreach (config('admin_pages') as $page => $definition) {
            $response = $this->get(route($definition['route']))->assertOk()->assertViewHas('page', $page)
                ->assertSee('data-cafe-sidebar', false)->assertDontSee('/admin/dashboard?tab=', false);
            if ($page === 'orders') {
                $response->assertDontSee('id="staff-list-title"', false)->assertDontSee('id="shift-dialog-title"', false);
            }
        }
    }

    public function test_legacy_dashboard_bookmarks_redirect_to_the_canonical_feature_and_preserve_filters(): void
    {
        $this->actingAs($this->owner());
        foreach (config('admin_pages') as $page => $definition) {
            $this->get(route('admin.dashboard', ['tab' => $page, 'date' => '2026-11-04']))
                ->assertRedirect(route($definition['route'], ['date' => '2026-11-04']));
        }
        $this->get(route('admin.dashboard', ['tab' => 'crew']))->assertRedirect(route('admin.users.index'));
        $this->get(route('admin.dashboard', ['tab' => 'analytics']))->assertRedirect(route('admin.dashboard'));
        $this->get(route('admin.dashboard', ['tab' => '../../secret']))->assertNotFound();
    }

    #[TestWith(['orders', 'view-orders'])]
    #[TestWith(['inventory', 'manage-inventory'])]
    #[TestWith(['shifts', 'manage-crew'])]
    #[TestWith(['logs', 'view-reports'])]
    #[TestWith(['settings', 'manage-settings'])]
    public function test_canonical_feature_enforces_its_own_permission(string $page, string $ability): void
    {
        $user = $this->owner();
        $user->role = 'crew';
        $user->permissions = [];
        $user->save();
        $this->actingAs($user)->getJson(route(config('admin_pages.'.$page.'.route')))->assertForbidden();
        $user->permissions = [$ability];
        $user->save();
        $this->get(route(config('admin_pages.'.$page.'.route')))->assertOk()->assertViewHas('page', $page);
    }

    public function test_order_only_staff_can_open_orders_without_being_redirected_to_another_home_page(): void
    {
        $user = $this->owner();
        $user->role = 'crew';
        $user->permissions = ['view-orders'];
        $user->save();
        $this->actingAs($user)->get(route('admin.orders.index'))->assertOk()->assertViewHas('page', 'orders');
        $this->get(route('admin.inventory.index'))->assertForbidden();
    }

    public function test_guests_cannot_open_operational_pages_and_legacy_crew_home_redirects_to_own_schedule(): void
    {
        $this->getJson(route('admin.orders.index'))->assertUnauthorized();
        $this->actingAs($this->owner())->get(route('admin.crew.home'))->assertRedirect(route('admin.my-schedule'));
    }

    #[TestWith(['/admin/api/dashboard/orders'])]
    #[TestWith(['/admin/api/dashboard/products'])]
    public function test_paginated_data_endpoints_reject_unbounded_or_invalid_page_sizes(string $endpoint): void
    {
        $this->actingAs($this->owner())->getJson($endpoint.'?per_page=100000&page=0')
            ->assertUnprocessable()->assertJsonValidationErrors(['per_page', 'page']);
        $this->getJson($endpoint.'?per_page=150')->assertOk()->assertJsonPath('per_page', 150);
    }

    public function test_old_configuration_cache_can_load_the_new_page_definitions(): void
    {
        $owner = $this->owner();
        config(['admin_pages' => null]);
        $this->app->getProvider(AppServiceProvider::class)->boot();

        $this->actingAs($owner)->get(route('admin.orders.index'))->assertOk()->assertViewHas('page', 'orders');
    }

    public function test_audit_log_does_not_require_the_unrelated_financial_report_schema(): void
    {
        $owner = $this->owner();
        Schema::table('orders', fn ($table) => $table->dropColumn('grand_total'));

        $this->actingAs($owner)->get(route('admin.activity-logs.index'))->assertOk();
        $this->getJson(route('admin.logs.index'))->assertOk();
    }

    public function test_integration_tokens_are_not_serialized_into_pages_or_settings_responses(): void
    {
        $owner = $this->owner();
        $owner->shop->update(['gobiz_access_token' => 'private-access-example', 'gobiz_refresh_token' => 'private-refresh-example']);

        $this->actingAs($owner)->get(route('admin.settings.index'))->assertOk()
            ->assertDontSee('private-access-example')->assertDontSee('private-refresh-example');
        $this->postJson('/admin/api/settings', ['name' => $owner->shop->name])->assertOk()
            ->assertJsonMissingPath('shop.gobiz_access_token')->assertJsonMissingPath('shop.gobiz_refresh_token');
        $this->assertSame('private-access-example', $owner->shop->fresh()->gobiz_access_token);
    }
}
