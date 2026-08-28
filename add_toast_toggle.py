import re

with open('resources/views/Admin/Dashboard/dashboard.blade.php', 'r', encoding='utf-8') as f:
    content = f.read()

old_logic = """                toggleSoldOut(id) {
                    fetch(`/admin/api/menu/${id}/toggle`, {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                    }).then(res => res.json()).then(data => {
                        const item = this.menuItems.find(m => m.id === id);
                        if (item) item.is_sold_out = data.is_sold_out;
                    });
                },"""

new_logic = """                toggleSoldOut(id) {
                    fetch(`/admin/api/menu/${id}/toggle`, {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                    }).then(res => res.json()).then(data => {
                        const item = this.menuItems.find(m => m.id === id);
                        if (item) {
                            item.is_sold_out = data.is_sold_out;
                            this.addToast(data.is_sold_out ? 'Menu ditandai Sold Out' : 'Menu tersedia kembali', 'success');
                        }
                    }).catch(err => {
                        this.addToast('Gagal mengubah status menu', 'error');
                    });
                },"""

content = content.replace(old_logic, new_logic)

with open('resources/views/Admin/Dashboard/dashboard.blade.php', 'w', encoding='utf-8') as f:
    f.write(content)
print("Added toast notification to toggleSoldOut")
