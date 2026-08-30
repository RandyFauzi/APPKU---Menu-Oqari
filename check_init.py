import re

with open('resources/views/Admin/Dashboard/dashboard.blade.php', 'r', encoding='utf-8') as f:
    content = f.read()

# I will find the first init() and remove it, because the second init() seems more complete (has loadMenu, INITIAL_DATA mapping, etc). 
# Wait, the first init() has `this.fetchLiveOrders(true)` and `window.addEventListener('storage', ...)` which is ALSO in the second init()!
# Let's inspect the second init().
