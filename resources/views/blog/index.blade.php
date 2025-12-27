{{-- Blog Index Template --}}
@extends('layout.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-4xl font-bold text-center text-gray-100 mb-8">Blog</h1>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        {{-- Blog Post Cards --}}
        @foreach ($posts as $post)
        <div class="bg-gray-800 rounded-lg shadow-md overflow-hidden">
            <img src="{{ $post['image'] }}" alt="{{ $post['title'] }}" class="w-full h-48 object-cover">
            <div class="p-4">
                <h2 class="text-xl font-bold text-gray-100">{{ $post['title'] }}</h2>
                <p class="text-gray-400 text-sm mt-2">{{ $post['excerpt'] }}</p>
                <a href="{{ $post['url'] }}" class="text-blue-400 hover:underline mt-4 inline-block">Read More</a>
            </div>
        </div>
        @endforeach
    </div>
    <div>
        @livewire('blog-posts')
    </div>
</div>
@endsection
