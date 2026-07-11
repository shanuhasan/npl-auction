<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuctionState extends Model
{
    protected $fillable = [
        'auction_id', 'current_auction_player_id', 'current_highest_bid',
        'current_highest_team_id', 'bid_increment_rule', 'timer_seconds', 'timer_end_at', 'auto_sold', 'manual_bid_increment',
    ];

    protected $casts = [
        'current_highest_bid' => 'decimal:2',
        'bid_increment_rule' => 'array',
        'timer_seconds' => 'integer',
        'timer_end_at' => 'datetime',
        'created_at' => 'datetime',
        'auto_sold' => 'boolean',
    ];

    public function auction()
    {
        return $this->belongsTo(Auction::class);
    }

    public function currentAuctionPlayer()
    {
        return $this->belongsTo(AuctionPlayer::class, 'current_auction_player_id');
    }

    public function currentHighestTeam()
    {
        return $this->belongsTo(Team::class, 'current_highest_team_id');
    }
}
