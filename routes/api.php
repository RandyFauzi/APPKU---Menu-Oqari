<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// OQARI API BOUNDARIES (v1)
Route::prefix('v1')->middleware(['auth:sanctum'])->group(function () {
    
    // 1. Tablet POS Endpoints
    Route::prefix('pos')->group(function () {
        // e.g., Route::get('/menu', [PosController::class, 'menu'])->can('access-pos');
        // e.g., Route::post('/orders', [PosController::class, 'store'])->can('manage-orders');
    });

    // 2. Kitchen Display System (KDS) Endpoints
    Route::prefix('kitchen')->group(function () {
        // e.g., Route::get('/tickets', [KitchenController::class, 'index'])->can('view-kitchen');
        // e.g., Route::patch('/tickets/{id}/status', [KitchenController::class, 'updateStatus'])->can('update-kitchen-status');
    });

    // 3. Mobile Crew / Staff Endpoints
    Route::prefix('crew')->group(function () {
        // e.g., Route::get('/schedule', [CrewController::class, 'schedule'])->can('view-own-schedule');
    });

    // 4. External Integrations (GoFood, GrabFood)
    Route::prefix('integrations')->group(function () {
        // e.g., Route::post('/webhooks/gofood/orders', [IntegrationController::class, 'gofood']);
    });
});