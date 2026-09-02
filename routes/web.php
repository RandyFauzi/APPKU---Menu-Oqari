<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\GoBizIntegrationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ShopController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check() && auth()->user()->role === 'superadmin') {
        return redirect()->route('superadmin.dashboard');
    }
    return redirect()->route('admin.dashboard');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/admin/api/orders/live', [DashboardController::class, 'getLiveOrders']);
    Route::post('/admin/api/orders/{order}/status', [DashboardController::class, 'updateOrderStatus']);
    Route::get('/admin/api/orders/{order}/print', [DashboardController::class, 'printOrder']);
    Route::post('/admin/api/menu/{menu}/toggle', [DashboardController::class, 'toggleMenuStatus']);
    Route::post('/admin/api/menu/bulk', [DashboardController::class, 'saveMenuBulk']);
    Route::post('/admin/api/menu', [DashboardController::class, 'saveMenu']);
    Route::delete('/admin/api/menu/{id}', [DashboardController::class, 'deleteMenu']);
    Route::post('/admin/api/settings', [DashboardController::class, 'saveSettings']);
    Route::post('/admin/api/profile', [DashboardController::class, 'updateProfile']);
    Route::post('/admin/api/crew', [DashboardController::class, 'saveCrew']);
    Route::delete('/admin/api/crew/{id}', [DashboardController::class, 'deleteCrew']);
    Route::put('/admin/api/crew/{id}', [DashboardController::class, 'updateCrew']);

    // Shifts & Logs
    Route::get('/admin/api/logs', [DashboardController::class, 'getLogs']);
    Route::get('/admin/api/shifts', [DashboardController::class, 'getShifts']);
    Route::post('/admin/api/shifts', [DashboardController::class, 'saveShift']);
    Route::delete('/admin/api/shifts/{id}', [DashboardController::class, 'deleteShift']);

    Route::post('/admin/api/table', [DashboardController::class, 'saveTable']);
    Route::put('/admin/api/table', [DashboardController::class, 'updateTableQR']);

    // GoBiz Integration Routes
    Route::get('/admin/integrations/gobiz/connect', [GoBizIntegrationController::class, 'connect'])->name('admin.integrations.gobiz.connect');
    Route::get('/admin/integrations/gobiz/callback', [GoBizIntegrationController::class, 'callback'])->name('admin.integrations.gobiz.callback');
    Route::post('/admin/integrations/gobiz/sync', [GoBizIntegrationController::class, 'syncCatalog'])->name('admin.integrations.gobiz.sync');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

Route::middleware(['auth', 'superadmin'])->prefix('superadmin')->name('superadmin.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\SuperAdminController::class, 'index'])->name('dashboard');
    Route::delete('/users/{id}', [\App\Http\Controllers\SuperAdminController::class, 'deleteUser'])->name('users.delete');
    Route::delete('/shops/{id}', [\App\Http\Controllers\SuperAdminController::class, 'deleteShop'])->name('shops.delete');
});


Route::get('/{slug}', [ShopController::class, 'show'])->name('shop.menu');
Route::get('/{slug}/cart', [ShopController::class, 'cart'])->name('shop.cart');
Route::get('/{slug}/tracking', [ShopController::class, 'tracking'])->name('shop.tracking');
Route::post('/{slug}/order', [ShopController::class, 'submitOrder'])->name('shop.order.submit');
