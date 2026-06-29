<x-layouts.app :title="__('Projects')">

    {{-- ── Header ───────────────────────────────────────────────── --}}
    <div class="mb-8 flex items-end justify-between gap-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-widest text-zinc-500">Visual Builder</p>
            <h1 class="mt-1 text-3xl font-semibold tracking-tight text-zinc-100">Projects</h1>
        </div>
        <a href="{{ route('projects.create') }}"
           class="inline-flex items-center gap-2 rounded-xl bg-white px-4 py-2 text-sm font-semibold text-zinc-900 transition-colors hover:bg-zinc-100">
            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
            </svg>
            New project
        </a>
    </div>

    {{-- Status flash --}}
    @if(session('status'))
        <div class="mb-6 rounded-xl border border-emerald-500/40 bg-emerald-900/30 px-4 py-3 text-sm font-medium text-emerald-200">
            {{ session('status') }}
        </div>
    @endif

    {{-- ── Empty state ──────────────────────────────────────────── --}}
    @if($projects->isEmpty())
        <div class="rounded-3xl border border-dashed border-zinc-700 bg-zinc-900/50 px-6 py-20 text-center">
            <div class="mx-auto mb-5 inline-flex h-16 w-16 items-center justify-center rounded-2xl border border-zinc-700 bg-zinc-800">
                <svg class="h-8 w-8 text-zinc-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.25" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.53 16.122a3 3 0 00-5.78 1.128 2.25 2.25 0 01-2.4 2.245 4.5 4.5 0 008.4-2.245c0-.399-.078-.78-.22-1.128zm0 0a15.998 15.998 0 003.388-1.62m-5.043-.025a15.994 15.994 0 011.622-3.395m3.42 3.42a15.995 15.995 0 004.764-4.648l3.876-5.814a1.151 1.151 0 00-1.597-1.597L14.146 6.32a15.996 15.996 0 00-4.649 4.763m3.42 3.42a6.776 6.776 0 00-3.42-3.42"/>
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-zinc-100">No projects yet</h3>
            <p class="mt-2 text-sm text-zinc-400 max-w-xs mx-auto">
                Create your first project and choose any language, framework, and integrations.
            </p>
            <a href="{{ route('projects.create') }}"
               class="mt-6 inline-flex items-center gap-2 rounded-xl bg-white px-5 py-2.5 text-sm font-semibold text-zinc-900 transition-colors hover:bg-zinc-100">
                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                </svg>
                Create project
            </a>
        </div>
    @else

    {{-- ── Projects grid ────────────────────────────────────────── --}}
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">

        {{-- "New project" card --}}
        <a href="{{ route('projects.create') }}"
           class="group flex flex-col items-center justify-center gap-3 rounded-2xl border border-dashed border-zinc-700 bg-zinc-900/40 p-6 text-center transition-colors hover:border-zinc-600 hover:bg-zinc-900 min-h-[200px]">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl border border-zinc-700 bg-zinc-800 transition-colors group-hover:border-zinc-600">
                <svg class="h-6 w-6 text-zinc-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                </svg>
            </div>
            <p class="text-sm font-medium text-zinc-400 group-hover:text-zinc-200 transition-colors">New project</p>
        </a>

        @foreach($projects as $project)
            @php
                $lang = collect(config('technologies.languages', []))->firstWhere('id', $project->language);
                $statusMap = [
                    'published' => ['pill' => 'bg-emerald-900/40 text-emerald-300 border-emerald-700/50', 'dot' => 'bg-emerald-400'],
                    'draft'     => ['pill' => 'bg-zinc-800 text-zinc-400 border-zinc-700', 'dot' => 'bg-zinc-500'],
                ];
                $st = $statusMap[$project->status] ?? $statusMap['draft'];
                $typeLabel = collect(config('technologies.project_types', []))->firstWhere('id', $project->type)['label'] ?? ucfirst($project->type);
            @endphp

            <article class="group flex flex-col rounded-2xl border border-zinc-700 bg-zinc-900 transition-colors hover:border-zinc-600">

                {{-- Preview area --}}
                <div class="relative flex h-36 items-center justify-center overflow-hidden rounded-t-2xl border-b border-zinc-700 bg-zinc-800">
                    {{-- Language badge watermark --}}
                    @if($lang)
                        <div class="absolute inset-0 flex items-center justify-center opacity-10">
                            <span class="text-7xl font-black" style="color: {{ $lang['color'] }}">{{ $lang['abbr'] }}</span>
                        </div>
                    @endif
                    <div class="relative flex flex-col items-center gap-2">
                        @if($lang)
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl text-sm font-bold"
                                 style="background-color: {{ $lang['bg'] }}; color: {{ $lang['color'] }};">
                                {{ $lang['abbr'] }}
                            </div>
                        @endif
                        <p class="text-xs text-zinc-500">{{ $typeLabel }}</p>
                    </div>
                </div>

                {{-- Info --}}
                <div class="flex flex-1 flex-col gap-2 p-4">
                    <div class="flex items-start justify-between gap-2">
                        <h3 class="text-sm font-semibold text-zinc-100 leading-snug">{{ $project->name }}</h3>
                        <span class="shrink-0 inline-flex items-center gap-1 rounded-full border px-2 py-0.5 text-xs font-semibold {{ $st['pill'] }}">
                            <span class="h-1.5 w-1.5 rounded-full {{ $st['dot'] }}"></span>
                            {{ ucfirst($project->status) }}
                        </span>
                    </div>

                    <p class="text-xs text-zinc-500">{{ $project->stackLabel() }}</p>

                    @if($project->integrations && count($project->integrations) > 0)
                        <div class="flex flex-wrap gap-1">
                            @foreach(array_slice($project->integrations, 0, 3) as $intId)
                                <span class="rounded-full bg-zinc-800 border border-zinc-700 px-2 py-0.5 text-[10px] text-zinc-500">
                                    {{ $intId }}
                                </span>
                            @endforeach
                            @if(count($project->integrations) > 3)
                                <span class="rounded-full bg-zinc-800 border border-zinc-700 px-2 py-0.5 text-[10px] text-zinc-500">
                                    +{{ count($project->integrations) - 3 }}
                                </span>
                            @endif
                        </div>
                    @endif
                </div>

                {{-- Actions --}}
                <div class="flex items-center gap-1.5 border-t border-zinc-800 p-3">
                    <a href="{{ route('projects.canvas', $project) }}"
                       class="flex-1 inline-flex items-center justify-center gap-1.5 rounded-lg border border-zinc-700 bg-zinc-800 px-3 py-1.5 text-xs font-medium text-zinc-200 transition-colors hover:bg-zinc-700">
                        <svg class="h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487z"/>
                        </svg>
                        Open
                    </a>

                    <a href="{{ route('projects.show', $project) }}"
                       class="inline-flex items-center justify-center rounded-lg border border-zinc-700 p-1.5 text-zinc-500 transition-colors hover:bg-zinc-800 hover:text-zinc-300"
                       title="Project details">
                        <svg class="h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </a>

                    <form method="POST" action="{{ route('projects.destroy', $project) }}"
                          onsubmit="return confirm('Delete \'{{ addslashes($project->name) }}\'?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="inline-flex items-center justify-center rounded-lg border border-zinc-700 p-1.5 text-zinc-600 transition-colors hover:border-rose-700/50 hover:bg-rose-900/20 hover:text-rose-400"
                                title="Delete project">
                            <svg class="h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                            </svg>
                        </button>
                    </form>
                </div>

            </article>
        @endforeach

    </div>
    @endif

</x-layouts.app>
