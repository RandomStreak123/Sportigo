<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StorageSyncController extends Controller
{
    public function get(Request $request)
    {
        $keys = $request->input('keys', []);
        if (empty($keys) && $request->has('key')) {
            $keys = [$request->input('key')];
        }

        if (empty($keys)) {
            return response()->json(\App\Models\StorageSync::all()->pluck('value', 'key'));
        }

        $items = \App\Models\StorageSync::whereIn('key', $keys)->get()->pluck('value', 'key');
        return response()->json($items);
    }

    public function set(Request $request)
    {
        $request->validate([
            'key' => 'required|string',
            'value' => 'nullable|string',
        ]);

        $item = \App\Models\StorageSync::updateOrCreate(
            ['key' => $request->key],
            ['value' => $request->value]
        );

        return response()->json([
            'message' => 'Storage synchronized successfully',
            'key' => $item->key,
        ]);
    }
}
