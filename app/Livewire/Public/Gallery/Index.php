<?php

namespace App\Livewire\Public\Gallery;

use App\Models\Gallery;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $filter = 'all'; // all, photo, video

    public function updatedFilter()
    {
        $this->resetPage();
    }

    public function setFilter($type)
    {
        $this->filter = $type;
        $this->resetPage();
    }

    public function render()
    {
        $query = Gallery::where('is_active', true)
            ->orderBy('created_at', 'desc');

        if ($this->filter === 'photo') {
            $query->where('type', 'photo');
        } elseif ($this->filter === 'video') {
            $query->where('type', 'video');
        }

        $galleries = $query->paginate(24);

        // All active items for the lightbox modal navigation
        $allGalleries = Gallery::where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('livewire.public.gallery.index', compact('galleries', 'allGalleries'))
            ->layout('layouts.ipl');
    }
}

