<x-layouts.app :title="__('New Project')">

@if($errors->any())
    <div class="mb-6 rounded-xl border border-rose-700/50 bg-rose-900/20 px-4 py-3">
        <p class="text-sm font-semibold text-rose-300">Please fix the following errors:</p>
        <ul class="mt-1.5 list-inside list-disc space-y-0.5 text-sm text-rose-400">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div
    x-data="projectWizard(@js($technologies))"
    class="mx-auto max-w-4xl space-y-8 py-4"
>

    {{-- ── Step progress bar ────────────────────────────────────── --}}
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-semibold tracking-tight text-zinc-100">New Project</h1>
            <span class="text-sm text-zinc-500" x-text="`Step ${step} of ${totalSteps}`"></span>
        </div>

        <div class="flex gap-1.5">
            <template x-for="i in totalSteps" :key="i">
                <div class="h-1 flex-1 rounded-full transition-colors duration-300"
                     :class="i <= step ? 'bg-white' : 'bg-zinc-800'"></div>
            </template>
        </div>

        <div class="flex items-center gap-2 text-sm font-medium text-zinc-400">
            <span x-show="step === 1">What are you building?</span>
            <span x-show="step === 2">Choose your language</span>
            <span x-show="step === 3">Pick a framework</span>
            <span x-show="step === 4">Add integrations</span>
            <span x-show="step === 5">Name your project</span>
        </div>
    </div>

    {{-- ── Step 1: Project type ─────────────────────────────────── --}}
    <div x-show="step === 1" x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
        <div class="grid grid-cols-2 gap-3 sm:grid-cols-3">
            @foreach($technologies['project_types'] as $type)
                <button type="button"
                        @click="selectType('{{ $type['id'] }}')"
                        :class="project_type === '{{ $type['id'] }}'
                            ? 'border-zinc-400 bg-zinc-800 ring-1 ring-zinc-400'
                            : 'border-zinc-700 bg-zinc-900 hover:border-zinc-600 hover:bg-zinc-800'"
                        class="group relative flex flex-col items-start gap-3 rounded-2xl border p-5 text-left transition-all">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl border border-zinc-700 bg-zinc-800 group-hover:border-zinc-600">
                        <flux:icon.{{ $type['icon'] }} class="h-5 w-5 text-zinc-300" />
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-zinc-100">{{ $type['label'] }}</p>
                        <p class="mt-0.5 text-xs text-zinc-400 leading-snug">{{ $type['description'] }}</p>
                    </div>
                    <div x-show="project_type === '{{ $type['id'] }}'"
                         class="absolute right-3 top-3 flex h-5 w-5 items-center justify-center rounded-full bg-white">
                        <svg class="h-3 w-3 text-zinc-900" viewBox="0 0 12 12" fill="currentColor">
                            <path d="M10 3L5 8.5 2 5.5"/>
                        </svg>
                    </div>
                </button>
            @endforeach
        </div>
    </div>

    {{-- ── Step 2: Language ─────────────────────────────────────── --}}
    <div x-show="step === 2" x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
        <div class="grid grid-cols-3 gap-3 sm:grid-cols-4 lg:grid-cols-5">
            @foreach($technologies['languages'] as $lang)
                <button type="button"
                        @click="selectLanguage('{{ $lang['id'] }}')"
                        :class="language === '{{ $lang['id'] }}'
                            ? 'border-zinc-400 bg-zinc-800 ring-1 ring-zinc-400'
                            : 'border-zinc-700 bg-zinc-900 hover:border-zinc-600 hover:bg-zinc-800'"
                        class="group relative flex flex-col items-center gap-2.5 rounded-2xl border p-4 text-center transition-all">

                    {{-- Language badge --}}
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl text-lg font-bold"
                         style="background-color: {{ $lang['bg'] }}; color: {{ $lang['color'] }};">
                        {{ $lang['abbr'] }}
                    </div>
                    <p class="text-xs font-medium text-zinc-300 leading-tight">{{ $lang['label'] }}</p>

                    <div x-show="language === '{{ $lang['id'] }}'"
                         class="absolute right-2 top-2 flex h-4 w-4 items-center justify-center rounded-full bg-white">
                        <svg class="h-2.5 w-2.5 text-zinc-900" viewBox="0 0 12 12" fill="currentColor">
                            <path d="M10 3L5 8.5 2 5.5"/>
                        </svg>
                    </div>
                </button>
            @endforeach
        </div>
    </div>

    {{-- ── Step 3: Framework ────────────────────────────────────── --}}
    <div x-show="step === 3" x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">

        <template x-if="frameworks.length === 0">
            <div class="rounded-2xl border border-zinc-700 bg-zinc-900 px-6 py-12 text-center">
                <p class="text-sm text-zinc-400">No frameworks available for the selected language. Continue to the next step.</p>
            </div>
        </template>

        <div x-show="frameworks.length > 0" class="space-y-2">
            {{-- "No framework" option --}}
            <button type="button"
                    @click="framework = null"
                    :class="framework === null
                        ? 'border-zinc-400 bg-zinc-800 ring-1 ring-zinc-400'
                        : 'border-zinc-700 bg-zinc-900 hover:border-zinc-600 hover:bg-zinc-800'"
                    class="relative flex w-full items-center gap-4 rounded-xl border px-4 py-3 text-left transition-all">
                <div class="flex h-8 w-8 items-center justify-center rounded-lg border border-zinc-700 bg-zinc-800 text-zinc-500">
                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-zinc-300">No framework</p>
                    <p class="text-xs text-zinc-500">Use the language directly without a framework</p>
                </div>
            </button>

            <template x-for="fw in frameworks" :key="fw.id">
                <button type="button"
                        @click="selectFramework(fw.id)"
                        :class="framework === fw.id
                            ? 'border-zinc-400 bg-zinc-800 ring-1 ring-zinc-400'
                            : 'border-zinc-700 bg-zinc-900 hover:border-zinc-600 hover:bg-zinc-800'"
                        class="relative flex w-full items-center gap-4 rounded-xl border px-4 py-3 text-left transition-all">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg border border-zinc-700 bg-zinc-800 text-xs font-bold text-zinc-300"
                         x-text="fw.label.charAt(0)">
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-zinc-200" x-text="fw.label"></p>
                        <p class="text-xs text-zinc-500 truncate" x-text="fw.description"></p>
                    </div>
                    <div x-show="framework === fw.id"
                         class="shrink-0 flex h-4 w-4 items-center justify-center rounded-full bg-white">
                        <svg class="h-2.5 w-2.5 text-zinc-900" viewBox="0 0 12 12" fill="currentColor">
                            <path d="M10 3L5 8.5 2 5.5"/>
                        </svg>
                    </div>
                </button>
            </template>
        </div>
    </div>

    {{-- ── Step 4: Integrations ─────────────────────────────────── --}}
    <div x-show="step === 4" x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">

        <div class="space-y-5">
            @foreach($technologies['integrations'] as $category => $items)
                <div>
                    <p class="mb-2 text-xs font-semibold uppercase tracking-widest text-zinc-500">{{ $category }}</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach($items as $item)
                            <button type="button"
                                    @click="toggleIntegration('{{ $item['id'] }}')"
                                    :class="isIntegrationSelected('{{ $item['id'] }}')
                                        ? 'bg-zinc-700 border-zinc-500 text-zinc-100 ring-1 ring-zinc-500'
                                        : 'bg-zinc-900 border-zinc-700 text-zinc-400 hover:bg-zinc-800 hover:text-zinc-200 hover:border-zinc-600'"
                                    class="inline-flex items-center gap-1.5 rounded-full border px-3 py-1.5 text-xs font-medium transition-all"
                                    :title="'{{ addslashes($item['description']) }}'">
                                <span x-show="isIntegrationSelected('{{ $item['id'] }}')" class="text-emerald-400">✓</span>
                                {{ $item['label'] }}
                            </button>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

        <p class="mt-4 text-xs text-zinc-600">
            Select any integrations you'd like set up. These can be changed later in project settings.
        </p>
    </div>

    {{-- ── Step 5: Name + confirm ───────────────────────────────── --}}
    <div x-show="step === 5" x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">

        <div class="space-y-6">

            <div class="space-y-2">
                <label class="block text-sm font-medium text-zinc-300" for="project-name">
                    Project name
                </label>
                <input id="project-name"
                       x-model="name"
                       type="text"
                       maxlength="120"
                       placeholder="My Awesome Project"
                       autofocus
                       class="w-full rounded-xl border border-zinc-700 bg-zinc-900 px-4 py-3 text-base text-zinc-100 placeholder-zinc-600 transition-colors focus:border-zinc-500 focus:outline-none focus:ring-1 focus:ring-zinc-500" />
            </div>

            {{-- Summary card --}}
            <div class="rounded-2xl border border-zinc-700 bg-zinc-900 p-5 space-y-3">
                <p class="text-xs font-semibold uppercase tracking-widest text-zinc-500">Project summary</p>

                <div class="grid grid-cols-2 gap-3 text-sm">
                    <div>
                        <p class="text-xs text-zinc-500">Type</p>
                        <p class="mt-0.5 font-medium text-zinc-200 capitalize" x-text="projectTypeLabel"></p>
                    </div>
                    <div>
                        <p class="text-xs text-zinc-500">Language</p>
                        <p class="mt-0.5 font-medium text-zinc-200" x-text="languageLabel"></p>
                    </div>
                    <div>
                        <p class="text-xs text-zinc-500">Framework</p>
                        <p class="mt-0.5 font-medium text-zinc-200" x-text="frameworkLabel || 'None'"></p>
                    </div>
                    <div>
                        <p class="text-xs text-zinc-500">Integrations</p>
                        <p class="mt-0.5 font-medium text-zinc-200"
                           x-text="integrations.length ? `${integrations.length} selected` : 'None'"></p>
                    </div>
                </div>

                <template x-if="integrations.length > 0">
                    <div class="flex flex-wrap gap-1.5 pt-1">
                        <template x-for="id in integrations" :key="id">
                            <span class="inline-flex items-center rounded-full bg-zinc-800 border border-zinc-700 px-2 py-0.5 text-xs text-zinc-400"
                                  x-text="integrationLabel(id)"></span>
                        </template>
                    </div>
                </template>
            </div>

        </div>
    </div>

    {{-- Hidden form that actually submits — must be outside all other forms --}}
    <form id="project-form" method="POST" action="{{ route('projects.store') }}" class="hidden">
        @csrf
    </form>

    {{-- ── Navigation ───────────────────────────────────────────── --}}
    <div class="flex items-center justify-between border-t border-zinc-800 pt-6">
        <button type="button"
                @click="back"
                x-show="step > 1"
                class="flex items-center gap-2 rounded-xl border border-zinc-700 bg-zinc-900 px-4 py-2 text-sm font-medium text-zinc-300 transition-colors hover:bg-zinc-800 hover:text-zinc-100">
            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/>
            </svg>
            Back
        </button>
        <div x-show="step === 1"></div>

        <template x-if="step < totalSteps">
            <button type="button"
                    @click="next"
                    :disabled="!canProceed"
                    :class="canProceed
                        ? 'bg-white text-zinc-900 hover:bg-zinc-100 cursor-pointer'
                        : 'bg-zinc-800 text-zinc-600 cursor-not-allowed'"
                    class="flex items-center gap-2 rounded-xl px-5 py-2 text-sm font-semibold transition-colors">
                Continue
                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/>
                </svg>
            </button>
        </template>

        <template x-if="step === totalSteps">
            <button type="button"
                    @click="submitForm"
                    :disabled="!canProceed"
                    :class="canProceed
                        ? 'bg-white text-zinc-900 hover:bg-zinc-100 cursor-pointer'
                        : 'bg-zinc-800 text-zinc-600 cursor-not-allowed'"
                    class="flex items-center gap-2 rounded-xl px-5 py-2 text-sm font-semibold transition-colors">
                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z"/>
                </svg>
                Create Project
            </button>
        </template>
    </div>

