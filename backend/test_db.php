<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $user = \App\Models\User::create([
        'name' => 'test2',
        'username' => 'test2',
        'email' => 'test2@playconnect.com',
        'password' => \Illuminate\Support\Facades\Hash::make('password')
    ]);
    
    $token = $user->createToken('auth_token')->plainTextToken;
    echo "SUCCESS: User created and token generated. Token: " . $token . "\n";
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
