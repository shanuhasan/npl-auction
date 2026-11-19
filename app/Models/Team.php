<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Team extends Model
{
    protected $fillable = [
        'guid', 'name', 'slug', 'short_name', 'logo', 'primary_color', 'budget', 'remaining_budget', 'owner_id', 'is_approved'
    ];

    protected $casts = [
        'budget' => 'decimal:2',
        'remaining_budget' => 'decimal:2',
    ];

    protected static function booted()
    {
        static::creating(function (Team $team) {
            if (empty($team->guid)) {
                $team->guid = (string) Str::uuid();
            }
        });

        // Generate the slug on create, and again whenever the name changes
        static::saving(function (Team $team) {
            if (empty($team->slug) || $team->isDirty('name')) {
                $team->slug = static::uniqueSlug($team->name, $team->id);
            }
        });
    }

    /**
     * "Royal Strikers" -> "royal-strikers" (or "royal-strikers-2" if already taken).
     */
    public static function uniqueSlug(?string $name, $ignoreId = null): string
    {
        $base = Str::slug((string) $name) ?: 'team';
        $slug = $base;
        $i = 2;

        while (static::where('slug', $slug)->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = $base . '-' . $i++;
        }

        return $slug;
    }

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

    public function fixtures()
    {
        return Fixture::forTeam($this->id);
    }

    public function auctions()
    {
        return $this->belongsToMany(Auction::class);
    }
}
