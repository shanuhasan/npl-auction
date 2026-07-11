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

    public function mount()
    {
        $this->app_name = setting('app_name', 'Naugawan Premier League');
        $this->logo = setting('logo', '');
        $this->favicon = setting('favicon', '');
        $this->contact_email = setting('contact_email', '');
        $this->contact_phone = setting('contact_phone', '');
    }

    public function save()
    {
        $this->validate([
            'app_name' => 'required|string|max:255',
            'new_logo' => 'nullable|image|max:2048', // max 2MB
            'new_favicon' => 'nullable|mimes:ico,png,jpg,jpeg|max:1024', // max 1MB
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:255',
        ]);

        \App\Models\Setting::set('app_name', $this->app_name);
        
        if ($this->new_logo) {
            $logoPath = $this->new_logo->store('settings', 'public');
            \App\Models\Setting::set('logo', $logoPath);
            $this->logo = $logoPath;
            $this->new_logo = null;
        }

        if ($this->new_favicon) {
            $faviconPath = $this->new_favicon->store('settings', 'public');
            \App\Models\Setting::set('favicon', $faviconPath);
            $this->favicon = $faviconPath;
            $this->new_favicon = null;
        }

        \App\Models\Setting::set('contact_email', $this->contact_email);
        \App\Models\Setting::set('contact_phone', $this->contact_phone);

        session()->flash('success', 'Settings updated successfully.');
    }

    public function render()
    {
        return view('livewire.admin.settings')->layout('layouts.app');
    }
}
