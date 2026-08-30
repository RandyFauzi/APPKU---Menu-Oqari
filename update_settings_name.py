import re

with open('resources/views/Admin/Dashboard/dashboard.blade.php', 'r', encoding='utf-8') as f:
    content = f.read()

content = content.replace(
    "{ id: 'settings', name: 'Profile & Branding', icon: 'fas fa-store' },",
    "{ id: 'settings', name: 'Toko & Branding', icon: 'fas fa-store' },"
)

with open('resources/views/Admin/Dashboard/dashboard.blade.php', 'w', encoding='utf-8') as f:
    f.write(content)
print("Updated settings tab name")
