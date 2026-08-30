<?php

use App\Http\Controllers\Api\GoBizWebhookController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/webhooks/gobiz/orders', [GoBizWebhookController::class, 'handle']);
