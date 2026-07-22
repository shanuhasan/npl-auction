<?php

namespace App\Livewire\Admin\Gallery;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Gallery;
use Illuminate\Support\Facades\Storage;

class Index extends Component
{
    use WithFileUploads;

    public $galleries;
    public $gallery_id, $title, $type = 'photo', $file_path, $video_url, $is_active = true;
    public $isModalOpen = false;
    public $existing_file = null;

    protected $rules = [
        'title' => 'nullable|string|max:255',
        'type' => 'required|in:photo,video',
        'file_path' => 'nullable|file|mimes:jpeg,png,jpg,webp,mp4,mov,avi|max:40960',
        'video_url' => 'nullable|url',
        'is_active' => 'boolean',
    ];

    public function render()
    {
        $this->galleries = Gallery::orderBy('created_at', 'desc')->get();
        return view('livewire.admin.gallery.index')->layout('layouts.app');
    }

    public function create()
    {
        $this->resetCreateForm();
        $this->openModal();
    }

    public function openModal()
    {
        $this->isModalOpen = true;
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
    }

    private function resetCreateForm()
    {
        $this->gallery_id = null;
        $this->title = '';
        $this->type = 'photo';
        $this->file_path = null;
        $this->existing_file = null;
        $this->video_url = '';
        $this->is_active = true;
    }

    public function store()
    {
        $this->validate();

        $savedPath = $this->existing_file;

        if ($this->file_path) {
            if ($this->existing_file) {
                Storage::disk('public')->delete($this->existing_file);
                $publicPath = public_path('storage/' . $this->existing_file);
                if (file_exists($publicPath)) {
                    @unlink($publicPath);
                }
            }
            
            if ($this->type == 'photo') {
                $filename = pathinfo($this->file_path->hashName(), PATHINFO_FILENAME) . '.webp';
                $manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
                $image = $manager->read($this->file_path->getRealPath())
                    ->scaleDown(800, 800)
                    ->toWebp(70);
                    
                Storage::disk('public')->put('gallery/' . $filename, (string) $image);
                $savedPath = 'gallery/' . $filename;
            } else {
                $savedPath = $this->file_path->store('gallery', 'public');
            }
        }

        Gallery::updateOrCreate(['id' => $this->gallery_id], [
            'title' => $this->title,
            'type' => $this->type,
            'file_path' => $savedPath,
            'video_url' => $this->video_url,
            'is_active' => $this->is_active,
        ]);

        session()->flash('message', $this->gallery_id ? 'Item Updated Successfully.' : 'Item Added Successfully.');
        $this->closeModal();
        $this->resetCreateForm();
    }

    public function edit($id)
    {
        $gallery = Gallery::findOrFail($id);
        $this->gallery_id = $gallery->id;
        $this->title = $gallery->title;
        $this->type = $gallery->type;
        $this->existing_file = $gallery->file_path;
        $this->video_url = $gallery->video_url;
        $this->is_active = $gallery->is_active;
        
        $this->openModal();
    }

    public function delete($id)
    {
        $gallery = Gallery::findOrFail($id);
        if ($gallery->file_path) {
            Storage::disk('public')->delete($gallery->file_path);
            $publicPath = public_path('storage/' . $gallery->file_path);
            if (file_exists($publicPath)) {
                @unlink($publicPath);
            }
        }
        $gallery->delete();
        session()->flash('message', 'Item Deleted Successfully.');
    }

    public function toggleStatus($id)
    {
        $gallery = Gallery::findOrFail($id);
        $gallery->update(['is_active' => !$gallery->is_active]);
        session()->flash('message', 'Status Updated Successfully.');
    }
}
