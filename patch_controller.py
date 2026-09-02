import re
with open("app/Http/Controllers/Admin/DashboardController.php", "r", encoding="utf-8") as f:
    content = f.read()

# Remove the manual aborts
content = re.sub(r"// P0 Security Fix: RBAC and Whitelist\s*if \(\$request->user\(\)->role !== 'admin'\) \{\s*abort\(403, 'Akses ditolak\. Hanya Pemilik Toko \(Admin\) yang dapat menambah crew\.'\);\s*\}", "", content)

content = re.sub(r"if \(\$request->user\(\)->role !== 'admin'\) \{\s*abort\(403, 'Akses ditolak\.'\);\s*\}", "", content)
content = re.sub(r"if \(Auth::user\(\)->role !== 'admin'\) \{\s*abort\(403, 'Akses ditolak\.'\);\s*\}", "", content)

# Also update the validation for saveCrew and updateCrew to use 'owner' instead of 'admin', or rather just remove 'admin' from crew creation because a shop owner shouldn't create more owners through the crew panel unless desired. Let's allow 'manager,cashier,kitchen,waiter'.
content = content.replace("'role' => 'required|string|in:admin,cashier,kitchen,waiter'", "'role' => 'required|string|in:manager,cashier,kitchen,waiter'")

# If there is any string checking for 'admin', change to 'owner'
# Let's see if there are other occurrences of 'admin' -> 'owner'
content = content.replace("=== 'admin'", "=== 'owner'")
content = content.replace("!== 'admin'", "!== 'owner'")

with open("app/Http/Controllers/Admin/DashboardController.php", "w", encoding="utf-8") as f:
    f.write(content)
