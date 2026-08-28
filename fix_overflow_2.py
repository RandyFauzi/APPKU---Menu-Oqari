import re

with open('resources/views/Admin/Dashboard/tabs/menu.blade.php', 'r', encoding='utf-8') as f:
    content = f.read()

content = content.replace('<div class="flex flex-col gap-3 pb-48">', '<div class="flex flex-col gap-3 pb-[240px]">')

with open('resources/views/Admin/Dashboard/tabs/menu.blade.php', 'w', encoding='utf-8') as f:
    f.write(content)
print("Updated to pb-[240px]")
