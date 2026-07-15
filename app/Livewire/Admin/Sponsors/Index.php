<?php

namespace App\Livewire\Admin\Sponsors;

use App\Models\Sponsor;
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
    public $type = 'sponsor';
    public $logo;
    public $url;
    public $order = 0;
    public $editId = null;

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'type' => 'required|in:title_sponsor,premier_partner,sponsor',
            'logo' => 'nullable|image|max:5120', // 5MB Max
            'url' => 'nullable|url|max:255',
            'order' => 'integer',
        ];
    }

    public function save()
    {
        $this->validate();

        $filename = null;

        if ($this->logo) {
            $manager = new ImageManager(new Driver());
            $imageInstance = $manager->read($this->logo->getRealPath());
            $imageInstance->scaleDown(width: 800);
            $encodedImage = $imageInstance->toWebp(80);
            
            $filename = 'sponsors/' . Str::random(40) . '.webp';
            Storage::disk('public')->put($filename, $encodedImage->toString());
        }

        if ($this->editId) {
            $sponsor = Sponsor::findOrFail($this->editId);
            $data = [
                'name' => $this->name,
                'type' => $this->type,
                'url' => $this->url,
                'order' => $this->order,
            ];
            if ($filename) {
                if ($sponsor->logo_path && Storage::disk('public')->exists($sponsor->logo_path)) {
                    Storage::disk('public')->delete($sponsor->logo_path);
                }
                $data['logo_path'] = $filename;
            }
            $sponsor->update($data);
            $message = 'Sponsor successfully updated.';
        } else {
            Sponsor::create([
                'name' => $this->name,
                'type' => $this->type,
                'logo_path' => $filename,
                'url' => $this->url,
                'order' => $this->order,
                'is_active' => true,
            ]);
            $message = 'Sponsor successfully added.';
        }

        $this->reset(['name', 'type', 'logo', 'url', 'order', 'editId']);
        
        session()->flash('message', $message);
    }

    public function edit($id)
    {
        $sponsor = Sponsor::findOrFail($id);
        $this->editId = $sponsor->id;
        $this->name = $sponsor->name;
        $this->type = $sponsor->type;
        $this->url = $sponsor->url;
        $this->order = $sponsor->order;
        $this->logo = null; 
    }

    public function cancelEdit()
    {
        $this->reset(['name', 'type', 'logo', 'url', 'order', 'editId']);
    }

    public function toggleActive($id)
    {
        $sponsor = Sponsor::findOrFail($id);
        $sponsor->is_active = !$sponsor->is_active;
        $sponsor->save();
    }

    public function delete($id)
    {
        $sponsor = Sponsor::findOrFail($id);
        if ($sponsor->logo_path && Storage::disk('public')->exists($sponsor->logo_path)) {
            Storage::disk('public')->delete($sponsor->logo_path);
        }
        $sponsor->delete();
        
        session()->flash('message', 'Sponsor successfully deleted.');
    }

    public function updateOrder($id, $order)
    {
        $sponsor = Sponsor::findOrFail($id);
        $sponsor->order = (int) $order;
        $sponsor->save();
    }

    public function render()
    {
        $sponsors = Sponsor::orderBy('type')->orderBy('order', 'asc')->get();
        return view('livewire.admin.sponsors.index', compact('sponsors'))->layout('layouts.app');
    }
}
