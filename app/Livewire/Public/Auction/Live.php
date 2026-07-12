<?php

namespace App\Livewire\Public\Auction;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use App\Models\Auction;
use App\Models\AuctionState;
use App\Models\AuctionPlayer;
use App\Models\Team;

#[Layout('layouts.auction-live')]
class Live extends Component
{
    public $auction;
    public $state;
    public $teams = [];
    
    public $currentPlayer = null;
    public $currentHighestBid = 0;
    public $currentHighestTeam = null;
    public $timerEndAt = null;

    public $statusOverlay = null; // 'sold', 'unsold', null

    public function mount(Auction $auction)
    {
        if ($auction->status === 'upcoming') {
            return redirect('/');
        }
        
        $this->auction = $auction;
        $this->loadState();
    }

    public function loadState()
    {
        $this->state = AuctionState::where('auction_id', $this->auction->id)->first();
        $participatingTeams = $this->auction->teams;
        
        $teamQuery = Team::with(['auctionPlayers' => function($q) {
            $q->where('auction_id', $this->auction->id)
              ->where('status', 'sold')
              ->with('player');
        }]);

        if ($participatingTeams->isNotEmpty()) {
            $teamQuery->whereIn('id', $participatingTeams->pluck('id'));
        }

        $this->teams = $teamQuery->get()->keyBy('id')->toArray();
        
        if ($this->state && $this->state->current_auction_player_id) {
            $ap = AuctionPlayer::with('player')->find($this->state->current_auction_player_id);
            if ($ap) {
                $this->currentPlayer = $ap->toArray();
                if ($ap->status === 'sold') {
                    $this->statusOverlay = 'sold';
                } elseif ($ap->status === 'unsold') {
                    $this->statusOverlay = 'unsold';
                } else {
                    $this->statusOverlay = null;
                }
            } else {
                $this->currentPlayer = null;
            }
            $this->currentHighestBid = $this->state->current_highest_bid ?? 0;
            if ($this->state->current_highest_team_id) {
                $this->currentHighestTeam = $this->teams[$this->state->current_highest_team_id] ?? null;
            } else {
                $this->currentHighestTeam = null;
            }
            $this->timerEndAt = $this->state->timer_end_at;
        } else {
            $this->currentPlayer = null;
            $this->currentHighestBid = 0;
            $this->currentHighestTeam = null;
            $this->timerEndAt = null;
            $this->statusOverlay = null;
        }
    }

    // Listeners for Reverb Events

    public function getListeners()
    {
        return [
            "echo:auction.{$this->auction->id},PlayerOnAuction" => 'handlePlayerOnAuction',
            "echo:auction.{$this->auction->id},BidPlaced" => 'handleBidPlaced',
            "echo:auction.{$this->auction->id},PlayerSold" => 'handlePlayerSold',
            "echo:auction.{$this->auction->id},PlayerUnsold" => 'handlePlayerUnsold',
            "echo:auction.{$this->auction->id},AuctionEnded" => 'handleAuctionEnded',
        ];
    }

    public function handlePlayerOnAuction($payload)
    {
        $this->loadState();
        $this->statusOverlay = null;
        $this->dispatch('player-changed', timerEndAt: $this->timerEndAt ? $this->timerEndAt->toISOString() : null);
    }

    public function handleBidPlaced($payload)
    {
        $this->loadState();
        $this->dispatch('bid-placed', timerEndAt: $this->timerEndAt ? $this->timerEndAt->toISOString() : null);
    }

    public function handlePlayerSold($payload)
    {
        $this->loadState();
        $this->statusOverlay = 'sold';
        $this->dispatch('player-sold');
    }

    public function handlePlayerUnsold($payload)
    {
        $this->loadState();
        $this->statusOverlay = 'unsold';
        $this->dispatch('player-unsold');
    }

    public function handleAuctionEnded($payload)
    {
        $this->auction->refresh();
        $this->loadState();
    }

    public function render()
    {
        return view('livewire.public.auction.live');
    }
}
