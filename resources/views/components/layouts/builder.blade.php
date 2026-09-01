<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>{{ $title ?? 'Page Builder' }} — {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/css/builder.css'])
</head>
<body class="overflow-hidden bg-zinc-950 text-zinc-100">
    {{ $slot }}
    @vite(['resources/js/app.js', 'resources/js/builder/editor.js'])
    @fluxScripts
</body>
</html>
