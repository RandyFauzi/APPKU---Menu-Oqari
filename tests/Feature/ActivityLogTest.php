<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActivityLogTest extends TestCase
{
    use RefreshDatabase;

    private function owner(): User
    {
        return User::factory()->create(['shop_id' => Shop::factory()->create()->id, 'role' => 'owner']);
    }

    private function log(User $user, array $attributes = []): ActivityLog
    {
        $log = new ActivityLog;
        $log->forceFill(array_merge(['shop_id' => $user->shop_id, 'user_id' => $user->id, 'action' => 'user.created', 'description' => 'Menambahkan pegawai'], $attributes))->save();

        return $log;
    }

    public function test_logs_require_authentication_and_report_permission(): void
    {
        $this->getJson(route('admin.logs.index'))->assertUnauthorized();
        $user = $this->owner();
        $user->role = 'crew';
        $user->permissions = [];
        $user->save();

        $this->actingAs($user)->getJson(route('admin.logs.index'))->assertForbidden();
    }

    public function test_only_own_shop_logs_are_returned_even_when_search_matches_another_shop(): void
    {
        $owner = $this->owner();
        $own = $this->log($owner);
        $foreign = $this->log($this->owner());

        $this->actingAs($owner)->getJson(route('admin.logs.index', ['search' => 'pegawai']))
            ->assertOk()->assertJsonPath('total', 1)->assertJsonPath('data.0.id', $own->id)
            ->assertJsonMissing(['id' => $foreign->id]);
    }

    public function test_logs_paginate_in_stable_newest_first_order(): void
    {
        $owner = $this->owner();
        for ($index = 0; $index < 21; $index++) {
            $this->log($owner, ['created_at' => '2026-11-04 08:00:00']);
        }

        $this->actingAs($owner)->getJson(route('admin.logs.index'))
            ->assertJsonCount(20, 'data')->assertJsonPath('total', 21)->assertJsonPath('data.0.id', 21);
        $this->getJson(route('admin.logs.index', ['page' => 2]))
            ->assertJsonCount(1, 'data')->assertJsonPath('data.0.id', 1);
    }

    public function test_filters_match_actor_action_and_date_and_return_empty_results(): void
    {
        $owner = $this->owner();
        $this->log($owner, ['created_at' => '2026-11-04 08:00:00']);
        $this->log($owner, ['action' => 'register.closed', 'created_at' => '2026-11-03 08:00:00']);

        $this->actingAs($owner)->getJson(route('admin.logs.index', ['search' => $owner->name, 'date' => '2026-11-04']))
            ->assertJsonPath('total', 1)->assertJsonPath('data.0.action', 'user.created');
        $this->getJson(route('admin.logs.index', ['search' => 'register.closed']))->assertJsonPath('total', 1);
        $this->getJson(route('admin.logs.index', ['search' => 'nonexistent']))->assertJsonCount(0, 'data');
    }

    public function test_system_logs_remain_visible_and_sensitive_audit_payload_is_not_exposed(): void
    {
        $owner = $this->owner();
        $this->log($owner, ['user_id' => null, 'new_values' => ['password' => 'secret'], 'ip_address' => '127.0.0.1']);

        $this->actingAs($owner)->getJson(route('admin.logs.index'))
            ->assertJsonPath('data.0.user', 'Sistem / akun dihapus')
            ->assertJsonMissingPath('data.0.new_values')->assertJsonMissingPath('data.0.ip_address');
    }

    public function test_invalid_filters_return_validation_errors(): void
    {
        $this->actingAs($this->owner())->getJson(route('admin.logs.index', ['page' => 0, 'date' => 'invalid', 'search' => str_repeat('a', 151)]))
            ->assertUnprocessable()->assertJsonValidationErrors(['page', 'date', 'search']);
    }
}
