<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$request = \Illuminate\Http\Request::create('/api/login', 'POST', [], [], [], [
    'CONTENT_TYPE' => 'application/json',
    'HTTP_ACCEPT' => 'application/json'
], json_encode([
    'username' => 'Devan',
    'password' => '24681000'
]));

$response = $kernel->handle($request);
echo "Status Code: " . $response->getStatusCode() . "\n";
echo "Body: " . $response->getContent() . "\n";
