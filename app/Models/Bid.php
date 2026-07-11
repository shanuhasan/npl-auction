<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bid extends Model
{
    protected $fillable = [
        'auction_player_id', 'team_id', 'bid_amount',
    ];

    protected $casts = [
        'bid_amount' => 'decimal:2',
    ];

    public function auctionPlayer()
    {
        return $this->belongsTo(AuctionPlayer::class);
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }
}
