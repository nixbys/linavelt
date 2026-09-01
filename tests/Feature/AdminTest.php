<?php

namespace Tests\Feature;

use App\Jobs\GenerateProjectModules;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_from_admin_dashboard(): void
    {
        $response = $this->get(route('admin.dashboard'));

        $response->assertRedirect(route('login'));
    }

    public function test_non_admin_users_cannot_visit_admin_dashboard(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get(route('admin.dashboard'));

        $response->assertStatus(403);
    }

    public function test_admin_users_can_visit_admin_dashboard(): void
    {
        /** @var User $user */
        $user = User::factory()->admin()->create();
        $this->actingAs($user);

        $response = $this->get(route('admin.dashboard'));

        $response->assertStatus(200);
    }

    public function test_unverified_users_are_redirected_from_admin_dashboard(): void
    {
        /** @var User $user */
        $user = User::factory()->unverified()->create();
        $this->actingAs($user);

        $response = $this->get(route('admin.dashboard'));

        $response->assertRedirect(route('verification.notice'));
    }

    public function test_admin_dashboard_shows_security_and_module_generation_summaries(): void
    {
        /** @var User $user */
        $user = User::factory()->admin()->create([
            'module_generation_status' => 'failed',
            'onboarding_preferences' => [
                'frontend_layer' => 'Livewire + Flux',
            ],
        ]);

        $this->actingAs($user);

        $response = $this->get(route('admin.dashboard'));

        $response
            ->assertOk()
            ->assertSeeText('Automation Report')
            ->assertSeeText('Module Generation Summary')
            ->assertSeeText('Retry generation')
            ->assertSeeText('Security Observability');
    }

    public function test_admin_can_retry_failed_module_generation(): void
    {
        Queue::fake();

        /** @var User $user */
        $user = User::factory()->create([
            'module_generation_status' => 'failed',
            'onboarding_preferences' => [
                'frontend_layer' => 'Livewire + Flux',
                'backend_layer' => 'Laravel Monolith',
                'data_integrations' => 'MySQL + Queues',
            ],
        ]);

        /** @var User $actor */
        $actor = User::factory()->admin()->create();

        $this->actingAs($actor)
            ->post(route('admin.module-generation.retry', $user))
            ->assertRedirect();

        Queue::assertPushed(GenerateProjectModules::class, function ($job) use ($user) {
            return $job->userId === $user->id;
        });

        $user->refresh();

        $this->assertSame('pending', $user->module_generation_status);
        $this->assertNull($user->module_generation_started_at);
        $this->assertNull($user->module_generation_completed_at);
    }
}
