<x-layouts.app :title="__('Dashboard')">
    @php
        $dashboard = config('linavelt.dashboard', []);
        $metrics = $dashboard['metrics'] ?? [];
        $workstreams = $dashboard['workstreams'] ?? [];
        $domains = config('linavelt.onboarding.domains', []);

        $userSelections = auth()->user()?->onboarding_preferences ?? [];
        $onboardingCompletedAt = auth()->user()?->onboarding_completed_at;
        $moduleGenerationStatus = auth()->user()?->module_generation_status;
        $moduleGenerationCompletedAt = auth()->user()?->module_generation_completed_at;

        $selectionCards = [];

        foreach ($domains as $domain) {
            $key = $domain['key'] ?? null;

            if (! $key || ! isset($userSelections[$key])) {
                continue;
            }

            $selectionCards[] = [
                'name' => $domain['name'],
                'value' => $userSelections[$key],
            ];
        }

        $totalDomains = count($domains);
        $completedDomains = count($selectionCards);
        $completionPercentage = $totalDomains > 0 ? (int) round(($completedDomains / $totalDomains) * 100) : 0;
    @endphp

    <div class="space-y-5">
        <section class="overflow-hidden rounded-3xl border border-orange-200/20 bg-gradient-to-br from-[#1b1f3a] via-[#111526] to-[#2a160f] p-6 sm:p-8">
            <p class="text-xs font-semibold uppercase tracking-[0.17em] text-orange-200/80">{{ $dashboard['kicker'] ?? '' }}</p>
            <h1 class="mt-2 text-3xl font-semibold tracking-tight text-white sm:text-4xl">
                {{ $dashboard['title'] ?? '' }}
            </h1>
            <p class="mt-3 max-w-3xl text-sm leading-relaxed text-slate-200/85 sm:text-base">
                {{ $dashboard['description'] ?? '' }}
            </p>

            <div class="mt-6 flex flex-wrap gap-3">
                <a href="{{ route('builder.onboarding') }}" class="rounded-xl border border-orange-300/35 bg-orange-500 px-4 py-2 text-sm font-semibold text-[#23140e] transition hover:bg-orange-400">
                    {{ $onboardingCompletedAt ? 'Update Stack Profile' : 'Continue Onboarding' }}
                </a>
                <a href="{{ route('blog.index') }}" class="rounded-xl border border-white/20 bg-white/10 px-4 py-2 text-sm font-medium text-white transition hover:bg-white/20">
                    Read Product Notes
                </a>
            </div>
        </section>

        <section class="grid gap-4 md:grid-cols-4">
            @foreach ($metrics as $metric)
                <article class="rounded-2xl border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900">
                    <p class="text-xs uppercase tracking-[0.15em] text-zinc-500 dark:text-zinc-400">{{ $metric['label'] }}</p>
                    <p class="mt-2 text-2xl font-semibold text-zinc-900 dark:text-zinc-100">{{ $metric['value'] }}</p>
                    <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-300">{{ $metric['hint'] }}</p>
                </article>
            @endforeach

            <article class="rounded-2xl border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900">
                <p class="text-xs uppercase tracking-[0.15em] text-zinc-500 dark:text-zinc-400">Stack Profile Completion</p>
                <p class="mt-2 text-2xl font-semibold text-zinc-900 dark:text-zinc-100">{{ $completionPercentage }}%</p>
                <div class="mt-3 h-2 rounded-full bg-zinc-200 dark:bg-zinc-700">
                    <div class="h-2 rounded-full bg-orange-500 transition-all" style="width: {{ $completionPercentage }}%"></div>
                </div>
                <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-300">{{ $completedDomains }} of {{ $totalDomains }} onboarding domains selected.</p>
            </article>
        </section>

        <section class="grid gap-4 lg:grid-cols-[1.1fr_1fr_1fr_1fr]">
            <article class="rounded-2xl border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900">
                <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">Saved Stack Profile</h2>
                @if ($onboardingCompletedAt)
                    <p class="mt-1 text-xs font-medium uppercase tracking-[0.14em] text-emerald-600 dark:text-emerald-300">
                        Completed {{ $onboardingCompletedAt->diffForHumans() }}
                    </p>
                @endif

                @if (count($selectionCards))
                    <ul class="mt-3 space-y-2">
                        @foreach ($selectionCards as $selection)
                            <li class="rounded-lg border border-zinc-200 px-3 py-2 dark:border-zinc-700">
                                <p class="text-xs uppercase tracking-[0.12em] text-zinc-500 dark:text-zinc-400">{{ $selection['name'] }}</p>
                                <p class="mt-1 text-sm font-medium text-zinc-800 dark:text-zinc-100">{{ $selection['value'] }}</p>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="mt-3 text-sm text-zinc-600 dark:text-zinc-300">
                        No profile selections saved yet.
                    </p>
                    <a href="{{ route('builder.onboarding') }}" class="mt-3 inline-flex rounded-lg border border-orange-300/40 bg-orange-500 px-3 py-2 text-xs font-semibold text-[#23140e] transition hover:bg-orange-400">
                        Complete Onboarding
                    </a>
                @endif
            </article>

            @foreach ($workstreams as $stream)
                <article class="rounded-2xl border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900">
                    <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">{{ $stream['title'] }}</h2>
                    <p class="mt-2 text-sm leading-relaxed text-zinc-600 dark:text-zinc-300">
                        {{ $stream['text'] }}
                    </p>
                </article>
            @endforeach
        </section>

        @if ($moduleGenerationStatus)
            <section class="rounded-2xl border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">Module Generation</h2>
                        <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-300">
                            Background orchestration of project modules based on your stack profile.
                        </p>
                    </div>

                    @php
                        $statusColor = match ($moduleGenerationStatus) {
                            'pending'  => 'border-amber-300/50 bg-amber-100 text-amber-800 dark:border-amber-500/40 dark:bg-amber-900/30 dark:text-amber-200',
                            'running'  => 'border-blue-300/50 bg-blue-100 text-blue-800 dark:border-blue-500/40 dark:bg-blue-900/30 dark:text-blue-200',
                            'complete' => 'border-emerald-300/50 bg-emerald-100 text-emerald-800 dark:border-emerald-500/40 dark:bg-emerald-900/30 dark:text-emerald-200',
                            'failed'   => 'border-rose-300/50 bg-rose-100 text-rose-800 dark:border-rose-500/40 dark:bg-rose-900/30 dark:text-rose-200',
                            default    => 'border-zinc-300/50 bg-zinc-100 text-zinc-800 dark:border-zinc-500/40 dark:bg-zinc-800 dark:text-zinc-200',
                        };
                        $statusLabel = match ($moduleGenerationStatus) {
                            'pending'  => 'Pending — queued for execution',
                            'running'  => 'Running — generating modules…',
                            'complete' => 'Complete',
                            'failed'   => 'Failed — will retry',
                            default    => ucfirst($moduleGenerationStatus),
                        };
                    @endphp

                    <span class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-semibold {{ $statusColor }}">
                        {{ $statusLabel }}
                    </span>
                </div>

                @if ($moduleGenerationStatus === 'running')
                    <div class="mt-4 h-2 w-full overflow-hidden rounded-full bg-zinc-200 dark:bg-zinc-700">
                        <div class="h-2 animate-pulse rounded-full bg-blue-500" style="width: 60%"></div>
                    </div>
                @elseif ($moduleGenerationStatus === 'complete' && $moduleGenerationCompletedAt)
                    <p class="mt-3 text-xs text-zinc-500 dark:text-zinc-400">
                        Completed {{ $moduleGenerationCompletedAt->diffForHumans() }}.
                        Project files are stored and ready.
                    </p>
                @elseif ($moduleGenerationStatus === 'failed')
                    <p class="mt-3 text-xs text-rose-600 dark:text-rose-300">
                        Generation encountered an error. Re-save your stack profile to re-queue.
                    </p>
                @endif
            </section>
        @endif
    </div>
</x-layouts.app>
