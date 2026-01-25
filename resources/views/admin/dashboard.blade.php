{{-- Admin Dashboard Template --}}
@extends('layout.app')

@section(section: 'content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-4xl font-bold text-center text-gray-100 mb-8">Admin Dashboard</h1>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div class="bg-gray-800 rounded-lg shadow-md p-4">
            <h2 class="text-xl font-bold text-gray-100">Users</h2>
            <p class="text-gray-400 text-sm mt-2">{{ $data['users'] }} registered users.</p>
            <a href="/admin/users" class="text-blue-400 hover:underline mt-4 inline-block">View Users</a>
        </div>
        <div class="bg-gray-800 rounded-lg shadow-md p-4">
            <h2 class="text-xl font-bold text-gray-100">Posts</h2>
            <p class="text-gray-400 text-sm mt-2">{{ $data['posts'] }} blog posts.</p>
            <a href="/admin/posts" class="text-blue-400 hover:underline mt-4 inline-block">View Posts</a>
        </div>
        <div class="bg-gray-800 rounded-lg shadow-md p-4">
            <h2 class="text-xl font-bold text-gray-100">Settings</h2>
            <p class="text-gray-400 text-sm mt-2">{{ $data['settings'] }} configurable settings.</p>
            <a href="/admin/settings" class="text-blue-400 hover:underline mt-4 inline-block">View Settings</a>
        </div>
    </div>
</div>
@endsection
