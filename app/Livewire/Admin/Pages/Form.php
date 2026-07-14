<?php

namespace App\Livewire\Admin\Pages;

use App\Models\Page;
use Livewire\Component;
use Illuminate\Support\Str;

class Form extends Component
{
    public $pageId;
    public $title;
    public $slug;
    public $content;
    public $is_active = true;
    public $isEditMode = false;

    public function mount($pageId = null)
    {
        if ($pageId) {
            $this->isEditMode = true;
            $this->pageId = $pageId;
            $page = Page::findOrFail($pageId);
            $this->title = $page->title;
            $this->slug = $page->slug;
            $this->content = $page->content;
            $this->is_active = $page->is_active;
        }
    }

    public function updatedTitle()
    {
        if (!$this->isEditMode || empty($this->slug)) {
            $this->slug = Str::slug($this->title);
        }
    }

    public function save()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:pages,slug,' . $this->pageId,
            'content' => 'required|string',
            'is_active' => 'boolean',
        ]);

        if ($this->isEditMode) {
            $page = Page::findOrFail($this->pageId);
            $page->update([
                'title' => $this->title,
                'slug' => $this->slug,
                'content' => $this->content,
                'is_active' => $this->is_active,
            ]);
            session()->flash('message', 'Page updated successfully.');
        } else {
            Page::create([
                'title' => $this->title,
                'slug' => $this->slug,
                'content' => $this->content,
                'is_active' => $this->is_active,
            ]);
            session()->flash('message', 'Page created successfully.');
        }

        return redirect()->route('admin.pages.index');
    }

    public function render()
    {
        return view('livewire.admin.pages.form')->layout('layouts.app');
    }
}
