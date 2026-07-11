<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuctionPlayer extends Model
{
    protected $fillable = [
        'auction_id', 'player_id', 'order_no', 'status', 'final_price', 'sold_to_team_id',
    ];

    protected $casts = [
        'final_price' => 'decimal:2',
    ];

    public function auction()
    {
        return $this->belongsTo(Auction::class);
    }

    public function player()
    {
        return $this->belongsTo(Player::class);
    }

    public function soldToTeam()
    {
        return $this->belongsTo(Team::class, 'sold_to_team_id');
    }

    public function bids()
    {
        return $this->hasMany(Bid::class);
    }
}
