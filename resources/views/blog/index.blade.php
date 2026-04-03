{{-- Blog Index --}}
@extends('layout.app')

@section('title', 'Blog')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <h1 class="mb-8 text-center text-4xl font-bold text-gray-100">Blog</h1>

        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
            @foreach ($posts as $post)
                <div class="overflow-hidden rounded-lg bg-gray-800 shadow-md">
                    <img
                        src="{{ $post['image'] }}"
                        alt="{{ $post['title'] }}"
                        class="h-48 w-full object-cover"
                    >
                    <div class="p-4">
                        <h2 class="text-xl font-bold text-gray-100">{{ $post['title'] }}</h2>
                        <p class="mt-2 text-sm text-gray-400">{{ $post['excerpt'] }}</p>
                        <a href="{{ $post['url'] }}" class="mt-4 inline-block text-blue-400 hover:underline">
                            Read More
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        @livewire('blog-posts')
    </div>
@endsection

