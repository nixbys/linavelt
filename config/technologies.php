<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Project Types
    |--------------------------------------------------------------------------
    */
    'project_types' => [
        [
            'id'          => 'website',
            'label'       => 'Website',
            'description' => 'Marketing, portfolio, or content site',
            'icon'        => 'globe-alt',
        ],
        [
            'id'          => 'web_app',
            'label'       => 'Web Application',
            'description' => 'Interactive app with backend logic and user accounts',
            'icon'        => 'squares-2x2',
        ],
        [
            'id'          => 'static_site',
            'label'       => 'Static Site',
            'description' => 'JAMstack or pre-rendered, deployed to a CDN',
            'icon'        => 'bolt',
        ],
        [
            'id'          => 'mobile_app',
            'label'       => 'Mobile App',
            'description' => 'Cross-platform mobile app (React Native, Flutter)',
            'icon'        => 'device-phone-mobile',
        ],
        [
            'id'          => 'desktop_app',
            'label'       => 'Desktop App',
            'description' => 'Native-like desktop app via Electron or Tauri',
            'icon'        => 'computer-desktop',
        ],
        [
            'id'          => 'api',
            'label'       => 'API / Backend',
            'description' => 'REST, GraphQL, or gRPC service with no frontend',
            'icon'        => 'server',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Languages
    |--------------------------------------------------------------------------
    */
    'languages' => [
        [
            'id'     => 'php',
            'label'  => 'PHP',
            'abbr'   => 'PHP',
            'color'  => '#8892BF',
            'bg'     => '#1e1e3a',
        ],
        [
            'id'     => 'javascript',
            'label'  => 'JavaScript',
            'abbr'   => 'JS',
            'color'  => '#F7DF1E',
            'bg'     => '#2a2600',
        ],
        [
            'id'     => 'typescript',
            'label'  => 'TypeScript',
            'abbr'   => 'TS',
            'color'  => '#3178C6',
            'bg'     => '#0f1f3a',
        ],
        [
            'id'     => 'python',
            'label'  => 'Python',
            'abbr'   => 'PY',
            'color'  => '#3776AB',
            'bg'     => '#0f1f30',
        ],
        [
            'id'     => 'ruby',
            'label'  => 'Ruby',
            'abbr'   => 'RB',
            'color'  => '#CC342D',
            'bg'     => '#2d0f0e',
        ],
        [
            'id'     => 'go',
            'label'  => 'Go',
            'abbr'   => 'Go',
            'color'  => '#00ADD8',
            'bg'     => '#002933',
        ],
        [
            'id'     => 'rust',
            'label'  => 'Rust',
            'abbr'   => 'RS',
            'color'  => '#DEA584',
            'bg'     => '#2d1a0e',
        ],
        [
            'id'     => 'java',
            'label'  => 'Java',
            'abbr'   => 'JV',
            'color'  => '#ED8B00',
            'bg'     => '#2d1e00',
        ],
        [
            'id'     => 'csharp',
            'label'  => 'C#',
            'abbr'   => 'C#',
            'color'  => '#9B4993',
            'bg'     => '#1e0f1e',
        ],
        [
            'id'     => 'swift',
            'label'  => 'Swift',
            'abbr'   => 'SW',
            'color'  => '#FA7343',
            'bg'     => '#2d1200',
        ],
        [
            'id'     => 'kotlin',
            'label'  => 'Kotlin',
            'abbr'   => 'KT',
            'color'  => '#7F52FF',
            'bg'     => '#160f2d',
        ],
        [
            'id'     => 'html',
            'label'  => 'HTML / CSS',
            'abbr'   => 'WEB',
            'color'  => '#E34C26',
            'bg'     => '#2d0f00',
        ],
        [
            'id'     => 'dart',
            'label'  => 'Dart',
            'abbr'   => 'DT',
            'color'  => '#00B4AB',
            'bg'     => '#002a28',
        ],
        [
            'id'     => 'elixir',
            'label'  => 'Elixir',
            'abbr'   => 'EX',
            'color'  => '#6E4A7E',
            'bg'     => '#180f1e',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Frameworks (keyed by language id)
    |--------------------------------------------------------------------------
    */
    'frameworks' => [

        'php' => [
            ['id' => 'laravel',      'label' => 'Laravel',      'description' => 'The PHP Framework for Web Artisans — full-stack, batteries included'],
            ['id' => 'symfony',      'label' => 'Symfony',      'description' => 'High-performance enterprise PHP framework'],
            ['id' => 'codeigniter',  'label' => 'CodeIgniter',  'description' => 'Lightweight framework with a tiny footprint'],
            ['id' => 'wordpress',    'label' => 'WordPress',    'description' => "World's most popular CMS and plugin ecosystem"],
            ['id' => 'lumen',        'label' => 'Lumen',        'description' => 'Laravel micro-framework for API services'],
            ['id' => 'slim',         'label' => 'Slim',         'description' => 'Minimal micro-framework for APIs and microservices'],
        ],

        'javascript' => [
            ['id' => 'react',     'label' => 'React',      'description' => 'Component-based UI library from Meta'],
            ['id' => 'vue',       'label' => 'Vue.js',     'description' => 'Progressive JavaScript framework — approachable and versatile'],
            ['id' => 'angular',   'label' => 'Angular',    'description' => 'Opinionated platform for building large-scale web apps'],
            ['id' => 'svelte',    'label' => 'Svelte',     'description' => 'Compiles away at build time — no virtual DOM'],
            ['id' => 'nextjs',    'label' => 'Next.js',    'description' => 'React meta-framework with SSR/SSG/ISR built in'],
            ['id' => 'nuxt',      'label' => 'Nuxt.js',    'description' => 'Vue meta-framework — intuitive and full-stack ready'],
            ['id' => 'astro',     'label' => 'Astro',      'description' => 'Content-first static site builder — ships zero JS by default'],
            ['id' => 'remix',     'label' => 'Remix',      'description' => 'Full-stack web framework focused on web fundamentals'],
            ['id' => 'solidjs',   'label' => 'SolidJS',    'description' => 'Fine-grained reactivity, no virtual DOM overhead'],
            ['id' => 'express',   'label' => 'Express',    'description' => 'Minimal Node.js web framework for APIs and servers'],
            ['id' => 'nestjs',    'label' => 'NestJS',     'description' => 'Scalable Node.js framework with TypeScript-first DX'],
            ['id' => 'vanilla',   'label' => 'Vanilla JS', 'description' => 'No framework — pure JavaScript with full control'],
        ],

        'typescript' => [
            ['id' => 'react_ts',   'label' => 'React + TypeScript',  'description' => 'React with strict TypeScript typing'],
            ['id' => 'nextjs_ts',  'label' => 'Next.js + TypeScript', 'description' => 'Full-stack React with TypeScript'],
            ['id' => 'angular',    'label' => 'Angular',              'description' => 'TypeScript-first enterprise framework'],
            ['id' => 'nestjs_ts',  'label' => 'NestJS',               'description' => 'TypeScript Node.js backend framework'],
            ['id' => 'nuxt_ts',    'label' => 'Nuxt + TypeScript',    'description' => 'Nuxt with full TypeScript support'],
            ['id' => 'svelte_ts',  'label' => 'SvelteKit',            'description' => 'Full-stack Svelte meta-framework'],
        ],

        'python' => [
            ['id' => 'django',   'label' => 'Django',   'description' => 'Batteries-included web framework for perfectionists with deadlines'],
            ['id' => 'flask',    'label' => 'Flask',    'description' => 'Lightweight WSGI micro-framework with maximum flexibility'],
            ['id' => 'fastapi',  'label' => 'FastAPI',  'description' => 'Modern async API framework — OpenAPI docs built in'],
            ['id' => 'starlette','label' => 'Starlette','description' => 'Lightweight ASGI framework for async Python'],
            ['id' => 'tornado',  'label' => 'Tornado',  'description' => 'Async networking framework for real-time apps'],
            ['id' => 'pyramid',  'label' => 'Pyramid',  'description' => 'Flexible framework that starts small and scales'],
        ],

        'ruby' => [
            ['id' => 'rails',   'label' => 'Ruby on Rails', 'description' => 'Convention over configuration — the full-stack gold standard'],
            ['id' => 'sinatra', 'label' => 'Sinatra',       'description' => 'DSL for quick HTTP services and APIs'],
            ['id' => 'hanami',  'label' => 'Hanami',        'description' => 'Modern, clean-architecture Ruby framework'],
        ],

        'go' => [
            ['id' => 'gin',   'label' => 'Gin',   'description' => 'Fast HTTP framework with a Martini-like API'],
            ['id' => 'echo',  'label' => 'Echo',  'description' => 'High-performance, extensible Go web framework'],
            ['id' => 'fiber', 'label' => 'Fiber', 'description' => 'Express-inspired, built on Fasthttp for speed'],
            ['id' => 'chi',   'label' => 'Chi',   'description' => 'Lightweight, idiomatic router for Go'],
        ],

        'rust' => [
            ['id' => 'actix', 'label' => 'Actix Web', 'description' => 'Powerful, pragmatic, and extremely fast web framework'],
            ['id' => 'axum',  'label' => 'Axum',      'description' => 'Ergonomic and modular framework built on Tokio + Tower'],
            ['id' => 'rocket','label' => 'Rocket',    'description' => 'Type-safe, batteries-included Rust web framework'],
            ['id' => 'tide',  'label' => 'Tide',      'description' => 'Modular async web framework'],
        ],

        'java' => [
            ['id' => 'spring_boot', 'label' => 'Spring Boot', 'description' => 'Production-grade Spring applications with minimal config'],
            ['id' => 'quarkus',     'label' => 'Quarkus',     'description' => 'Kubernetes-native Java — supersonic startup times'],
            ['id' => 'micronaut',   'label' => 'Micronaut',   'description' => 'JVM framework optimised for microservices'],
            ['id' => 'vert_x',      'label' => 'Vert.x',      'description' => 'Reactive, polyglot event-driven toolkit for the JVM'],
        ],

        'csharp' => [
            ['id' => 'aspnet',   'label' => 'ASP.NET Core', 'description' => 'Cross-platform, high-performance web framework from Microsoft'],
            ['id' => 'blazor',   'label' => 'Blazor',       'description' => 'Build interactive web UIs using C# instead of JavaScript'],
            ['id' => 'minimal',  'label' => 'Minimal APIs', 'description' => 'Lightweight endpoint-only .NET APIs'],
        ],

        'swift' => [
            ['id' => 'vapor',   'label' => 'Vapor',     'description' => 'Server-side Swift web framework'],
            ['id' => 'hummingbird','label' => 'Hummingbird','description' => 'Lightweight HTTP server framework for Swift'],
        ],

        'kotlin' => [
            ['id' => 'ktor',   'label' => 'Ktor',   'description' => 'Async web framework for Kotlin — built by JetBrains'],
            ['id' => 'spring', 'label' => 'Spring', 'description' => 'Spring Boot with Kotlin coroutines'],
        ],

        'html' => [
            ['id' => 'vanilla',    'label' => 'Vanilla',       'description' => 'Pure HTML, CSS, and JavaScript — no build step needed'],
            ['id' => 'bootstrap',  'label' => 'Bootstrap',     'description' => 'The world\'s most popular CSS component framework'],
            ['id' => 'tailwind',   'label' => 'Tailwind CSS',  'description' => 'Utility-first CSS framework with JIT compilation'],
            ['id' => 'foundation', 'label' => 'Foundation',    'description' => 'Responsive front-end framework by Zurb'],
            ['id' => 'bulma',      'label' => 'Bulma',         'description' => 'Modern CSS framework based on Flexbox'],
            ['id' => '11ty',       'label' => 'Eleventy',      'description' => 'Zero-config static site generator'],
        ],

        'dart' => [
            ['id' => 'flutter', 'label' => 'Flutter', 'description' => 'Google\'s UI toolkit for cross-platform native apps'],
            ['id' => 'aqueduct','label' => 'Aqueduct','description' => 'Server-side Dart HTTP framework'],
        ],

        'elixir' => [
            ['id' => 'phoenix',    'label' => 'Phoenix',     'description' => 'Productive. Reliable. Fast. The Elixir web framework'],
            ['id' => 'plug',       'label' => 'Plug',        'description' => 'Composable Elixir web middleware'],
            ['id' => 'nerves',     'label' => 'Nerves',      'description' => 'Build embedded systems with Elixir'],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Integrations / Add-ons
    |--------------------------------------------------------------------------
    */
    'integrations' => [

        'Styling' => [
            ['id' => 'tailwindcss', 'label' => 'Tailwind CSS',   'description' => 'Utility-first CSS'],
            ['id' => 'sass',        'label' => 'Sass / SCSS',     'description' => 'CSS preprocessor'],
            ['id' => 'postcss',     'label' => 'PostCSS',         'description' => 'CSS transformations'],
            ['id' => 'css_modules', 'label' => 'CSS Modules',     'description' => 'Scoped CSS files'],
            ['id' => 'styled',      'label' => 'styled-components','description' => 'CSS-in-JS'],
        ],

        'UI Libraries' => [
            ['id' => 'shadcn',      'label' => 'shadcn/ui',    'description' => 'Re-usable Tailwind + Radix components'],
            ['id' => 'radix',       'label' => 'Radix UI',     'description' => 'Accessible, unstyled primitives'],
            ['id' => 'headlessui',  'label' => 'Headless UI',  'description' => 'Unstyled accessible components'],
            ['id' => 'mantine',     'label' => 'Mantine',      'description' => 'Full-featured React component library'],
            ['id' => 'daisy',       'label' => 'DaisyUI',      'description' => 'Tailwind CSS component library'],
            ['id' => 'chakra',      'label' => 'Chakra UI',    'description' => 'Accessible React component system'],
        ],

        'Build & Tooling' => [
            ['id' => 'vite',     'label' => 'Vite',    'description' => 'Lightning-fast build tool'],
            ['id' => 'webpack',  'label' => 'Webpack', 'description' => 'Bundler for JS applications'],
            ['id' => 'esbuild',  'label' => 'esbuild', 'description' => 'Extremely fast bundler'],
            ['id' => 'turbo',    'label' => 'Turborepo','description' => 'Monorepo build system'],
            ['id' => 'docker',   'label' => 'Docker',  'description' => 'Container platform'],
            ['id' => 'nix',      'label' => 'Nix',     'description' => 'Reproducible dev environments'],
        ],

        'Database' => [
            ['id' => 'postgres',  'label' => 'PostgreSQL', 'description' => 'Advanced open-source relational DB'],
            ['id' => 'mysql',     'label' => 'MySQL',      'description' => 'Popular relational database'],
            ['id' => 'sqlite',    'label' => 'SQLite',     'description' => 'Lightweight embedded database'],
            ['id' => 'mongodb',   'label' => 'MongoDB',    'description' => 'Document-oriented NoSQL'],
            ['id' => 'planetscale','label' => 'PlanetScale','description' => 'MySQL-compatible serverless DB'],
            ['id' => 'supabase',  'label' => 'Supabase',  'description' => 'Open-source Firebase alternative'],
        ],

        'Caching & Queue' => [
            ['id' => 'redis',    'label' => 'Redis',      'description' => 'In-memory data structure store'],
            ['id' => 'memcached','label' => 'Memcached',  'description' => 'Distributed memory caching'],
            ['id' => 'rabbitmq', 'label' => 'RabbitMQ',   'description' => 'Message broker'],
            ['id' => 'kafka',    'label' => 'Apache Kafka','description' => 'Distributed event streaming'],
        ],

        'Authentication' => [
            ['id' => 'auth0',    'label' => 'Auth0',        'description' => 'Identity platform as a service'],
            ['id' => 'clerk',    'label' => 'Clerk',        'description' => 'Complete user management'],
            ['id' => 'supabase_auth','label' => 'Supabase Auth','description' => 'Open-source auth'],
            ['id' => 'nextauth', 'label' => 'NextAuth.js', 'description' => 'Auth for Next.js apps'],
            ['id' => 'lucia',    'label' => 'Lucia',        'description' => 'Simple, flexible auth library'],
        ],

        'Payments' => [
            ['id' => 'stripe',   'label' => 'Stripe',     'description' => 'Online payment infrastructure'],
            ['id' => 'paddle',   'label' => 'Paddle',     'description' => 'Revenue delivery platform'],
            ['id' => 'lemon',    'label' => 'LemonSqueezy','description' => 'Payments for SaaS'],
        ],

        'Deployment' => [
            ['id' => 'vercel',   'label' => 'Vercel',       'description' => 'Frontend deployment platform'],
            ['id' => 'netlify',  'label' => 'Netlify',      'description' => 'Composable web platform'],
            ['id' => 'railway',  'label' => 'Railway',      'description' => 'Infra from code'],
            ['id' => 'fly',      'label' => 'Fly.io',       'description' => 'Deploy near users globally'],
            ['id' => 'aws',      'label' => 'AWS',          'description' => 'Amazon Web Services'],
            ['id' => 'gcp',      'label' => 'Google Cloud', 'description' => 'Google Cloud Platform'],
            ['id' => 'azure',    'label' => 'Azure',        'description' => 'Microsoft Azure'],
        ],

        'CI / CD' => [
            ['id' => 'github_actions', 'label' => 'GitHub Actions', 'description' => 'CI/CD in GitHub'],
            ['id' => 'gitlab_ci',      'label' => 'GitLab CI',      'description' => 'Built-in GitLab pipelines'],
            ['id' => 'circleci',       'label' => 'CircleCI',       'description' => 'Fast, scalable CI/CD'],
        ],

        'API' => [
            ['id' => 'graphql',    'label' => 'GraphQL', 'description' => 'Query language for your API'],
            ['id' => 'rest',       'label' => 'REST',    'description' => 'HTTP API standard'],
            ['id' => 'trpc',       'label' => 'tRPC',    'description' => 'End-to-end type-safe APIs'],
            ['id' => 'openapi',    'label' => 'OpenAPI', 'description' => 'API documentation standard'],
        ],

        'Testing' => [
            ['id' => 'vitest',   'label' => 'Vitest',       'description' => 'Vite-native test framework'],
            ['id' => 'jest',     'label' => 'Jest',         'description' => 'JavaScript testing framework'],
            ['id' => 'playwright','label' => 'Playwright',  'description' => 'End-to-end browser testing'],
            ['id' => 'cypress',  'label' => 'Cypress',      'description' => 'E2E testing for the modern web'],
            ['id' => 'phpunit',  'label' => 'PHPUnit',      'description' => 'PHP testing framework'],
            ['id' => 'pytest',   'label' => 'pytest',       'description' => 'Python testing framework'],
        ],

        'Observability' => [
            ['id' => 'sentry',   'label' => 'Sentry',       'description' => 'Error monitoring and performance'],
            ['id' => 'datadog',  'label' => 'Datadog',      'description' => 'Monitoring and analytics'],
            ['id' => 'posthog',  'label' => 'PostHog',      'description' => 'Product analytics'],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Extensions Marketplace
    |--------------------------------------------------------------------------
    */
    'extensions' => [

        'Featured' => [
            [
                'id'          => 'flux-ui',
                'name'        => 'Flux UI Integration',
                'author'      => 'Livewire Team',
                'description' => 'Full suite of FluxUI blocks and components for the visual canvas — buttons, cards, navbars, data tables, and more.',
                'version'     => '2.1.0',
                'category'    => 'UI Library',
                'installs'    => '24.3k',
                'rating'      => 5,
                'installed'   => true,
                'tags'        => ['ui', 'components', 'livewire'],
            ],
            [
                'id'          => 'tailwind-blocks',
                'name'        => 'Tailwind Block Library',
                'author'      => 'Linavelt',
                'description' => '200+ hand-crafted Tailwind CSS sections: heroes, features, testimonials, pricing tables, footers, and more.',
                'version'     => '1.4.2',
                'category'    => 'Block Library',
                'installs'    => '18.7k',
                'rating'      => 5,
                'installed'   => true,
                'tags'        => ['blocks', 'tailwind', 'sections'],
            ],
            [
                'id'          => 'code-export',
                'name'        => 'Multi-Framework Code Export',
                'author'      => 'Linavelt',
                'description' => 'Export your visual design to clean, production-ready code in React, Vue, Svelte, Laravel Blade, or plain HTML.',
                'version'     => '1.0.0',
                'category'    => 'Code Generation',
                'installs'    => '12.1k',
                'rating'      => 4,
                'installed'   => false,
                'tags'        => ['export', 'codegen', 'react', 'vue'],
            ],
        ],

        'UI & Design' => [
            [
                'id'          => 'icon-pack',
                'name'        => 'Heroicons Pack',
                'author'      => 'Tailwind Labs',
                'description' => 'All 292 Heroicons available as drag-and-drop canvas blocks.',
                'version'     => '2.0.1',
                'category'    => 'Icons',
                'installs'    => '9.2k',
                'rating'      => 5,
                'installed'   => false,
                'tags'        => ['icons'],
            ],
            [
                'id'          => 'shadcn-blocks',
                'name'        => 'shadcn/ui Blocks',
                'author'      => 'Community',
                'description' => 'Canvas blocks based on shadcn/ui — accessible, beautiful, and production-ready.',
                'version'     => '0.8.0',
                'category'    => 'UI Library',
                'installs'    => '6.5k',
                'rating'      => 4,
                'installed'   => false,
                'tags'        => ['ui', 'shadcn', 'radix'],
            ],
            [
                'id'          => 'dark-mode',
                'name'        => 'Dark Mode Manager',
                'author'      => 'Linavelt',
                'description' => 'Toggle dark/light mode preview in the canvas, with automatic palette generation.',
                'version'     => '1.2.0',
                'category'    => 'Theming',
                'installs'    => '5.1k',
                'rating'      => 4,
                'installed'   => false,
                'tags'        => ['theming', 'dark-mode'],
            ],
        ],

        'Code & Integration' => [
            [
                'id'          => 'github-sync',
                'name'        => 'GitHub Sync',
                'author'      => 'Linavelt',
                'description' => 'Commit your project directly to a GitHub repository. Two-way sync between canvas and code.',
                'version'     => '1.1.0',
                'category'    => 'Version Control',
                'installs'    => '7.8k',
                'rating'      => 5,
                'installed'   => false,
                'tags'        => ['github', 'git', 'sync'],
            ],
            [
                'id'          => 'vercel-deploy',
                'name'        => 'Vercel Deploy',
                'author'      => 'Vercel',
                'description' => 'Deploy your project to Vercel in one click, directly from the builder.',
                'version'     => '1.0.3',
                'category'    => 'Deployment',
                'installs'    => '4.2k',
                'rating'      => 4,
                'installed'   => false,
                'tags'        => ['deploy', 'vercel', 'hosting'],
            ],
            [
                'id'          => 'api-mocking',
                'name'        => 'API Mock Studio',
                'author'      => 'Community',
                'description' => 'Define mock API endpoints and wire them to your canvas components for realistic prototyping.',
                'version'     => '0.5.0',
                'category'    => 'API',
                'installs'    => '2.1k',
                'rating'      => 3,
                'installed'   => false,
                'tags'        => ['api', 'mocking', 'prototype'],
            ],
        ],

        'Analytics & SEO' => [
            [
                'id'          => 'seo-tools',
                'name'        => 'SEO Toolkit',
                'author'      => 'Linavelt',
                'description' => 'In-canvas SEO panel: meta tags, Open Graph, sitemap generation, and page-speed hints.',
                'version'     => '1.3.0',
                'category'    => 'SEO',
                'installs'    => '3.9k',
                'rating'      => 4,
                'installed'   => false,
                'tags'        => ['seo', 'meta', 'og'],
            ],
            [
                'id'          => 'analytics-panel',
                'name'        => 'Analytics Panel',
                'author'      => 'Community',
                'description' => 'Inject Google Analytics, Plausible, or PostHog tracking into your project from the canvas.',
                'version'     => '0.9.0',
                'category'    => 'Analytics',
                'installs'    => '1.8k',
                'rating'      => 3,
                'installed'   => false,
                'tags'        => ['analytics', 'tracking'],
            ],
        ],
    ],

];
