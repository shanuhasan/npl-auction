<?php

namespace App\Livewire\Admin\Blogs;

use App\Models\Blog;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;

class Form extends Component
{
    use WithFileUploads;

    public $blog;
    public $title, $slug, $excerpt, $content, $image, $new_image;
    public $meta_title, $meta_description, $meta_keywords;
    public $is_published = false;

    public function mount(Blog $blog = null)
    {
        if ($blog && $blog->exists) {
            $this->blog = $blog;
            $this->title = $blog->title;
            $this->slug = $blog->slug;
            $this->excerpt = $blog->excerpt;
            $this->content = $blog->content;
            $this->image = $blog->image;
            $this->meta_title = $blog->meta_title;
            $this->meta_description = $blog->meta_description;
            $this->meta_keywords = $blog->meta_keywords;
            $this->is_published = $blog->is_published;
        }
    }

    public function updatedTitle()
    {
        if (!$this->blog || !$this->blog->exists) {
            $this->slug = Str::slug($this->title);
        }
    }

    public function save()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:blogs,slug' . ($this->blog && $this->blog->exists ? ',' . $this->blog->id : ''),
            'excerpt' => 'nullable|string',
            'content' => 'required|string',
            'new_image' => 'nullable|image|max:2048',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string|max:255',
            'is_published' => 'boolean',
        ]);

        $imagePath = $this->image;
        if ($this->new_image) {
            $filename = pathinfo($this->new_image->hashName(), PATHINFO_FILENAME) . '.webp';
            $manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
            $img = $manager->read($this->new_image->getRealPath())
                ->scaleDown(1200, 800)
                ->toWebp(75);
            \Illuminate\Support\Facades\Storage::disk('public')->put('blogs/' . $filename, (string) $img);
            $imagePath = 'blogs/' . $filename;
        }

        $data = [
            'title' => $this->title,
            'slug' => $this->slug,
            'excerpt' => $this->excerpt,
            'content' => $this->content,
            'image' => $imagePath,
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'meta_keywords' => $this->meta_keywords,
            'is_published' => $this->is_published,
        ];

        if ($this->is_published && (!$this->blog || !$this->blog->published_at)) {
            $data['published_at'] = now();
        }

        if ($this->blog && $this->blog->exists) {
            $this->blog->update($data);
            session()->flash('success', 'Blog updated successfully.');
        } else {
            Blog::create($data);
            session()->flash('success', 'Blog created successfully.');
        }

        return redirect()->route('admin.blogs.index');
    }

    public function render()
    {
        return view('livewire.admin.blogs.form')
            ->layout('layouts.app');
    }
}
