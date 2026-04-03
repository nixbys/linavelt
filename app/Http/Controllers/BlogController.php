<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class BlogController extends Controller
{
    public function index(): View
    {
        $posts = [
            [
                'title' => 'First Blog Post',
                'excerpt' => 'This is the excerpt for the first blog post.',
                'image' => '/images/blog1.jpg',
                'url' => '/blog/first-post',
            ],
            [
                'title' => 'Second Blog Post',
                'excerpt' => 'This is the excerpt for the second blog post.',
                'image' => '/images/blog2.jpg',
                'url' => '/blog/second-post',
            ],
        ];

        return view('blog.index', compact('posts'));
    }
}
