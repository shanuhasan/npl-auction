<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'player_id',
        'amount',
        'gateway',
        'transaction_id',
        'status',
    ];

    public function player()
    {
        return $this->belongsTo(Player::class);
    }
}
