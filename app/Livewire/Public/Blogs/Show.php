<?php

namespace App\Livewire\Public\Blogs;

use App\Models\Blog;
use Livewire\Component;

class Show extends Component
{
    public $blog;

    public function mount($slug)
    {
        $this->blog = Blog::where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();
    }

    public function render()
    {
        return view('livewire.public.blogs.show')
            ->layout('layouts.ipl');
    }
}
