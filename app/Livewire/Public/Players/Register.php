<?php

namespace App\Livewire\Public\Players;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Player;
use Illuminate\Support\Facades\Storage;

class Register extends Component
{
    use WithFileUploads;

    public $name = '';
    public $role = 'batsman';
    public $country = 'India';
    public $city = '';
    public $contact_no = '';
    public $batting_style = '';
    public $bowling_style = '';
    public $photo;
    
    public $stats = [
        'matches' => 0,
        'runs' => 0,
        'wickets' => 0,
        'average' => 0,
        'strike_rate' => 0,
    ];

    public $isSubmitted = false;

    protected $rules = [
        'name' => 'required|string|max:255',
        'role' => 'required|in:batsman,bowler,all-rounder,wicketkeeper',
        'country' => 'required|string|max:100',
        'city' => 'nullable|string|max:100',
        'contact_no' => 'required|string|max:20|unique:players,contact_no',
        'batting_style' => 'nullable|string|max:100',
        'bowling_style' => 'nullable|string|max:100',
        'photo' => 'required|image|max:2048', // 2MB Max
        'stats.matches' => 'nullable|integer',
        'stats.runs' => 'nullable|integer',
        'stats.wickets' => 'nullable|integer',
        'stats.average' => 'nullable|numeric',
        'stats.strike_rate' => 'nullable|numeric',
    ];

    public function register()
    {
        $this->validate();

        $photoPath = null;
        if ($this->photo) {
            $filename = pathinfo($this->photo->hashName(), PATHINFO_FILENAME) . '.webp';
            $manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
            $image = $manager->read($this->photo->getRealPath())
                ->scaleDown(800, 800)
                ->toWebp(80);
                
            \Illuminate\Support\Facades\Storage::disk('public')->put('players/' . $filename, (string) $image);
            $photoPath = 'players/' . $filename;
        }

        Player::create([
            'name' => $this->name,
            'photo' => $photoPath,
            'role' => $this->role,
            'country' => $this->country,
            'city' => $this->city,
            'contact_no' => $this->contact_no,
            'batting_style' => $this->batting_style,
            'bowling_style' => $this->bowling_style,
            'base_price' => 1000,
            'category' => 'set-c',
            'status' => 'available',
            'stats' => $this->stats,
            'is_approved' => false,
        ]);

        $this->isSubmitted = true;
    }

    public function render()
    {
        return view('livewire.public.players.register')->layout('layouts.ipl');
    }
}
