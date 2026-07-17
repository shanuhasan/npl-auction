<?php

namespace App\Livewire\Admin\Guests;

use App\Models\Guest;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class Index extends Component
{
    use WithFileUploads;

    public $name;
    public $designation;
    public $type = 'guest';
    public $image;
    public $order = 0;
    public $editId = null;

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'designation' => 'nullable|string|max:255',
            'type' => 'required|in:guest,chief_guest',
            'image' => 'nullable|image|max:5120', // 5MB Max
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
            
            // Resize the image to a max width of 800px to save space, keeping aspect ratio
            $imageInstance->scaleDown(width: 800);
            
            // Encode image as WebP for better compression (80% quality)
            $encodedImage = $imageInstance->toWebp(80);
            
            // Generate a unique file name
            $filename = 'guests/' . Str::random(40) . '.webp';
            
            // Save to public storage
            Storage::disk('public')->put($filename, $encodedImage->toString());
        }

        if ($this->editId) {
            $guest = Guest::findOrFail($this->editId);
            $data = [
                'name' => $this->name,
                'designation' => $this->designation,
                'type' => $this->type,
                'order' => $this->order,
            ];
            if ($filename) {
                if ($guest->image_path && Storage::disk('public')->exists($guest->image_path)) {
                    Storage::disk('public')->delete($guest->image_path);
                }
                $data['image_path'] = $filename;
            }
            $guest->update($data);
            $message = 'Guest successfully updated.';
        } else {
            Guest::create([
                'name' => $this->name,
                'designation' => $this->designation,
                'type' => $this->type,
                'image_path' => $filename,
                'order' => $this->order,
                'is_active' => true,
            ]);
            $message = 'Guest successfully added.';
        }

        $this->reset(['name', 'designation', 'type', 'image', 'order', 'editId']);
        
        session()->flash('message', $message);
    }

    public function edit($id)
    {
        $guest = Guest::findOrFail($id);
        $this->editId = $guest->id;
        $this->name = $guest->name;
        $this->designation = $guest->designation;
        $this->type = $guest->type;
        $this->order = $guest->order;
        $this->image = null; // Clear any uploaded image
    }

    public function cancelEdit()
    {
        $this->reset(['name', 'designation', 'type', 'image', 'order', 'editId']);
    }

    public function toggleActive($id)
    {
        $guest = Guest::findOrFail($id);
        $guest->is_active = !$guest->is_active;
        $guest->save();
    }

    public function delete($id)
    {
        $guest = Guest::findOrFail($id);
        if ($guest->image_path && Storage::disk('public')->exists($guest->image_path)) {
            Storage::disk('public')->delete($guest->image_path);
        }
        $guest->delete();
        
        session()->flash('message', 'Guest successfully deleted.');
    }

    public function updateOrder($id, $order)
    {
        $guest = Guest::findOrFail($id);
        $guest->order = (int) $order;
        $guest->save();
    }

    public function render()
    {
        $guests = Guest::orderBy('order', 'asc')->get();
        return view('livewire.admin.guests.index', compact('guests'))->layout('layouts.app');
    }
}
