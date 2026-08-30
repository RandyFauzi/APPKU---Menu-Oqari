import re

with open('resources/views/Admin/Dashboard/dashboard.blade.php', 'r', encoding='utf-8') as f:
    content = f.read()

content = content.replace(
    "@include('Admin.Dashboard.tabs.qr')",
    "@include('Admin.Dashboard.tabs.qr')\n        @include('Admin.Dashboard.tabs.crew')"
)

with open('resources/views/Admin/Dashboard/dashboard.blade.php', 'w', encoding='utf-8') as f:
    f.write(content)
print("Included crew tab")
