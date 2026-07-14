<?php

namespace App\Livewire\Public\Teams;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Team;

class Index extends Component
{
    public function render()
    {
        $teams = Team::where('is_approved', true)->withCount('players')->get();
        return view('livewire.public.teams.index', [
            'teams' => $teams
        ])->layout('layouts.ipl');
    }
}
