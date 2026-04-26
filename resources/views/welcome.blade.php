<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('linavelt.brand.name') }} | Build Full-Stack Websites Your Way</title>

        <link rel="icon" href="/favicon.ico" sizes="any">
        <link rel="icon" href="/favicon.svg" type="image/svg+xml">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=space-grotesk:400,500,600,700|plus-jakarta-sans:400,500,600,700" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            :root {
                --lv-bg: #05070e;
                --lv-panel: #0f1424;
                --lv-muted: #97a7c6;
                --lv-text: #eaf0ff;
                --lv-accent: #ff5a22;
                --lv-border: rgba(158, 181, 226, 0.2);
            }

            body {
                font-family: 'Plus Jakarta Sans', sans-serif;
                background:
                    radial-gradient(1200px 620px at 15% 8%, rgba(255, 90, 34, 0.17), transparent 60%),
                    radial-gradient(900px 520px at 92% 22%, rgba(94, 119, 255, 0.14), transparent 60%),
                    linear-gradient(180deg, #060914 0%, #05070e 55%, #06060b 100%);
                color: var(--lv-text);
            }

            .brand-title {
                font-family: 'Space Grotesk', sans-serif;
                letter-spacing: -0.02em;
            }

            .hero-grid {
                background-image:
                    linear-gradient(rgba(167, 186, 224, 0.11) 1px, transparent 1px),
                    linear-gradient(90deg, rgba(167, 186, 224, 0.11) 1px, transparent 1px);
                background-size: 34px 34px;
            }

            .float-in {
                animation: float-in 700ms ease-out both;
            }

            .float-in-delay {
                animation: float-in 1000ms ease-out both;
            }

            .stagger > * {
                opacity: 0;
                transform: translateY(16px);
                animation: rise 660ms cubic-bezier(0.22, 1, 0.36, 1) forwards;
            }

            .stagger > *:nth-child(1) { animation-delay: 120ms; }
            .stagger > *:nth-child(2) { animation-delay: 220ms; }
            .stagger > *:nth-child(3) { animation-delay: 320ms; }
            .stagger > *:nth-child(4) { animation-delay: 420ms; }

            @keyframes float-in {
                from {
                    opacity: 0;
                    transform: translateY(18px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            @keyframes rise {
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
        </style>
    </head>
    <body class="min-h-screen antialiased selection:bg-orange-300/35 selection:text-white">
        @php
            $home = config('linavelt.homepage', []);
            $features = $home['feature_cards'] ?? [];
            $steps = $home['blueprint_steps'] ?? [];
            $badges = $home['stack_badges'] ?? [];

            $domains = config('linavelt.onboarding.domains', []);
            $userSelections = auth()->user()?->onboarding_preferences ?? [];
            $completedDomains = 0;

            foreach ($domains as $domain) {
                $key = $domain['key'] ?? null;

                if ($key && isset($userSelections[$key])) {
                    $completedDomains++;
                }
            }

            $totalDomains = count($domains);
            $completionPercentage = $totalDomains > 0 ? (int) round(($completedDomains / $totalDomains) * 100) : 0;
            $hasCompletedProfile = auth()->check() && $completionPercentage === 100;
        @endphp

        <div class="mx-auto flex min-h-screen w-full max-w-7xl flex-col px-5 py-6 sm:px-8 lg:px-12">
            <header class="float-in flex items-center justify-between rounded-2xl border border-[var(--lv-border)] bg-[color:var(--lv-panel)]/80 px-4 py-3 backdrop-blur-xl sm:px-6">
                <a href="{{ route('home') }}" class="brand-title text-lg font-semibold tracking-tight text-white sm:text-xl">
                    {{ config('linavelt.brand.name') }}
                </a>

                @if (Route::has('login'))
                    <nav class="flex items-center gap-2 text-sm">
                        @auth
                            <a href="{{ route('dashboard') }}" class="rounded-lg border border-orange-300/30 bg-orange-400/15 px-3 py-2 font-medium text-orange-100 transition hover:bg-orange-400/25">
                                Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="rounded-lg border border-white/15 px-3 py-2 text-slate-100 transition hover:border-white/30 hover:bg-white/6">
                                Log in
                            </a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="rounded-lg border border-orange-300/40 bg-orange-500 px-3 py-2 font-semibold text-[#1b120d] transition hover:bg-orange-400">
                                    Start free
                                </a>
                            @endif
                        @endauth
                    </nav>
                @endif
            </header>

            <main class="hero-grid mt-6 flex grow flex-col gap-7 overflow-hidden rounded-3xl border border-[var(--lv-border)] bg-[#090f1f]/85 p-6 sm:p-8 lg:p-10">
                <section class="grid gap-8 lg:grid-cols-[1.25fr_0.9fr] lg:items-start">
                    <div class="stagger space-y-5">
                        <p class="w-fit rounded-full border border-orange-200/30 bg-orange-400/10 px-3 py-1 text-xs font-semibold uppercase tracking-[0.16em] text-orange-100">
                            {{ $home['badge'] ?? '' }}
                        </p>

                        <h1 class="brand-title max-w-3xl text-4xl font-semibold leading-tight text-white sm:text-5xl lg:text-6xl">
                            {{ $home['headline'] ?? '' }}
                        </h1>

                        <p class="max-w-2xl text-base leading-relaxed text-[color:var(--lv-muted)] sm:text-lg">
                            {{ $home['description'] ?? '' }}
                        </p>

                        @auth
                            <div class="inline-flex items-center rounded-full border border-emerald-300/35 bg-emerald-300/15 px-3 py-1 text-xs font-semibold uppercase tracking-[0.12em] text-emerald-100">
                                Stack profile {{ $completionPercentage }}% complete
                            </div>
                        @endauth

                        <div class="flex flex-wrap gap-3 pt-2">
                            @auth
                                <a href="{{ route('builder.onboarding') }}" class="rounded-xl bg-orange-500 px-5 py-3 text-sm font-semibold text-[#1f130c] transition hover:bg-orange-400 sm:text-base">
                                    {{ $hasCompletedProfile ? 'Update Stack Profile' : 'Launch the Builder' }}
                                </a>
                            @else
                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="rounded-xl bg-orange-500 px-5 py-3 text-sm font-semibold text-[#1f130c] transition hover:bg-orange-400 sm:text-base">
                                        Launch the Builder
                                    </a>
                                @else
                                    <a href="{{ route('login') }}" class="rounded-xl bg-orange-500 px-5 py-3 text-sm font-semibold text-[#1f130c] transition hover:bg-orange-400 sm:text-base">
                                        Launch the Builder
                                    </a>
                                @endif
                            @endauth
                            <a href="{{ route('blog.index') }}" class="rounded-xl border border-white/15 px-5 py-3 text-sm font-medium text-slate-100 transition hover:border-white/35 hover:bg-white/8 sm:text-base">
                                Explore Product Notes
                            </a>
                        </div>
                    </div>

                    <aside class="float-in-delay rounded-2xl border border-white/12 bg-gradient-to-br from-slate-700/25 via-slate-900/45 to-orange-950/35 p-5 shadow-[0_18px_40px_rgba(0,0,0,0.35)]">
                        <h2 class="brand-title text-xl font-semibold text-white">{{ $home['blueprint_title'] ?? 'Project Blueprint' }}</h2>
                        <div class="mt-4 space-y-3 text-sm">
                            @foreach ($steps as $step)
                                <div class="rounded-xl border border-white/10 bg-black/20 p-3">
                                    <p class="text-[11px] uppercase tracking-[0.18em] text-orange-200/80">{{ $step['label'] }}</p>
                                    <p class="mt-1 font-medium text-white">{{ $step['text'] }}</p>
                                </div>
                            @endforeach
                        </div>
                    </aside>
                </section>

                <section class="grid gap-4 md:grid-cols-3">
                    @foreach ($features as $feature)
                        <article class="rounded-2xl border border-white/10 bg-black/20 p-5">
                            <p class="text-xs uppercase tracking-[0.16em] text-orange-200/80">{{ $feature['kicker'] }}</p>
                            <h3 class="brand-title mt-2 text-xl font-semibold text-white">{{ $feature['title'] }}</h3>
                            <p class="mt-2 text-sm leading-relaxed text-[color:var(--lv-muted)]">
                                {{ $feature['text'] }}
                            </p>
                        </article>
                    @endforeach
                </section>

                <section class="grid gap-4 rounded-2xl border border-white/10 bg-[#0b1327]/90 p-5 md:grid-cols-[1fr_auto] md:items-center">
                    <div>
                        <p class="text-xs uppercase tracking-[0.15em] text-orange-200/80">{{ $home['stack_heading'] ?? '' }}</p>
                        <p class="mt-2 text-sm text-[color:var(--lv-muted)] sm:text-base">
                            {{ $home['stack_description'] ?? '' }}
                        </p>
                    </div>
                    <div class="flex flex-wrap items-center gap-2 text-xs font-medium text-white/80 sm:text-sm">
                        @foreach ($badges as $badge)
                            <span class="rounded-md border border-white/15 bg-black/25 px-2 py-1">{{ $badge }}</span>
                        @endforeach
                    </div>
                </section>
            </main>
        </div>
    </body>
</html>
