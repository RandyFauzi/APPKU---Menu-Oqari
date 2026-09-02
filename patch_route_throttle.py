import re

with open("routes/web.php", "r", encoding="utf-8") as f:
    content = f.read()

# Add throttle middleware to submitOrder
old_route = "Route::post('/{slug}/submit', [App\Http\Controllers\ShopController::class, 'submitOrder'])->name('shop.submit');"
new_route = "Route::post('/{slug}/submit', [App\Http\Controllers\ShopController::class, 'submitOrder'])->name('shop.submit')->middleware('throttle:5,1'); // Max 5 order per minute per IP"

content = content.replace(old_route, new_route)

with open("routes/web.php", "w", encoding="utf-8") as f:
    f.write(content)
