import re

with open('routes/web.php', 'r', encoding='utf-8') as f:
    content = f.read()

if "Route::post('/admin/api/profile'" not in content:
    content = content.replace(
        "Route::post('/admin/api/settings', [DashboardController::class, 'saveSettings']);",
        "Route::post('/admin/api/settings', [DashboardController::class, 'saveSettings']);\n    Route::post('/admin/api/profile', [DashboardController::class, 'updateProfile']);"
    )

with open('routes/web.php', 'w', encoding='utf-8') as f:
    f.write(content)
print("Added profile route")
