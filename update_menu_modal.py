import re

with open('resources/views/Admin/Dashboard/dashboard.blade.php', 'r', encoding='utf-8') as f:
    content = f.read()

content = content.replace(
    '<h3 class="font-heading font-extrabold text-lg text-[#2D3748]"><i class="fas fa-hamburger mr-2 text-[#1E5A7A]"></i> Tambah Menu Baru</h3>',
    '<h3 class="font-heading font-extrabold text-lg text-[#2D3748]"><i class="fas fa-hamburger mr-2 text-[#1E5A7A]"></i> <span x-text="newMenu.id ? \'Edit Menu\' : \'Tambah Menu Baru\'"></span></h3>'
)

content = content.replace(
    '<button @click="saveNewMenu" class="px-4 py-2 rounded text-sm font-bold bg-[#1E5A7A] text-white shadow-sm hover:bg-[#154660] transition">Simpan Menu</button>',
    '<button @click="saveNewMenu" class="px-4 py-2 rounded text-sm font-bold bg-[#1E5A7A] text-white shadow-sm hover:bg-[#154660] transition" x-text="newMenu.id ? \'Simpan Perubahan\' : \'Simpan Menu\'"></button>'
)

with open('resources/views/Admin/Dashboard/dashboard.blade.php', 'w', encoding='utf-8') as f:
    f.write(content)
print("Updated Add/Edit Menu modal UI texts")
