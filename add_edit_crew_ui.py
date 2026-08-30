import re

with open('resources/views/Admin/Dashboard/tabs/crew.blade.php', 'r', encoding='utf-8') as f:
    content = f.read()

# Add edit button to the table row
row_pattern = r"""<td class="py-4 px-6 text-right">\s*<button @click="deleteCrew\(user\.id\)" x-show="user\.role !== 'admin'" class="text-red-400 hover:text-red-600 p-2"><i class="fas fa-trash"></i></button>\s*</td>"""
row_repl = r"""<td class="py-4 px-6 text-right">
                                <button @click="openEditCrew(user)" x-show="user.role !== 'admin'" class="text-gray-400 hover:text-blue-600 p-2 transition-colors mr-1"><i class="fas fa-pen"></i></button>
                                <button @click="deleteCrew(user.id)" x-show="user.role !== 'admin'" class="text-red-400 hover:text-red-600 p-2 transition-colors"><i class="fas fa-trash"></i></button>
                            </td>"""
content = re.sub(row_pattern, row_repl, content)

# Add Edit Modal to the bottom
edit_modal = r"""

    <!-- Modal Edit Crew -->
    <div x-show="showEditCrewModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center bg-black/40 backdrop-blur-sm" x-transition.opacity>
        <div class="bg-white rounded-[24px] w-full max-w-md p-8 shadow-2xl" @click.away="showEditCrewModal = false" x-transition>
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-[24px] font-bold text-[#164A35]" style="font-family: 'Playfair Display', serif;">Edit Crew</h3>
                <button @click="showEditCrewModal = false" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times text-xl"></i></button>
            </div>
            <form @submit.prevent="updateCrew" class="flex flex-col gap-4">
                <div>
                    <label class="block text-[13px] font-bold text-[#777873] mb-1.5 uppercase tracking-wide">Name</label>
                    <input type="text" x-model="editCrewData.name" required class="w-full bg-[#F8F7F3] border border-[#E3E1DC] rounded-xl px-4 py-3 text-sm focus:border-[#164A35] focus:ring-1 focus:ring-[#164A35] outline-none">
                </div>
                <div>
                    <label class="block text-[13px] font-bold text-[#777873] mb-1.5 uppercase tracking-wide">Email</label>
                    <input type="email" x-model="editCrewData.email" required class="w-full bg-[#F8F7F3] border border-[#E3E1DC] rounded-xl px-4 py-3 text-sm focus:border-[#164A35] focus:ring-1 focus:ring-[#164A35] outline-none">
                </div>
                <div>
                    <label class="block text-[13px] font-bold text-[#777873] mb-1.5 uppercase tracking-wide">Password <span class="text-[10px] lowercase normal-case text-gray-400">(Biarkan kosong jika tidak diubah)</span></label>
                    <input type="password" x-model="editCrewData.password" placeholder="••••••••" class="w-full bg-[#F8F7F3] border border-[#E3E1DC] rounded-xl px-4 py-3 text-sm focus:border-[#164A35] focus:ring-1 focus:ring-[#164A35] outline-none">
                </div>
                <div>
                    <label class="block text-[13px] font-bold text-[#777873] mb-1.5 uppercase tracking-wide">Role</label>
                    <select x-model="editCrewData.role" class="w-full bg-[#F8F7F3] border border-[#E3E1DC] rounded-xl px-4 py-3 text-sm focus:border-[#164A35] focus:ring-1 focus:ring-[#164A35] outline-none">
                        <option value="barista">Barista</option>
                        <option value="kitchen">Kitchen</option>
                    </select>
                </div>
                <div class="mt-4 flex gap-3">
                    <button type="button" @click="showEditCrewModal = false" class="flex-1 bg-white border border-[#E3E1DC] text-[#777873] py-3 rounded-xl font-bold hover:bg-gray-50 transition-colors">Cancel</button>
                    <button type="submit" class="flex-1 bg-[#164A35] text-white py-3 rounded-xl font-bold hover:bg-[#0f3526] transition-colors" :disabled="isSaving" x-text="isSaving ? 'Saving...' : 'Update Crew'"></button>
                </div>
            </form>
        </div>
    </div>
</div>"""
content = content.replace("</div>\n</div>", "</div>\n" + edit_modal)

with open('resources/views/Admin/Dashboard/tabs/crew.blade.php', 'w', encoding='utf-8') as f:
    f.write(content)
print("Added Edit Modal to crew tab")
