<?php

namespace App\Services;

use App\Models\Auction;
use App\Models\AuctionState;
use App\Models\AuctionPlayer;
use App\Models\Player;
use App\Models\Team;
use App\Models\Bid;
use Illuminate\Support\Facades\DB;
use App\Events\PlayerOnAuction;
use App\Events\BidPlaced;
use App\Events\PlayerSold;
use App\Events\PlayerUnsold;
use App\Events\AuctionEnded;

class AuctionService
{
    /**
     * Finds the first pending player and sets them as current.
     */
    public function startAuction($auctionId)
    {
        return DB::transaction(function () use ($auctionId) {
            $auction = Auction::findOrFail($auctionId);
            $state = AuctionState::where('auction_id', $auctionId)->firstOrFail();

            // Find first pending player
            $firstPlayer = AuctionPlayer::where('auction_id', $auctionId)
                ->where('status', 'pending')
                ->orderBy('order_no', 'asc')
                ->first();

            if (!$firstPlayer) {
                return ['success' => false, 'message' => 'No pending players available.'];
            }

            $firstPlayer->update(['status' => 'current']);

            $state->update([
                'current_auction_player_id' => $firstPlayer->id,
                'current_highest_bid' => null,
                'current_highest_team_id' => null,
                'timer_end_at' => now()->addSeconds($state->timer_seconds),
            ]);

            $auction->update(['status' => 'live']);

            // Fire event
            event(new PlayerOnAuction($auctionId, $firstPlayer->player_id, $firstPlayer->player->base_price));

            \App\Jobs\ProcessAuctionTimer::dispatch($state->id, $firstPlayer->id, $state->fresh()->timer_end_at->toISOString())
                ->delay(now()->addSeconds($state->timer_seconds));

            return ['success' => true, 'player' => $firstPlayer];
        });
    }

    /**
     * Dynamically calculates sensible bid increments for large amounts if DB rules are exceeded.
     */
    public static function calculateNextBidIncrement($currentBid, $rules = [], $manualIncrement = 0)
    {
        if ($manualIncrement > 0) {
            return $manualIncrement;
        }

        // First try DB rules
        if (!empty($rules)) {
            $sortedRules = collect($rules)->sortBy('upto');
            foreach ($sortedRules as $rule) {
                if ($currentBid <= $rule['upto']) {
                    return $rule['increment'];
                }
            }
        }
        
        // Dynamic realistic fallback for large numbers
        if ($currentBid < 100000) {
            return 5000;
        } elseif ($currentBid < 500000) {
            return 25000;
        } elseif ($currentBid < 2000000) {
            return 100000;
        } elseif ($currentBid < 5000000) {
            return 200000;
        } elseif ($currentBid < 10000000) {
            return 500000;
        } else {
            return 1000000;
        }
    }

    /**
     * Moves to the next player. Validates that current is sold/unsold.
     */
    public function nextPlayer($auctionId)
    {
        return DB::transaction(function () use ($auctionId) {
            $state = AuctionState::where('auction_id', $auctionId)->firstOrFail();
            
            // Check if current is already decided
            if ($state->current_auction_player_id) {
                $current = AuctionPlayer::find($state->current_auction_player_id);
                if ($current && $current->status === 'current') {
                    return ['success' => false, 'message' => 'Current player must be sold or unsold first.'];
                }
            }

            $nextPlayer = AuctionPlayer::where('auction_id', $auctionId)
                ->where('status', 'pending')
                ->orderBy('order_no', 'asc')
                ->first();

            if (!$nextPlayer) {
                return ['success' => false, 'message' => 'No more pending players.'];
            }

            $nextPlayer->update(['status' => 'current']);

            $state->update([
                'current_auction_player_id' => $nextPlayer->id,
                'current_highest_bid' => 0,
                'current_highest_team_id' => null,
                'timer_end_at' => now()->addSeconds($state->timer_seconds),
            ]);

            // Fire event
            event(new PlayerOnAuction($auctionId, $nextPlayer->player_id, $nextPlayer->player->base_price));

            \App\Jobs\ProcessAuctionTimer::dispatch($state->id, $nextPlayer->id, $state->fresh()->timer_end_at->toISOString())
                ->delay(now()->addSeconds($state->timer_seconds));

            return ['success' => true, 'message' => 'Next player: ' . $nextPlayer->player->name];
        });
    }

