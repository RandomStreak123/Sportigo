<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function index()
    {
        return response()->json(Activity::latest()->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_name' => 'required|string',
            'text' => 'required|string',
            'icon' => 'required|string',
            'type' => 'required|string',
            'avatar' => 'nullable|string',
            'time' => 'nullable|string'
        ]);

        $activity = Activity::create($validated);
        return response()->json($activity, 201);
    }
    
    public function update(Request $request, Activity $activity)
    {
        $validated = $request->validate([
            'text' => 'required|string'
        ]);
        
        $activity->update($validated);
        return response()->json($activity);
    }
}
