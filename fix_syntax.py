import re

with open('resources/views/Admin/Dashboard/tabs/analytics.blade.php', 'r', encoding='utf-8') as f:
    content = f.read()

content = content.replace("this.('currentTab'", "this.$watch('currentTab'")

with open('resources/views/Admin/Dashboard/tabs/analytics.blade.php', 'w', encoding='utf-8') as f:
    f.write(content)
print("Fixed syntax error")
