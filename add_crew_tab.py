import re

with open('resources/views/Admin/Dashboard/dashboard.blade.php', 'r', encoding='utf-8') as f:
    content = f.read()

old_tabs = '''                tabs: [
                    { id: 'analytics', name: 'Dashboard Analytics', icon: 'fas fa-chart-pie' },
                    { id: 'orders', name: 'Live Orders', icon: 'fas fa-receipt' },
                    { id: 'menu', name: 'Menu CMS', icon: 'fas fa-hamburger' },
                    { id: 'qr', name: 'Table & QR', icon: 'fas fa-qrcode' },
                    { id: 'settings', name: 'Profile & Branding', icon: 'fas fa-store' },
                ],'''

new_tabs = '''                tabs: [
                    { id: 'analytics', name: 'Dashboard Analytics', icon: 'fas fa-chart-pie' },
                    { id: 'orders', name: 'Live Orders', icon: 'fas fa-receipt' },
                    { id: 'menu', name: 'Menu CMS', icon: 'fas fa-hamburger' },
                    { id: 'qr', name: 'Table & QR', icon: 'fas fa-qrcode' },
                    { id: 'crew', name: 'Crew Management', icon: 'fas fa-users' },
                    { id: 'settings', name: 'Profile & Branding', icon: 'fas fa-store' },
                ],'''

if old_tabs in content:
    content = content.replace(old_tabs, new_tabs)
    with open('resources/views/Admin/Dashboard/dashboard.blade.php', 'w', encoding='utf-8') as f:
        f.write(content)
    print("Added Crew tab")
else:
    print("Could not find tabs array")
