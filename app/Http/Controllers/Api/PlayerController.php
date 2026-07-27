<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Player;

class PlayerController extends Controller
{
    /**
     * Get list of players.
     */
    public function index(Request $request)
    {
        $query = Player::with('currentTeam')
            ->where('is_approved', true);

        // Optional search by name
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Optional filter by role (Batsman, Bowler, All-rounder, Wicket Keeper)
        if ($request->has('role')) {
            $query->where('role', $request->role);
        }

        // Optional filter by team
        if ($request->has('team_id')) {
            $query->where('current_team_id', $request->team_id);
        }

        $perPage = $request->input('per_page', 15);
        
        $players = $query->orderBy('name', 'asc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $players
        ]);
    }

    /**
     * Get details of a single player.
     */
    public function show(Player $player)
    {
        // Ensure player is approved, or maybe allow viewing if explicitly requested
        if (!$player->is_approved) {
            return response()->json([
                'success' => false,
                'message' => 'Player not found or not approved.',
            ], 404);
        }

        $player->load(['currentTeam', 'auctionPlayers.auction']);

        return response()->json([
            'success' => true,
            'data' => $player
        ]);
    }
}
