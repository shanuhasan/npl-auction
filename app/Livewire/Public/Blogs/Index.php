<?php

namespace App\Livewire\Public\Blogs;

use App\Models\Blog;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public function render()
    {
        $blogs = Blog::where('is_published', true)
            ->orderBy('published_at', 'desc')
            ->paginate(12);

        return view('livewire.public.blogs.index', compact('blogs'))
            ->layout('layouts.ipl');
    }
}
