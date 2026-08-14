<?php

namespace App\Livewire\Admin\Blogs;

use App\Models\Blog;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';

    public function togglePublish(Blog $blog)
    {
        $blog->is_published = !$blog->is_published;
        if ($blog->is_published && !$blog->published_at) {
            $blog->published_at = now();
        }
        $blog->save();

        session()->flash('success', 'Blog status updated.');
    }

    public function deleteBlog(Blog $blog)
    {
        $blog->delete();
        session()->flash('success', 'Blog deleted successfully.');
    }

    public function render()
    {
        $blogs = Blog::where('title', 'like', '%' . $this->search . '%')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.admin.blogs.index', compact('blogs'))
            ->layout('layouts.app');
    }
}
