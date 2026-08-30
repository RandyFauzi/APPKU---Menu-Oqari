import re

with open('app/Models/User.php', 'r', encoding='utf-8') as f:
    content = f.read()

content = content.replace("#[Fillable(['name', 'email', 'password'])]", "#[Fillable(['name', 'email', 'password', 'shop_id', 'role'])]")

with open('app/Models/User.php', 'w', encoding='utf-8') as f:
    f.write(content)
print("Updated User model fillable")
