import re

with open('resources/views/Admin/Dashboard/dashboard.blade.php', 'r', encoding='utf-8') as f:
    content = f.read()

old_initial = r"""        window.INITIAL_DATA = {
            menu: @json($menuItems ?? []),
            orders: @json($orders ?? []),
            tables: @json($tables ?? []),
            shop: @json($shop ?? null)
        };"""

new_initial = r"""        window.INITIAL_DATA = {
            menu: @json($menuItems ?? []),
            orders: @json($orders ?? []),
            tables: @json($tables ?? []),
            shop: @json($shop ?? null),
            users: @json($users ?? [])
        };"""

content = content.replace(old_initial, new_initial)

with open('resources/views/Admin/Dashboard/dashboard.blade.php', 'w', encoding='utf-8') as f:
    f.write(content)
print("Updated INITIAL_DATA")
