<?php

namespace App\Livewire\Public;

use App\Models\Page;
use Livewire\Component;

class PageView extends Component
{
    public $page;

    public function mount($slug)
    {
        $this->page = Page::where('slug', $slug)->where('is_active', true)->firstOrFail();
    }

    public function render()
    {
        return view('livewire.public.page-view')->layout('layouts.ipl');
    }
}
