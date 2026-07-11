<?php

namespace App\Livewire\Admin\Auctions;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Auction;
use App\Models\AuctionState;
use App\Models\AuctionPlayer;
use App\Models\Team;
use App\Models\Bid;
use App\Services\AuctionService;

#[Layout('layouts.app')]
class Control extends Component
{
    public $auction;
    public $state;
    public $currentPlayer = null;
    
    // Stats
    public $pendingCount = 0;
    public $soldCount = 0;
    public $unsoldCount = 0;

    public $auto_sold = true;
    public $manualBidIncrement = 0;

    // Manual Override
    public $overrideAmount;
    public $overrideTeamId;
    public $teams = [];

    // Bids
    public $recentBids = [];

    public function mount(Auction $auction)
    {
        $this->auction = $auction;
        $this->teams = Team::all();
        $this->loadData();
    }

    public function loadData()
    {
        $this->auction->refresh();
        $this->state = AuctionState::where('auction_id', $this->auction->id)->first();
        
        if ($this->state) {
            $this->auto_sold = (bool) $this->state->auto_sold;
            $this->manualBidIncrement = $this->state->manual_bid_increment ?? 0;
        }

        $this->pendingCount = AuctionPlayer::where('auction_id', $this->auction->id)->where('status', 'pending')->count();
        $this->soldCount = AuctionPlayer::where('auction_id', $this->auction->id)->where('status', 'sold')->count();
        $this->unsoldCount = AuctionPlayer::where('auction_id', $this->auction->id)->where('status', 'unsold')->count();

        if ($this->state && $this->state->current_auction_player_id) {
            $ap = AuctionPlayer::with('player')->find($this->state->current_auction_player_id);
            $this->currentPlayer = $ap ? $ap->toArray() : null;
            
            $this->recentBids = Bid::with('team')
                ->where('auction_player_id', $this->state->current_auction_player_id)
                ->orderBy('id', 'desc')
                ->take(10)
                ->get()
                ->toArray();
        } else {
            $this->currentPlayer = null;
            $this->recentBids = [];
        }
    }

    public function getListeners()
    {
        return [
            "echo:auction.{$this->auction->id},PlayerOnAuction" => 'loadData',
            "echo:auction.{$this->auction->id},BidPlaced" => 'loadData',
            "echo:auction.{$this->auction->id},PlayerSold" => 'loadData',
            "echo:auction.{$this->auction->id},PlayerUnsold" => 'loadData',
            "echo:auction.{$this->auction->id},AuctionEnded" => 'loadData',
        ];
    }

    public function nextPlayer()
    {
        $service = app(AuctionService::class);
        $result = $service->nextPlayer($this->auction->id);
        if (!$result['success']) {
            session()->flash('error', $result['message']);
        }
        $this->loadData();
    }

    public function startAuction()
    {
        $service = app(AuctionService::class);
        $result = $service->startAuction($this->auction->id);
        if (!$result['success']) {
            session()->flash('error', $result['message']);
        }
        $this->loadData();
    }

    public function completeAuction()
    {
        $service = app(AuctionService::class);
        $result = $service->endAuction($this->auction->id);
        if (!$result['success']) {
            session()->flash('error', $result['message']);
        } else {
            session()->flash('success', $result['message']);
        }
        $this->loadData();
    }


    public function markSold()
    {
        if (!$this->currentPlayer || $this->currentPlayer['status'] !== 'current') return;
        
        $service = app(AuctionService::class);
        $result = $service->markSold($this->currentPlayer['id']);
        if (!$result['success']) {
            session()->flash('error', $result['message']);
        }
        $this->loadData();
    }

    public function sellToBid($bidId)
    {
        if (!$this->currentPlayer || $this->currentPlayer['status'] !== 'current') return;

        $service = app(AuctionService::class);
        $result = $service->markSoldToBid($bidId);
        if (!$result['success']) {
            session()->flash('error', $result['message']);
        } else {
            session()->flash('success', $result['message']);
        }
        $this->loadData();
    }

    public function markUnsold()
    {
        if (!$this->currentPlayer || $this->currentPlayer['status'] !== 'current') return;

        $service = app(AuctionService::class);
        $result = $service->markUnsold($this->currentPlayer['id']);
        if (!$result['success']) {
            session()->flash('error', $result['message']);
        }
        $this->loadData();
    }

    public function pauseAuction()
    {
        $this->auction->update(['status' => 'paused']);
        $this->loadData();
    }

    public function resumeAuction()
    {
        $this->auction->update(['status' => 'live']);
        $this->loadData();
    }

    public function overrideBid()
    {
        $this->validate([
            'overrideAmount' => 'required|numeric|min:0',
            'overrideTeamId' => 'required|exists:teams,id'
        ]);

        if (!$this->state || !$this->state->current_auction_player_id) {
            session()->flash('error', 'No active player to bid on.');
            return;
        }

        // We bypass standard rules for Admin Override
        Bid::create([
            'auction_player_id' => $this->state->current_auction_player_id,
            'team_id' => $this->overrideTeamId,
            'bid_amount' => $this->overrideAmount,
        ]);

        $this->state->update([
            'current_highest_bid' => $this->overrideAmount,
            'current_highest_team_id' => $this->overrideTeamId,
            'timer_end_at' => now()->addSeconds($this->state->timer_seconds),
        ]);

        $team = Team::find($this->overrideTeamId);
        event(new \App\Events\BidPlaced($this->auction->id, $team->id, $team->name, $this->overrideAmount));

        $this->overrideAmount = null;
        $this->overrideTeamId = null;
        $this->loadData();
        session()->flash('success', 'Bid manually overridden.');
    }

    public function toggleAutoSold()
    {
        if ($this->state) {
            $newValue = !$this->auto_sold;
            $this->state->update(['auto_sold' => $newValue]);
            $this->auto_sold = $newValue;
            session()->flash('message', 'Auto-Sold status updated to ' . ($newValue ? 'ON' : 'OFF'));
        }
    }

    public function updateManualIncrement()
    {
        if ($this->state) {
            $this->validate(['manualBidIncrement' => 'nullable|numeric|min:0']);
            $this->state->update(['manual_bid_increment' => (int) $this->manualBidIncrement]);
            session()->flash('message', 'Manual bid increment updated.');
        }
    }

    public function recallUnsold()
    {
        $service = app(AuctionService::class);
        $result = $service->recallUnsoldPlayers($this->auction->id);
        if (!$result['success']) {
            session()->flash('error', $result['message']);
        } else {
            session()->flash('success', $result['message']);
        }
        $this->loadData();
    }

    public function render()
    {
        return view('livewire.admin.auctions.control');
    }
}
