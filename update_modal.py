import re

with open('resources/views/Admin/Dashboard/dashboard.blade.php', 'r', encoding='utf-8') as f:
    content = f.read()

old_modal = r"""        <div x-show="showAddTableModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center">
            <!-- Backdrop -->
            <div @click="showAddTableModal = false" class="absolute inset-0 bg-black/50 backdrop-blur-sm transition-opacity"></div>
            <!-- Modal Content -->
            <div class="relative bg-white w-[400px] rounded-2xl shadow-xl flex flex-col overflow-hidden" @keydown.escape.window="showAddTableModal = false">
                <div class="p-5 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                    <h3 class="font-sans font-bold text-lg text-primary flex items-center gap-2"><i class="fas fa-qrcode text-accent"></i> Tambah Meja Baru</h3>
                    <button @click="showAddTableModal = false" class="text-gray-400 hover:text-red-500 transition-colors w-8 h-8 flex items-center justify-center rounded-full hover:bg-white">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="p-5 flex flex-col gap-4 font-mono">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-1 uppercase tracking-wider">Nama / Nomor Meja</label>
                        <input x-ref="tableInput" type="text" x-model="newTableId" @keydown.enter="addTableFromModal" placeholder="Misal: Meja 07" class="w-full border border-gray-200 rounded px-3 py-2 text-sm focus:border-primary focus:outline-none">
                    </div>
                </div>
                <div class="p-5 border-t border-gray-100 flex justify-end gap-2 bg-gray-50/50">
                    <button @click="showAddTableModal = false" class="px-4 py-2 rounded-lg text-sm font-bold text-gray-500 hover:bg-gray-100 transition">Batal</button>
                    <button @click="addTableFromModal" class="px-4 py-2 rounded-lg text-sm font-bold bg-primary text-white shadow-sm hover:bg-[#154660] transition flex items-center gap-2">Generate QR <i class="fas fa-arrow-right text-xs"></i></button>
                </div>
            </div>
        </div>"""

new_modal = r"""        <!-- MODAL: ADD TABLE -->
        <div x-show="showAddTableModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center bg-[#164A35]/40 backdrop-blur-sm" x-transition.opacity>
            <div class="bg-white rounded-[24px] w-full max-w-[420px] p-8 shadow-[0_20px_60px_rgba(22,74,53,0.15)] relative overflow-hidden" @click.away="showAddTableModal = false" x-transition>
                
                <!-- Decorative Circle -->
                <div class="absolute -top-16 -right-16 w-32 h-32 bg-[#F8F7F3] rounded-full pointer-events-none"></div>
                <div class="absolute top-4 right-4 z-10">
                    <button @click="showAddTableModal = false" class="text-[#777873] hover:text-[#202522] bg-[#F8F7F3] hover:bg-[#E3E1DC] transition-colors w-8 h-8 flex items-center justify-center rounded-full">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div class="flex items-center gap-4 mb-6 relative z-10">
                    <div class="w-12 h-12 rounded-full bg-[#DDEBDD] text-[#164A35] flex items-center justify-center text-xl shrink-0">
                        <i class="fas fa-qrcode"></i>
                    </div>
                    <div>
                        <h3 class="text-[24px] font-bold text-[#164A35] leading-tight" style="font-family: 'Playfair Display', serif;">Tambah Meja Baru</h3>
                        <p class="text-[13px] text-[#777873]">Buat QR code instan untuk meja baru.</p>
                    </div>
                </div>

                <div class="mb-8 relative z-10">
                    <label class="block text-[12px] font-bold text-[#777873] mb-2 uppercase tracking-widest">Nama / Nomor Meja</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fas fa-hashtag text-[#C5DBC5]"></i>
                        </div>
                        <input x-ref="tableInput" type="text" x-model="newTableId" @keydown.enter="addTableFromModal" placeholder="Misal: Meja 07" class="w-full bg-[#F8F7F3] border border-[#E3E1DC] rounded-[16px] pl-10 pr-4 py-3.5 text-[15px] font-bold text-[#202522] placeholder:font-medium placeholder:text-[#C5DBC5] focus:border-[#164A35] focus:ring-1 focus:ring-[#164A35] outline-none transition-all shadow-[inset_0_2px_4px_rgba(0,0,0,0.02)]">
                    </div>
                </div>

                <div class="flex gap-3 relative z-10">
                    <button @click="showAddTableModal = false" class="w-1/3 py-3.5 rounded-[14px] text-[14px] font-bold text-[#777873] bg-white border border-[#E3E1DC] hover:bg-[#F8F7F3] transition-colors text-center">Batal</button>
                    <button @click="addTableFromModal" class="flex-grow py-3.5 rounded-[14px] text-[14px] font-bold bg-[#164A35] text-white shadow-[0_4px_12px_rgba(22,74,53,0.2)] hover:bg-[#0f3526] hover:-translate-y-0.5 transition-all flex items-center justify-center gap-2">
                        Generate QR <i class="fas fa-arrow-right text-[12px]"></i>
                    </button>
                </div>
            </div>
        </div>"""

if old_modal in content:
    content = content.replace(old_modal, new_modal)
    with open('resources/views/Admin/Dashboard/dashboard.blade.php', 'w', encoding='utf-8') as f:
        f.write(content)
    print("Redesigned the Add Table Modal")
else:
    print("Could not find old modal")
