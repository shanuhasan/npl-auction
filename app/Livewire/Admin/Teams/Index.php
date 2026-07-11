<?php

namespace App\Livewire\Admin\Teams;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class Index extends Component
{
    use WithFileUploads;

    public $teams, $owners;
    public $team_id, $name, $short_name, $logo, $primary_color, $budget = 10000.00, $owner_id;
    public $isModalOpen = false;
    public $existing_logo = null;

    protected $rules = [
        'name' => 'required|string|max:255',
        'short_name' => 'required|string|max:10',
        'logo' => 'nullable|image|max:2048',
        'primary_color' => 'nullable|string|max:7',
        'budget' => 'required|numeric|min:0',
        'owner_id' => 'required|exists:users,id',
    ];

    public function render()
    {
        $this->teams = Team::with('owner')->get();
        $this->owners = User::where('role', 'team_owner')->get();
        
        return view('livewire.admin.teams.index')->layout('layouts.app');
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

    private function resetCreateForm(){
        $this->team_id = null;
        $this->name = '';
        $this->short_name = '';
        $this->logo = null;
        $this->existing_logo = null;
        $this->primary_color = '#000000';
        $this->budget = 10000.00;
        $this->owner_id = null;
    }

    public function store()
    {
        $this->validate();

        $logoPath = $this->existing_logo;
        if ($this->logo) {
            if ($this->existing_logo) {
                Storage::disk('public')->delete($this->existing_logo);
            }
            $logoPath = $this->logo->store('teams', 'public');
        }

        Team::updateOrCreate(['id' => $this->team_id], [
            'name' => $this->name,
            'short_name' => $this->short_name,
            'logo' => $logoPath,
            'primary_color' => $this->primary_color,
            'budget' => $this->budget,
            'owner_id' => $this->owner_id,
        ]);

        session()->flash('message', $this->team_id ? 'Team Updated Successfully.' : 'Team Created Successfully.');
        $this->closeModal();
        $this->resetCreateForm();
    }

    public function edit($id)
    {
        $team = Team::findOrFail($id);
        $this->team_id = $team->id;
        $this->name = $team->name;
        $this->short_name = $team->short_name;
        $this->existing_logo = $team->logo;
        $this->primary_color = $team->primary_color;
        $this->budget = $team->budget;
        $this->owner_id = $team->owner_id;
        
        $this->openModal();
    }

    public function delete($id)
    {
        $team = Team::findOrFail($id);
        if ($team->logo) {
            Storage::disk('public')->delete($team->logo);
        }
        $team->delete();
        session()->flash('message', 'Team Deleted Successfully.');
    }
}
