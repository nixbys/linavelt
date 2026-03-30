<?php

namespace App\Livewire;

use Illuminate\View\View;
use Livewire\Component;

class BlogPosts extends Component
{
    public function render(): View
    {
        return view('livewire.blog-posts');
    }
}
