import re
with open("routes/web.php", "r", encoding="utf-8") as f:
    content = f.read()

# Replace the entire auth group for admin/dashboard and admin/api
old_group_pattern = r"Route::middleware\(\['auth'\]\)->group\(function \(\) \{[\s\S]*?\n\s*\}\);"

new_group = """Route::middleware(['auth'])->group(function () {
    // POS & Umum (Semua bisa masuk kecuali superadmin yang diarahkan ke panel khusus)
    Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    
    // POS / Orders
    Route::middleware('can:access-pos')->group(function () {
        Route::get('/admin/api/orders/live', [DashboardController::class, 'getLiveOrders']);
        Route::post('/admin/api/orders/{order}/status', [DashboardController::class, 'updateOrderStatus']);
        Route::get('/admin/api/orders/{order}/print', [DashboardController::class, 'printOrder']);
        Route::post('/admin/api/shifts', [DashboardController::class, 'saveShift']);
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
});"""

content = re.sub(old_group_pattern, new_group, content)

with open("routes/web.php", "w", encoding="utf-8") as f:
    f.write(content)
