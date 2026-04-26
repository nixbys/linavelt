<x-layouts.app :title="config('linavelt.onboarding.title')">
    @php
        $onboarding = config('linavelt.onboarding', []);
        $domains = $onboarding['domains'] ?? [];
        $timeline = $onboarding['timeline'] ?? [];
        $savedSelections = $savedSelections ?? [];
        $onboardingCompletedAt = $onboardingCompletedAt ?? null;
        $moduleGenerationStatus = $moduleGenerationStatus ?? null;
        $moduleGenerationCompletedAt = $moduleGenerationCompletedAt ?? null;

        $summaryItems = [];

        foreach ($domains as $domain) {
            $key = $domain['key'] ?? null;

            if (! $key || ! isset($savedSelections[$key])) {
                continue;
            }

            $summaryItems[] = [
                'name' => $domain['name'],
                'value' => $savedSelections[$key],
            ];
        }

        $totalDomains = count($domains);
        $completedDomains = count($summaryItems);
        $completionPercentage = $totalDomains > 0 ? (int) round(($completedDomains / $totalDomains) * 100) : 0;
    @endphp

    <section class="space-y-6">
        <div class="overflow-hidden rounded-3xl border border-orange-200/20 bg-gradient-to-br from-[#1b1f3a] via-[#111526] to-[#2a160f] p-6 sm:p-8">
            <p class="mb-3 text-xs font-semibold uppercase tracking-[0.18em] text-orange-200/85">Stack Definition Flow</p>
            <h1 class="text-3xl font-semibold tracking-tight text-white sm:text-4xl">
                {{ $onboarding['title'] ?? 'Builder Onboarding' }}
            </h1>
            <p class="mt-3 max-w-3xl text-sm leading-relaxed text-slate-200/85 sm:text-base">
                {{ $onboarding['subtitle'] ?? '' }}
            </p>

            @if ($onboardingCompletedAt)
                <div class="mt-4 inline-flex items-center rounded-full border border-emerald-300/40 bg-emerald-400/15 px-3 py-1 text-xs font-semibold text-emerald-100">
                    Last saved {{ $onboardingCompletedAt->diffForHumans() }}
                </div>
            @endif

            <div class="mt-4 max-w-sm">
                <div class="flex items-center justify-between text-xs font-semibold uppercase tracking-[0.12em] text-orange-100/90">
                    <span>Profile completion</span>
                    <span>{{ $completionPercentage }}%</span>
                </div>
                <div class="mt-2 h-2 rounded-full bg-white/15">
                    <div class="h-2 rounded-full bg-orange-400 transition-all" style="width: {{ $completionPercentage }}%"></div>
                </div>
            </div>

            <div class="mt-6 flex flex-wrap gap-3">
                <a href="{{ route('dashboard') }}" class="rounded-xl border border-white/20 bg-white/10 px-4 py-2 text-sm font-medium text-white transition hover:bg-white/20">
                    Return to Dashboard
                </a>
                <a href="{{ route('home') }}" class="rounded-xl border border-orange-300/35 bg-orange-500 px-4 py-2 text-sm font-semibold text-[#23140e] transition hover:bg-orange-400">
                    View Landing Page
                </a>
            </div>
        </div>

        <div class="grid gap-4 lg:grid-cols-[1.35fr_0.85fr]">
            <article class="rounded-2xl border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900">
                <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">Choose Technology Preferences</h2>
                <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-300">
                    These selections map directly to how your project gets structured and automated.
                </p>

                @if (session('status'))
                    <div class="mt-4 rounded-xl border border-emerald-300/50 bg-emerald-100 px-3 py-2 text-sm font-medium text-emerald-800 dark:border-emerald-500/40 dark:bg-emerald-900/30 dark:text-emerald-200">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('builder.onboarding.update') }}" class="mt-5 space-y-4">
                    @csrf
                    @foreach ($domains as $domain)
                        @php
                            $domainKey = $domain['key'];
                            $selectedValue = old("preferences.$domainKey", $savedSelections[$domainKey] ?? null);
                        @endphp
                        <div class="rounded-xl border border-zinc-200 p-4 dark:border-zinc-700">
                            <p class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">{{ $domain['name'] }}</p>
                            <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">{{ $domain['description'] }}</p>
                            <label class="mt-3 block text-xs font-medium uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
                                Preferred Option
                                <select class="mt-2 w-full rounded-lg border border-zinc-300 bg-zinc-50 px-3 py-2 text-sm text-zinc-800 focus:border-orange-400 focus:outline-none dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-100" name="preferences[{{ $domainKey }}]">
                                    @foreach (($domain['options'] ?? []) as $option)
                                        <option value="{{ $option }}" @selected($selectedValue === $option)>{{ $option }}</option>
                                    @endforeach
                                </select>
                            </label>
                            @error("preferences.$domainKey")
                                <p class="mt-2 text-xs font-medium text-rose-600 dark:text-rose-300">{{ $message }}</p>
                            @enderror
                        </div>
                    @endforeach

                    <button type="submit" class="w-full rounded-xl bg-orange-500 px-4 py-2 text-sm font-semibold text-[#26170f] transition hover:bg-orange-400">
                        Save Preferences
                    </button>
                </form>
            </article>

            <aside class="space-y-4">
                <article class="rounded-2xl border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900">
                    <h3 class="text-base font-semibold text-zinc-900 dark:text-zinc-100">Saved Stack Profile</h3>
                    @if (count($summaryItems))
                        <ul class="mt-3 space-y-2">
                            @foreach ($summaryItems as $item)
                                <li class="rounded-lg border border-zinc-200 px-3 py-2 dark:border-zinc-700">
                                    <p class="text-xs uppercase tracking-[0.12em] text-zinc-500 dark:text-zinc-400">{{ $item['name'] }}</p>
                                    <p class="mt-1 text-sm font-medium text-zinc-800 dark:text-zinc-100">{{ $item['value'] }}</p>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="mt-3 text-sm text-zinc-600 dark:text-zinc-300">
                            No preferences saved yet. Choose options and save to build your profile.
                        </p>
                    @endif
                </article>

                <article class="rounded-2xl border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900">
                    <h3 class="text-base font-semibold text-zinc-900 dark:text-zinc-100">Module Generation</h3>
                    @if ($moduleGenerationStatus)
                        @php
                            $statusColor = match ($moduleGenerationStatus) {
                                'pending'  => 'border-amber-300/50 bg-amber-100 text-amber-800 dark:border-amber-500/40 dark:bg-amber-900/30 dark:text-amber-200',
                                'running'  => 'border-blue-300/50 bg-blue-100 text-blue-800 dark:border-blue-500/40 dark:bg-blue-900/30 dark:text-blue-200',
                                'complete' => 'border-emerald-300/50 bg-emerald-100 text-emerald-800 dark:border-emerald-500/40 dark:bg-emerald-900/30 dark:text-emerald-200',
                                'failed'   => 'border-rose-300/50 bg-rose-100 text-rose-800 dark:border-rose-500/40 dark:bg-rose-900/30 dark:text-rose-200',
                                default    => 'border-zinc-300/50 bg-zinc-100 text-zinc-800 dark:border-zinc-500/40 dark:bg-zinc-800 dark:text-zinc-200',
                            };
                            $statusLabel = match ($moduleGenerationStatus) {
                                'pending'  => 'Pending',
                                'running'  => 'Running…',
                                'complete' => 'Complete',
                                'failed'   => 'Failed',
                                default    => ucfirst($moduleGenerationStatus),
                            };
                        @endphp
                        <span class="mt-2 inline-flex items-center rounded-full border px-3 py-1 text-xs font-semibold {{ $statusColor }}">
                            {{ $statusLabel }}
                        </span>
                        @if ($moduleGenerationStatus === 'running')
                            <div class="mt-3 h-1.5 w-full overflow-hidden rounded-full bg-zinc-200 dark:bg-zinc-700">
                                <div class="h-1.5 animate-pulse rounded-full bg-blue-500" style="width: 60%"></div>
                            </div>
                        @elseif ($moduleGenerationStatus === 'complete' && $moduleGenerationCompletedAt)
                            <p class="mt-2 text-xs text-zinc-500 dark:text-zinc-400">
                                Completed {{ $moduleGenerationCompletedAt->diffForHumans() }}.
                            </p>
                        @elseif ($moduleGenerationStatus === 'failed')
                            <p class="mt-2 text-xs text-rose-600 dark:text-rose-300">
                                Re-save preferences to retry.
                            </p>
                        @endif
                    @else
                        <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-300">
                            Generation will be queued when you save your preferences.
                        </p>
                    @endif
                </article>

                <article class="rounded-2xl border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900">
                    <h3 class="text-base font-semibold text-zinc-900 dark:text-zinc-100">Onboarding Timeline</h3>
                    <ol class="mt-3 space-y-2">
                        @foreach ($timeline as $index => $phase)
                            <li class="flex items-start gap-3 rounded-lg border border-zinc-200 px-3 py-2 dark:border-zinc-700">
                                <span class="mt-0.5 inline-flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-orange-500/15 text-xs font-semibold text-orange-600 dark:text-orange-300">{{ $index + 1 }}</span>
                                <span class="text-sm text-zinc-700 dark:text-zinc-200">{{ $phase }}</span>
                            </li>
                        @endforeach
                    </ol>
                </article>

                <article class="rounded-2xl border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900">
                    <h3 class="text-base font-semibold text-zinc-900 dark:text-zinc-100">Next Action</h3>
                    <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-300">
                        When your selections are finalized, the next phase is generating modules and page scaffolds.
                    </p>
                    <a href="{{ route('dashboard') }}" class="mt-4 block w-full rounded-xl bg-orange-500 px-4 py-2 text-center text-sm font-semibold text-[#26170f] transition hover:bg-orange-400">
                        Continue to Dashboard
                    </a>
                </article>
            </aside>
        </div>
    </section>
</x-layouts.app>
