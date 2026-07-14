<?php

namespace App\Livewire\Public\Teams;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Team;

class Show extends Component
{
    public $team;
    public $auctions;
    public $selectedAuctionId;

    public function mount(Team $team)
    {
        if (!$team->is_approved) {
            abort(404);
        }

        $this->team = $team;
        $this->auctions = \App\Models\Auction::orderBy('created_at', 'desc')->get();
        
        if ($this->auctions->isNotEmpty()) {
            $this->selectedAuctionId = $this->auctions->first()->id;
        }
    }

    public function render()
    {
        $players = collect();
        $totalSpent = 0;

        if ($this->selectedAuctionId) {
            $auctionPlayers = \App\Models\AuctionPlayer::with(['player.auctionPlayers' => function($q) {
                $q->where('sold_to_team_id', $this->team->id)->latest();
            }])
            ->where('auction_id', $this->selectedAuctionId)
            ->where('sold_to_team_id', $this->team->id)
            ->where('status', 'sold')
            ->get();

            $players = $auctionPlayers->pluck('player');
            $totalSpent = $auctionPlayers->sum('final_price');
        }

        $playersByRole = $players->groupBy('role');
        $remainingBudget = $this->team->budget - $totalSpent;
        
        return view('livewire.public.teams.show', [
            'playersByRole' => $playersByRole,
            'totalSpent' => $totalSpent,
            'remainingBudget' => $remainingBudget,
            'totalPlayers' => $players->count()
        ])->layout('layouts.ipl');
    }
}
