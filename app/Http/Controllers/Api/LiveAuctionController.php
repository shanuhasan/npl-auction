<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Auction;
use App\Models\AuctionState;
use App\Models\AuctionPlayer;
use App\Models\Team;

class LiveAuctionController extends Controller
{
    public function current()
    {
        // Find the most recent active/live auction
        $auction = Auction::where('status', 'live')->latest()->first();

        if (!$auction) {
            return response()->json([
                'success' => false,
                'message' => 'No active live auction found.',
            ], 404);
        }

        return $this->show($auction);
    }

    public function show(Auction $auction)
    {
        // For completed or upcoming auctions, just return the auction details.
        // We will include the state even if completed.
        $state = AuctionState::where('auction_id', $auction->id)->first();
        
        $participatingTeams = $auction->teams;
        
        $teamQuery = Team::with(['auctionPlayers' => function($q) use ($auction) {
            $q->where('auction_id', $auction->id)
              ->where('status', 'sold')
              ->with('player');
        }]);

        if ($participatingTeams->isNotEmpty()) {
            $teamQuery->whereIn('id', $participatingTeams->pluck('id'));
        }

        $teams = $teamQuery->get()->keyBy('id')->toArray();
        
        $currentPlayer = null;
        $currentHighestBid = 0;
        $currentHighestTeam = null;
        $timerEndAt = null;
        $statusOverlay = null;

        if ($state && $state->current_auction_player_id) {
            $ap = AuctionPlayer::with('player')->find($state->current_auction_player_id);
            if ($ap) {
                $currentPlayer = $ap->toArray();
                if ($ap->status === 'sold') {
                    $statusOverlay = 'sold';
                } elseif ($ap->status === 'unsold') {
                    $statusOverlay = 'unsold';
                }
            }
            
            $currentHighestBid = $state->current_highest_bid ?? 0;
            if ($state->current_highest_team_id) {
                $currentHighestTeam = $teams[$state->current_highest_team_id] ?? null;
            }
            
            $timerEndAt = $state->timer_end_at;
        }

        return response()->json([
            'success' => true,
            'data' => [
                'auction' => [
                    'id' => $auction->id,
                    'title' => $auction->title,
                    'status' => $auction->status,
                ],
                'state' => [
                    'current_player' => $currentPlayer,
                    'current_highest_bid' => $currentHighestBid,
                    'current_highest_team' => $currentHighestTeam,
                    'timer_end_at' => $timerEndAt,
                    'status_overlay' => $statusOverlay, // 'sold', 'unsold', null
                ],
                'teams' => array_values($teams) // Reset keys for JSON array
            ]
        ]);
    }
}
