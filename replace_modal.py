import re

with open('resources/views/Admin/Dashboard/dashboard.blade.php', 'r', encoding='utf-8') as f:
    content = f.read()

# Replace the entire showAddMenuModal section
pattern = r"<!-- MODAL: ADD MENU -->.*?<!-- MODAL: ADD TABLE -->"

new_modal = """<!-- MODAL: ADD/EDIT MENU -->
        <div x-show="showAddMenuModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4 font-sans">
            <!-- Backdrop -->
            <div @click="showAddMenuModal = false" class="absolute inset-0 bg-[#202522]/30 backdrop-blur-sm transition-opacity"></div>
            
            <!-- Modal Content -->
            <div class="relative bg-[#FFFFFF] w-full max-w-2xl rounded-[28px] p-8 shadow-[0_10px_35px_rgba(0,0,0,0.05)] flex flex-col border border-[#E3E1DC]" @keydown.escape.window="showAddMenuModal = false" x-transition>
                
                <!-- Header -->
                <div class="flex justify-between items-start mb-6 pb-6 border-b border-dashed border-[#E3E1DC]">
                    <div class="flex gap-5">
                        <div class="w-16 h-16 rounded-[16px] bg-[#164A35] text-white flex items-center justify-center shadow-sm">
                            <i class="fas fa-pen text-2xl" x-show="newMenu.id"></i>
                            <i class="fas fa-hamburger text-2xl" x-show="!newMenu.id"></i>
                        </div>
                        <div class="flex flex-col justify-center">
                            <h2 class="text-[28px] text-[#164A35] leading-tight mb-1" style="font-family: 'Playfair Display', serif; font-weight: 700;" x-text="newMenu.id ? 'Edit Menu' : 'Tambah Menu Baru'"></h2>
                            <p class="text-[#777873] text-[15px]" x-text="newMenu.id ? 'Perbarui detail, harga, dan gambar menu ini.' : 'Tambahkan menu baru ke dalam daftar toko Anda.'"></p>
                        </div>
                    </div>
                    <button @click="showAddMenuModal = false" class="text-[#777873] hover:text-[#202522] bg-[#F8F7F3] hover:bg-[#E3E1DC] transition-colors w-8 h-8 flex items-center justify-center rounded-full mt-1">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <!-- Body -->
                <div class="flex flex-col gap-5">
                    <div class="grid grid-cols-2 gap-5">
                        <div>
                            <label class="block text-[12px] font-bold text-[#777873] mb-2 uppercase tracking-widest">Kategori</label>
                            <select x-model="newMenu.categoryId" class="w-full bg-[#F8F7F3] border border-[#E3E1DC] rounded-[14px] px-4 py-3.5 text-[15px] font-bold text-[#202522] focus:border-[#164A35] focus:ring-1 focus:ring-[#164A35] outline-none appearance-none">
                                <option value="beverages">Beverages</option>
                                <option value="foods">Foods</option>
                                <option value="snacks">Snacks</option>
                                <option value="sweets">Sweets</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[12px] font-bold text-[#777873] mb-2 uppercase tracking-widest">Harga (Rp)</label>
                            <div class="relative">
                                <span class="absolute left-4 top-3.5 text-[15px] text-[#202522] font-medium">Rp</span>
                                <input type="number" x-model="newMenu.price" placeholder="0" class="w-full bg-[#F8F7F3] border border-[#E3E1DC] rounded-[14px] pl-11 pr-4 py-3.5 text-[15px] font-bold text-[#202522] focus:border-[#164A35] focus:ring-1 focus:ring-[#164A35] outline-none">
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[12px] font-bold text-[#777873] mb-2 uppercase tracking-widest">Nama Menu</label>
                        <input type="text" x-model="newMenu.name" placeholder="Misal: Iced Matcha Latte" class="w-full bg-[#F8F7F3] border border-[#E3E1DC] rounded-[14px] px-4 py-3.5 text-[15px] font-bold text-[#202522] placeholder:font-medium placeholder:text-[#C5DBC5] focus:border-[#164A35] focus:ring-1 focus:ring-[#164A35] outline-none">
                    </div>

                    <div>
                        <label class="block text-[12px] font-bold text-[#777873] mb-2 uppercase tracking-widest">Deskripsi Singkat</label>
                        <textarea x-model="newMenu.desc" placeholder="Penjelasan menarik tentang menu ini..." rows="2" class="w-full bg-[#F8F7F3] border border-[#E3E1DC] rounded-[14px] px-4 py-3.5 text-[15px] font-medium text-[#202522] placeholder:text-[#C5DBC5] focus:border-[#164A35] focus:ring-1 focus:ring-[#164A35] outline-none"></textarea>
                    </div>

                    <div>
                        <label class="block text-[12px] font-bold text-[#777873] mb-2 uppercase tracking-widest">Upload Gambar (Opsional)</label>
                        <div class="flex items-center gap-4">
                            <div class="w-16 h-16 rounded-[14px] bg-[#F8F7F3] border border-dashed border-[#C5DBC5] flex items-center justify-center shrink-0">
                                <i class="fas fa-image text-[#C5DBC5] text-xl"></i>
                            </div>
                            <input type="file" x-ref="menuImageInput" class="w-full text-sm text-[#777873] file:mr-4 file:py-2.5 file:px-5 file:rounded-xl file:border-0 file:text-[13px] file:font-bold file:bg-[#DDEBDD] file:text-[#164A35] hover:file:bg-[#C5DBC5] cursor-pointer outline-none">
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="mt-8 flex justify-end gap-3 pt-6 border-t border-[#E3E1DC]">
                    <button @click="showAddMenuModal = false" class="px-6 py-3 rounded-[12px] font-bold text-[14px] bg-white border border-[#E3E1DC] text-[#777873] hover:bg-[#F8F7F3] transition-colors">Batal</button>
                    <button @click="saveNewMenu" class="px-6 py-3 rounded-[12px] font-bold text-[14px] bg-[#164A35] text-white hover:bg-[#0f3526] transition-colors shadow-sm flex items-center gap-2">
                        <i class="fas fa-check" x-show="!newMenu.id"></i>
                        <i class="fas fa-save" x-show="newMenu.id"></i>
                        <span x-text="newMenu.id ? 'Simpan Perubahan' : 'Simpan Menu'"></span>
                    </button>
                </div>
            </div>
        </div>

        <!-- MODAL: ADD TABLE -->"""

content = re.sub(pattern, new_modal, content, flags=re.DOTALL)

with open('resources/views/Admin/Dashboard/dashboard.blade.php', 'w', encoding='utf-8') as f:
    f.write(content)
print("Updated Add/Edit Menu modal UI to match modern theme")
