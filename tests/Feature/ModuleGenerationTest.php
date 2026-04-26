<?php

namespace Tests\Feature;

use App\Jobs\GenerateProjectModules;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ModuleGenerationTest extends TestCase
{
    use RefreshDatabase;

    public function test_saving_onboarding_preferences_dispatches_generate_job(): void
    {
        Queue::fake();

        $user = User::factory()->create();
        $preferences = $this->validPreferences();

        $this->actingAs($user)->post(route('builder.onboarding.update'), [
            'preferences' => $preferences,
        ]);

        Queue::assertPushed(GenerateProjectModules::class, function ($job) use ($user) {
            return $job->userId === $user->id;
        });
    }

    public function test_saving_preferences_sets_generation_status_to_pending(): void
    {
        Queue::fake();

        $user = User::factory()->create();

        $this->actingAs($user)->post(route('builder.onboarding.update'), [
            'preferences' => $this->validPreferences(),
        ]);

        $user->refresh();

        $this->assertSame('pending', $user->module_generation_status);
        $this->assertNull($user->module_generation_started_at);
        $this->assertNull($user->module_generation_completed_at);
    }

    public function test_generate_job_creates_project_files_and_marks_complete(): void
    {
        Storage::fake();

        $user = User::factory()->create([
            'onboarding_preferences' => $this->validPreferences(),
        ]);

        $job = new GenerateProjectModules($user->id);
        $job->handle();

        $user->refresh();

        $this->assertSame('complete', $user->module_generation_status);
        $this->assertNotNull($user->module_generation_completed_at);

        Storage::assertExists("projects/{$user->id}/generation-manifest.json");
    }

    public function test_generate_job_writes_frontend_layer_files(): void
    {
        Storage::fake();

        $user = User::factory()->create([
            'onboarding_preferences' => [
                'frontend_layer' => 'Livewire + Flux',
                'backend_layer' => 'Laravel Monolith',
                'data_integrations' => 'MySQL + Queues',
            ],
        ]);

        (new GenerateProjectModules($user->id))->handle();

        Storage::assertExists("projects/{$user->id}/resources/views/pages/home.blade.php");
        Storage::assertExists("projects/{$user->id}/app/Livewire/Pages/Home.php");
    }

    public function test_generate_job_marks_failed_on_storage_error(): void
    {
        // Use a real Storage fake but make the user ID non-existent after job is
        // created to simulate a mid-run failure path via failed() callback.
        Storage::fake();

        $user = User::factory()->create([
            'onboarding_preferences' => $this->validPreferences(),
        ]);

        $jobId = $user->id;

        // Delete the user to trigger early return (non-failure path), so instead
        // test the failed() method directly with a live user.
        $user2 = User::factory()->create([
            'module_generation_status' => 'running',
        ]);

        $job = new GenerateProjectModules($user2->id);
        $job->failed(new \RuntimeException('Simulated failure'));

        $user2->refresh();

        $this->assertSame('failed', $user2->module_generation_status);
    }

    public function test_generation_status_visible_on_onboarding_page(): void
    {
        $user = User::factory()->create([
            'onboarding_preferences' => $this->validPreferences(),
            'onboarding_completed_at' => now(),
            'module_generation_status' => 'complete',
            'module_generation_completed_at' => now(),
        ]);

        $response = $this->actingAs($user)->get(route('builder.onboarding'));

        $response
            ->assertOk()
            ->assertSeeText('Module Generation')
            ->assertSeeText('Complete');
    }

    public function test_generation_status_visible_on_dashboard(): void
    {
        $user = User::factory()->create([
            'onboarding_preferences' => $this->validPreferences(),
            'onboarding_completed_at' => now(),
            'module_generation_status' => 'running',
        ]);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response
            ->assertOk()
            ->assertSeeText('Module Generation')
            ->assertSeeText('Running');
    }

    public function test_generation_status_not_shown_before_first_save(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response
            ->assertOk()
            ->assertDontSeeText('Running — generating modules');
    }

    public function test_manifest_contains_correct_user_id_and_preferences(): void
    {
        Storage::fake();

        $preferences = $this->validPreferences();
        $user = User::factory()->create([
            'onboarding_preferences' => $preferences,
        ]);

        (new GenerateProjectModules($user->id))->handle();

        $manifest = json_decode(Storage::get("projects/{$user->id}/generation-manifest.json"), true);

        $this->assertSame($user->id, $manifest['user_id']);
        $this->assertSame($preferences, $manifest['preferences']);
        $this->assertIsArray($manifest['files']);
    }

    // -----------------------------------------------------------------------

    private function validPreferences(): array
    {
        $preferences = [];

        foreach (config('linavelt.onboarding.domains', []) as $domain) {
            $preferences[$domain['key']] = $domain['options'][0];
        }

        return $preferences;
    }
}
