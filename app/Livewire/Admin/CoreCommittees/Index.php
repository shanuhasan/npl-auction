<?php

namespace App\Livewire\Admin\CoreCommittees;

use App\Models\CoreCommittee;
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
    public $role;
    public $image;
    public $order = 0;

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'role' => 'nullable|string|max:255',
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
            $filename = 'core-committees/' . Str::random(40) . '.webp';
            
            // Save to public storage
            Storage::disk('public')->put($filename, $encodedImage->toString());
        }

        CoreCommittee::create([
            'name' => $this->name,
            'role' => $this->role,
            'image_path' => $filename,
            'order' => $this->order,
            'is_active' => true,
        ]);

        $this->reset(['name', 'role', 'image', 'order']);
        
        session()->flash('message', 'Member successfully added.');
    }

    public function toggleActive($id)
    {
        $member = CoreCommittee::findOrFail($id);
        $member->is_active = !$member->is_active;
        $member->save();
    }

    public function delete($id)
    {
        $member = CoreCommittee::findOrFail($id);
        if ($member->image_path && Storage::disk('public')->exists($member->image_path)) {
            Storage::disk('public')->delete($member->image_path);
        }
        $member->delete();
        
        session()->flash('message', 'Member successfully deleted.');
    }

    public function updateOrder($id, $order)
    {
        $member = CoreCommittee::findOrFail($id);
        $member->order = (int) $order;
        $member->save();
    }

    public function render()
    {
        $members = CoreCommittee::orderBy('order', 'asc')->get();
        return view('livewire.admin.core-committees.index', compact('members'))->layout('layouts.app');
    }
}
