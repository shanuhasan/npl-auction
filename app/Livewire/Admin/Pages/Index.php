<?php

namespace App\Livewire\Admin\Pages;

use App\Models\Page;
use Livewire\Component;

class Index extends Component
{
    public function toggleStatus(Page $page)
    {
        $page->update(['is_active' => !$page->is_active]);
        session()->flash('message', 'Page status updated successfully.');
    }

    public function deletePage(Page $page)
    {
        $page->delete();
        session()->flash('message', 'Page deleted successfully.');
    }

    public function render()
    {
        $pages = Page::latest()->get();
        return view('livewire.admin.pages.index', compact('pages'))->layout('layouts.app');
    }
}
