<?php

namespace App\Livewire\Admin\Auctions;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;
use App\Models\Auction;
use App\Models\AuctionState;
use App\Models\AuctionPlayer;
use App\Models\Team;
use App\Models\Bid;
use App\Models\Player;
use App\Services\AuctionService;
use Illuminate\Support\Facades\Storage;

#[Layout('layouts.app')]
class Control extends Component
{
    use WithFileUploads;

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

    // Add Missed Player Modal State
    public $isAddPlayerModalOpen = false;
    public $addPlayerTab = 'existing'; // 'existing' or 'new'
    public $searchMissedPlayer = '';
    public $selectedMissedPlayerId = '';
    public $addPosition = 'next'; // 'next' or 'end'

    // New Player Quick Create Form
    public $new_name = '';
    public $new_role = 'batsman';
    public $new_country = 'India';
    public $new_city = '';
    public $new_contact_no = '';
    public $new_base_price = 1000;
    public $new_category = 'set-a';
    public $new_photo = null;

    public function mount(Auction $auction)
    {
        if ($auction->status === 'completed') {
            return redirect()->route('admin.auctions');
        }
        
        $this->auction = $auction;
        $this->teams = $auction->teams()->get();
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

        $this->pendingCount = AuctionPlayer::whereHas('player', fn($q) => $q->where('is_approved', true))->where('auction_id', $this->auction->id)->where('status', 'pending')->count();
        $this->soldCount = AuctionPlayer::whereHas('player', fn($q) => $q->where('is_approved', true))->where('auction_id', $this->auction->id)->where('status', 'sold')->count();
        $this->unsoldCount = AuctionPlayer::whereHas('player', fn($q) => $q->where('is_approved', true))->where('auction_id', $this->auction->id)->where('status', 'unsold')->count();

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
            $this->loadData();
        } else {
            session()->flash('success', $result['message']);
            return redirect()->route('admin.auctions');
        }
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

