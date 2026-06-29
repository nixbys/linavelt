<x-layouts.app :title="__('Dashboard')">
    @php
        $user = auth()->user();
        $dashboard = config('linavelt.dashboard', []);
        $metrics = $dashboard['metrics'] ?? [];
        $workstreams = $dashboard['workstreams'] ?? [];
        $domains = config('linavelt.onboarding.domains', []);

        $userSelections = $user?->onboarding_preferences ?? [];
        $onboardingCompletedAt = $user?->onboarding_completed_at;
        $moduleGenerationStatus = $user?->module_generation_status;
        $moduleGenerationCompletedAt = $user?->module_generation_completed_at;

        $selectionCards = [];
        foreach ($domains as $domain) {
            $key = $domain['key'] ?? null;
            if ($key && isset($userSelections[$key])) {
                $selectionCards[] = ['name' => $domain['name'], 'value' => $userSelections[$key]];
            }
        }

        $totalDomains = count($domains);
        $completedDomains = count($selectionCards);
        $completionPercentage = $totalDomains > 0 ? (int) round(($completedDomains / $totalDomains) * 100) : 0;

        $statusMap = [
            'pending'  => ['pill' => 'border-amber-700/50 bg-amber-900/30 text-amber-300',  'label' => 'Pending — queued'],
            'running'  => ['pill' => 'border-blue-700/50 bg-blue-900/30 text-blue-300',     'label' => 'Running — generating…'],
            'complete' => ['pill' => 'border-emerald-700/50 bg-emerald-900/30 text-emerald-300', 'label' => 'Complete'],
            'failed'   => ['pill' => 'border-rose-700/50 bg-rose-900/30 text-rose-300',     'label' => 'Failed — re-save to retry'],
        ];
        $statusInfo = $statusMap[$moduleGenerationStatus] ?? ['pill' => 'border-zinc-700 bg-zinc-800 text-zinc-400', 'label' => ucfirst((string) $moduleGenerationStatus)];
    @endphp

    <div class="space-y-5">

        {{-- ── Hero ────────────────────────────────────────────────── --}}
        <section class="overflow-hidden rounded-3xl border border-zinc-700/40 bg-gradient-to-br from-zinc-900 via-zinc-950 to-zinc-900 p-6 sm:p-8">
            <p class="text-xs font-semibold uppercase tracking-widest text-zinc-500">{{ $dashboard['kicker'] ?? 'Visual Builder' }}</p>
            <h1 class="mt-2 text-3xl font-semibold tracking-tight text-zinc-100 sm:text-4xl">
                {{ $dashboard['title'] ?? 'Welcome to Linavelt' }}
            </h1>
            <p class="mt-3 max-w-2xl text-sm leading-relaxed text-zinc-400 sm:text-base">
                {{ $dashboard['description'] ?? 'Build websites and apps visually, in any language, with any stack.' }}
            </p>

            <div class="mt-6 flex flex-wrap gap-3">
                <a href="{{ route('projects.index') }}"
                   class="inline-flex items-center gap-2 rounded-xl bg-white px-4 py-2 text-sm font-semibold text-zinc-900 transition-colors hover:bg-zinc-100">
                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.53 16.122a3 3 0 00-5.78 1.128 2.25 2.25 0 01-2.4 2.245 4.5 4.5 0 008.4-2.245c0-.399-.078-.78-.22-1.128zm0 0a15.998 15.998 0 003.388-1.62m-5.043-.025a15.994 15.994 0 011.622-3.395m3.42 3.42a15.995 15.995 0 004.764-4.648l3.876-5.814a1.151 1.151 0 00-1.597-1.597L14.146 6.32a15.996 15.996 0 00-4.649 4.763m3.42 3.42a6.776 6.776 0 00-3.42-3.42"/>
                    </svg>
                    Open Projects
                </a>
                <a href="{{ route('builder.onboarding') }}"
                   class="inline-flex items-center gap-2 rounded-xl border border-zinc-700 bg-zinc-800 px-4 py-2 text-sm font-medium text-zinc-300 transition-colors hover:bg-zinc-700 hover:text-zinc-100">
                    {{ $onboardingCompletedAt ? 'Update Stack Profile' : 'Complete Onboarding' }}
                </a>
            </div>
        </section>

        {{-- ── Metric cards ─────────────────────────────────────────── --}}
        @if(count($metrics))
        <section class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($metrics as $metric)
                <article class="rounded-2xl border border-zinc-700 bg-zinc-900 p-5">
                    <p class="text-xs font-semibold uppercase tracking-widest text-zinc-500">{{ $metric['label'] }}</p>
                    <p class="mt-2 text-2xl font-semibold text-zinc-100">{{ $metric['value'] }}</p>
                    <p class="mt-1.5 text-sm text-zinc-400">{{ $metric['hint'] }}</p>
                </article>
            @endforeach

            <article class="rounded-2xl border border-zinc-700 bg-zinc-900 p-5">
                <p class="text-xs font-semibold uppercase tracking-widest text-zinc-500">Stack Profile</p>
                <p class="mt-2 text-2xl font-semibold text-zinc-100">{{ $completionPercentage }}%</p>
                <div class="mt-3 h-1.5 rounded-full bg-zinc-800">
                    <div class="h-1.5 rounded-full bg-white transition-all" style="width: {{ $completionPercentage }}%"></div>
                </div>
                <p class="mt-2 text-sm text-zinc-400">{{ $completedDomains }} of {{ $totalDomains }} domains set</p>
            </article>
        </section>
        @endif

        {{-- ── Detail grid ──────────────────────────────────────────── --}}
        <section class="grid gap-4 lg:grid-cols-3">

            {{-- Stack profile --}}
            <article class="rounded-2xl border border-zinc-700 bg-zinc-900 p-5">
                <div class="flex items-center justify-between">
                    <h2 class="text-sm font-semibold uppercase tracking-widest text-zinc-500">Stack Profile</h2>
                    @if($onboardingCompletedAt)
                        <span class="text-xs text-zinc-600">{{ $onboardingCompletedAt->diffForHumans() }}</span>
                    @endif
                </div>

                @if(count($selectionCards))
                    <ul class="mt-4 space-y-2">
                        @foreach ($selectionCards as $selection)
                            <li class="rounded-lg border border-zinc-800 bg-zinc-800/50 px-3 py-2">
                                <p class="text-xs font-medium uppercase tracking-widest text-zinc-500">{{ $selection['name'] }}</p>
                                <p class="mt-0.5 text-sm font-medium text-zinc-200">{{ $selection['value'] }}</p>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="mt-4 text-sm text-zinc-500">No selections saved yet.</p>
                    <a href="{{ route('builder.onboarding') }}"
                       class="mt-3 inline-flex items-center gap-1.5 rounded-lg border border-zinc-700 bg-zinc-800 px-3 py-1.5 text-xs font-medium text-zinc-300 transition-colors hover:bg-zinc-700 hover:text-zinc-100">
                        Complete onboarding
                    </a>
                @endif
            </article>

            {{-- Workstreams --}}
            @foreach ($workstreams as $stream)
                <article class="rounded-2xl border border-zinc-700 bg-zinc-900 p-5">
                    <h2 class="text-sm font-semibold uppercase tracking-widest text-zinc-500">{{ $stream['title'] }}</h2>
                    <p class="mt-3 text-sm leading-relaxed text-zinc-400">{{ $stream['text'] }}</p>
                </article>
            @endforeach

        </section>

        {{-- ── Module generation status ─────────────────────────────── --}}
        @if($moduleGenerationStatus)
            <section class="rounded-2xl border border-zinc-700 bg-zinc-900 p-5">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h2 class="text-sm font-semibold uppercase tracking-widest text-zinc-500">Module Generation</h2>
                        <p class="mt-1 text-sm text-zinc-400">Background orchestration of project modules based on your stack profile.</p>
                    </div>
                    <span class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-semibold {{ $statusInfo['pill'] }}">
                        {{ $statusInfo['label'] }}
                    </span>
                </div>

                @if($moduleGenerationStatus === 'running')
                    <div class="mt-4 h-1.5 w-full overflow-hidden rounded-full bg-zinc-800">
                        <div class="h-1.5 animate-pulse rounded-full bg-blue-500" style="width: 60%"></div>
                    </div>
                @elseif($moduleGenerationStatus === 'complete' && $moduleGenerationCompletedAt)
                    <p class="mt-3 text-xs text-zinc-500">Completed {{ $moduleGenerationCompletedAt->diffForHumans() }}. Project files are stored and ready.</p>
                @elseif($moduleGenerationStatus === 'failed')
                    <p class="mt-3 text-xs text-rose-400">Generation encountered an error. Re-save your stack profile to re-queue.</p>
                @endif
            </section>
        @endif

    </div>
</x-layouts.app>
