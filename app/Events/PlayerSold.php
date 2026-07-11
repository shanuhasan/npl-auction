<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PlayerSold implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $auction_id;
    public $player_id;
    public $team_id;
    public $final_price;

    /**
     * Create a new event instance.
     */
    public function __construct($auction_id, $player_id, $team_id, $final_price)
    {
        $this->auction_id = $auction_id;
        $this->player_id = $player_id;
        $this->team_id = $team_id;
        $this->final_price = $final_price;
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
