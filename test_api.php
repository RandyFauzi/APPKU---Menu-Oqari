<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = \App\Models\User::first();
Auth::login($user);

$request = Illuminate\Http\Request::create('/admin/api/crew', 'POST', [
    'name' => 'Koso',
    'email' => 'koso'.rand(1,100).'@gmail.com',
    'password' => 'password123',
    'role' => 'barista'
]);
$request->headers->set('Accept', 'application/json');

$response = app()->handle($request);
echo "Status: " . $response->getStatusCode() . "\n";
echo "Content: " . $response->getContent() . "\n";
