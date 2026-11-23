<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * A scheduled match between two teams of an auction/season.
 * (Named "Fixture" because "Match" is a reserved word in PHP 8.)
 */
class Fixture extends Model
{
    protected $fillable = [
        'auction_id', 'match_no', 'stage', 'team_a_id', 'team_a_label', 'team_b_id', 'team_b_label', 'match_date', 'venue',
        'status', 'team_a_score', 'team_b_score', 'winner_team_id', 'result',
    ];

    protected $casts = [
        'match_date' => 'datetime',
    ];

    public function auction()
    {
        return $this->belongsTo(Auction::class);
    }

    public function teamA()
    {
        return $this->belongsTo(Team::class, 'team_a_id');
    }

    public function teamB()
    {
        return $this->belongsTo(Team::class, 'team_b_id');
    }

    public function winner()
    {
        return $this->belongsTo(Team::class, 'winner_team_id');
    }

    public function scopeForTeam($query, $teamId)
    {
        return $query->where(fn ($q) => $q->where('team_a_id', $teamId)->orWhere('team_b_id', $teamId));
    }

    /**
     * Text shown for a side whose team is not decided yet ('a' or 'b').
     */
    public function placeholder(string $side): string
    {
        return $this->{"team_{$side}_label"} ?: 'TBC';
    }

    public function isPlayoff(): bool
    {
        return $this->stage !== 'League';
    }

    public function opponentOf($teamId): ?Team
    {
        return $this->team_a_id == $teamId ? $this->teamB : $this->teamA;
    }
}
