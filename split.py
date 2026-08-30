import re

with open('resources/views/Admin/Dashboard/dashboard.blade.php', 'r', encoding='utf-8') as f:
    content = f.read()

def extract_tab(tab_name, fname):
    start_str = f'<div x-show="currentTab === \'{tab_name}\'"'
    start = content.find(start_str)
    if start == -1: return None
    
    count = 0
    end = start
    for match in re.finditer(r'</?div[^>]*>', content[start:]):
        tag = match.group(0)
        if tag.startswith('</div'):
            count -= 1
        elif tag.startswith('<div'):
            count += 1
            
        if count == 0:
            end = start + match.end()
            break
            
    with open('resources/views/Admin/Dashboard/tabs/' + fname, 'w', encoding='utf-8') as out:
        out.write(content[start:end])
    return start, end

extract_tab('orders', 'orders.blade.php')
extract_tab('analytics', 'analytics.blade.php')
extract_tab('qr', 'qr.blade.php')
extract_tab('settings', 'settings.blade.php')

# For menu, we know it ends right before analytics
start_menu = content.find('<div x-show="currentTab === \'menu\'"')
start_analytics = content.find('<!-- VIEW: ANALYTICS (OWNER) -->')

menu_content = content[start_menu:start_analytics].strip()
# Add missing closing div!
while menu_content.count('<div') > menu_content.count('</div'):
    menu_content += '\n</div>'

with open('resources/views/Admin/Dashboard/tabs/menu.blade.php', 'w', encoding='utf-8') as out:
    out.write(menu_content)

# Now construct the new dashboard file
top_part = content[:content.find('<div x-show="currentTab === \'orders\'"')]
end_settings = extract_tab('settings', 'settings.blade.php')[1]
bottom_part = content[end_settings:]

new_content = top_part
new_content += "        @include('Admin.Dashboard.tabs.orders')\n"
new_content += "        @include('Admin.Dashboard.tabs.menu')\n"
new_content += "        \n"
new_content += "        <!-- VIEW: ANALYTICS (OWNER) -->\n"
new_content += "        @include('Admin.Dashboard.tabs.analytics')\n"
new_content += "        @include('Admin.Dashboard.tabs.qr')\n"
new_content += "        @include('Admin.Dashboard.tabs.settings')\n"
new_content += bottom_part

with open('resources/views/Admin/Dashboard/dashboard.blade.php', 'w', encoding='utf-8') as f:
    f.write(new_content)
print('Extraction and replacement complete.')
