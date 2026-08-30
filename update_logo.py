import re

with open('resources/views/Admin/Dashboard/dashboard.blade.php', 'r', encoding='utf-8') as f:
    content = f.read()

old_logo = '''if (window.INITIAL_DATA.shop.logo_url) {
                            this.settings.logoPreview = '/storage/' + window.INITIAL_DATA.shop.logo_url;
                        }'''

new_logo = '''if (window.INITIAL_DATA.shop.logo_url) {
                            this.settings.logoPreview = '/storage/' + window.INITIAL_DATA.shop.logo_url + '?v=' + new Date(window.INITIAL_DATA.shop.updated_at).getTime();
                        }'''

if old_logo in content:
    content = content.replace(old_logo, new_logo)
    with open('resources/views/Admin/Dashboard/dashboard.blade.php', 'w', encoding='utf-8') as f:
        f.write(content)
    print("Updated logo loading")
else:
    print("Could not find logo block")
