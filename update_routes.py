import re

with open('routes/web.php', 'r', encoding='utf-8') as f:
    content = f.read()

old_routes = r"""    Route::post('/admin/api/menu', [DashboardController::class, 'saveMenu']);
    Route::post('/admin/api/settings', [DashboardController::class, 'saveSettings']);"""

new_routes = r"""    Route::post('/admin/api/menu', [DashboardController::class, 'saveMenu']);
    Route::post('/admin/api/settings', [DashboardController::class, 'saveSettings']);
    Route::post('/admin/api/crew', [DashboardController::class, 'saveCrew']);
    Route::delete('/admin/api/crew/{id}', [DashboardController::class, 'deleteCrew']);"""

content = content.replace(old_routes, new_routes)

with open('routes/web.php', 'w', encoding='utf-8') as f:
    f.write(content)
print("Added routes")
