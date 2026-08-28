import re

with open('resources/views/Admin/Dashboard/tabs/menu.blade.php', 'r', encoding='utf-8') as f:
    content = f.read()

old_div = '''<div x-data="{ 
                                        open: false,'''
new_div = '''<div :class="open ? 'relative z-50' : 'relative'" x-data="{ 
                                        open: false,'''

if old_div in content:
    content = content.replace(old_div, new_div)
    with open('resources/views/Admin/Dashboard/tabs/menu.blade.php', 'w', encoding='utf-8') as f:
        f.write(content)
    print("Added z-50 to category container when open")
else:
    print("Could not find the x-data div")
