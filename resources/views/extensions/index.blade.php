@php
    $extensions = config('technologies.extensions', []);
    $categories = array_keys($extensions);
    $allExtensions = collect($extensions)->flatten(1);
    $installedCount = $allExtensions->where('installed', true)->count();
@endphp

<x-layouts.app :title="__('Extensions')">

    {{-- ── Header ───────────────────────────────────────────────── --}}
    <div class="mb-8 overflow-hidden rounded-3xl border border-zinc-700/40 bg-gradient-to-br from-zinc-900 via-zinc-950 to-zinc-900 p-6 sm:p-8">
        <div class="flex items-start justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-widest text-zinc-500">Marketplace</p>
                <h1 class="mt-1 text-3xl font-semibold tracking-tight text-zinc-100 sm:text-4xl">Extensions</h1>
                <p class="mt-2 max-w-xl text-sm leading-relaxed text-zinc-400">
                    Extend Linavelt with block libraries, code generators, deployment integrations, analytics plugins, and more. Install in one click.
                </p>
            </div>
            <div class="shrink-0 text-right">
                <p class="text-3xl font-bold text-zinc-100">{{ $installedCount }}</p>
                <p class="mt-0.5 text-xs text-zinc-500">installed</p>
            </div>
        </div>

        {{-- Search bar (UI stub) --}}
        <div class="mt-6 flex items-center gap-3">
            <div class="relative flex-1 max-w-md">
                <svg class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-zinc-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                </svg>
                <input type="search" placeholder="Search extensions…"
                       class="w-full rounded-xl border border-zinc-700 bg-zinc-800/80 py-2 pl-9 pr-4 text-sm text-zinc-300 placeholder-zinc-600 focus:border-zinc-500 focus:outline-none focus:ring-1 focus:ring-zinc-500" />
            </div>
        </div>
    </div>

    <div class="flex gap-6">

        {{-- ── Sidebar: categories ──────────────────────────────── --}}
        <nav class="hidden w-48 shrink-0 lg:block">
            <p class="mb-3 text-xs font-semibold uppercase tracking-widest text-zinc-600">Categories</p>
            <ul class="space-y-0.5">
                <li>
                    <a href="#featured"
                       class="flex items-center justify-between rounded-lg px-3 py-2 text-sm font-medium text-zinc-300 transition-colors hover:bg-zinc-800 hover:text-zinc-100">
                        All
                        <span class="text-xs text-zinc-600">{{ $allExtensions->count() }}</span>
                    </a>
                </li>
                @foreach($categories as $cat)
                    <li>
                        <a href="#{{ Str::slug($cat) }}"
                           class="flex items-center justify-between rounded-lg px-3 py-2 text-sm text-zinc-500 transition-colors hover:bg-zinc-800 hover:text-zinc-300">
                            {{ $cat }}
                            <span class="text-xs text-zinc-700">{{ count($extensions[$cat]) }}</span>
                        </a>
                    </li>
                @endforeach
            </ul>

            <div class="mt-6 rounded-xl border border-zinc-700/50 bg-zinc-900 p-4">
                <p class="text-xs font-semibold text-zinc-400">Publish an extension</p>
                <p class="mt-1.5 text-xs text-zinc-600 leading-relaxed">Build plugins, block packs, or integrations for the Linavelt marketplace.</p>
                <a href="#" class="mt-3 block text-xs font-medium text-zinc-400 transition-colors hover:text-zinc-200 underline underline-offset-2">
                    Developer docs →
                </a>
            </div>
        </nav>

        {{-- ── Extension listings ───────────────────────────────── --}}
        <div class="flex-1 min-w-0 space-y-10">

            @foreach($extensions as $category => $items)
                <section id="{{ Str::slug($category) }}">
                    <h2 class="mb-4 text-sm font-semibold uppercase tracking-widest text-zinc-500">{{ $category }}</h2>

                    <div class="grid gap-3 sm:grid-cols-2">
                        @foreach($items as $ext)
                            <div class="group flex flex-col rounded-2xl border {{ $ext['installed'] ? 'border-zinc-600 bg-zinc-900' : 'border-zinc-700 bg-zinc-900' }} p-5 transition-colors hover:border-zinc-600">

                                <div class="flex items-start justify-between gap-3">
                                    {{-- Icon --}}
                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-zinc-700 bg-zinc-800 text-sm font-bold text-zinc-300">
                                        {{ strtoupper(substr($ext['name'], 0, 1)) }}
                                    </div>

                                    {{-- Install toggle --}}
                                    @if($ext['installed'])
                                        <button class="inline-flex items-center gap-1.5 rounded-lg border border-emerald-700/50 bg-emerald-900/30 px-3 py-1 text-xs font-semibold text-emerald-300 transition-colors hover:bg-rose-900/20 hover:text-rose-400 hover:border-rose-700/50">
                                            <svg class="h-3 w-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            Installed
                                        </button>
                                    @else
                                        <button class="inline-flex items-center gap-1.5 rounded-lg border border-zinc-700 bg-zinc-800 px-3 py-1 text-xs font-semibold text-zinc-300 transition-colors hover:bg-zinc-700 hover:text-zinc-100">
                                            <svg class="h-3 w-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                                            </svg>
                                            Install
                                        </button>
                                    @endif
                                </div>

                                <div class="mt-3">
                                    <div class="flex items-center gap-2">
                                        <h3 class="text-sm font-semibold text-zinc-100">{{ $ext['name'] }}</h3>
                                        <span class="rounded-full border border-zinc-700 bg-zinc-800 px-1.5 py-0.5 text-[10px] font-medium text-zinc-500">
                                            v{{ $ext['version'] }}
                                        </span>
                                    </div>
                                    <p class="mt-1 text-xs font-medium text-zinc-600">by {{ $ext['author'] }}</p>
                                    <p class="mt-2 text-xs text-zinc-400 leading-relaxed">{{ $ext['description'] }}</p>
                                </div>

                                <div class="mt-4 flex items-center justify-between">
                                    <div class="flex items-center gap-1">
                                        @for($i = 0; $i < 5; $i++)
                                            <svg class="h-3 w-3 {{ $i < $ext['rating'] ? 'text-amber-400' : 'text-zinc-700' }}"
                                                 xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                                <path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.007 5.404.433c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.433 2.082-5.006z" clip-rule="evenodd"/>
                                            </svg>
                                        @endfor
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="rounded-full border border-zinc-700 bg-zinc-800 px-2 py-0.5 text-[10px] text-zinc-500">
                                            {{ $ext['category'] }}
                                        </span>
                                        <span class="text-[10px] text-zinc-600">{{ $ext['installs'] }} installs</span>
                                    </div>
                                </div>

                                @if(isset($ext['tags']) && count($ext['tags']) > 0)
                                    <div class="mt-3 flex flex-wrap gap-1">
                                        @foreach($ext['tags'] as $tag)
                                            <span class="rounded-full bg-zinc-800/60 px-2 py-0.5 text-[10px] text-zinc-600">#{{ $tag }}</span>
                                        @endforeach
                                    </div>
                                @endif

                            </div>
                        @endforeach
                    </div>
                </section>
            @endforeach

        </div>
    </div>

</x-layouts.app>
