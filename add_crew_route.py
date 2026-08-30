import re

with open('routes/web.php', 'r', encoding='utf-8') as f:
    content = f.read()

content = content.replace(
    "Route::delete('/admin/api/crew/{id}', [DashboardController::class, 'deleteCrew']);",
    "Route::delete('/admin/api/crew/{id}', [DashboardController::class, 'deleteCrew']);\n    Route::put('/admin/api/crew/{id}', [DashboardController::class, 'updateCrew']);"
)

with open('routes/web.php', 'w', encoding='utf-8') as f:
    f.write(content)
print("Added updateCrew route")
