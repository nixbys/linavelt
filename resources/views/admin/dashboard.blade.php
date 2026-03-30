{{-- Admin Dashboard --}}
@extends('layout.app')

@section('title', 'Admin Dashboard')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <h1 class="mb-8 text-center text-4xl font-bold text-gray-100">Admin Dashboard</h1>

        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
            <div class="rounded-lg bg-gray-800 p-4 shadow-md">
                <h2 class="text-xl font-bold text-gray-100">Users</h2>
                <p class="mt-2 text-sm text-gray-400">{{ $data['users'] }} registered users.</p>
                <a href="/admin/users" class="mt-4 inline-block text-blue-400 hover:underline">View Users</a>
            </div>

            <div class="rounded-lg bg-gray-800 p-4 shadow-md">
                <h2 class="text-xl font-bold text-gray-100">Posts</h2>
                <p class="mt-2 text-sm text-gray-400">{{ $data['posts'] }} blog posts.</p>
                <a href="/admin/posts" class="mt-4 inline-block text-blue-400 hover:underline">View Posts</a>
            </div>

            <div class="rounded-lg bg-gray-800 p-4 shadow-md">
                <h2 class="text-xl font-bold text-gray-100">Settings</h2>
                <p class="mt-2 text-sm text-gray-400">{{ $data['settings'] }} configurable settings.</p>
                <a href="/admin/settings" class="mt-4 inline-block text-blue-400 hover:underline">View Settings</a>
            </div>
        </div>
    </div>
@endsection

