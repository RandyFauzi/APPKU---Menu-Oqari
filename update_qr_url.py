import re

with open('resources/views/Admin/Dashboard/dashboard.blade.php', 'r', encoding='utf-8') as f:
    content = f.read()

old_baseUrl = "baseUrl: window.location.origin + window.location.pathname.replace('dashboard.html', 'index.html'),\n                getQRUrl(tableCode, token = '') {\n                    const url = `${this.baseUrl}?table=${tableCode}${token ? '&token='+token : ''}`;"

new_baseUrl = """getQRUrl(tableCode, token = '') {
                    const slug = window.INITIAL_DATA.shop?.slug || 'menu';
                    const baseUrl = window.location.origin + '/' + slug;
                    const url = `${baseUrl}?table=${tableCode}${token ? '&token='+token : ''}`;"""

content = content.replace(old_baseUrl, new_baseUrl)

with open('resources/views/Admin/Dashboard/dashboard.blade.php', 'w', encoding='utf-8') as f:
    f.write(content)
print("Fixed getQRUrl")
