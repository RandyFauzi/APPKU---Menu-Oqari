import re

with open('resources/views/Admin/Dashboard/dashboard.blade.php', 'r', encoding='utf-8') as f:
    content = f.read()

old_update = """                                    item.name = this.newMenu.name;
                                    item.price = parseInt(this.newMenu.price);
                                    item.desc = this.newMenu.desc;
                                    item.categoryId = this.newMenu.categoryId;"""

new_update = """                                    item.name = this.newMenu.name;
                                    item.price = parseInt(this.newMenu.price);
                                    item.desc = this.newMenu.desc;
                                    item.categoryId = this.newMenu.categoryId;
                                    if (data.menu.image_url) {
                                        item.image = '/storage/' + data.menu.image_url + '?v=' + Date.now();
                                    }"""

content = content.replace(old_update, new_update)

with open('resources/views/Admin/Dashboard/dashboard.blade.php', 'w', encoding='utf-8') as f:
    f.write(content)
print("Updated frontend image live-reload")
