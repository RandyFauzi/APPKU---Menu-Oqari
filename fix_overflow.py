import re

with open('resources/views/Admin/Dashboard/tabs/menu.blade.php', 'r', encoding='utf-8') as f:
    content = f.read()

old_html = '''        <!-- Scrollable Body -->
        <div class="flex-grow overflow-y-auto hide-scroll py-2">
            <div class="flex flex-col gap-3">'''

new_html = '''        <!-- Scrollable Body -->
        <div class="flex-grow overflow-y-auto hide-scroll py-2">
            <div class="flex flex-col gap-3 pb-48">'''

if old_html in content:
    content = content.replace(old_html, new_html)
    with open('resources/views/Admin/Dashboard/tabs/menu.blade.php', 'w', encoding='utf-8') as f:
        f.write(content)
    print("Added pb-48 to scrollable body")
else:
    print("Could not find the HTML to replace")
