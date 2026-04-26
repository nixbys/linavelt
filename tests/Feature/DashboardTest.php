<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_the_login_page(): void
    {
        $response = $this->get(route('dashboard'));
        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_users_can_visit_the_dashboard(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get(route('dashboard'));
        $response->assertStatus(200);
    }

    public function test_dashboard_displays_saved_stack_profile_for_authenticated_users(): void
    {
        $user = User::factory()->create([
            'onboarding_preferences' => [
                'frontend_layer' => 'Livewire + Flux',
                'backend_layer' => 'Laravel Monolith',
                'data_integrations' => 'MySQL + Queues',
            ],
            'onboarding_completed_at' => now()->subHour(),
        ]);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response
            ->assertOk()
            ->assertSeeText('Saved Stack Profile')
            ->assertSeeText('Livewire + Flux')
            ->assertSeeText('Completed');
    }
}
