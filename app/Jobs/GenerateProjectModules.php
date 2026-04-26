<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class GenerateProjectModules implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $timeout = 120;

    public function __construct(public readonly int $userId) {}

    public function handle(): void
    {
        $user = User::find($this->userId);

        if (! $user) {
            return;
        }

        $user->forceFill([
            'module_generation_status' => 'running',
            'module_generation_started_at' => now(),
            'module_generation_completed_at' => null,
        ])->save();

        try {
            $preferences = $user->onboarding_preferences ?? [];
            $templates = config('linavelt.module_templates', []);
            $projectBase = "projects/{$user->id}";
            $generatedPaths = [];

            foreach ($preferences as $domainKey => $selectedOption) {
                $domainTemplates = $templates[$domainKey][$selectedOption] ?? [];

                foreach ($domainTemplates as $template) {
                    $path = $projectBase.'/'.ltrim($template['path'], '/');
                    Storage::put($path, $template['stub']);
                    $generatedPaths[] = $path;
                }
            }

            Storage::put(
                "{$projectBase}/generation-manifest.json",
                json_encode([
                    'generated_at' => now()->toIso8601String(),
                    'user_id' => $user->id,
                    'preferences' => $preferences,
                    'files' => $generatedPaths,
                ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
            );

            $user->forceFill([
                'module_generation_status' => 'complete',
                'module_generation_completed_at' => now(),
            ])->save();

            Log::info('GenerateProjectModules completed', [
                'user_id' => $user->id,
                'files_generated' => count($generatedPaths),
            ]);
        } catch (\Throwable $e) {
            $user->forceFill([
                'module_generation_status' => 'failed',
            ])->save();

            Log::error('GenerateProjectModules failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        $user = User::find($this->userId);

        if ($user) {
            $user->forceFill(['module_generation_status' => 'failed'])->save();
        }
    }
}
