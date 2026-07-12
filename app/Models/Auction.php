<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Str;

class Auction extends Model
{
    protected $fillable = [
        'guid', 'title', 'auction_date', 'status',
    ];

    protected static function booted()
    {
        static::creating(function ($auction) {
            if (empty($auction->guid)) {
                $auction->guid = Str::uuid()->toString();
            }
        });
    }

    protected $casts = [
        'auction_date' => 'datetime',
    ];

    public function auctionPlayers()
    {
        return $this->hasMany(AuctionPlayer::class);
    }

    public function players()
    {
        return $this->belongsToMany(Player::class, 'auction_players')
                    ->withPivot('status', 'sold_price', 'buyer_team_id', 'order_no');
    }

    public function state()
    {
        return $this->hasOne(AuctionState::class);
    }

    public function teams()
    {
        return $this->belongsToMany(Team::class);
    }

    public function getRouteKeyName()
    {
        return 'guid';
    }
}
