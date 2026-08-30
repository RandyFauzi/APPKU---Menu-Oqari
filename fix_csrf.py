import re

with open('resources/views/Admin/Dashboard/dashboard.blade.php', 'r', encoding='utf-8') as f:
    content = f.read()

content = content.replace(
    """document.querySelector('meta[name="csrf-token"]').getAttribute('content')""",
    """'{{ csrf_token() }}'"""
)

with open('resources/views/Admin/Dashboard/dashboard.blade.php', 'w', encoding='utf-8') as f:
    f.write(content)
print("Fixed CSRF token retrieval")
