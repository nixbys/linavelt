<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class BuilderOnboardingTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login_for_onboarding_page(): void
    {
        $response = $this->get(route('builder.onboarding'));

        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_users_can_view_onboarding_page(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('builder.onboarding'));

        $response->assertOk();
    }

    public function test_authenticated_users_can_save_onboarding_preferences(): void
    {
        Queue::fake();

        $user = User::factory()->create();

        $preferences = [];

        foreach (config('linavelt.onboarding.domains', []) as $domain) {
            $preferences[$domain['key']] = $domain['options'][0];
        }

        $response = $this->actingAs($user)->post(route('builder.onboarding.update'), [
            'preferences' => $preferences,
        ]);

        $response
            ->assertRedirect(route('builder.onboarding'))
            ->assertSessionHas('status', 'Onboarding preferences saved. Module generation queued.');

        $user->refresh();

        $this->assertSame($preferences, $user->onboarding_preferences);
        $this->assertNotNull($user->onboarding_completed_at);
        $this->assertSame('pending', $user->module_generation_status);

        $page = $this->actingAs($user)->get(route('builder.onboarding'));

        $page
            ->assertOk()
            ->assertSeeText('Saved Stack Profile')
            ->assertSeeText('Last saved')
            ->assertSeeText('Profile completion')
            ->assertSeeText($preferences['frontend_layer']);
    }

    public function test_onboarding_rejects_invalid_preference_values(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->from(route('builder.onboarding'))->post(route('builder.onboarding.update'), [
            'preferences' => [
                'frontend_layer' => 'Not a real option',
                'backend_layer' => 'Laravel Monolith',
                'data_integrations' => 'MySQL + Queues',
            ],
        ]);

        $response
            ->assertRedirect(route('builder.onboarding'))
            ->assertSessionHasErrors('preferences.frontend_layer');

        $user->refresh();

        $this->assertNull($user->onboarding_preferences);
        $this->assertNull($user->onboarding_completed_at);
    }
}
