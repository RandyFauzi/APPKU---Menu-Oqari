<?php

use App\Http\Controllers\Admin\Api\AnalyticsApiController;
use App\Http\Controllers\Admin\Api\DashboardApiController;
use App\Http\Controllers\Admin\Api\OrderApiController;
use App\Http\Controllers\Admin\Api\ProductApiController;
use App\Http\Controllers\Admin\AttendanceController;
use App\Http\Controllers\Admin\CafeUserController;
use App\Http\Controllers\Admin\CashRegisterController;
use App\Http\Controllers\Admin\CrewController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\HppCalculatorController;
use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\Admin\KitchenController;
use App\Http\Controllers\Admin\OnboardingController;
use App\Http\Controllers\Admin\PosController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ShiftController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\SuperAdmin\UserController;
use App\Http\Controllers\SuperAdminController;
use App\Models\Order;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $dashboardUrl = null;
    if (auth()->check()) {
        $dashboardUrl = route(auth()->user()->homeRoute());
    }

    return view('landing', compact('dashboardUrl'));
})->name('landing');

Route::get('/harga', function () {
    return view('harga');
})->name('harga');

Route::middleware(['auth'])->group(function () {
    // Onboarding
    Route::middleware('can:manage-settings')->group(function () {
        Route::get('/admin/onboarding', [OnboardingController::class, 'index'])->name('admin.onboarding');
        Route::post('/admin/onboarding/step', [OnboardingController::class, 'updateStep'])->name('admin.onboarding.step');
        Route::post('/admin/onboarding/complete', [OnboardingController::class, 'complete'])->name('admin.onboarding.complete');
    });

    Route::get('/admin/attendance', [AttendanceController::class, 'index'])->name('admin.attendance.index');
    Route::post('/admin/attendance', [AttendanceController::class, 'store'])->middleware('throttle:20,1')->name('admin.attendance.store');
    Route::get('/admin/attendance/report', [AttendanceController::class, 'report'])->name('admin.attendance.report');

    // Dashboard (accessible to all authenticated users)
    Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

    Route::middleware('can:manage-users')->group(function () {
        Route::get('/admin/users', [CafeUserController::class, 'index'])->name('admin.users.index');
        Route::get('/admin/users/create', [CafeUserController::class, 'create'])->name('admin.users.create');
        Route::post('/admin/users', [CafeUserController::class, 'store'])->name('admin.users.store');
        Route::get('/admin/users/{cafeUser}/edit', [CafeUserController::class, 'edit'])->whereNumber('cafeUser')->name('admin.users.edit');
        Route::put('/admin/users/{cafeUser}', [CafeUserController::class, 'update'])->whereNumber('cafeUser')->name('admin.users.update');
        Route::patch('/admin/users/{cafeUser}/deactivate', [CafeUserController::class, 'deactivate'])->whereNumber('cafeUser')->name('admin.users.deactivate');
        Route::post('/admin/api/crew', [CafeUserController::class, 'store']);
        Route::put('/admin/api/crew/{id}', [CafeUserController::class, 'update'])->whereNumber('id');
        Route::delete('/admin/api/crew/{id}', [CafeUserController::class, 'deactivate'])->whereNumber('id');
    });

    // POS & Orders
    // POS Terminal + APIs
    Route::middleware('can:access-pos')->group(function () {
        Route::get('/admin/pos', [PosController::class, 'index'])->name('admin.pos.index');
        Route::post('/admin/pos/orders', [PosController::class, 'submitOrder'])->middleware('can:manage-payments')->name('admin.pos.orders.submit');
        Route::post('/admin/pos/orders/hold', [PosController::class, 'holdOrder'])->name('admin.pos.orders.hold');
        Route::get('/admin/pos/orders/held', [PosController::class, 'heldOrders'])->name('admin.pos.orders.held');
        Route::post('/admin/pos/orders/{order}/recall', [PosController::class, 'recallOrder'])->name('admin.pos.orders.recall');
        Route::get('/admin/pos/products', [PosController::class, 'products'])->name('admin.pos.products');
        // Legacy order status updates
    });

    Route::get('/admin/api/orders/live', [DashboardController::class, 'getLiveOrders'])->middleware('can:view-orders');
    Route::post('/admin/api/orders/{order}/status', [DashboardController::class, 'updateOrderStatus'])->middleware('can:manage-orders');
    Route::get('/admin/api/orders/{order}/print', [DashboardController::class, 'printOrder'])->middleware('can:view-orders');

    // Cash Register / Shift Session
    Route::get('/admin/cash-reconciliation', [CashRegisterController::class, 'index'])->middleware('can:manage-register')->name('admin.register.index');
    Route::post('/shift/open', [CashRegisterController::class, 'openShift'])->middleware('can:manage-register')->name('shift.open');
    Route::post('/shift/close', [CashRegisterController::class, 'closeShift'])->middleware('can:manage-register')->name('shift.close');

    Route::middleware('can:manage-hpp')->group(function () {
        Route::get('/admin/hpp', [HppCalculatorController::class, 'index'])->name('admin.hpp.index');
        Route::post('/admin/hpp/materials', [HppCalculatorController::class, 'storeMaterial'])->name('admin.hpp.materials.store');
        Route::post('/admin/hpp/calculate', [HppCalculatorController::class, 'calculate'])->name('admin.hpp.calculate');
        Route::put('/admin/hpp/products/{product}', [HppCalculatorController::class, 'update'])->whereNumber('product')->name('admin.hpp.update');
    });

    // Menu Management
    Route::middleware('can:manage-menu')->group(function () {
        Route::post('/admin/api/menu/{menu}/toggle', [DashboardController::class, 'toggleMenuStatus']);
        Route::post('/admin/api/menu/bulk', [DashboardController::class, 'saveMenuBulk']);
        Route::post('/admin/api/menu', [DashboardController::class, 'saveMenu']);
        Route::delete('/admin/api/menu/{id}', [DashboardController::class, 'deleteMenu']);
        Route::post('/admin/api/category', [DashboardController::class, 'saveCategory']);

    });

    Route::middleware('can:manage-inventory')->group(function () {
        // Inventory & Recipes Management
        Route::get('/admin/api/inventory', [InventoryController::class, 'index']);
        Route::post('/admin/api/inventory/material', [InventoryController::class, 'saveMaterial']);
        Route::delete('/admin/api/inventory/material/{id}', [InventoryController::class, 'deleteMaterial']);
        Route::post('/admin/api/inventory/adjust', [InventoryController::class, 'adjustStock']);
        Route::get('/admin/api/inventory/recipes/{productId}', [InventoryController::class, 'getProductRecipes']);
        Route::post('/admin/api/inventory/recipes/{productId}', [InventoryController::class, 'saveProductRecipes']);
    });

    Route::get('/admin/api/dashboard/summary', [DashboardApiController::class, 'getSummary'])->middleware('can:view-dashboard');
    Route::get('/admin/api/dashboard/orders', [OrderApiController::class, 'index'])->middleware('can:view-orders');
    Route::get('/admin/api/dashboard/products', [ProductApiController::class, 'index'])->middleware('can:manage-menu');
    Route::get('/admin/api/dashboard/analytics', [AnalyticsApiController::class, 'index'])->middleware('can:view-reports');
    Route::post('/admin/api/profile', [DashboardController::class, 'updateProfile']);
    // Settings
    Route::middleware('can:manage-settings')->group(function () {

        Route::post('/admin/api/settings', [DashboardController::class, 'saveSettings']);

        // Table & QR Management
        Route::post('/admin/api/table', [DashboardController::class, 'saveTable']);
        Route::put('/admin/api/table', [DashboardController::class, 'updateTableQR']);
    });

    // Crew Management (Owner/Manager)
    Route::middleware('can:manage-crew')->group(function () {
        Route::get('/admin/shifts', [ShiftController::class, 'index'])->name('admin.shifts.index');
        Route::post('/admin/shifts', [ShiftController::class, 'store'])->name('admin.shifts.store');
        Route::get('/admin/api/shifts', [DashboardController::class, 'getShifts']);
        Route::post('/admin/api/shifts', [DashboardController::class, 'saveShift']);
        Route::delete('/admin/api/shifts/{shift}', [ShiftController::class, 'destroy']);
    });

    // My Schedule (Crew/Barista — personal view only)
    Route::middleware('can:view-own-schedule')->group(function () {
        Route::get('/admin/my-schedule', [ShiftController::class, 'mySchedule'])->name('admin.my-schedule');
        Route::get('/admin/crew-home', [CrewController::class, 'home'])->name('admin.crew.home');
    });

    // Kitchen Display (Barista/Kitchen)
    Route::middleware('can:view-kitchen')->group(function () {
        Route::get('/admin/kitchen', [KitchenController::class, 'index'])->name('admin.kitchen.index');
        Route::get('/admin/kitchen/orders', fn () => response()->json(
            Order::where('shop_id', auth()->user()->shop_id)
                ->whereIn('order_status', ['CONFIRMED', 'PREPARING', 'READY'])
                ->with('items')
                ->latest()
                ->limit(150)
                ->get()
                ->map(fn ($o) => [
                    'id' => $o->id, 'status' => $o->order_status,
                    'time' => $o->created_at->format('H:i'),
                    'items' => $o->items->map(fn ($i) => [
                        'id' => $i->id, 'name' => $i->product_name,
                        'qty' => $i->quantity, 'variant' => $i->variant_name,
                        'modifiers' => $i->modifiers ?? [],
                        'notes' => $i->notes,
                    ]),
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

// Customer Receipt (Signed URLs)
Route::get('/receipt/{order}', [ReceiptController::class, 'show'])->name('receipt.web');
Route::get('/receipt/{order}/pdf', [ReceiptController::class, 'downloadPdf'])->name('receipt.pdf');

// Public Customer Menu
Route::get('/{slug}', [ShopController::class, 'show'])->name('shop.menu');
Route::get('/{slug}/cart', [ShopController::class, 'cart'])->name('shop.cart');
Route::get('/{slug}/tracking', [ShopController::class, 'tracking'])->name('shop.tracking');
Route::post('/{slug}/order', [ShopController::class, 'submitOrder'])->name('shop.order.submit');
