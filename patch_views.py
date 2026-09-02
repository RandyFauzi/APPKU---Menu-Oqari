import os

def replace_in_file(filepath, old, new):
    with open(filepath, "r", encoding="utf-8") as f:
        content = f.read()
    content = content.replace(old, new)
    with open(filepath, "w", encoding="utf-8") as f:
        f.write(content)

replace_in_file("resources/views/Admin/Dashboard/tabs/crew.blade.php", "user.role === 'admin'", "user.role === 'owner'")
replace_in_file("resources/views/Admin/Dashboard/tabs/crew.blade.php", "user.role !== 'admin'", "user.role !== 'owner'")

replace_in_file("resources/views/Admin/Dashboard/tabs/shifts.blade.php", "user.role === 'admin'", "['owner', 'manager'].includes(user.role)")
replace_in_file("resources/views/SuperAdmin/dashboard.blade.php", "$user->role === 'admin'", "$user->role === 'owner'")
