<?php

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make("Illuminate\Contracts\Console\Kernel")->bootstrap();
$request = Request::create('/goodwill', 'GET');
$response = $app->make(Kernel::class)->handle($request);
echo $response->getContent();
