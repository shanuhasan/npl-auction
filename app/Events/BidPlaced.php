<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BidPlaced implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $auction_id;
    public $team_id;
    public $team_name;
    public $bid_amount;

    /**
     * Create a new event instance.
     */
    public function __construct($auction_id, $team_id, $team_name, $bid_amount)
    {
        $this->auction_id = $auction_id;
        $this->team_id = $team_id;
        $this->team_name = $team_name;
        $this->bid_amount = $bid_amount;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('auction.' . $this->auction_id),
        ];
    }
}
