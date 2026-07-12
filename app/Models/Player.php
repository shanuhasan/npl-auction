<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Player extends Model
{
    protected $fillable = [
        'name', 'photo', 'role', 'country', 'city', 'batting_style', 'bowling_style',
        'base_price', 'category', 'stats', 'status', 'current_team_id',
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
        'stats' => 'array',
    ];

    public function currentTeam()
    {
        return $this->belongsTo(Team::class, 'current_team_id');
    }

    public function auctionPlayers()
    {
        return $this->hasMany(AuctionPlayer::class);
    }
}
