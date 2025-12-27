<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Laravel Blog')</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-900 text-gray-100">
    <header class="bg-gray-800 p-4">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-100">Laravel Blog</h1>
            <nav class="flex space-x-4">
                <a href="/" class="text-gray-400 hover:text-gray-100">Home</a>
                <a href="/blog" class="text-gray-400 hover:text-gray-100">Blog</a>
                <a href="/about" class="text-gray-400 hover:text-gray-100">About</a>
            </nav>
        </div>
    </header>

    <main class="container mx-auto py-8">
        @yield('content')
    </main>

    <footer class="bg-gray-800 p-4 mt-8">
        <div class="container mx-auto text-center text-gray-400">
            &copy; 2025 Laravel Blog. All rights reserved.
        </div>
    </footer>
</body>
</html>
