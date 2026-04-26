<?php

return [
    'brand' => [
        'name' => 'Linavelt',
    ],

    'homepage' => [
        'badge' => 'Laravel-inspired full-stack studio',
        'headline' => 'Design the stack first. Build the website end-to-end after.',
        'description' => 'Linavelt combines the clarity of Laravel with the visual workflow spirit of Figma, Zoho, and Odoo. Pick your preferred technologies, lock your architecture, then ship a complete website from UI to backend in one connected experience.',
        'blueprint_title' => 'Project Blueprint',
        'blueprint_steps' => [
            [
                'label' => 'Step 1',
                'text' => 'Choose frontend, backend, CMS, and integrations.',
            ],
            [
                'label' => 'Step 2',
                'text' => 'Generate a connected workspace with your preferred stack.',
            ],
            [
                'label' => 'Step 3',
                'text' => 'Ship design, logic, and deployment from one pipeline.',
            ],
        ],
        'feature_cards' => [
            [
                'kicker' => 'Technology Control',
                'title' => 'Build your own stack matrix',
                'text' => 'Select frameworks, runtimes, and deployment preferences before writing screens or API code.',
            ],
            [
                'kicker' => 'Visual + Code Workflow',
                'title' => 'Design logic meets source control',
                'text' => 'Bridge page-building speed with full-stack implementation so teams stay aligned across roles.',
            ],
            [
                'kicker' => 'From Idea to Launch',
                'title' => 'A complete website pipeline',
                'text' => 'Move from concept to production with generated structure, Laravel backend flow, and deployment-ready output.',
            ],
        ],
        'stack_heading' => 'Powered by your current stack',
        'stack_description' => 'Laravel 11, Livewire, Flux, Tailwind v4, Vite, and MCP integrations are already in this workspace.',
        'stack_badges' => [
            'Laravel',
            'Livewire',
            'Tailwind v4',
            'MCP',
        ],
    ],

    'onboarding' => [
        'title' => 'Builder Onboarding',
        'subtitle' => 'Choose your stack profile, then launch a guided full-stack workflow.',
        'domains' => [
            [
                'key' => 'frontend_layer',
                'name' => 'Frontend Layer',
                'description' => 'Define how your interface is composed and styled.',
                'options' => ['Livewire + Flux', 'Vue + Inertia', 'React + Inertia', 'Blade + Alpine'],
            ],
            [
                'key' => 'backend_layer',
                'name' => 'Backend Layer',
                'description' => 'Select the architecture style and service boundaries.',
                'options' => ['Laravel Monolith', 'Laravel API + SPA', 'Modular Laravel', 'Hybrid with MCP Services'],
            ],
            [
                'key' => 'data_integrations',
                'name' => 'Data + Integrations',
                'description' => 'Plan storage, automation, and external platform connections.',
                'options' => ['MySQL + Queues', 'PostgreSQL + Queues', 'Mixed SQL + Search', 'MCP-driven Integrations'],
            ],
        ],
        'timeline' => [
            'Technology Selection',
            'Project Scaffolding',
            'Page and Logic Generation',
            'Testing, QA, and Launch',
        ],
    ],

    'dashboard' => [
        'kicker' => 'Workspace Command Center',
        'title' => 'Build, test, and ship your stack-defined website.',
        'description' => 'Track your architecture decisions, implementation progress, and launch readiness from one dashboard.',
        'metrics' => [
            [
                'label' => 'Configured Modules',
                'value' => '12',
                'hint' => 'Frontend, backend, auth, CMS, and deployment tracks.',
            ],
            [
                'label' => 'Automation Jobs',
                'value' => '8',
                'hint' => 'Scheduler, security automation, and MCP routines.',
            ],
            [
                'label' => 'Pipeline Health',
                'value' => 'Stable',
                'hint' => 'Core dependencies and build steps are operational.',
            ],
        ],
        'workstreams' => [
            [
                'title' => 'Technology Matrix',
                'text' => 'Review and adjust selected technologies before sprint execution.',
            ],
            [
                'title' => 'Build Progress',
                'text' => 'Monitor generated pages, component completion, and backend endpoints.',
            ],
            [
                'title' => 'Release Readiness',
                'text' => 'Track test coverage, deployment checks, and launch blockers.',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Module Generation Templates
    |--------------------------------------------------------------------------
    | Maps each domain option value to the set of modules/scaffolding files
    | that will be generated when a user's project modules are built.
    |
    | Each module entry has:
    |   path   – destination path relative to the user's project directory
    |   stub   – content stub (use {{NAME}} as a placeholder token)
    */
    'module_templates' => [
        'frontend_layer' => [
            'Livewire + Flux' => [
                ['path' => 'resources/views/pages/home.blade.php',   'stub' => '<x-layouts.app><flux:heading>Home</flux:heading></x-layouts.app>'],
                ['path' => 'app/Livewire/Pages/Home.php',            'stub' => "<?php\n\nnamespace App\\Livewire\\Pages;\n\nuse Livewire\\Component;\n\nclass Home extends Component\n{\n    public function render() { return view('pages.home'); }\n}\n"],
            ],
            'Vue + Inertia' => [
                ['path' => 'resources/js/Pages/Home.vue', 'stub' => "<template>\n  <div>\n    <h1>Home</h1>\n  </div>\n</template>\n"],
                ['path' => 'resources/js/app.js',         'stub' => "import './bootstrap';\nimport { createApp, h } from 'vue';\nimport { createInertiaApp } from '@inertiajs/vue3';\ncreateInertiaApp({ resolve: name => require(`./Pages/${name}`), setup({ el, App, props, plugin }) { createApp({ render: () => h(App, props) }).use(plugin).mount(el); } });\n"],
            ],
            'React + Inertia' => [
                ['path' => 'resources/js/Pages/Home.jsx', 'stub' => "export default function Home() {\n  return <h1>Home</h1>;\n}\n"],
                ['path' => 'resources/js/app.jsx',        'stub' => "import { createInertiaApp } from '@inertiajs/react';\nimport { createRoot } from 'react-dom/client';\ncreateInertiaApp({ resolve: name => require(`./Pages/${name}`), setup({ el, App, props }) { createRoot(el).render(<App {...props} />); } });\n"],
            ],
            'Blade + Alpine' => [
                ['path' => 'resources/views/pages/home.blade.php', 'stub' => "<x-layouts.app>\n  <div x-data=\"{ open: false }\">\n    <h1 class=\"text-2xl font-bold\">Home</h1>\n  </div>\n</x-layouts.app>\n"],
            ],
        ],

        'backend_layer' => [
            'Laravel Monolith' => [
                ['path' => 'app/Http/Controllers/PageController.php',   'stub' => "<?php\n\nnamespace App\\Http\\Controllers;\n\nuse Illuminate\\View\\View;\n\nclass PageController extends Controller\n{\n    public function home(): View\n    {\n        return view('pages.home');\n    }\n}\n"],
                ['path' => 'routes/web-generated.php',                  'stub' => "<?php\n\nuse App\\Http\\Controllers\\PageController;\nuse Illuminate\\Support\\Facades\\Route;\n\nRoute::get('/', [PageController::class, 'home'])->name('home.generated');\n"],
            ],
            'Laravel API + SPA' => [
                ['path' => 'app/Http/Controllers/Api/StatusController.php', 'stub' => "<?php\n\nnamespace App\\Http\\Controllers\\Api;\n\nuse Illuminate\\Http\\JsonResponse;\nuse Illuminate\\Routing\\Controller;\n\nclass StatusController extends Controller\n{\n    public function index(): JsonResponse\n    {\n        return response()->json(['status' => 'ok']);\n    }\n}\n"],
                ['path' => 'routes/api-generated.php',                      'stub' => "<?php\n\nuse App\\Http\\Controllers\\Api\\StatusController;\nuse Illuminate\\Support\\Facades\\Route;\n\nRoute::get('/status', [StatusController::class, 'index']);\n"],
            ],
            'Modular Laravel' => [
                ['path' => 'modules/Core/Providers/CoreServiceProvider.php', 'stub' => "<?php\n\nnamespace Modules\\Core\\Providers;\n\nuse Illuminate\\Support\\ServiceProvider;\n\nclass CoreServiceProvider extends ServiceProvider\n{\n    public function register(): void {}\n    public function boot(): void {}\n}\n"],
                ['path' => 'modules/Core/routes/web.php',                    'stub' => "<?php\n\nuse Illuminate\\Support\\Facades\\Route;\n\nRoute::get('/core', fn () => 'Core module active');\n"],
            ],
            'Hybrid with MCP Services' => [
                ['path' => 'app/Services/McpClient.php', 'stub' => "<?php\n\nnamespace App\\Services;\n\nuse Illuminate\\Support\\Facades\\Http;\n\nclass McpClient\n{\n    public function __construct(private readonly string \$baseUrl, private readonly string \$apiKey) {}\n\n    public function status(): array\n    {\n        return Http::withToken(\$this->apiKey)->get(\"\$this->baseUrl/status\")->json();\n    }\n}\n"],
            ],
        ],

        'data_integrations' => [
            'MySQL + Queues' => [
                ['path' => 'database/migrations/generated_create_posts_table.php', 'stub' => "<?php\n\nuse Illuminate\\Database\\Migrations\\Migration;\nuse Illuminate\\Database\\Schema\\Blueprint;\nuse Illuminate\\Support\\Facades\\Schema;\n\nreturn new class extends Migration {\n    public function up(): void\n    {\n        Schema::create('posts', function (Blueprint \$table) {\n            \$table->id();\n            \$table->string('title');\n            \$table->text('body')->nullable();\n            \$table->timestamps();\n        });\n    }\n    public function down(): void { Schema::dropIfExists('posts'); }\n};\n"],
                ['path' => 'app/Jobs/SampleQueueJob.php',                          'stub' => "<?php\n\nnamespace App\\Jobs;\n\nuse Illuminate\\Bus\\Queueable;\nuse Illuminate\\Contracts\\Queue\\ShouldQueue;\nuse Illuminate\\Foundation\\Bus\\Dispatchable;\nuse Illuminate\\Queue\\InteractsWithQueue;\nuse Illuminate\\Queue\\SerializesModels;\n\nclass SampleQueueJob implements ShouldQueue\n{\n    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;\n\n    public function handle(): void\n    {\n        // Sample queue processing logic\n    }\n}\n"],
            ],
            'PostgreSQL + Queues' => [
                ['path' => 'database/migrations/generated_create_posts_table.php', 'stub' => "<?php\n\nuse Illuminate\\Database\\Migrations\\Migration;\nuse Illuminate\\Database\\Schema\\Blueprint;\nuse Illuminate\\Support\\Facades\\Schema;\n\nreturn new class extends Migration {\n    public function up(): void\n    {\n        Schema::create('posts', function (Blueprint \$table) {\n            \$table->id();\n            \$table->string('title');\n            \$table->text('body')->nullable();\n            \$table->timestampsTz();\n        });\n    }\n    public function down(): void { Schema::dropIfExists('posts'); }\n};\n"],
                ['path' => 'app/Jobs/SampleQueueJob.php', 'stub' => "<?php\n\nnamespace App\\Jobs;\n\nuse Illuminate\\Bus\\Queueable;\nuse Illuminate\\Contracts\\Queue\\ShouldQueue;\nuse Illuminate\\Foundation\\Bus\\Dispatchable;\nuse Illuminate\\Queue\\InteractsWithQueue;\nuse Illuminate\\Queue\\SerializesModels;\n\nclass SampleQueueJob implements ShouldQueue\n{\n    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;\n\n    public function handle(): void\n    {\n        // Sample queue processing logic\n    }\n}\n"],
            ],
            'Mixed SQL + Search' => [
                ['path' => 'app/Models/SearchablePost.php', 'stub' => "<?php\n\nnamespace App\\Models;\n\nuse Illuminate\\Database\\Eloquent\\Model;\n\nclass SearchablePost extends Model\n{\n    protected \$table = 'posts';\n    protected \$fillable = ['title', 'body'];\n}\n"],
            ],
            'MCP-driven Integrations' => [
                ['path' => 'app/Services/McpDataSync.php', 'stub' => "<?php\n\nnamespace App\\Services;\n\nclass McpDataSync\n{\n    public function sync(array \$payload): bool\n    {\n        // Sync data through MCP pipeline\n        return true;\n    }\n}\n"],
            ],
        ],
    ],
];
