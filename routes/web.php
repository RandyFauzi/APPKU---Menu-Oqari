<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CashRegisterController;

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
    // POS & Umum (Semua bisa masuk kecuali superadmin yang diarahkan ke panel khusus)
    Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    
    // POS / Orders
    Route::middleware('can:access-pos')->group(function () {
        Route::get('/admin/api/orders/live', [DashboardController::class, 'getLiveOrders']);
        Route::post('/admin/api/orders/{order}/status', [DashboardController::class, 'updateOrderStatus']);
        Route::get('/admin/api/orders/{order}/print', [DashboardController::class, 'printOrder']);
        Route::post('/admin/api/shifts', [DashboardController::class, 'saveShift']);
    });



    // Reporting & Analytics
    Route::middleware('can:view-reports')->group(function () {
        Route::get('/admin/reports', [App\Http\Controllers\Admin\ReportController::class, 'index'])->name('admin.reports.index');
    });
    // POS Terminal
    Route::middleware('can:access-pos')->group(function () {
        Route::get('/admin/pos', [App\Http\Controllers\Admin\PosController::class, 'index'])->name('admin.pos.index');
        // Nanti kita tambah route /admin/pos/open-register, dll.
    });
    // Menu Management
    Route::middleware('can:manage-menu')->group(function () {
        Route::post('/admin/api/menu/{menu}/toggle', [DashboardController::class, 'toggleMenuStatus']);
        Route::post('/admin/api/menu/bulk', [DashboardController::class, 'saveMenuBulk']);
        Route::post('/admin/api/menu', [DashboardController::class, 'saveMenu']);
        Route::delete('/admin/api/menu/{id}', [DashboardController::class, 'deleteMenu']);
    });

    // Settings
    Route::middleware('can:manage-settings')->group(function () {
        Route::post('/admin/api/settings', [DashboardController::class, 'saveSettings']);
        Route::post('/admin/api/profile', [DashboardController::class, 'updateProfile']);
    });

    // Crew Management
    Route::middleware('can:manage-crew')->group(function () {
        Route::post('/admin/api/crew', [DashboardController::class, 'saveCrew']);
        Route::delete('/admin/api/crew/{id}', [DashboardController::class, 'deleteCrew']);
        Route::put('/admin/api/crew/{id}', [DashboardController::class, 'updateCrew']);
    });

    // Reports / Logs / Finance
    Route::middleware('can:view-reports')->group(function () {
        Route::get('/admin/api/logs', [DashboardController::class, 'getLogs']);
        Route::get('/admin/api/shifts', [DashboardController::class, 'getShifts']);
        Route::delete('/admin/api/shifts/{id}', [DashboardController::class, 'deleteShift']);
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';


Route::middleware(['auth', 'superadmin'])->prefix('superadmin')->name('superadmin.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\SuperAdminController::class, 'index'])->name('dashboard');

    Route::resource('shops', \App\Http\Controllers\SuperAdmin\ShopController::class);
    Route::post('/shops/{shop}/suspend', [\App\Http\Controllers\SuperAdmin\ShopController::class, 'suspend'])->name('shops.suspend');
    Route::post('/shops/{shop}/activate', [\App\Http\Controllers\SuperAdmin\ShopController::class, 'activate'])->name('shops.activate');

    Route::resource('users', \App\Http\Controllers\SuperAdmin\UserController::class);
});


Route::get('/{slug}', [ShopController::class, 'show'])->name('shop.menu');
Route::get('/{slug}/cart', [ShopController::class, 'cart'])->name('shop.cart');
Route::get('/{slug}/tracking', [ShopController::class, 'tracking'])->name('shop.tracking');
Route::post('/{slug}/order', [ShopController::class, 'submitOrder'])->name('shop.order.submit');

