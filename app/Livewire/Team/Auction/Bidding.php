<?php

namespace App\Livewire\Team\Auction;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use App\Models\Auction;
use App\Models\AuctionState;
use App\Models\AuctionPlayer;
use App\Models\Team;
use App\Services\AuctionService;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.auction-live')]
class Bidding extends Component
{
    public $auction;
    public $myTeam;
    
    public $state;
    public $currentPlayer = null;
    public $currentHighestBid = 0;
    public $currentHighestTeam = null;
    public $timerEndAt = null;
    public $statusOverlay = null;

    public $squad = [];

    public $nextValidBid = 0;
    public $canBid = false;

    public function mount(Auction $auction)
    {
        $this->auction = $auction;
        $this->myTeam = Team::where('owner_id', Auth::id())->first();

        if (!$this->myTeam) {
            abort(403, 'You do not own any team.');
        }

        $this->loadState();
    }

    public function loadState()
    {
        $this->myTeam->refresh();
        $this->state = AuctionState::where('auction_id', $this->auction->id)->first();
        
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
                $this->currentHighestTeam = Team::find($this->state->current_highest_team_id)->toArray();
            } else {
                $this->currentHighestTeam = null;
            }
            $this->timerEndAt = $this->state->timer_end_at;
            
            $this->calculateNextBid();
        } else {
            $this->currentPlayer = null;
            $this->currentHighestBid = 0;
            $this->currentHighestTeam = null;
            $this->timerEndAt = null;
            $this->statusOverlay = null;
            $this->nextValidBid = 0;
            $this->canBid = false;
        }

        $this->squad = AuctionPlayer::with('player')
            ->where('auction_id', $this->auction->id)
            ->where('sold_to_team_id', $this->myTeam->id)
            ->orderBy('updated_at', 'desc')
            ->get()->toArray();
    }

    public function calculateNextBid()
    {
        if (!$this->currentPlayer || $this->statusOverlay || $this->auction->status !== 'live') {
            $this->canBid = false;
            return;
        }

        $currentBid = $this->currentHighestBid;
        if ($currentBid == 0) {
            $this->nextValidBid = $this->currentPlayer['player']['base_price'];
        } else {
            $increment = \App\Services\AuctionService::calculateNextBidIncrement($currentBid, $this->state->bid_increment_rule, $this->state->manual_bid_increment);
            $this->nextValidBid = $currentBid + $increment;
        }

        // Check if can bid
        $this->canBid = true;
        if ($this->myTeam->remaining_budget < $this->nextValidBid) {
            $this->canBid = false;
        }
        if ($this->currentHighestTeam && $this->currentHighestTeam['id'] === $this->myTeam->id) {
            $this->canBid = false; // Already highest bidder
        }
    }

    public function placeBid()
    {
        $this->loadState(); // Ensure freshest state
        
        if (!$this->canBid) {
            return; // Prevent if illegally called
        }

        $service = app(AuctionService::class);
        $result = $service->placeBid($this->auction->id, $this->myTeam->id, $this->nextValidBid);

        if (!$result['success']) {
            $this->addError('bid', $result['message']);
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
        return view('livewire.team.auction.bidding');
    }
}
