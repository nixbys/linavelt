@php
    $lang = collect(config('technologies.languages', []))->firstWhere('id', $project->language);
    $type = collect(config('technologies.project_types', []))->firstWhere('id', $project->type);
    $typeLabel = $type['label'] ?? ucfirst($project->type);
    $fwLabel = null;
    if ($project->framework && $project->language) {
        $fw = collect(config("technologies.frameworks.{$project->language}", []))->firstWhere('id', $project->framework);
        $fwLabel = $fw['label'] ?? null;
    }
    $statusMap = [
        'published' => 'bg-emerald-900/40 text-emerald-300 border-emerald-700/50',
        'draft'     => 'bg-zinc-800 text-zinc-400 border-zinc-700',
    ];
@endphp

<x-layouts.app :title="$project->name">

    {{-- Flash --}}
    @if(session('status'))
        <div class="mb-6 rounded-xl border border-emerald-500/40 bg-emerald-900/30 px-4 py-3 text-sm font-medium text-emerald-200">
            {{ session('status') }}
        </div>
    @endif

    {{-- ── Project hero ─────────────────────────────────────────── --}}
    <div class="mb-6 overflow-hidden rounded-3xl border border-zinc-700/40 bg-gradient-to-br from-zinc-900 via-zinc-950 to-zinc-900 p-6 sm:p-8">

        <div class="flex items-start justify-between gap-4">
            <div class="flex items-center gap-4">
                {{-- Language badge --}}
                @if($lang)
                    <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl text-base font-black"
                         style="background-color: {{ $lang['bg'] }}; color: {{ $lang['color'] }}; border: 1px solid {{ $lang['color'] }}40;">
                        {{ $lang['abbr'] }}
                    </div>
                @endif
                <div>
                    <p class="text-xs font-semibold uppercase tracking-widest text-zinc-500">{{ $typeLabel }}</p>
                    <h1 class="mt-0.5 text-2xl font-semibold tracking-tight text-zinc-100 sm:text-3xl">{{ $project->name }}</h1>
                    <div class="mt-1.5 flex flex-wrap items-center gap-2">
                        @if($lang)
                            <span class="text-sm text-zinc-400">{{ $lang['label'] }}</span>
                        @endif
                        @if($fwLabel)
                            <span class="text-zinc-600">·</span>
                            <span class="text-sm text-zinc-400">{{ $fwLabel }}</span>
                        @endif
                        <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold {{ $statusMap[$project->status] ?? $statusMap['draft'] }}">
                            {{ ucfirst($project->status) }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Primary actions --}}
            <div class="flex items-center gap-2 shrink-0">
                <a href="{{ route('projects.canvas', $project) }}"
                   class="inline-flex items-center gap-2 rounded-xl bg-white px-4 py-2 text-sm font-semibold text-zinc-900 transition-colors hover:bg-zinc-100">
                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487z"/>
                    </svg>
                    Open in Builder
                </a>

                @if($project->status !== 'published')
                    <form method="POST" action="{{ route('projects.publish', $project) }}">
                        @csrf
                        <button type="submit"
                                class="inline-flex items-center gap-2 rounded-xl border border-emerald-700/50 bg-emerald-900/30 px-4 py-2 text-sm font-semibold text-emerald-300 transition-colors hover:bg-emerald-800/50">
                            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Publish
                        </button>
                    </form>
                @endif
            </div>
        </div>

        {{-- Timestamps --}}
        <div class="mt-4 flex flex-wrap gap-4 text-xs text-zinc-600">
            <span>Created {{ $project->created_at->diffForHumans() }}</span>
            <span>·</span>
            <span>Updated {{ $project->updated_at->diffForHumans() }}</span>
            @if($project->published_at)
                <span>·</span>
                <span>Published {{ $project->published_at->diffForHumans() }}</span>
            @endif
        </div>
    </div>

    {{-- ── Detail grid ──────────────────────────────────────────── --}}
    <div class="grid gap-4 lg:grid-cols-3">

        {{-- Stack details --}}
        <article class="rounded-2xl border border-zinc-700 bg-zinc-900 p-5">
            <h3 class="text-xs font-semibold uppercase tracking-widest text-zinc-500">Tech Stack</h3>
            <div class="mt-4 space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-xs text-zinc-500">Project type</span>
                    <span class="text-sm font-medium text-zinc-200">{{ $typeLabel }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-xs text-zinc-500">Language</span>
                    <div class="flex items-center gap-1.5">
                        @if($lang)
                            <div class="h-4 w-4 rounded text-[9px] font-bold flex items-center justify-center"
                                 style="background-color: {{ $lang['bg'] }}; color: {{ $lang['color'] }};">
                                {{ substr($lang['abbr'], 0, 1) }}
                            </div>
                        @endif
                        <span class="text-sm font-medium text-zinc-200">{{ $lang['label'] ?? '—' }}</span>
                    </div>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-xs text-zinc-500">Framework</span>
                    <span class="text-sm font-medium text-zinc-200">{{ $fwLabel ?? 'None' }}</span>
                </div>
            </div>
        </article>

        {{-- Integrations --}}
        <article class="rounded-2xl border border-zinc-700 bg-zinc-900 p-5">
            <h3 class="text-xs font-semibold uppercase tracking-widest text-zinc-500">Integrations</h3>
            @if($project->integrations && count($project->integrations) > 0)
                <div class="mt-4 flex flex-wrap gap-1.5">
                    @foreach($project->integrations as $intId)
                        @php
                            $intLabel = $intId;
                            foreach (config('technologies.integrations', []) as $items) {
                                $found = collect($items)->firstWhere('id', $intId);
                                if ($found) { $intLabel = $found['label']; break; }
                            }
                        @endphp
                        <span class="inline-flex items-center rounded-full border border-zinc-700 bg-zinc-800 px-2.5 py-0.5 text-xs font-medium text-zinc-300">
                            {{ $intLabel }}
                        </span>
                    @endforeach
                </div>
            @else
                <p class="mt-4 text-sm text-zinc-600">No integrations configured.</p>
            @endif
        </article>

        {{-- Quick actions --}}
        <article class="rounded-2xl border border-zinc-700 bg-zinc-900 p-5">
            <h3 class="text-xs font-semibold uppercase tracking-widest text-zinc-500">Actions</h3>
            <div class="mt-4 space-y-2">
                <a href="{{ route('projects.canvas', $project) }}"
                   class="flex items-center gap-3 rounded-xl border border-zinc-700 bg-zinc-800 px-3 py-2.5 text-sm font-medium text-zinc-200 transition-colors hover:bg-zinc-700 hover:text-zinc-100">
                    <svg class="h-4 w-4 text-zinc-400 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.53 16.122a3 3 0 00-5.78 1.128 2.25 2.25 0 01-2.4 2.245 4.5 4.5 0 008.4-2.245c0-.399-.078-.78-.22-1.128zm0 0a15.998 15.998 0 003.388-1.62m-5.043-.025a15.994 15.994 0 011.622-3.395m3.42 3.42a15.995 15.995 0 004.764-4.648l3.876-5.814a1.151 1.151 0 00-1.597-1.597L14.146 6.32a15.996 15.996 0 00-4.649 4.763m3.42 3.42a6.776 6.776 0 00-3.42-3.42"/>
                    </svg>
                    Visual Builder
                </a>
                <a href="{{ route('extensions.index') }}"
                   class="flex items-center gap-3 rounded-xl border border-zinc-700 bg-zinc-800 px-3 py-2.5 text-sm font-medium text-zinc-200 transition-colors hover:bg-zinc-700 hover:text-zinc-100">
                    <svg class="h-4 w-4 text-zinc-400 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.25 6.087c0-.355.186-.676.401-.959.221-.29.349-.634.349-1.003 0-1.036-1.007-1.875-2.25-1.875s-2.25.84-2.25 1.875c0 .369.128.713.349 1.003.215.283.401.604.401.959v0a.64.64 0 01-.657.643 48.39 48.39 0 01-4.163-.3c.186 1.613.293 3.25.315 4.907a.656.656 0 01-.658.663v0c-.355 0-.676-.186-.959-.401a1.647 1.647 0 00-1.003-.349c-1.036 0-1.875 1.007-1.875 2.25s.84 2.25 1.875 2.25c.369 0 .713-.128 1.003-.349.283-.215.604-.401.959-.401v0c.31 0 .555.26.532.57a48.039 48.039 0 01-.642 5.056c1.518.19 3.058.309 4.616.354a.64.64 0 00.657-.643v0c0-.355-.186-.676-.401-.959a1.647 1.647 0 01-.349-1.003c0-1.035 1.008-1.875 2.25-1.875 1.243 0 2.25.84 2.25 1.875 0 .369-.128.713-.349 1.003-.215.283-.4.604-.4.959v0c0 .333.277.599.61.58a48.1 48.1 0 005.427-.63 48.05 48.05 0 00.582-4.717.532.532 0 00-.533-.57v0c-.355 0-.676.186-.959.401-.29.221-.634.349-1.003.349-1.035 0-1.875-1.007-1.875-2.25s.84-2.25 1.875-2.25c.37 0 .713.128 1.003.349.283.215.604.401.959.401v0a.656.656 0 00.658-.663 48.422 48.422 0 00-.37-5.36c-1.886.342-3.81.574-5.766.689a.578.578 0 01-.61-.58v0z"/>
                    </svg>
                    Add Extensions
                </a>
                <form method="POST" action="{{ route('projects.destroy', $project) }}"
                      onsubmit="return confirm('Delete this project?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="flex w-full items-center gap-3 rounded-xl border border-zinc-700 bg-zinc-800 px-3 py-2.5 text-sm font-medium text-zinc-500 transition-colors hover:border-rose-700/50 hover:bg-rose-900/20 hover:text-rose-400">
                        <svg class="h-4 w-4 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                        </svg>
                        Delete project
                    </button>
                </form>
            </div>
        </article>

    </div>

</x-layouts.app>
