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
            'latestRevision' => $user?->builderRevisions()->latest()->first(),
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

        \App\Models\BuilderRevision::create([
            'user_id' => $request->user()->id,
            'status' => 'draft',
            'payload' => $validated['preferences'],
        ]);

        GenerateProjectModules::dispatch($request->user()->id);

        return redirect()
            ->route('builder.onboarding')
            ->with('status', 'Onboarding preferences saved. Module generation queued.');
    }

    public function publishRevision(Request $request): RedirectResponse
    {
        $revision = $request->user()->builderRevisions()->latest()->first();

        if (! $revision) {
            return back()->with('status', 'No revision is available to publish.');
        }

        $revision->forceFill([
            'status' => 'published',
            'published_at' => now(),
            'rolled_back_at' => null,
        ])->save();

        return back()->with('status', 'Latest revision published.');
    }

    public function rollbackRevision(Request $request): RedirectResponse
    {
        $revision = $request->user()->builderRevisions()->where('status', 'published')->latest('published_at')->first();

        if (! $revision) {
            return back()->with('status', 'No published revision is available to roll back.');
        }

        $request->user()->forceFill([
            'onboarding_preferences' => $revision->payload,
            'module_generation_status' => 'pending',
            'module_generation_started_at' => null,
            'module_generation_completed_at' => null,
        ])->save();

        $revision->forceFill([
            'status' => 'rolled_back',
            'rolled_back_at' => now(),
        ])->save();

        GenerateProjectModules::dispatch($request->user()->id);

        return back()->with('status', 'Latest published revision rolled back and module generation re-queued.');
    }
}
