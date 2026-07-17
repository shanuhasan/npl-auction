<?php

namespace App\Livewire\Public\Teams;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class Register extends Component
{
    use WithFileUploads;

    public $team_name = '';
    public $short_name = '';
    public $primary_color = '#FFC800';
    public $logo;
    
    public $owner_name = '';
    public $owner_email = '';
    public $owner_password = '';

    public $isSubmitted = false;
    public $errorMessage = '';

    protected $rules = [
        'team_name' => 'required|string|max:255|unique:teams,name',
        'short_name' => 'required|string|max:10|unique:teams,short_name',
        'primary_color' => 'nullable|string|max:7',
        'logo' => 'required|image|max:2048', // 2MB Max
        
        'owner_name' => 'required|string|max:255',
        'owner_email' => 'required|email|max:255|unique:users,email',
        'owner_password' => 'required|string|min:8',
    ];

    public function register()
    {
        $this->validate();

        try {
            DB::beginTransaction();

            // Create User (Owner)
            $user = User::create([
                'name' => $this->owner_name,
                'email' => $this->owner_email,
                'password' => Hash::make($this->owner_password),
                'role' => 'team_owner',
            ]);

            $logoPath = null;
            if ($this->logo) {
                $filename = pathinfo($this->logo->hashName(), PATHINFO_FILENAME) . '.webp';
                $manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
                $image = $manager->read($this->logo->getRealPath())
                    ->scaleDown(500, 500)
                    ->toWebp(80);
                    
                Storage::disk('public')->put('teams/' . $filename, (string) $image);
                $logoPath = 'teams/' . $filename;
            }

            // Create Team
            Team::create([
                'name' => $this->team_name,
                'short_name' => strtoupper($this->short_name),
                'logo' => $logoPath,
                'primary_color' => $this->primary_color,
                'budget' => 100000.00,
                'remaining_budget' => 100000.00,
                'owner_id' => $user->id,
                'is_approved' => false,
            ]);

            DB::commit();
            $this->isSubmitted = true;
        } catch (\Exception $e) {
            DB::rollBack();
            $this->errorMessage = 'Something went wrong. Please try again. ' . $e->getMessage();
        }
    }

    public function render()
    {
        return view('livewire.public.teams.register')->layout('layouts.ipl');
    }
}
