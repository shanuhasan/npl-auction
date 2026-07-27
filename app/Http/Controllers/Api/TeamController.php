<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Team;

class TeamController extends Controller
{
    /**
     * Get list of teams.
     */
    public function index(Request $request)
    {
        $query = Team::where('is_approved', true);

        // Optional search by name
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('short_name', 'like', '%' . $request->search . '%');
        }

        $perPage = $request->input('per_page', 15);
        
        // We will include the owner count and players count optionally, or just basic data
        $teams = $query->withCount('players')
            ->orderBy('name', 'asc')
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $teams
        ]);
    }

    /**
     * Get details of a single team.
     */
    public function show(Team $team)
    {
        // Ensure team is approved
        if (!$team->is_approved) {
            return response()->json([
                'success' => false,
                'message' => 'Team not found or not approved.',
            ], 404);
        }

        // Load the owner and the current players
        $team->load(['owner', 'players']);

        return response()->json([
            'success' => true,
            'data' => $team
        ]);
    }
}
