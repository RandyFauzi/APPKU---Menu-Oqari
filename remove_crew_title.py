import re

with open('resources/views/Admin/Dashboard/tabs/crew.blade.php', 'r', encoding='utf-8') as f:
    content = f.read()

old_header = """<div class="flex justify-between items-end mb-8">
            <div>
                <h2 class="text-[32px] text-[#164A35] leading-tight mb-2" style="font-family: 'Playfair Display', serif; font-weight: 700;">Crew Management</h2>
                <p class="text-[#777873] text-[16px]">Manage your team members and roles.</p>
            </div>
            <button @click="showAddCrewModal = true" class="bg-[#164A35] text-white px-5 py-2.5 rounded-[12px] font-bold text-[14px] hover:bg-[#0f3526] transition-colors shadow-sm flex items-center gap-2">
                <i class="fas fa-plus"></i> Add Crew
            </button>
        </div>"""

new_header = """<div class="flex justify-between items-center mb-8">
            <p class="text-[#777873] text-[16px]">Manage your team members and roles.</p>
            <button @click="showAddCrewModal = true" class="bg-[#164A35] text-white px-5 py-2.5 rounded-[12px] font-bold text-[14px] hover:bg-[#0f3526] transition-colors shadow-sm flex items-center gap-2">
                <i class="fas fa-plus"></i> Add Crew
            </button>
        </div>"""

content = content.replace(old_header, new_header)

with open('resources/views/Admin/Dashboard/tabs/crew.blade.php', 'w', encoding='utf-8') as f:
    f.write(content)
print("Removed duplicate title in crew.blade.php")
