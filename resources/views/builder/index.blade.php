<x-layouts.app :title="__('Page Designs')">

    <section class="space-y-6">

        {{-- Header --}}
        <div class="overflow-hidden rounded-3xl border border-zinc-700/40 bg-gradient-to-br from-zinc-900 via-zinc-950 to-zinc-900 p-6 sm:p-8">
            <p class="mb-3 text-xs font-semibold uppercase tracking-[0.18em] text-zinc-400">Visual Builder</p>
            <h1 class="text-3xl font-semibold tracking-tight text-zinc-100 sm:text-4xl">Page Designs</h1>
            <p class="mt-3 max-w-2xl text-sm leading-relaxed text-zinc-400 sm:text-base">
                Build, preview, and publish page layouts using the drag-and-drop canvas with Flux UI components.
            </p>
            <div class="mt-6">
                <a href="{{ route('builder.designs.create') }}"
                   class="inline-flex items-center gap-2 rounded-xl bg-white px-5 py-2.5 text-sm font-semibold text-zinc-900 transition-colors hover:bg-zinc-100">
                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                    </svg>
                    New design
                </a>
            </div>
        </div>

        {{-- Status messages --}}
        @if(session('status'))
            <div class="rounded-xl border border-emerald-500/40 bg-emerald-900/30 px-4 py-3 text-sm font-medium text-emerald-200">
                {{ session('status') }}
            </div>
        @endif

        {{-- Designs grid --}}
        @if($designs->isEmpty())
            <div class="rounded-2xl border border-zinc-700 bg-zinc-900 px-6 py-16 text-center">
                <div class="mx-auto mb-4 inline-flex h-14 w-14 items-center justify-center rounded-2xl border border-zinc-700 bg-zinc-800">
                    <svg class="h-7 w-7 text-zinc-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.53 16.122a3 3 0 00-5.78 1.128 2.25 2.25 0 01-2.4 2.245 4.5 4.5 0 008.4-2.245c0-.399-.078-.78-.22-1.128zm0 0a15.998 15.998 0 003.388-1.62m-5.043-.025a15.994 15.994 0 011.622-3.395m3.42 3.42a15.995 15.995 0 004.764-4.648l3.876-5.814a1.151 1.151 0 00-1.597-1.597L14.146 6.32a15.996 15.996 0 00-4.649 4.763m3.42 3.42a6.776 6.776 0 00-3.42-3.42"/>
                    </svg>
                </div>
                <h3 class="text-base font-semibold text-zinc-100">No designs yet</h3>
                <p class="mt-2 text-sm text-zinc-400">Create your first page design to get started with the visual builder.</p>
                <a href="{{ route('builder.designs.create') }}"
                   class="mt-6 inline-flex items-center gap-2 rounded-xl bg-white px-4 py-2 text-sm font-semibold text-zinc-900 transition-colors hover:bg-zinc-100">
                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                    </svg>
                    Create design
                </a>
            </div>
        @else
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($designs as $design)
                    @php
                        $statusColor = match($design->status) {
                            'published' => 'bg-emerald-900/40 text-emerald-300 border-emerald-700/50',
                            'draft'     => 'bg-zinc-800 text-zinc-400 border-zinc-700',
                            default     => 'bg-zinc-800 text-zinc-400 border-zinc-700',
                        };
                    @endphp
                    <article class="group relative flex flex-col rounded-2xl border border-zinc-700 bg-zinc-900 p-5 transition-colors hover:border-zinc-600">

                        {{-- Thumbnail placeholder --}}
                        <div class="mb-4 flex h-32 items-center justify-center overflow-hidden rounded-xl border border-zinc-700 bg-zinc-800">
                            <svg class="h-10 w-10 text-zinc-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.53 16.122a3 3 0 00-5.78 1.128 2.25 2.25 0 01-2.4 2.245 4.5 4.5 0 008.4-2.245c0-.399-.078-.78-.22-1.128zm0 0a15.998 15.998 0 003.388-1.62m-5.043-.025a15.994 15.994 0 011.622-3.395m3.42 3.42a15.995 15.995 0 004.764-4.648l3.876-5.814a1.151 1.151 0 00-1.597-1.597L14.146 6.32a15.996 15.996 0 00-4.649 4.763m3.42 3.42a6.776 6.776 0 00-3.42-3.42"/>
                            </svg>
                        </div>

                        {{-- Info --}}
                        <div class="flex flex-1 flex-col gap-1">
                            <div class="flex items-center justify-between gap-2">
                                <h3 class="truncate text-sm font-semibold text-zinc-100">{{ $design->name }}</h3>
                                <span class="shrink-0 inline-flex items-center rounded-full border px-2 py-0.5 text-xs font-semibold {{ $statusColor }}">
                                    {{ ucfirst($design->status) }}
                                </span>
                            </div>
                            <p class="text-xs text-zinc-500">
                                Updated {{ $design->updated_at->diffForHumans() }}
                            </p>
                        </div>

                        {{-- Actions --}}
                        <div class="mt-4 flex items-center gap-2 border-t border-zinc-800 pt-4">
                            <a href="{{ route('builder.designs.edit', $design) }}"
                               class="flex-1 inline-flex items-center justify-center gap-1.5 rounded-lg border border-zinc-700 bg-zinc-800 px-3 py-1.5 text-xs font-medium text-zinc-200 transition-colors hover:bg-zinc-700 hover:text-zinc-100">
                                <svg class="h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125"/>
                                </svg>
                                Edit
                            </a>

                            @if($design->status !== 'published')
                                <form method="POST" action="{{ route('builder.designs.publish', $design) }}">
                                    @csrf
                                    <button type="submit"
                                            class="inline-flex items-center gap-1.5 rounded-lg border border-emerald-700/50 bg-emerald-900/30 px-3 py-1.5 text-xs font-medium text-emerald-300 transition-colors hover:bg-emerald-800/50">
                                        <svg class="h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        Publish
                                    </button>
                                </form>
                            @endif

                            <form method="POST" action="{{ route('builder.designs.destroy', $design) }}"
                                  onsubmit="return confirm('Delete \'{{ addslashes($design->name) }}\'?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="inline-flex items-center gap-1.5 rounded-lg border border-zinc-700 px-2.5 py-1.5 text-xs font-medium text-zinc-500 transition-colors hover:border-rose-700/50 hover:bg-rose-900/20 hover:text-rose-400">
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

    </section>

</x-layouts.app>
