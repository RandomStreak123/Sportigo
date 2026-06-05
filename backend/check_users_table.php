<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$columns = DB::select('SHOW COLUMNS FROM users');
foreach ($columns as $c) {
    echo "Column: {$c->Field} | Type: {$c->Type} | Null: {$c->Null} | Key: {$c->Key}\n";
}
