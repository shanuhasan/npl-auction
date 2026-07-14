<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    protected $fillable = [
        'name', 'short_name', 'logo', 'primary_color', 'budget', 'remaining_budget', 'owner_id', 'is_approved'
    ];

    protected $casts = [
        'budget' => 'decimal:2',
        'remaining_budget' => 'decimal:2',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function players()
    {
        return $this->hasMany(Player::class, 'current_team_id');
    }


    public function auctionPlayers()
    {
        return $this->hasMany(AuctionPlayer::class, 'sold_to_team_id');
    }

    public function bids()
    {
        return $this->hasMany(Bid::class);
    }

    public function auctions()
    {
        return $this->belongsToMany(Auction::class);
    }
}
