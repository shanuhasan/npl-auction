<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Auction extends Model
{
    protected $fillable = [
        'title', 'auction_date', 'status',
    ];

    protected $casts = [
        'auction_date' => 'datetime',
    ];

    public function auctionPlayers()
    {
        return $this->hasMany(AuctionPlayer::class);
    }

    public function state()
    {
        return $this->hasOne(AuctionState::class);
    }
}
