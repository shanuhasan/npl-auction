<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Player;
use App\Models\Team;
use App\Models\Auction;

class Dashboard extends Component
{
    public function resetSeason()
    {
        \Illuminate\Support\Facades\DB::statement('UPDATE teams SET remaining_budget = budget');
        
        Player::query()->update([
            'status' => 'available',
            'current_team_id' => null
        ]);
        
        session()->flash('success', 'Season Reset Successful! All teams have their original budgets back and all players are now available. You can now create a New Auction without recreating teams and players.');
    }

    public function render()
    {
        return view('livewire.admin.dashboard', [
            'totalPlayers' => Player::count(),
            'totalTeams' => Team::count(),
            'upcomingAuctions' => Auction::where('status', 'upcoming')->count(),
            'completedAuctions' => Auction::where('status', 'completed')->count(),
            'hasLiveAuctions' => Auction::where('status', 'live')->exists(),
            'totalVisitors' => \App\Models\Visitor::count(),
        ])->layout('layouts.app');
    }
}