    /**
     * Processes a bid.
     */
    public function placeBid($auctionId, $teamId, $bidAmount)
    {
        return DB::transaction(function () use ($auctionId, $teamId, $bidAmount) {
            $auction = Auction::findOrFail($auctionId);
            $state = AuctionState::where('auction_id', $auctionId)->lockForUpdate()->firstOrFail();
            $team = Team::findOrFail($teamId);
            
            if ($auction->status !== 'live') {
                return ['success' => false, 'message' => 'Auction is not live.'];
            }

            if (!$state->current_auction_player_id) {
                return ['success' => false, 'message' => 'No active player on auction.'];
            }

            $auctionPlayer = AuctionPlayer::findOrFail($state->current_auction_player_id);
            $player = $auctionPlayer->player;

            $currentBid = $state->current_highest_bid ?? 0;

            // Calculate min valid bid
            if ($state->current_highest_bid === null || $state->current_highest_bid == 0) {
                // If it's the very first bid, the required amount is EXACTLY the base price
                $minRequired = $player->base_price;
            } else {
                $increment = self::calculateNextBidIncrement($currentBid, $state->bid_increment_rule, $state->manual_bid_increment);
                $minRequired = $currentBid + $increment;
            }

            if ($bidAmount < $minRequired) {
                return ['success' => false, 'message' => 'Bid amount must be at least ₹' . number_format($minRequired) . '.'];
            }

            if ($team->remaining_budget < $bidAmount) {
                return ['success' => false, 'message' => 'Insufficient remaining budget.'];
            }

            if ($state->current_highest_team_id == $teamId) {
                return ['success' => false, 'message' => 'You are already the highest bidder.'];
            }

            // Valid bid!
            Bid::create([
                'auction_player_id' => $auctionPlayer->id,
                'team_id' => $teamId,
                'bid_amount' => $bidAmount,
            ]);

            $state->update([
                'current_highest_bid' => $bidAmount,
                'current_highest_team_id' => $teamId,
                'timer_end_at' => now()->addSeconds($state->timer_seconds),
            ]);

            event(new BidPlaced($auctionId, $teamId, $team->name, $bidAmount));

            \App\Jobs\ProcessAuctionTimer::dispatch($state->id, $auctionPlayer->id, $state->fresh()->timer_end_at->toISOString())
                ->delay(now()->addSeconds($state->timer_seconds));

            return ['success' => true, 'message' => 'Bid placed successfully!'];
        });
    }

    /**
     * Marks the current player as sold.
     */
    public function markSold($auctionPlayerId)
    {
        return DB::transaction(function () use ($auctionPlayerId) {
            $auctionPlayer = AuctionPlayer::findOrFail($auctionPlayerId);
            $state = AuctionState::where('auction_id', $auctionPlayer->auction_id)->firstOrFail();

            if (!$state->current_highest_team_id || !$state->current_highest_bid) {
                return ['success' => false, 'message' => 'Cannot sell without any bids.'];
            }

            $team = Team::findOrFail($state->current_highest_team_id);
            $finalPrice = $state->current_highest_bid;

            $auctionPlayer->update([
                'status' => 'sold',
                'final_price' => $finalPrice,
                'sold_to_team_id' => $team->id,
            ]);

            $auctionPlayer->player->update([
                'status' => 'sold',
                'current_team_id' => $team->id,
            ]);

            // Deduct budget
            $team->update([
                'remaining_budget' => $team->remaining_budget - $finalPrice
            ]);

            event(new PlayerSold($auctionPlayer->auction_id, $auctionPlayer->player_id, $team->id, $finalPrice));

            return ['success' => true, 'message' => 'Player sold to ' . $team->name];
        });
    }

    /**
     * Marks the current player as sold to a specific bid.
     */
    public function markSoldToBid($bidId)
    {
        return DB::transaction(function () use ($bidId) {
            $bid = Bid::findOrFail($bidId);
            $auctionPlayer = AuctionPlayer::findOrFail($bid->auction_player_id);
            $state = AuctionState::where('auction_id', $auctionPlayer->auction_id)->firstOrFail();
            $team = Team::findOrFail($bid->team_id);

            // Update state to match this bid
            $state->update([
                'current_highest_bid' => $bid->bid_amount,
                'current_highest_team_id' => $team->id,
            ]);

            $finalPrice = $bid->bid_amount;

            $auctionPlayer->update([
                'status' => 'sold',
                'final_price' => $finalPrice,
                'sold_to_team_id' => $team->id,
            ]);

            $auctionPlayer->player->update([
                'status' => 'sold',
                'current_team_id' => $team->id,
            ]);

            // Deduct budget
            $team->update([
                'remaining_budget' => $team->remaining_budget - $finalPrice
            ]);

            event(new PlayerSold($auctionPlayer->auction_id, $auctionPlayer->player_id, $team->id, $finalPrice));

            return ['success' => true, 'message' => 'Player sold manually to ' . $team->name];
        });
    }

    /**
     * Marks current player as unsold.
     */
    public function markUnsold($auctionPlayerId)
    {
        return DB::transaction(function () use ($auctionPlayerId) {
            $auctionPlayer = AuctionPlayer::findOrFail($auctionPlayerId);

            $auctionPlayer->update(['status' => 'unsold']);
            $auctionPlayer->player->update(['status' => 'unsold']);

            event(new PlayerUnsold($auctionPlayer->auction_id, $auctionPlayer->player_id));

            return ['success' => true, 'message' => 'Player marked as unsold.'];
        });
    }

    /**
     * Ends the auction.
     */
    public function endAuction($auctionId)
    {
        return DB::transaction(function () use ($auctionId) {
            $auction = Auction::findOrFail($auctionId);
            $auction->update(['status' => 'completed']);

            $state = AuctionState::where('auction_id', $auctionId)->first();
            if ($state) {
                $state->update([
                    'current_auction_player_id' => null,
                    'current_highest_bid' => null,
                    'current_highest_team_id' => null,
                    'timer_end_at' => null,
                ]);
            }

            event(new AuctionEnded($auctionId));

            return ['success' => true, 'message' => 'Auction ended.'];
        });
    }
}