    public function deleteBid($bidId)
    {
        if (!$this->currentPlayer || $this->currentPlayer['status'] !== 'current') return;

        $service = app(AuctionService::class);
        $result = $service->deleteBid($bidId);
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

    public function shufflePendingPlayers()
    {
        $pendingPlayers = AuctionPlayer::where('auction_id', $this->auction->id)
            ->where('status', 'pending')
            ->get()
            ->shuffle();
        
        $order = 1;
        foreach ($pendingPlayers as $ap) {
            $ap->update(['order_no' => $order]);
            $order++;
        }
        
        session()->flash('success', 'Pending players have been shuffled successfully.');
        $this->loadData();
    }

    public function openAddPlayerModal()
    {
        $this->resetAddPlayerForm();
        $this->isAddPlayerModalOpen = true;
    }

    public function closeAddPlayerModal()
    {
        $this->isAddPlayerModalOpen = false;
        $this->resetAddPlayerForm();
    }

    public function resetAddPlayerForm()
    {
        $this->addPlayerTab = 'existing';
        $this->searchMissedPlayer = '';
        $this->selectedMissedPlayerId = '';
        $this->addPosition = 'next';
        $this->new_name = '';
        $this->new_role = 'batsman';
        $this->new_country = 'India';
        $this->new_city = '';
        $this->new_contact_no = '';
        $this->new_base_price = 1000;
        $this->new_category = 'set-a';
        $this->new_photo = null;
        $this->resetErrorBag();
    }

    public function addExistingPlayer()
    {
        $this->validate([
            'selectedMissedPlayerId' => 'required|exists:players,id',
            'addPosition' => 'required|in:end,next',
        ], [
            'selectedMissedPlayerId.required' => 'Please select a player to add.',
        ]);

        $exists = AuctionPlayer::where('auction_id', $this->auction->id)
            ->where('player_id', $this->selectedMissedPlayerId)
            ->exists();

        if ($exists) {
            session()->flash('error', 'This player is already in this auction.');
            return;
        }

        $orderNo = 1;
        if ($this->addPosition === 'next') {
            $minOrder = AuctionPlayer::where('auction_id', $this->auction->id)
                ->where('status', 'pending')
                ->min('order_no');
            $orderNo = ($minOrder !== null) ? ($minOrder - 1) : 1;
        } else {
            $maxOrder = AuctionPlayer::where('auction_id', $this->auction->id)->max('order_no') ?? 0;
            $orderNo = $maxOrder + 1;
        }

        AuctionPlayer::create([
            'auction_id' => $this->auction->id,
            'player_id' => $this->selectedMissedPlayerId,
            'order_no' => $orderNo,
            'status' => 'pending',
        ]);

        Player::where('id', $this->selectedMissedPlayerId)->update(['status' => 'available']);

        session()->flash('success', 'Player added to auction successfully!');
        $this->closeAddPlayerModal();
        $this->loadData();
    }

    public function quickCreatePlayer()
    {
        $this->validate([
            'new_name' => 'required|string|max:255',
            'new_role' => 'required|in:batsman,bowler,all-rounder,wicketkeeper',
            'new_base_price' => 'required|numeric|min:0',
            'new_category' => 'required|in:marquee,set-a,set-b,set-c',
            'new_contact_no' => 'nullable|string|max:20',
            'new_city' => 'nullable|string|max:100',
            'new_country' => 'required|string|max:100',
            'new_photo' => 'nullable|image|max:2048',
            'addPosition' => 'required|in:end,next',
        ]);

        $photoPath = null;
        if ($this->new_photo) {
            try {
                $filename = pathinfo($this->new_photo->hashName(), PATHINFO_FILENAME) . '.webp';
                $manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
                $image = $manager->read($this->new_photo->getRealPath())
                    ->scaleDown(800, 800)
                    ->toWebp(80);
                Storage::disk('public')->put('players/' . $filename, (string) $image);
                $photoPath = 'players/' . $filename;
            } catch (\Throwable $e) {
                $photoPath = $this->new_photo->store('players', 'public');
            }
        }

        $player = Player::create([
            'name' => $this->new_name,
            'photo' => $photoPath,
            'role' => $this->new_role,
            'country' => $this->new_country ?: 'India',
            'city' => $this->new_city,
            'contact_no' => $this->new_contact_no ?: rand(1000000000, 9999999999),
            'base_price' => $this->new_base_price,
            'category' => $this->new_category,
            'status' => 'available',
            'is_approved' => true,
            'stats' => [
                'matches' => 0,
                'runs' => 0,
                'wickets' => 0,
                'average' => 0,
                'strike_rate' => 0,
            ],
        ]);

        $orderNo = 1;
        if ($this->addPosition === 'next') {
            $minOrder = AuctionPlayer::where('auction_id', $this->auction->id)
                ->where('status', 'pending')
                ->min('order_no');
            $orderNo = ($minOrder !== null) ? ($minOrder - 1) : 1;
        } else {
            $maxOrder = AuctionPlayer::where('auction_id', $this->auction->id)->max('order_no') ?? 0;
            $orderNo = $maxOrder + 1;
        }

        AuctionPlayer::create([
            'auction_id' => $this->auction->id,
            'player_id' => $player->id,
            'order_no' => $orderNo,
            'status' => 'pending',
        ]);

        session()->flash('success', "Player {$player->name} created and added to auction!");
        $this->closeAddPlayerModal();
        $this->loadData();
    }

    public function render()
    {
        $playersList = AuctionPlayer::with(['player', 'soldToTeam'])
            ->whereHas('player', fn($q) => $q->where('is_approved', true))
            ->where('auction_id', $this->auction->id)
            ->orderBy('order_no', 'asc')
            ->get();

        $existingPlayerIds = AuctionPlayer::where('auction_id', $this->auction->id)->pluck('player_id');
        $missedPlayers = Player::where('is_approved', true)
            ->whereNotIn('id', $existingPlayerIds)
            ->when($this->searchMissedPlayer, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->searchMissedPlayer . '%')
                      ->orWhere('role', 'like', '%' . $this->searchMissedPlayer . '%')
                      ->orWhere('category', 'like', '%' . $this->searchMissedPlayer . '%');
                });
            })
            ->orderBy('name', 'asc')
            ->get();

        return view('livewire.admin.auctions.control', [
            'playersList' => $playersList,
            'missedPlayers' => $missedPlayers,
        ]);
    }
    public function revertPlayer($auctionPlayerId)
    {
        $service = app(AuctionService::class);
        $result = $service->revertPlayer($auctionPlayerId);
        if (!$result['success']) {
            session()->flash('error', $result['message']);
        } else {
            session()->flash('success', $result['message']);
        }
        $this->loadData();
    }
    public function exportResults()
    {
        $players = AuctionPlayer::with(['player', 'soldToTeam'])
            ->where('auction_id', $this->auction->id)
            ->orderBy('order_no', 'asc')
            ->get();

        $csvHeader = ['S.No', 'Player Name', 'Role', 'Status'];
        $csvData = [];
        
        $sno = 1;
        foreach ($players as $ap) {
            $csvData[] = [
                $sno++,
                $ap->player->name ?? 'N/A',
                ucfirst($ap->player->role ?? 'N/A'),
                ucfirst($ap->status),
            ];
        }

        $fileName = 'auction_' . $this->auction->id . '_results_' . now()->format('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($csvHeader, $csvData) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $csvHeader);
            foreach ($csvData as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        }, $fileName);
    }
}
