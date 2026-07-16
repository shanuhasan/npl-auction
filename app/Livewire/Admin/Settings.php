<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithFileUploads;

class Settings extends Component
{
    use WithFileUploads;

    public $app_name;
    public $logo;
    public $new_logo;
    public $favicon;
    public $new_favicon;
    public $contact_email;
    public $contact_phone;

    public $season;
    public $developer_name;
    public $developer_url;
    public $our_mission;
    public $our_vision;

    public function mount()
    {
        $this->app_name = setting('app_name', 'Naugawan Premier League');
        $this->season = setting('season', date('Y'));
        $this->logo = setting('logo', '');
        $this->favicon = setting('favicon', '');
        $this->contact_email = setting('contact_email', '');
        $this->contact_phone = setting('contact_phone', '');
        $this->developer_name = setting('developer_name', 'Shanu Saifi');
        $this->developer_url = setting('developer_url', '#');
        $this->our_mission = setting('our_mission', '');
        $this->our_vision = setting('our_vision', '');
    }

    public function save()
    {
        $this->validate([
            'app_name' => 'required|string|max:255',
            'season' => 'required|string|max:255',
            'new_logo' => 'nullable|image|max:2048', // max 2MB
            'new_favicon' => 'nullable|mimes:ico,png,jpg,jpeg|max:1024', // max 1MB
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:255',
            'developer_name' => 'nullable|string|max:255',
            'developer_url' => 'nullable|string|max:255',
            'our_mission' => 'nullable|string',
            'our_vision' => 'nullable|string',
        ]);

        \App\Models\Setting::set('app_name', $this->app_name);
        \App\Models\Setting::set('season', $this->season);
        
        if ($this->new_logo) {
            $filename = pathinfo($this->new_logo->hashName(), PATHINFO_FILENAME) . '.webp';
            $manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
            $image = $manager->read($this->new_logo->getRealPath())
                ->scaleDown(800, 800)
                ->toWebp(80);
                
            \Illuminate\Support\Facades\Storage::disk('public')->put('settings/' . $filename, (string) $image);
            $logoPath = 'settings/' . $filename;
            \App\Models\Setting::set('logo', $logoPath);
            $this->logo = $logoPath;
            $this->new_logo = null;
        }

        if ($this->new_favicon) {
            $filename = pathinfo($this->new_favicon->hashName(), PATHINFO_FILENAME) . '.webp';
            $manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
            $image = $manager->read($this->new_favicon->getRealPath())
                ->scaleDown(128, 128)
                ->toWebp(80);
                
            \Illuminate\Support\Facades\Storage::disk('public')->put('settings/' . $filename, (string) $image);
            $faviconPath = 'settings/' . $filename;
            \App\Models\Setting::set('favicon', $faviconPath);
            $this->favicon = $faviconPath;
            $this->new_favicon = null;
        }

        \App\Models\Setting::set('contact_email', $this->contact_email);
        \App\Models\Setting::set('contact_phone', $this->contact_phone);
        \App\Models\Setting::set('developer_name', $this->developer_name);
        \App\Models\Setting::set('developer_url', $this->developer_url);
        \App\Models\Setting::set('our_mission', $this->our_mission);
        \App\Models\Setting::set('our_vision', $this->our_vision);

        session()->flash('success', 'Settings updated successfully.');
    }

    public function render()
    {
        return view('livewire.admin.settings')->layout('layouts.app');
    }
}
