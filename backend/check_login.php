<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = \App\Models\User::where('username', 'Ajith')->first();
if ($user) {
    echo "User found: " . $user->username . "\n";
    echo "Hash check for '24681000': " . (\Illuminate\Support\Facades\Hash::check('24681000', $user->password) ? 'TRUE' : 'FALSE') . "\n";
} else {
    echo "User Ajith NOT found\n";
}
