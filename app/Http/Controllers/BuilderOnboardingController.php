<?php

namespace App\Http\Controllers;

use App\Jobs\GenerateProjectModules;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class BuilderOnboardingController extends Controller
{
    public function show(Request $request): View
    {
        $user = $request->user();

        return view('builder.onboarding', [
            'savedSelections' => $user?->onboarding_preferences ?? [],
            'onboardingCompletedAt' => $user?->onboarding_completed_at,
            'moduleGenerationStatus' => $user?->module_generation_status,
            'moduleGenerationCompletedAt' => $user?->module_generation_completed_at,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $domains = config('linavelt.onboarding.domains', []);
        $rules = [];

        foreach ($domains as $domain) {
            $key = $domain['key'] ?? null;

            if (! $key || ! isset($domain['options']) || ! is_array($domain['options'])) {
                continue;
            }

            $rules["preferences.$key"] = ['required', 'string', Rule::in($domain['options'])];
        }

        $validated = $request->validate($rules);

        $request->user()->forceFill([
            'onboarding_preferences' => $validated['preferences'],
            'onboarding_completed_at' => now(),
            'module_generation_status' => 'pending',
            'module_generation_started_at' => null,
            'module_generation_completed_at' => null,
        ])->save();

        GenerateProjectModules::dispatch($request->user()->id);

        return redirect()
            ->route('builder.onboarding')
            ->with('status', 'Onboarding preferences saved. Module generation queued.');
    }
}
