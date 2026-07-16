<?php

namespace App\Livewire\Public;

use Livewire\Component;

class Contact extends Component
{
    public $name;
    public $email;
    public $phone;
    public $message;

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'phone' => 'nullable|string|max:20',
        'message' => 'required|string',
    ];

    public function submit()
    {
        $this->validate();

        \App\Models\ContactMessage::create([
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'message' => $this->message,
        ]);

        $this->reset(['name', 'email', 'phone', 'message']);

        session()->flash('success', 'Your message has been sent successfully! We will get back to you soon.');
    }

    public function render()
    {
        return view('livewire.public.contact')->layout('layouts.ipl');
    }
}
