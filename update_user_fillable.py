import re

with open('app/Models/User.php', 'r', encoding='utf-8') as f:
    content = f.read()

content = content.replace("#[Fillable(['name', 'email', 'password', 'shop_id', 'role'])]\n", "")
content = content.replace("class User extends Authenticatable\n{", "class User extends Authenticatable\n{\n    protected $fillable = ['name', 'email', 'password', 'shop_id', 'role'];\n")

with open('app/Models/User.php', 'w', encoding='utf-8') as f:
    f.write(content)
print("Updated User model to use protected $fillable")
