<?php

namespace App\Livewire\Admin\Auctions;

use Livewire\Component;
use App\Models\Auction;
use App\Models\AuctionState;
use App\Models\Player;
use App\Models\AuctionPlayer;
use App\Models\Team;

class Edit extends Component
{
    public Auction $auction;
    
    public $title;
    public $auction_date;
    public $timer_seconds;
    
    public $bid_rules = [];

    public $selectedPlayers = [];
    public $selectedTeams = [];

    protected $rules = [
        'title' => 'required|string|max:255',
        'auction_date' => 'required|date',
        'timer_seconds' => 'required|integer|min:5',
        'bid_rules.*.upto' => 'required|numeric|min:0',
        'bid_rules.*.increment' => 'required|numeric|min:0',
    ];

    public function mount(Auction $auction)
    {
        $this->auction = $auction;
        $this->title = $auction->title;
        // Format for datetime-local
        $this->auction_date = \Carbon\Carbon::parse($auction->auction_date)->format('Y-m-d\TH:i');
        
        if ($auction->state) {
            $this->timer_seconds = $auction->state->timer_seconds;
            $this->bid_rules = $auction->state->bid_increment_rule ?? [];
        } else {
            $this->timer_seconds = 15;
            $this->bid_rules = [
                ['upto' => 100, 'increment' => 10],
                ['upto' => 500, 'increment' => 25],
            ];
        }

        $this->selectedTeams = $auction->teams()->pluck('teams.id')->map(fn($id) => (string)$id)->toArray();
        $this->selectedPlayers = $auction->players()->pluck('players.id')->map(fn($id) => (string)$id)->toArray();
    }

    public function addRule()
    {
        $this->bid_rules[] = ['upto' => 0, 'increment' => 0];
    }

    public function removeRule($index)
    {
        unset($this->bid_rules[$index]);
        $this->bid_rules = array_values($this->bid_rules);
    }

    public function selectAllPlayers()
    {
        $this->selectedPlayers = Player::where('status', 'available')->pluck('id')->map(fn($id) => (string)$id)->toArray();
    }

    public function deselectAllPlayers()
    {
        $this->selectedPlayers = [];
    }

    public function render()
    {
        // Group players by category for display
        // We only want to show available players OR players already attached to this auction
        $playersByCategory = Player::where(function($query) {
                $query->where('status', 'available')
                      ->orWhereIn('id', $this->selectedPlayers);
            })
            ->orderBy('base_price', 'desc')
            ->get()
            ->groupBy('category');
            
        $teams = Team::all();

        return view('livewire.admin.auctions.edit', [
            'playersByCategory' => $playersByCategory,
            'teams' => $teams,
        ])->layout('layouts.app');
    }

    public function save()
    {
        $this->validate();

        $this->auction->update([
            'title' => $this->title,
            'auction_date' => $this->auction_date,
        ]);

        if ($this->auction->state) {
            $this->auction->state->update([
                'timer_seconds' => $this->timer_seconds,
                'bid_increment_rule' => $this->bid_rules,
            ]);
        } else {
            AuctionState::create([
                'auction_id' => $this->auction->id,
                'timer_seconds' => $this->timer_seconds,
                'bid_increment_rule' => $this->bid_rules,
            ]);
        }

        // Teams Update
        $this->auction->teams()->sync($this->selectedTeams);

        // Players Update
        $existingPlayerIds = $this->auction->players()->pluck('players.id')->toArray();
        $newSelectedPlayerIds = array_map('intval', $this->selectedPlayers);
        
        $playersToAdd = array_diff($newSelectedPlayerIds, $existingPlayerIds);
        $playersToRemove = array_diff($existingPlayerIds, $newSelectedPlayerIds);

        if (!empty($playersToRemove)) {
            // Remove players that are no longer selected (maybe only if they haven't been sold yet, 
            // but for now we just delete the AuctionPlayer records).
            AuctionPlayer::where('auction_id', $this->auction->id)
                         ->whereIn('player_id', $playersToRemove)
                         ->where('status', 'pending') // only safe to remove if pending
                         ->delete();
        }

        if (!empty($playersToAdd)) {
            $players = Player::whereIn('id', $playersToAdd)->get();
            
            // Priority map
            $priority = [
                'marquee' => 1,
                'set-a' => 2,
                'set-b' => 3,
                'set-c' => 4,
            ];

            $orderedPlayers = $players->sortBy(function($player) use ($priority) {
                $prio = $priority[$player->category] ?? 99;
                return sprintf('%02d-%010d', $prio, 9999999999 - $player->base_price);
            })->values();

            // Find max order_no currently in the auction to append correctly
            $maxOrderNo = AuctionPlayer::where('auction_id', $this->auction->id)->max('order_no') ?? 0;
            $orderNo = $maxOrderNo + 1;

            foreach ($orderedPlayers as $player) {
                AuctionPlayer::create([
                    'auction_id' => $this->auction->id,
                    'player_id' => $player->id,
                    'order_no' => $orderNo++,
                    'status' => 'pending',
                ]);
            }
        }

        session()->flash('message', 'Auction updated successfully!');
        return $this->redirect(route('admin.auctions'), navigate: true);
    }
}
