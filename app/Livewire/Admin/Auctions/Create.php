<?php

namespace App\Livewire\Admin\Auctions;

use Livewire\Component;
use App\Models\Auction;
use App\Models\AuctionState;
use App\Models\Player;
use App\Models\AuctionPlayer;

use App\Models\Team;

class Create extends Component
{
    public $title;
    public $auction_date;
    public $timer_seconds = 15;
    
    public $bid_rules = [
        ['upto' => 100, 'increment' => 10],
        ['upto' => 500, 'increment' => 25],
    ];

    public $selectedPlayers = [];
    public $selectedTeams = [];

    protected $rules = [
        'title' => 'required|string|max:255',
        'auction_date' => 'required|date',
        'timer_seconds' => 'required|integer|min:5',
        'bid_rules.*.upto' => 'required|numeric|min:0',
        'bid_rules.*.increment' => 'required|numeric|min:0',
    ];

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
        $playersByCategory = Player::where('status', 'available')
            ->orderBy('base_price', 'desc')
            ->get()
            ->groupBy('category');
            
        $teams = Team::all();

        return view('livewire.admin.auctions.create', [
            'playersByCategory' => $playersByCategory,
            'teams' => $teams,
        ])->layout('layouts.app');
    }

    public function save()
    {
        $this->validate();

        $auction = Auction::create([
            'title' => $this->title,
            'auction_date' => $this->auction_date,
            'status' => 'upcoming',
        ]);

        AuctionState::create([
            'auction_id' => $auction->id,
            'timer_seconds' => $this->timer_seconds,
            'bid_increment_rule' => $this->bid_rules,
        ]);

        // Auto-order players based on category priority
        if (!empty($this->selectedPlayers)) {
            $players = Player::whereIn('id', $this->selectedPlayers)->get();
            
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

            $orderNo = 1;
            foreach ($orderedPlayers as $player) {
                AuctionPlayer::create([
                    'auction_id' => $auction->id,
                    'player_id' => $player->id,
                    'order_no' => $orderNo++,
                    'status' => 'pending',
                ]);
            }
        }

        if (!empty($this->selectedTeams)) {
            $auction->teams()->attach($this->selectedTeams);
        }

        session()->flash('message', 'Auction created successfully!');
        return $this->redirect(route('admin.auctions'), navigate: true);
    }
}
