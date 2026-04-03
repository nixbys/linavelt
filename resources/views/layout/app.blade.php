<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'Laravel'))</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-gray-900 text-gray-100">
    <header class="bg-gray-800 p-4">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-100">{{ config('app.name', 'Laravel') }}</h1>
            <nav class="flex space-x-4">
                <a href="{{ route('home') }}" class="text-gray-400 hover:text-gray-100">Home</a>
                <a href="{{ route('blog.index') }}" class="text-gray-400 hover:text-gray-100">Blog</a>
                @auth
                    <a href="{{ route('dashboard') }}" class="text-gray-400 hover:text-gray-100">Dashboard</a>
                    <a href="{{ route('admin.dashboard') }}" class="text-gray-400 hover:text-gray-100">Admin</a>
                @else
                    <a href="{{ route('login') }}" class="text-gray-400 hover:text-gray-100">Login</a>
                @endauth
            </nav>
        </div>
    </header>

    <main class="container mx-auto py-8">
        @yield('content')
    </main>

    <footer class="bg-gray-800 p-4 mt-8">
        <div class="container mx-auto text-center text-gray-400">
            &copy; {{ date('Y') }} {{ config('app.name', 'Laravel') }}. All rights reserved.
        </div>
    </footer>

    @livewireScripts
</body>
</html>
