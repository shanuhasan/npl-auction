<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Player;
use App\Models\Team;

#[Layout('layouts.app')]
class Analytics extends Component
{
    public function render()
    {
        // Top Player
        $topPlayer = Player::where('players.status', 'sold')
            ->join('auction_players', 'players.id', '=', 'auction_players.player_id')
            ->select('players.*', 'auction_players.final_price')
            ->where('auction_players.status', 'sold')
            ->orderByDesc('auction_players.final_price')
            ->first();

        // Top Spender
        $teams = Team::all();
        $topSpender = $teams->sortByDesc(function($t) { return $t->budget - $t->remaining_budget; })->first();

        // Category Data
        $categories = ['marquee', 'set-a', 'set-b', 'set-c'];
        $categoryData = [];

        foreach($categories as $cat) {
            $sum = Player::where('players.status', 'sold')->where('players.category', $cat)
                ->join('auction_players', 'players.id', '=', 'auction_players.player_id')
                ->where('auction_players.status', 'sold')
                ->sum('auction_players.final_price');
            
            $categoryData[] = $sum;
        }

        return view('livewire.admin.analytics', [
            'topPlayer' => $topPlayer,
            'topSpender' => $topSpender,
            'categoryLabels' => ['Marquee', 'Set-A', 'Set-B', 'Set-C'],
            'categoryData' => $categoryData
        ]);
    }
}
