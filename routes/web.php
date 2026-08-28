<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\DashboardController;

Route::get('/', function () {
    return redirect()->route('admin.dashboard');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::post('/admin/api/orders/{order}/status', [DashboardController::class, 'updateOrderStatus']);
    Route::post('/admin/api/menu/{menu}/toggle', [DashboardController::class, 'toggleMenuStatus']);
    Route::post('/admin/api/menu', [DashboardController::class, 'saveMenu']);
    Route::post('/admin/api/settings', [DashboardController::class, 'saveSettings']);
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

use App\Http\Controllers\ShopController;
Route::get('/{slug}', [ShopController::class, 'show'])->name('shop.menu');
Route::get('/{slug}/cart', [ShopController::class, 'cart'])->name('shop.cart');
Route::get('/{slug}/tracking', [ShopController::class, 'tracking'])->name('shop.tracking');
Route::post('/{slug}/order', [ShopController::class, 'submitOrder'])->name('shop.order.submit');
