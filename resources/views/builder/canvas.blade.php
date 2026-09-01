<x-layouts.builder :title="$design?->name ?? 'New Design'">

    {{-- ── Toolbar ──────────────────────────────────────────────── --}}
    <header class="flex h-14 items-center gap-1 border-b border-zinc-800 bg-zinc-950 px-3">

        {{-- Back + logo --}}
        <a href="{{ route('builder.designs.index') }}"
           class="mr-2 flex items-center gap-1.5 rounded-lg px-2 py-1.5 text-zinc-500 transition-colors hover:bg-zinc-800 hover:text-zinc-100"
           title="Back to designs">
            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/>
            </svg>
        </a>

        <x-app-logo class="h-6 w-auto opacity-60" />

        {{-- Design name --}}
        <span class="ml-3 mr-auto text-sm font-medium text-zinc-200">
            {{ $design?->name ?? 'Untitled Design' }}
        </span>

        {{-- Save state indicator --}}
        <span id="save-state" class="text-xs font-medium text-zinc-500 transition-colors"></span>

        {{-- Divider --}}
        <div class="mx-2 h-5 w-px bg-zinc-800"></div>

        {{-- Undo / Redo --}}
        <button id="btn-undo" title="Undo (⌘Z)"
                class="flex h-8 w-8 items-center justify-center rounded-md text-zinc-400 transition-colors hover:bg-zinc-800 hover:text-zinc-100">
            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3"/>
            </svg>
        </button>

        <button id="btn-redo" title="Redo (⌘⇧Z)"
                class="flex h-8 w-8 items-center justify-center rounded-md text-zinc-400 transition-colors hover:bg-zinc-800 hover:text-zinc-100">
            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 15l6-6m0 0l-6-6m6 6H9a6 6 0 000 12h3"/>
            </svg>
        </button>

        {{-- Divider --}}
        <div class="mx-2 h-5 w-px bg-zinc-800"></div>

        {{-- Viewport toggle --}}
        <div class="flex overflow-hidden rounded-lg border border-zinc-700 bg-zinc-900">
            <button id="btn-desktop" data-active="true"
                    class="builder-viewport-btn flex items-center gap-1.5 px-2.5 py-1.5 text-xs font-medium text-zinc-100 bg-zinc-700 transition-colors"
                    title="Desktop">
                <svg class="h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0V12a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 12V5.25"/>
                </svg>
                <span class="hidden sm:inline">Desktop</span>
            </button>
            <button id="btn-tablet" data-active="false"
                    class="builder-viewport-btn flex items-center gap-1.5 border-x border-zinc-700 px-2.5 py-1.5 text-xs font-medium text-zinc-400 transition-colors hover:bg-zinc-800 hover:text-zinc-100"
                    title="Tablet">
                <svg class="h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5h3m-6.75 2.25h10.5a2.25 2.25 0 002.25-2.25v-15a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v15a2.25 2.25 0 002.25 2.25z"/>
                </svg>
                <span class="hidden sm:inline">Tablet</span>
            </button>
            <button id="btn-mobile" data-active="false"
                    class="builder-viewport-btn flex items-center gap-1.5 px-2.5 py-1.5 text-xs font-medium text-zinc-400 transition-colors hover:bg-zinc-800 hover:text-zinc-100"
                    title="Mobile">
                <svg class="h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3"/>
                </svg>
                <span class="hidden sm:inline">Mobile</span>
            </button>
        </div>

        {{-- Divider --}}
        <div class="mx-2 h-5 w-px bg-zinc-800"></div>

        {{-- Clear canvas --}}
        <button id="btn-clear" title="Clear canvas"
                class="flex h-8 w-8 items-center justify-center rounded-md text-zinc-500 transition-colors hover:bg-zinc-800 hover:text-rose-400">
            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
            </svg>
        </button>

        {{-- Preview --}}
        <button id="btn-preview"
                class="flex h-8 items-center gap-1.5 rounded-md px-3 text-xs font-medium text-zinc-300 transition-colors hover:bg-zinc-800 hover:text-zinc-100 border border-zinc-700">
            <svg class="h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            Preview
        </button>

        {{-- Save --}}
        <button id="btn-save"
                class="flex h-8 items-center gap-1.5 rounded-md bg-white px-3 text-xs font-semibold text-zinc-900 transition-colors hover:bg-zinc-100">
            <svg class="h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/>
            </svg>
            Save
        </button>

        {{-- Publish (only for existing designs) --}}
        @if($design?->id)
            <form method="POST" action="{{ route('builder.designs.publish', $design) }}">
                @csrf
                <button type="submit"
                        class="flex h-8 items-center gap-1.5 rounded-md bg-emerald-600 px-3 text-xs font-semibold text-white transition-colors hover:bg-emerald-500">
                    <svg class="h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Publish
                </button>
            </form>
        @endif

    </header>

    {{-- ── Editor body ──────────────────────────────────────────── --}}
    <div class="flex" style="height: calc(100vh - 56px);">

        {{-- ── Left panel (Blocks + Layers) ──────────────────────── --}}
        <aside class="flex w-60 shrink-0 flex-col border-r border-zinc-800 bg-zinc-900">

            {{-- Panel tabs --}}
            <div class="flex border-b border-zinc-800">
                <button data-left-tab="blocks"
                        aria-selected="true"
                        class="flex-1 border-b-2 border-zinc-400 py-2.5 text-xs font-semibold uppercase tracking-wider text-zinc-100 transition-colors">
                    Blocks
                </button>
                <button data-left-tab="layers"
                        aria-selected="false"
                        class="flex-1 py-2.5 text-xs font-semibold uppercase tracking-wider text-zinc-500 transition-colors hover:text-zinc-300">
                    Layers
                </button>
            </div>

            {{-- Blocks panel --}}
            <div data-left-panel="blocks" class="flex-1 overflow-y-auto">
                <div id="gjs-blocks" class="pb-4"></div>
            </div>

            {{-- Layers panel --}}
            <div data-left-panel="layers" class="hidden flex-1 overflow-y-auto">
                <div id="gjs-layers"></div>
            </div>

        </aside>

        {{-- ── Canvas ──────────────────────────────────────────── --}}
        <main class="relative flex-1 overflow-hidden">
            <div id="gjs"
                 data-design-id="{{ $design?->id ?? '' }}"
                 data-save-url="{{ route('builder.designs.save') }}"
                 class="h-full w-full">
            </div>
        </main>

        {{-- ── Right panel (Styles + Traits) ──────────────────────── --}}
        <aside class="flex w-72 shrink-0 flex-col border-l border-zinc-800 bg-zinc-900">

            {{-- Panel tabs --}}
            <div class="flex border-b border-zinc-800">
                <button data-right-tab="styles"
                        aria-selected="true"
                        class="flex-1 border-b-2 border-zinc-400 py-2.5 text-xs font-semibold uppercase tracking-wider text-zinc-100 transition-colors">
                    Styles
                </button>
                <button data-right-tab="traits"
                        aria-selected="false"
                        class="flex-1 py-2.5 text-xs font-semibold uppercase tracking-wider text-zinc-500 transition-colors hover:text-zinc-300">
                    Settings
                </button>
            </div>

            {{-- Styles --}}
            <div data-right-panel="styles" class="flex-1 overflow-y-auto">
                <div id="gjs-styles"></div>
            </div>

            {{-- Traits --}}
            <div data-right-panel="traits" class="hidden flex-1 overflow-y-auto">
                <div id="gjs-traits" class="p-3"></div>
            </div>

        </aside>

    </div>

</x-layouts.builder>
