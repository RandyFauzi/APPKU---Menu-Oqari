import re

# 1. Update routes/web.php
with open('routes/web.php', 'r', encoding='utf-8') as f:
    content = f.read()

content = content.replace(
    "Route::post('/admin/api/menu', [DashboardController::class, 'saveMenu']);",
    "Route::post('/admin/api/menu', [DashboardController::class, 'saveMenu']);\n    Route::delete('/admin/api/menu/{id}', [DashboardController::class, 'deleteMenu']);"
)

with open('routes/web.php', 'w', encoding='utf-8') as f:
    f.write(content)


# 2. Update DashboardController.php
with open('app/Http/Controllers/Admin/DashboardController.php', 'r', encoding='utf-8') as f:
    content = f.read()

new_delete = """    public function deleteMenu($id)
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        $menu = \App\Models\Product::where('shop_id', $user->shop_id)->where('id', $id)->first();
        if ($menu) {
            $menu->delete();
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false, 'message' => 'Menu not found'], 404);
    }

    public function toggleMenuStatus"""

content = content.replace("    public function toggleMenuStatus", new_delete)

with open('app/Http/Controllers/Admin/DashboardController.php', 'w', encoding='utf-8') as f:
    f.write(content)


# 3. Update dashboard.blade.php
with open('resources/views/Admin/Dashboard/dashboard.blade.php', 'r', encoding='utf-8') as f:
    content = f.read()

old_delete_js = """                deleteMenu(id) {
                    if (confirm("Yakin ingin menghapus menu ini? (Belum diimplementasikan API-nya)")) {
                        this.menuItems = this.menuItems.filter(m => m.id !== id);
                    }
                },"""

new_delete_js = """                deleteMenu(id) {
                    if (confirm("Yakin ingin menghapus menu ini?")) {
                        fetch('/admin/api/menu/' + id, {
                            method: 'DELETE',
                            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
                        }).then(res => res.json()).then(data => {
                            if (data.success) {
                                this.menuItems = this.menuItems.filter(m => m.id !== id);
                                this.addToast('Menu berhasil dihapus', 'success');
                            } else {
                                this.addToast(data.message || 'Gagal menghapus menu', 'error');
                            }
                        }).catch(err => {
                            this.addToast('Terjadi kesalahan jaringan', 'error');
                        });
                    }
                },"""

content = content.replace(old_delete_js, new_delete_js)

with open('resources/views/Admin/Dashboard/dashboard.blade.php', 'w', encoding='utf-8') as f:
    f.write(content)

print("Implemented Menu DELETE API and Toast feedback")
