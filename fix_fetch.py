import re

with open('resources/views/Admin/Dashboard/dashboard.blade.php', 'r', encoding='utf-8') as f:
    content = f.read()

# Add isSaving to state
content = content.replace("newCrew: { name: '', email: '', password: '', role: 'barista' },", "newCrew: { name: '', email: '', password: '', role: 'barista' },\n                isSaving: false,")

# Add Accept headers to fetch calls
content = content.replace("'Content-Type': 'application/json',", "'Content-Type': 'application/json',\n                            'Accept': 'application/json',")

with open('resources/views/Admin/Dashboard/dashboard.blade.php', 'w', encoding='utf-8') as f:
    f.write(content)
print("Fixed isSaving and Accept headers")
