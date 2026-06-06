<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = App\Models\User::where('email', 'admin@vendorbridge.com')->first();
$token = $user->createToken('test')->accessToken;

$client = new \GuzzleHttp\Client();
$res = $client->request('GET', 'http://127.0.0.1:8000/api/v1/vendors', [
    'headers' => [
        'Accept' => 'application/json'
    ],
    'http_errors' => false
]);
echo "Status: " . $res->getStatusCode() . "\n";
echo "Body: " . $res->getBody() . "\n";
