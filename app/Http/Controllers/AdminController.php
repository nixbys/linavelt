<?php

namespace App\Http\Controllers;

use App\Jobs\GenerateProjectModules;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function dashboard(): View
    {
        $data = [
            'users' => 120,
            'posts' => 45,
            'settings' => 10,
        ];

        $moduleGenerationSummary = User::query()
            ->selectRaw('module_generation_status, count(*) as total')
            ->whereNotNull('module_generation_status')
            ->groupBy('module_generation_status')
            ->pluck('total', 'module_generation_status')
            ->all();

        $failedModuleUsers = User::query()
            ->where('module_generation_status', 'failed')
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'module_generation_completed_at']);

        return view('admin.dashboard', [
            'data' => $data,
            'moduleGenerationSummary' => $moduleGenerationSummary,
            'failedModuleUsers' => $failedModuleUsers,
            'automationReport' => $this->latestAutomationReport(),
        ]);
    }

    public function retryModuleGeneration(User $user): RedirectResponse
    {
        if (! $user->onboarding_preferences) {
            throw ValidationException::withMessages([
                'user' => 'The selected user does not have onboarding preferences to regenerate.',
            ]);
        }

        $user->forceFill([
            'module_generation_status' => 'pending',
            'module_generation_started_at' => null,
            'module_generation_completed_at' => null,
        ])->save();

        GenerateProjectModules::dispatch($user->id);

        return back()->with('status', sprintf('Queued module regeneration for %s.', $user->name));
    }

    private function latestAutomationReport(): array
    {
        $reportDirectory = base_path('mcp-server/security-reports');

        if (! is_dir($reportDirectory)) {
            return [
                'generated_at' => null,
                'dependabot_open' => null,
                'code_scanning_open' => null,
                'npm_root_total' => null,
                'npm_mcp_total' => null,
                'composer_advisories' => null,
            ];
        }

        $files = glob($reportDirectory.'/*.json') ?: [];
        sort($files);

        if ($files === []) {
            return [
                'generated_at' => null,
                'dependabot_open' => null,
                'code_scanning_open' => null,
                'npm_root_total' => null,
                'npm_mcp_total' => null,
                'composer_advisories' => null,
            ];
        }

        $report = json_decode((string) file_get_contents($files[array_key_last($files)]), true) ?: [];

        return [
            'generated_at' => $report['timestamp'] ?? null,
            'dependabot_open' => $report['githubAlerts']['dependabotOpen'] ?? null,
            'code_scanning_open' => $report['githubAlerts']['codeScanningOpen'] ?? null,
            'npm_root_total' => $report['audits']['npmRoot']['summary']['total'] ?? null,
            'npm_mcp_total' => $report['audits']['npmMcpServer']['summary']['total'] ?? null,
            'composer_advisories' => $report['audits']['composer']['summary']['advisories'] ?? null,
        ];
    }
}
