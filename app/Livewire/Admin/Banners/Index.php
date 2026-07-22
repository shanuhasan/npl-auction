<?php

namespace App\Livewire\Admin\Banners;

use App\Models\Banner;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class Index extends Component
{
    use WithFileUploads;

    public $title;
    public $description;
    public $link;
    public $button_name;
    public $image;
    public $order = 0;
    public $editId = null;

    public function rules()
    {
        return [
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'link' => 'nullable|url',
            'button_name' => 'nullable|string|max:50',
            'image' => $this->editId ? 'nullable|image|max:5120' : 'required|image|max:5120', // 5MB Max
            'order' => 'integer',
        ];
    }

    public function save()
    {
        $this->validate();

        $filename = null;
        if ($this->image) {
            // Initialize ImageManager with GD driver
            $manager = new ImageManager(new Driver());
            
            // Read the uploaded image
            $imageInstance = $manager->read($this->image->getRealPath());
            
            // Resize the image to a max width of 1920px to save space, keeping aspect ratio
            $imageInstance->scaleDown(width: 1920);
            
            // Encode image as WebP for better compression (80% quality)
            $encodedImage = $imageInstance->toWebp(80);
            
            // Generate a unique file name
            $filename = 'banners/' . Str::random(40) . '.webp';
            
            // Save to public storage
            Storage::disk('public')->put($filename, $encodedImage->toString());
        }

        if ($this->editId) {
            $banner = Banner::findOrFail($this->editId);
            $data = [
                'title' => $this->title,
                'description' => $this->description,
                'link' => $this->link,
                'button_name' => $this->button_name,
                'order' => $this->order,
            ];
            if ($filename) {
                if ($banner->image_path) {
                    Storage::disk('public')->delete($banner->image_path);
                    $publicPath = public_path('storage/' . $banner->image_path);
                    if (file_exists($publicPath)) {
                        @unlink($publicPath);
                    }
                }
                $data['image_path'] = $filename;
            }
            $banner->update($data);
            $message = 'Banner successfully updated.';
        } else {
            Banner::create([
                'title' => $this->title,
                'description' => $this->description,
                'link' => $this->link,
                'button_name' => $this->button_name,
                'image_path' => $filename,
                'order' => $this->order,
                'is_active' => true,
            ]);
            $message = 'Banner successfully uploaded and compressed.';
        }

        $this->reset(['title', 'description', 'link', 'button_name', 'image', 'order', 'editId']);
        
        session()->flash('message', $message);
    }

    public function edit($id)
    {
        $banner = Banner::findOrFail($id);
        $this->editId = $banner->id;
        $this->title = $banner->title;
        $this->description = $banner->description;
        $this->link = $banner->link;
        $this->button_name = $banner->button_name;
        $this->order = $banner->order;
        $this->image = null; // Clear any uploaded image
    }

    public function cancelEdit()
    {
        $this->reset(['title', 'description', 'link', 'button_name', 'image', 'order', 'editId']);
    }

    public function toggleActive($id)
    {
        $banner = Banner::findOrFail($id);
        $banner->is_active = !$banner->is_active;
        $banner->save();
    }

    public function delete($id)
    {
        $banner = Banner::findOrFail($id);
        if ($banner->image_path) {
            Storage::disk('public')->delete($banner->image_path);
            $publicPath = public_path('storage/' . $banner->image_path);
            if (file_exists($publicPath)) {
                @unlink($publicPath);
            }
        }
        $banner->delete();
        
        session()->flash('message', 'Banner successfully deleted.');
    }

    public function updateOrder($id, $order)
    {
        $banner = Banner::findOrFail($id);
        $banner->order = (int) $order;
        $banner->save();
    }

    public function render()
    {
        $banners = Banner::orderBy('order', 'asc')->get();
        return view('livewire.admin.banners.index', compact('banners'))->layout('layouts.app');
    }
}
