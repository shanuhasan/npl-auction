<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Team;
use Barryvdh\DomPDF\Facade\Pdf;

class PdfController extends Controller
{
    public function teamSquad(Team $team)
    {
        $auctionId = request('auction_id');
        $totalSpent = 0;

        if ($auctionId) {
            $auctionPlayers = \App\Models\AuctionPlayer::with('player')
                ->where('sold_to_team_id', $team->id)
                ->where('auction_id', $auctionId)
                ->where('status', 'sold')
                ->get();
            
            $players = collect();
            foreach ($auctionPlayers as $ap) {
                $ap->player->setRelation('auctionPlayers', collect([$ap]));
                $players->push($ap->player);
            }
            $totalSpent = $auctionPlayers->sum('final_price');
        } else {
            $team->load(['players.auctionPlayers' => function($query) use ($team) {
                $query->where('sold_to_team_id', $team->id)->where('status', 'sold')->latest();
            }]);
            $players = $team->players;
            
            foreach ($players as $player) {
                $bought = $player->auctionPlayers->first();
                if ($bought) {
                    $totalSpent += $bought->final_price;
                }
            }
        }

        $remainingBudget = $team->budget - $totalSpent;
        $playersByRole = $players->groupBy('role');

        $pdf = Pdf::loadView('pdf.team-squad', [
            'team' => $team,
            'playersByRole' => $playersByRole,
            'totalSpent' => $totalSpent,
            'remainingBudget' => $remainingBudget
        ]);

        return $pdf->download($team->short_name . '_Squad.pdf');
    }

    public function playersList()
    {
        // Get approved players sorted by category then name, or just latest
        $players = \App\Models\Player::where('is_approved', true)->orderBy('name')->get();
        
        $pdf = Pdf::loadView('pdf.players-list', [
            'players' => $players
        ]);

        return $pdf->download('Players_List.pdf');
    }
}