</div>

<script>
function projectWizard(tech) {
    return {
        step: 1,
        totalSteps: 5,
        project_type: null,
        language: null,
        framework: null,
        integrations: [],
        name: '',

        get frameworks() {
            if (!this.language) return [];
            return (tech.frameworks && tech.frameworks[this.language]) || [];
        },

        get projectTypeLabel() {
            const t = (tech.project_types || []).find(t => t.id === this.project_type);
            return t ? t.label : '—';
        },

        get languageLabel() {
            const l = (tech.languages || []).find(l => l.id === this.language);
            return l ? l.label : '—';
        },

        get frameworkLabel() {
            if (!this.framework || !this.language) return null;
            const fws = (tech.frameworks && tech.frameworks[this.language]) || [];
            const f = fws.find(f => f.id === this.framework);
            return f ? f.label : null;
        },

        integrationLabel(id) {
            for (const cat of Object.values(tech.integrations || {})) {
                const found = cat.find(i => i.id === id);
                if (found) return found.label;
            }
            return id;
        },

        selectType(id)      { this.project_type = id; },
        selectLanguage(id)  { this.language = id; this.framework = null; },
        selectFramework(id) { this.framework = id; },

        toggleIntegration(id) {
            const idx = this.integrations.indexOf(id);
            if (idx >= 0) this.integrations.splice(idx, 1);
            else this.integrations.push(id);
        },

        isIntegrationSelected(id) {
            return this.integrations.includes(id);
        },

        get canProceed() {
            if (this.step === 1) return this.project_type !== null;
            if (this.step === 2) return this.language !== null;
            if (this.step === 5) return this.name.trim().length > 0;
            return true;
        },

        next() {
            if (this.canProceed) this.step = Math.min(this.step + 1, this.totalSteps);
        },
        back() {
            this.step = Math.max(this.step - 1, 1);
        },

        submitForm() {
            if (!this.canProceed) return;

            const form = document.getElementById('project-form');

            // Clear any previously appended inputs, keeping only the CSRF token
            form.querySelectorAll('input:not([name="_token"])').forEach(el => el.remove());

            const add = (name, value) => {
                const el = document.createElement('input');
                el.type = 'hidden';
                el.name = name;
                el.value = value;
                form.appendChild(el);
            };

            add('name', this.name);
            add('type', this.project_type);
            add('language', this.language);
            add('framework', this.framework || '');
            this.integrations.forEach(id => add('integrations[]', id));

            form.submit();
        },
    };
}
</script>

</x-layouts.app>
