import re
with open("app/Http/Controllers/Admin/DashboardController.php", "r", encoding="utf-8") as f:
    content = f.read()

# Fix role default for new shops
content = content.replace("'role' => 'admin', // Jadikan dia admin dari tokonya sendiri", "'role' => 'owner', // Jadikan dia owner dari tokonya sendiri")

# Fix updateCrew validation
old_val = r"'role' => 'required\|string',"
new_val = r"'role' => 'required|string|in:manager,cashier,kitchen,waiter',"
content = re.sub(old_val, new_val, content)

# Remove the manual aborts now that middleware handles it
content = re.sub(r"if \(\$user->role !== 'owner'\) \{\s*return response\(\)->json\(\[\], 403\);\s*\}", "", content)
content = re.sub(r"if \(\$user->role !== 'owner'\) \{\s*return response\(\)->json\(\['success' => false\], 403\);\s*\}", "", content)
content = re.sub(r"if \(\$request->user\(\)->role !== 'owner'\) \{\s*abort\(403, 'Akses ditolak\.'\);\s*\}", "", content)

with open("app/Http/Controllers/Admin/DashboardController.php", "w", encoding="utf-8") as f:
    f.write(content)
