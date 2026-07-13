<?php

namespace App\Livewire\Admin\Players;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\Player;
use App\Models\Team;
use App\Models\Auction;
use App\Models\AuctionPlayer;
use Illuminate\Support\Facades\Storage;

class Index extends Component
{
    use WithPagination, WithFileUploads;

    public $search = '';
    public $filterRole = '';
    public $filterStatus = '';

    public $filterApproval = '';

    public $player_id, $name, $role, $country, $city, $batting_style, $bowling_style, $base_price, $category, $status, $is_approved;
    public $photo, $existing_photo;
    
    // Stats array for json
    public $stats = [
        'matches' => 0,
        'runs' => 0,
        'wickets' => 0,
        'average' => 0,
        'strike_rate' => 0,
    ];

    public $isModalOpen = false;

    // Manual Assignment Properties
    public $isAssignModalOpen = false;
    public $assignPlayerId;
    public $assignTeamId;
    public $assignPrice = 0;

    protected $rules = [
        'name' => 'required|string|max:255',
        'role' => 'required|in:batsman,bowler,all-rounder,wicketkeeper',
        'country' => 'required|string|max:100',
        'city' => 'nullable|string|max:100',
        'batting_style' => 'nullable|string|max:100',
        'bowling_style' => 'nullable|string|max:100',
        'base_price' => 'required|numeric|min:0',
        'category' => 'required|in:marquee,set-a,set-b,set-c',
        'status' => 'required|in:available,sold,unsold',
        'is_approved' => 'boolean',
        'photo' => 'nullable|image|max:2048',
        'stats.matches' => 'nullable|integer',
        'stats.runs' => 'nullable|integer',
        'stats.wickets' => 'nullable|integer',
        'stats.average' => 'nullable|numeric',
        'stats.strike_rate' => 'nullable|numeric',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $teams = Team::all();

        $players = Player::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->when($this->filterRole, function ($query) {
                $query->where('role', $this->filterRole);
            })
            ->when($this->filterStatus, function ($query) {
                $query->where('status', $this->filterStatus);
            })
            ->when($this->filterApproval !== '', function ($query) {
                $query->where('is_approved', $this->filterApproval === '1');
            })
            ->latest()
            ->paginate(10);

        return view('livewire.admin.players.index', [
            'players' => $players,
            'teams' => $teams,
        ])->layout('layouts.app');
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

    private function resetCreateForm()
    {
        $this->player_id = null;
        $this->name = '';
        $this->role = 'batsman';
        $this->country = 'India';
        $this->city = '';
        $this->batting_style = '';
        $this->bowling_style = '';
        $this->base_price = 0;
        $this->category = 'set-a';
        $this->status = 'available';
        $this->is_approved = true;
        $this->photo = null;
        $this->existing_photo = null;
        $this->stats = [
            'matches' => 0,
            'runs' => 0,
            'wickets' => 0,
            'average' => 0,
            'strike_rate' => 0,
        ];
    }

    public function store()
    {
        $this->validate();

        $photoPath = $this->existing_photo;
        if ($this->photo) {
            if ($this->existing_photo) {
                Storage::disk('public')->delete($this->existing_photo);
            }
            $photoPath = $this->photo->store('players', 'public');
        }

        Player::updateOrCreate(['id' => $this->player_id], [
            'name' => $this->name,
            'photo' => $photoPath,
            'role' => $this->role,
            'country' => $this->country,
            'city' => $this->city,
            'batting_style' => $this->batting_style,
            'bowling_style' => $this->bowling_style,
            'base_price' => $this->base_price,
            'category' => $this->category,
            'status' => $this->status,
            'is_approved' => $this->is_approved,
            'stats' => $this->stats,
        ]);

        session()->flash('message', $this->player_id ? 'Player Updated Successfully.' : 'Player Created Successfully.');
        $this->closeModal();
        $this->resetCreateForm();
    }

    public function edit($id)
    {
        $player = Player::findOrFail($id);
        $this->player_id = $player->id;
        $this->name = $player->name;
        $this->role = $player->role;
        $this->country = $player->country;
        $this->city = $player->city;
        $this->batting_style = $player->batting_style;
        $this->bowling_style = $player->bowling_style;
        $this->base_price = $player->base_price;
        $this->category = $player->category;
        $this->status = $player->status;
        $this->is_approved = $player->is_approved;
        $this->existing_photo = $player->photo;
        
        $stats = $player->stats ?? [];
        $this->stats = array_merge([
            'matches' => 0,
            'runs' => 0,
            'wickets' => 0,
            'average' => 0,
            'strike_rate' => 0,
        ], $stats);
        
        $this->openModal();
    }

    public function approve($id)
    {
        $player = Player::findOrFail($id);
        $player->is_approved = true;
        $player->save();
        session()->flash('message', 'Player Approved Successfully.');
    }

    public function delete($id)
    {
        $player = Player::findOrFail($id);
        if ($player->photo) {
            Storage::disk('public')->delete($player->photo);
        }
        $player->delete();
        session()->flash('message', 'Player Deleted Successfully.');
    }

    // --- MANUAL ASSIGNMENT ---

    public function openAssignModal($id)
    {
        $this->assignPlayerId = $id;
        $player = Player::findOrFail($id);
        $this->assignPrice = $player->base_price;
        $this->assignTeamId = '';
        $this->isAssignModalOpen = true;
    }

    public function closeAssignModal()
    {
        $this->isAssignModalOpen = false;
        $this->assignPlayerId = null;
        $this->assignTeamId = '';
        $this->assignPrice = 0;
    }

    public function assignPlayer()
    {
        $this->validate([
            'assignTeamId' => 'required|exists:teams,id',
            'assignPrice' => 'required|numeric|min:0',
        ]);

        $player = Player::findOrFail($this->assignPlayerId);
        $team = Team::findOrFail($this->assignTeamId);

        // Deduct budget
        if ($this->assignPrice > 0) {
            $team->remaining_budget -= $this->assignPrice;
            $team->save();
        }

        // Mark player as sold
        $player->status = 'sold';
        $player->current_team_id = $team->id;
        $player->save();

        // Create an AuctionPlayer record for the latest active/upcoming auction so it shows in the reports
        $auction = Auction::whereIn('status', ['live', 'paused', 'upcoming'])->latest()->first();
        if (!$auction) {
            $auction = Auction::latest()->first();
        }

        if ($auction) {
            AuctionPlayer::create([
                'auction_id' => $auction->id,
                'player_id' => $player->id,
                'status' => 'sold',
                'final_price' => $this->assignPrice,
                'sold_to_team_id' => $team->id,
                'order_no' => 0, // 0 to indicate pre-auction retention/assignment
            ]);
        }

        $this->closeAssignModal();
        session()->flash('message', "Player manually assigned to {$team->name} successfully.");
    }
}
