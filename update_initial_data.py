import re

with open('resources/views/Admin/Dashboard/dashboard.blade.php', 'r', encoding='utf-8') as f:
    content = f.read()

content = content.replace(
    "shop: @json($shop ?? null),",
    "shop: @json($shop ?? null),\n            user: @json(auth()->user()),"
)

with open('resources/views/Admin/Dashboard/dashboard.blade.php', 'w', encoding='utf-8') as f:
    f.write(content)
print("Injected user to INITIAL_DATA")
