<?php

use App\Http\Controllers\Admin\CashRegisterController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PosController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ShiftController;
use App\Http\Controllers\Admin\KitchenController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\SuperAdmin\UserController;
use App\Http\Controllers\SuperAdminController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check() && auth()->user()->role === 'superadmin') {
        return redirect()->route('superadmin.dashboard');
    }

    return redirect()->route('admin.dashboard');
});

Route::middleware(['auth'])->group(function () {

    // Dashboard (accessible to all authenticated users)
    Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

    // POS & Orders
    // POS Terminal + APIs
    Route::middleware('can:access-pos')->group(function () {
        Route::get('/admin/pos', [PosController::class, 'index'])->name('admin.pos.index');
        Route::post('/admin/pos/orders', [PosController::class, 'submitOrder'])->name('admin.pos.orders.submit');
        Route::post('/admin/pos/orders/hold', [PosController::class, 'holdOrder'])->name('admin.pos.orders.hold');
        Route::get('/admin/pos/orders/held', [PosController::class, 'heldOrders'])->name('admin.pos.orders.held');
        Route::post('/admin/pos/orders/{order}/recall', [PosController::class, 'recallOrder'])->name('admin.pos.orders.recall');
        Route::get('/admin/pos/products', [PosController::class, 'products'])->name('admin.pos.products');
        // Legacy order status updates
        Route::get('/admin/api/orders/live', [DashboardController::class, 'getLiveOrders']);
        Route::post('/admin/api/orders/{order}/status', [DashboardController::class, 'updateOrderStatus']);
        Route::get('/admin/api/orders/{order}/print', [DashboardController::class, 'printOrder']);
    });

    // Cash Register / Shift Session
    Route::post('/shift/open', [CashRegisterController::class, 'openShift'])->name('shift.open');
    Route::post('/shift/close', [CashRegisterController::class, 'closeShift'])->name('shift.close');

    // Menu Management
    Route::middleware('can:manage-menu')->group(function () {
        Route::post('/admin/api/menu/{menu}/toggle', [DashboardController::class, 'toggleMenuStatus']);
        Route::post('/admin/api/menu/bulk', [DashboardController::class, 'saveMenuBulk']);
        Route::post('/admin/api/menu', [DashboardController::class, 'saveMenu']);
        Route::delete('/admin/api/menu/{id}', [DashboardController::class, 'deleteMenu']);
        Route::post('/admin/api/category', [DashboardController::class, 'saveCategory']);
    });

    // Settings
    Route::middleware('can:manage-settings')->group(function () {
        Route::post('/admin/api/settings', [DashboardController::class, 'saveSettings']);
        Route::post('/admin/api/profile', [DashboardController::class, 'updateProfile']);
        // Table & QR Management
        Route::post('/admin/api/table', [DashboardController::class, 'saveTable']);
        Route::put('/admin/api/table', [DashboardController::class, 'updateTableQR']);
    });

    // Crew Management (Owner/Manager)
    Route::middleware('can:manage-crew')->group(function () {
        Route::post('/admin/api/crew', [DashboardController::class, 'saveCrew']);
        Route::delete('/admin/api/crew/{id}', [DashboardController::class, 'deleteCrew']);
        Route::put('/admin/api/crew/{id}', [DashboardController::class, 'updateCrew']);
        Route::get('/admin/shifts', [ShiftController::class, 'index'])->name('admin.shifts.index');
        Route::post('/admin/shifts', [ShiftController::class, 'store'])->name('admin.shifts.store');
    });

    // My Schedule (Crew/Barista — personal view only)
    Route::middleware('can:view-own-schedule')->group(function () {
        Route::get('/admin/my-schedule', [ShiftController::class, 'mySchedule'])->name('admin.my-schedule');
        Route::get('/admin/crew-home', [App\Http\Controllers\Admin\CrewController::class, 'home'])->name('admin.crew.home');
    });

    // Kitchen Display (Barista/Kitchen)
    Route::middleware('can:view-kitchen')->group(function () {
        Route::get('/admin/kitchen', [KitchenController::class, 'index'])->name('admin.kitchen.index');
        Route::get('/admin/kitchen/orders', fn() => response()->json(
            \App\Models\Order::where('shop_id', auth()->user()->shop_id)
                ->whereIn('order_status', ['CONFIRMED','PREPARING','READY'])
                ->with('items')
                ->latest()
                ->limit(150)
                ->get()
                ->map(fn($o) => [
                    'id' => $o->id, 'status' => $o->order_status,
                    'time' => $o->created_at->format('H:i'),
                    'items' => $o->items->map(fn($i) => [
                        'id' => $i->id, 'name' => $i->product_name,
                        'qty' => $i->quantity, 'variant' => $i->variant_name,
                        'modifiers' => $i->modifiers ? json_decode($i->modifiers, true) : [],
                        'notes' => $i->notes,
                    ])
                ])
        ))->name('admin.kitchen.orders');
        Route::post('/admin/kitchen/orders/{order}/status', [KitchenController::class, 'updateStatus'])->name('admin.kitchen.orders.status');
    });

    // Reporting & Analytics
    Route::middleware('can:view-reports')->group(function () {
        Route::get('/admin/reports', [ReportController::class, 'index'])->name('admin.reports.index');
        Route::get('/admin/api/logs', [DashboardController::class, 'getLogs']);
    });

    // Profile (all authenticated users)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

// SuperAdmin Panel
Route::middleware(['auth', 'superadmin'])->prefix('superadmin')->name('superadmin.')->group(function () {
    Route::get('/dashboard', [SuperAdminController::class, 'index'])->name('dashboard');
    Route::resource('shops', App\Http\Controllers\SuperAdmin\ShopController::class);
    Route::post('/shops/{shop}/suspend', [App\Http\Controllers\SuperAdmin\ShopController::class, 'suspend'])->name('shops.suspend');
    Route::post('/shops/{shop}/activate', [App\Http\Controllers\SuperAdmin\ShopController::class, 'activate'])->name('shops.activate');
    Route::resource('users', UserController::class);
});

// Public Customer Menu
Route::get('/{slug}', [ShopController::class, 'show'])->name('shop.menu');
Route::get('/{slug}/cart', [ShopController::class, 'cart'])->name('shop.cart');
Route::get('/{slug}/tracking', [ShopController::class, 'tracking'])->name('shop.tracking');
Route::post('/{slug}/order', [ShopController::class, 'submitOrder'])->name('shop.order.submit');
