<?php

namespace Tests\Feature;

use App\Models\Shop;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    #[TestWith(['owner'])]
    #[TestWith(['manager'])]
    #[TestWith(['crew'])]
    #[TestWith(['superadmin'])]
    public function test_legacy_dashboard_profile_redirects_to_the_single_profile_page(string $role): void
    {
        $user = User::factory()->create(['role' => $role, 'shop_id' => $role === 'superadmin' ? null : Shop::factory()->create(['onboarding_status' => 'completed'])->id]);

        $this->actingAs($user)->get(route('admin.dashboard', ['tab' => 'profile']))
            ->assertRedirect(route('profile.edit'));
        $this->get(route('profile.edit'))->assertOk()->assertSee('Profil Saya')->assertDontSee('Profile Settings');
    }

    public function test_dashboard_profile_links_point_to_the_canonical_page_and_old_write_endpoint_is_removed(): void
    {
        $owner = User::factory()->create(['role' => 'owner', 'shop_id' => Shop::factory()->create(['onboarding_status' => 'completed'])->id]);

        $response = $this->actingAs($owner)->get(route('admin.dashboard'))->assertOk()
            ->assertSee('href="'.route('profile.edit').'"', false)->assertDontSee('Profile Settings');
        $this->assertGreaterThanOrEqual(2, substr_count($response->getContent(), 'href="'.route('profile.edit').'"'));
        $this->postJson('/admin/api/profile', ['name' => 'Old handler', 'email' => 'old@example.test', 'password' => 'NewPassword123', 'password_confirmation' => 'NewPassword123'])->assertNotFound();
        $this->assertSame($owner->name, $owner->fresh()->name);
        $this->assertSame($owner->email, $owner->fresh()->email);
    }

    public function test_profile_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get('/profile');

        $response->assertOk();
    }

    public function test_profile_information_can_be_updated(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $user->refresh();

        $this->assertSame('Test User', $user->name);
        $this->assertSame('test@example.com', $user->email);
        $this->assertNull($user->email_verified_at);
    }

    public function test_email_verification_status_is_unchanged_when_the_email_address_is_unchanged(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Test User',
                'email' => $user->email,
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $this->assertNotNull($user->refresh()->email_verified_at);
    }

    public function test_user_can_delete_their_account(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->delete('/profile', [
                'password' => 'password',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/');

        $this->assertGuest();
        $this->assertNull($user->fresh());
    }

    public function test_correct_password_must_be_provided_to_delete_account(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from('/profile')
            ->delete('/profile', [
                'password' => 'wrong-password',
            ]);

        $response
            ->assertSessionHasErrorsIn('userDeletion', 'password')
            ->assertRedirect('/profile');

        $this->assertNotNull($user->fresh());
    }
}
