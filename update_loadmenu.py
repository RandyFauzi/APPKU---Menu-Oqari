import re

with open('resources/views/Admin/Dashboard/dashboard.blade.php', 'r', encoding='utf-8') as f:
    content = f.read()

old_loadMenu = '''                loadMenu() {
                    this.menuItems = window.INITIAL_DATA.menu.map(m => ({
                        ...m,
                        categoryId: m.category_name,
                        desc: m.description,
                        tags: m.tags || []
                    }));
                },'''

new_loadMenu = '''                loadMenu() {
                    this.menuItems = window.INITIAL_DATA.menu.map(m => ({
                        ...m,
                        categoryId: m.category_name,
                        desc: m.description,
                        image: m.image_url ? ('/storage/' + m.image_url + '?v=' + new Date(m.updated_at).getTime()) : null,
                        tags: m.tags || []
                    }));
                },'''

if old_loadMenu in content:
    content = content.replace(old_loadMenu, new_loadMenu)
    with open('resources/views/Admin/Dashboard/dashboard.blade.php', 'w', encoding='utf-8') as f:
        f.write(content)
    print("Updated loadMenu with cache-busting image URLs")
else:
    print("Could not find loadMenu block")
