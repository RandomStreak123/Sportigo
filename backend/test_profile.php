<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    $user = \App\Models\User::with(['joinedMatches', 'tournaments'])->findOrFail(1);
    $hostedMatches = \App\Models\SportsMatch::where('user_id', $user->id)->get();
    $allMatches = $hostedMatches->merge($user->joinedMatches)->unique('id')->values();
    echo json_encode(['success' => true, 'matches_count' => $allMatches->count()]);
} catch (\Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
