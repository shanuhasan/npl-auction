<?php

namespace App\Livewire\Reports;

use Livewire\Component;
use App\Models\Auction;
use App\Models\AuctionPlayer;

class AuctionReport extends Component
{
    public $auctions;
    public $selectedAuctionId;

    public function mount()
    {
        $this->auctions = Auction::orderBy('created_at', 'desc')->get();
        if ($this->auctions->isNotEmpty()) {
            $this->selectedAuctionId = $this->auctions->first()->id;
        }
    }

    public function render()
    {
        $stats = [];
        $teamsData = [];
        $unsoldPlayers = [];

        if ($this->selectedAuctionId) {
            $auctionPlayers = AuctionPlayer::with(['player', 'soldToTeam'])
                ->where('auction_id', $this->selectedAuctionId)
                ->get();

            $stats['total_players'] = $auctionPlayers->count();
            $stats['total_sold'] = $auctionPlayers->where('status', 'sold')->count();
            $stats['total_unsold'] = $auctionPlayers->where('status', 'unsold')->count();
            $stats['total_spent'] = $auctionPlayers->where('status', 'sold')->sum('final_price');
            
            // Highest Bid
            $highest = $auctionPlayers->where('status', 'sold')->sortByDesc('final_price')->first();
            $stats['highest_bid'] = $highest ? $highest->final_price : 0;
            $stats['highest_bid_player'] = $highest ? $highest->player->name : 'N/A';
            $stats['highest_bid_team'] = $highest ? $highest->soldToTeam->name : 'N/A';

            // Group sold players by team
            $allTeamsData = $auctionPlayers->where('status', 'sold')->groupBy('sold_to_team_id');
            
            if (auth()->check() && auth()->user()->role === 'team_owner') {
                $myTeam = \App\Models\Team::where('owner_id', auth()->id())->first();
                if ($myTeam && $allTeamsData->has($myTeam->id)) {
                    $teamsData = collect([$myTeam->id => $allTeamsData->get($myTeam->id)]);
                } else {
                    $teamsData = collect();
                }
            } else {
                $teamsData = $allTeamsData;
            }
            
            // Unsold players
            $unsoldPlayers = $auctionPlayers->where('status', 'unsold');
        }

        return view('livewire.reports.auction-report', [
            'stats' => $stats,
            'teamsData' => $teamsData,
            'unsoldPlayers' => $unsoldPlayers,
        ])->layout('layouts.app');
    }
}
