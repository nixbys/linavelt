{{-- Admin Dashboard --}}
@extends('layout.app')

@section('title', 'Admin Dashboard')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="mb-8 flex flex-col gap-2">
            <h1 class="text-4xl font-bold text-gray-100">Admin Dashboard</h1>
            <p class="text-sm text-gray-400">Operational status for product, module generation, and security automation.</p>
        </div>

        @if (session('status'))
            <div class="mb-6 rounded-lg border border-emerald-400/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200">
                {{ session('status') }}
            </div>
        @endif

        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-lg bg-gray-800 p-4 shadow-md">
                <h2 class="text-xl font-bold text-gray-100">Users</h2>
                <p class="mt-2 text-sm text-gray-400">{{ $data['users'] }} registered users.</p>
            </div>

            <div class="rounded-lg bg-gray-800 p-4 shadow-md">
                <h2 class="text-xl font-bold text-gray-100">Posts</h2>
                <p class="mt-2 text-sm text-gray-400">{{ $data['posts'] }} blog posts.</p>
            </div>

            <div class="rounded-lg bg-gray-800 p-4 shadow-md">
                <h2 class="text-xl font-bold text-gray-100">Settings</h2>
                <p class="mt-2 text-sm text-gray-400">{{ $data['settings'] }} configurable settings.</p>
            </div>

            <div class="rounded-lg bg-gray-800 p-4 shadow-md">
                <h2 class="text-xl font-bold text-gray-100">Automation Report</h2>
                <p class="mt-2 text-sm text-gray-400">
                    Latest report: {{ $automationReport['generated_at'] ? \Illuminate\Support\Carbon::parse($automationReport['generated_at'])->diffForHumans() : 'not available' }}
                </p>
                <p class="mt-1 text-xs text-gray-500">Dependabot: {{ $automationReport['dependabot_open'] ?? 'n/a' }}, Code scanning: {{ $automationReport['code_scanning_open'] ?? 'n/a' }}</p>
            </div>
        </div>

        <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-[1.1fr_0.9fr]">
            <div class="rounded-lg bg-gray-800 p-4 shadow-md">
                <h2 class="text-xl font-bold text-gray-100">Module Generation Summary</h2>
                <div class="mt-4 grid grid-cols-2 gap-3 text-sm text-gray-300 sm:grid-cols-4">
                    <div class="rounded-md bg-gray-900/70 p-3">Pending: {{ $moduleGenerationSummary['pending'] ?? 0 }}</div>
                    <div class="rounded-md bg-gray-900/70 p-3">Running: {{ $moduleGenerationSummary['running'] ?? 0 }}</div>
                    <div class="rounded-md bg-gray-900/70 p-3">Complete: {{ $moduleGenerationSummary['complete'] ?? 0 }}</div>
                    <div class="rounded-md bg-gray-900/70 p-3">Failed: {{ $moduleGenerationSummary['failed'] ?? 0 }}</div>
                </div>

                <h3 class="mt-6 text-sm font-semibold uppercase tracking-[0.16em] text-gray-400">Failed users</h3>
                @if ($failedModuleUsers->isNotEmpty())
                    <ul class="mt-3 space-y-3">
                        @foreach ($failedModuleUsers as $failedUser)
                            <li class="rounded-md border border-gray-700 bg-gray-900/60 p-3">
                                <div class="flex flex-wrap items-center justify-between gap-3">
                                    <div>
                                        <p class="font-medium text-gray-100">{{ $failedUser->name }}</p>
                                        <p class="text-xs text-gray-400">{{ $failedUser->email }}</p>
                                    </div>
                                    <form method="POST" action="{{ route('admin.module-generation.retry', $failedUser) }}">
                                        @csrf
                                        <button type="submit" class="rounded-md bg-blue-500 px-3 py-2 text-xs font-semibold text-white hover:bg-blue-400">
                                            Retry generation
                                        </button>
                                    </form>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="mt-3 text-sm text-gray-400">No failed module generations require attention.</p>
                @endif
            </div>

            <div class="rounded-lg bg-gray-800 p-4 shadow-md">
                <h2 class="text-xl font-bold text-gray-100">Security Observability</h2>
                <div class="mt-3 space-y-2 text-sm text-gray-300">
                    <p>NPM root vulnerabilities: {{ $automationReport['npm_root_total'] ?? 'n/a' }}</p>
                    <p>MCP vulnerabilities: {{ $automationReport['npm_mcp_total'] ?? 'n/a' }}</p>
                    <p>Composer advisories: {{ is_array($automationReport['composer_advisories'] ?? null) ? count($automationReport['composer_advisories']) : 'n/a' }}</p>
                </div>

                <div class="mt-6 rounded-md border border-gray-700 bg-gray-900/60 p-4">
                    <p class="text-sm font-semibold text-gray-100">Recommended next step</p>
                    <p class="mt-2 text-sm text-gray-400">Run <span class="font-mono text-gray-200">npm run -C mcp-server automation:report</span> to verify the latest summary before changing release automation.</p>
                </div>
            </div>
        </div>
    </div>
@endsection

