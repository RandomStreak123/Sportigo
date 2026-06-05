<?php

namespace App\Http\Controllers;

use App\Models\Tournament;
use Illuminate\Http\Request;

class TournamentController extends Controller
{
    public function index()
    {
        return Tournament::all();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'fee' => 'required|numeric',
            'max_teams' => 'required|integer|min:2',
            'prize_pool' => 'nullable|string',
            'format' => 'nullable|string',
            'organizer' => 'nullable|string',
            'banner_url' => 'nullable|string',
        ]);

        $validated['registered_teams'] = 0;
        $validated['status'] = 'open';

        $tournament = Tournament::create($validated);

        return response()->json([
            'message' => 'Tournament created successfully!',
            'tournament' => $tournament
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $tournament = Tournament::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'fee' => 'required|numeric',
            'max_teams' => 'required|integer|min:2',
            'prize_pool' => 'nullable|string',
            'format' => 'nullable|string',
            'organizer' => 'nullable|string',
            'banner_url' => 'nullable|string',
        ]);

        $tournament->update($validated);

        return response()->json([
            'message' => 'Tournament details updated successfully!',
            'tournament' => $tournament
        ]);
    }

    public function register(Request $request, $id)
    {
        $tournament = Tournament::findOrFail($id);
        $user = auth()->user();

        if ($tournament->participants()->where('user_id', $user->id)->exists()) {
            return response()->json(['message' => 'You are already registered for this tournament.'], 400);
        }

        if ($tournament->registered_teams >= $tournament->max_teams) {
            return response()->json([
                'message' => 'Tournament is already full.'
            ], 422);
        }

        // We can accept an optional team_name if it's a team sport
        $teamName = $request->input('team_name', $user->name);

        $tournament->participants()->attach($user->id, ['team_name' => $teamName]);
        $tournament->increment('registered_teams');

        return response()->json([
            'message' => 'Successfully registered for ' . $tournament->title . '!',
            'tournament' => $tournament
        ]);
    }

    public function reportScore(Request $request, $id)
    {
        $tournament = Tournament::findOrFail($id);
        
        $request->validate([
            't1_name' => 'required|string',
            't2_name' => 'required|string',
            't1_score' => 'required|integer',
            't2_score' => 'required|integer',
            'round' => 'nullable|integer'
        ]);

        $match = $tournament->matches()->create([
            't1_name' => $request->t1_name,
            't2_name' => $request->t2_name,
            't1_score' => $request->t1_score,
            't2_score' => $request->t2_score,
            'round' => $request->input('round', 1),
            'status' => 'completed'
        ]);

        return response()->json(['message' => 'Score reported successfully!', 'match' => $match]);
    }

    public function standings($id)
    {
        $tournament = Tournament::findOrFail($id);
        $matches = $tournament->matches()->where('status', 'completed')->get();
        
        $standings = [];

        foreach ($matches as $match) {
            // Initialize teams if not exists
            if (!isset($standings[$match->t1_name])) {
                $standings[$match->t1_name] = ['name' => $match->t1_name, 'played' => 0, 'won' => 0, 'lost' => 0, 'draw' => 0, 'points' => 0];
            }
            if (!isset($standings[$match->t2_name])) {
                $standings[$match->t2_name] = ['name' => $match->t2_name, 'played' => 0, 'won' => 0, 'lost' => 0, 'draw' => 0, 'points' => 0];
            }

            $standings[$match->t1_name]['played']++;
            $standings[$match->t2_name]['played']++;

            if ($match->t1_score > $match->t2_score) {
                $standings[$match->t1_name]['won']++;
                $standings[$match->t1_name]['points'] += 3;
                $standings[$match->t2_name]['lost']++;
            } elseif ($match->t1_score < $match->t2_score) {
                $standings[$match->t2_name]['won']++;
                $standings[$match->t2_name]['points'] += 3;
                $standings[$match->t1_name]['lost']++;
            } else {
                $standings[$match->t1_name]['draw']++;
                $standings[$match->t2_name]['draw']++;
                $standings[$match->t1_name]['points'] += 1;
                $standings[$match->t2_name]['points'] += 1;
            }
        }

        // Sort by points descending
        usort($standings, function($a, $b) {
            return $b['points'] <=> $a['points'];
        });

        // Add rank
        foreach ($standings as $index => &$team) {
            $team['rank'] = $index + 1;
        }

        return response()->json($standings);
    }
}
