import re

with open('resources/views/Admin/Dashboard/tabs/menu.blade.php', 'r', encoding='utf-8') as f:
    content = f.read()

content = content.replace("item.soldOut", "item.is_sold_out")

with open('resources/views/Admin/Dashboard/tabs/menu.blade.php', 'w', encoding='utf-8') as f:
    f.write(content)
print("Replaced item.soldOut with item.is_sold_out in menu UI")
