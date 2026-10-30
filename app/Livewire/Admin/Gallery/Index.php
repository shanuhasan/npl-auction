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

    // Bulk Upload Properties
    public $isBulkModalOpen = false;
    public $bulk_photos = [];
    public $uploadIteration = 0;

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

    // Bulk Upload Methods
    public function openBulkModal()
    {
        $this->bulk_photos = [];
        $this->uploadIteration++;
        $this->isBulkModalOpen = true;
    }

    public function closeBulkModal()
    {
        $this->isBulkModalOpen = false;
        $this->bulk_photos = [];
        $this->uploadIteration++;
    }

    public function removeBulkPhoto($index)
    {
        if (isset($this->bulk_photos[$index])) {
            unset($this->bulk_photos[$index]);
            $this->bulk_photos = array_values($this->bulk_photos);
        }
    }

    public function saveBulkPhotos()
    {
        $this->validate([
            'bulk_photos' => 'required|array|min:1',
            'bulk_photos.*' => 'image|mimes:jpeg,png,jpg,webp|max:10240',
        ], [
            'bulk_photos.required' => 'Please select at least one photo.',
            'bulk_photos.min' => 'Please select at least one photo.',
            'bulk_photos.*.image' => 'Each file must be an image.',
            'bulk_photos.*.mimes' => 'Allowed formats: jpeg, png, jpg, webp.',
            'bulk_photos.*.max' => 'Each photo must not exceed 10MB.',
        ]);

        @set_time_limit(300);
        @ini_set('memory_limit', '512M');

        $totalFiles = count($this->bulk_photos);
        $uploaded = 0;
        $failed = 0;

        $manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver());

        foreach ($this->bulk_photos as $photo) {
            try {
                $filename = pathinfo($photo->hashName(), PATHINFO_FILENAME) . '.webp';
                $image = $manager->read($photo->getRealPath())
                    ->scaleDown(800, 800)
                    ->toWebp(70);

                Storage::disk('public')->put('gallery/' . $filename, (string) $image);

                Gallery::create([
                    'title' => null,
                    'type' => 'photo',
                    'file_path' => 'gallery/' . $filename,
                    'video_url' => null,
                    'is_active' => true,
                ]);

                $uploaded++;
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error("Bulk upload photo error: " . $e->getMessage());
                $failed++;
            }
        }

        $this->closeBulkModal();

        if ($failed > 0 && $uploaded > 0) {
            session()->flash('message', "{$uploaded} photos uploaded successfully, {$failed} photos failed.");
        } elseif ($uploaded > 0) {
            session()->flash('message', "{$uploaded} photos uploaded successfully!");
        } else {
            session()->flash('error', "Failed to upload photos. Please try again with valid images.");
        }
    }
}
