<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\AuctionState;
use App\Services\AuctionService;

class ProcessAuctionTimer implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $stateId;
    public $auctionPlayerId;
    public $timerEndAtStr;

    public function __construct($stateId, $auctionPlayerId, $timerEndAtStr)
    {
        $this->stateId = $stateId;
        $this->auctionPlayerId = $auctionPlayerId;
        $this->timerEndAtStr = $timerEndAtStr;
    }

    public function handle(AuctionService $service): void
    {
        $state = AuctionState::find($this->stateId);
        if (!$state || $state->current_auction_player_id != $this->auctionPlayerId) {
            return;
        }

        if ($state->timer_end_at->toISOString() !== $this->timerEndAtStr) {
            // A new bid was placed, timer was extended. Abort this job.
            return;
        }

        if (now()->greaterThanOrEqualTo($state->timer_end_at)) {
            // Timer has expired, auto-sell if there are bids and auto_sold is enabled
            if ($state->auto_sold && $state->current_highest_bid > 0 && $state->current_highest_team_id) {
                $service->markSold($this->auctionPlayerId);
            }
        }
    }
}
