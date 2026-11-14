<div x-show="currentTab === 'addons'" x-cloak class="flex-grow p-4 md:p-6 lg:p-7 overflow-y-auto hide-scroll bg-white font-['DM_Sans',sans-serif]" style="font-feature-settings: 'cv02', 'cv03', 'cv04', 'cv11';">

    <!-- Header Page Title -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <div class="flex items-center gap-2.5">
                <h1 class="text-xl md:text-2xl font-bold text-[#111827] tracking-tight">Kelola Add-ons & Modifiers</h1>
                <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-[#fff0e6] text-[#df5826] border border-[#ffd2b8]" x-text="(modifierGroups.length) + ' Grup Add-on'"></span>
            </div>
            <p class="text-xs md:text-sm text-[#7f837e] mt-1 font-normal">Atur pilihan kustomisasi menu seperti pilihan tingkat es (Normal Ice, Less Ice), gula, dan topping tambahan (Extra Shot, Boba).</p>
        </div>
        <button type="button" @click="openCreateAddonModal()" class="px-5 py-2.5 rounded-[12px] bg-[#df5826] hover:bg-[#c94d00] text-white font-bold text-xs flex items-center justify-center gap-2 shadow-xs transition shrink-0 cursor-pointer">
            <i class="fas fa-plus"></i>
            <span>Tambah Grup Add-on</span>
        </button>
    </div>

    <!-- Quick Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-[16px] border border-[#e9ebf0] p-4 sm:p-5 shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-semibold text-[#7f837e] uppercase tracking-wider">Total Grup Add-on</span>
                <div class="w-8 h-8 rounded-[10px] bg-[#fff0e6] text-[#df5826] flex items-center justify-center text-sm">
                    <i class="fas fa-layer-group"></i>
                </div>
            </div>
            <p class="text-2xl font-bold text-[#111827] mt-2" x-text="modifierGroups.length"></p>
            <p class="text-[11px] text-[#7f837e] mt-0.5">Semua grup kustomisasi menu</p>
        </div>

        <div class="bg-white rounded-[16px] border border-[#e9ebf0] p-4 sm:p-5 shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-semibold text-[#7f837e] uppercase tracking-wider">Pilihan Tunggal (Radio)</span>
                <div class="w-8 h-8 rounded-[10px] bg-sky-50 text-sky-600 flex items-center justify-center text-sm">
                    <i class="fas fa-dot-circle"></i>
                </div>
            </div>
            <p class="text-2xl font-bold text-sky-600 mt-2" x-text="modifierGroups.filter(g => g.selection_type === 'SINGLE').length"></p>
            <p class="text-[11px] text-[#7f837e] mt-0.5">Opsi 1 pilihan (cth: Level Es, Sugar)</p>
        </div>

        <div class="bg-white rounded-[16px] border border-[#e9ebf0] p-4 sm:p-5 shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-semibold text-[#7f837e] uppercase tracking-wider">Pilihan Banyak (Checkbox)</span>
                <div class="w-8 h-8 rounded-[10px] bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm">
                    <i class="fas fa-check-square"></i>
                </div>
            </div>
            <p class="text-2xl font-bold text-emerald-600 mt-2" x-text="modifierGroups.filter(g => g.selection_type === 'MULTIPLE').length"></p>
            <p class="text-[11px] text-[#7f837e] mt-0.5">Bisa pilih banyak (cth: Extra Topping)</p>
        </div>
    </div>

    <!-- Search & Filter Controls -->
    <div class="bg-white rounded-[16px] border border-[#e9ebf0] p-3.5 mb-6 shadow-2xs flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
        <div class="relative flex-1 max-w-md">
            <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
            <input type="text" x-model="addonSearch" placeholder="Cari nama grup atau opsi addon..." class="w-full bg-[#f8f9fa] border border-[#e9ebf0] rounded-[10px] pl-9 pr-3.5 py-2 text-[12.5px] text-[#111827] placeholder:text-[#9ca3af] focus:bg-white focus:border-[#df5826] focus:ring-1 focus:ring-[#df5826] outline-none transition">
        </div>

        <div class="flex items-center gap-1.5 overflow-x-auto hide-scroll shrink-0">
            <button type="button" @click="addonFilter = 'all'" :class="addonFilter === 'all' ? 'bg-[#df5826] text-white font-bold' : 'bg-gray-100 text-[#53565a] hover:bg-gray-200'" class="px-3 py-1.5 rounded-[8px] text-[11.5px] transition cursor-pointer">
                Semua (<span x-text="modifierGroups.length"></span>)
            </button>
            <button type="button" @click="addonFilter = 'SINGLE'" :class="addonFilter === 'SINGLE' ? 'bg-[#df5826] text-white font-bold' : 'bg-gray-100 text-[#53565a] hover:bg-gray-200'" class="px-3 py-1.5 rounded-[8px] text-[11.5px] transition cursor-pointer">
                Pilihan Tunggal (Radio)
            </button>
            <button type="button" @click="addonFilter = 'MULTIPLE'" :class="addonFilter === 'MULTIPLE' ? 'bg-[#df5826] text-white font-bold' : 'bg-gray-100 text-[#53565a] hover:bg-gray-200'" class="px-3 py-1.5 rounded-[8px] text-[11.5px] transition cursor-pointer">
                Pilihan Banyak (Checkbox)
            </button>
        </div>
    </div>

    <!-- Addon Groups List -->
    <div class="space-y-4">
        <template x-for="group in filteredModifierGroups" :key="group.id">
            <div class="bg-white rounded-[16px] border border-[#e9ebf0] hover:border-[#dcdfe5] p-5 shadow-2xs transition">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-4 border-b border-[#f3f4f6]">
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-[12px] flex items-center justify-center shrink-0 mt-0.5"
                             :class="group.selection_type === 'SINGLE' ? 'bg-sky-50 text-sky-600' : 'bg-emerald-50 text-emerald-600'">
                            <i :class="group.selection_type === 'SINGLE' ? 'fas fa-dot-circle text-base' : 'fas fa-check-square text-base'"></i>
                        </div>
                        <div>
                            <div class="flex items-center gap-2 flex-wrap">
                                <h3 class="font-bold text-[15px] text-[#111827]" x-text="group.name"></h3>
                                
                                <!-- Selection Type Badge -->
                                <span class="px-2 py-0.5 rounded-[6px] text-[10.5px] font-bold"
                                      :class="group.selection_type === 'SINGLE' ? 'bg-sky-50 text-sky-700 border border-sky-200' : 'bg-emerald-50 text-emerald-700 border border-emerald-200'">
                                    <span x-text="group.selection_type === 'SINGLE' ? 'Pilih 1 (Radio)' : 'Bisa Banyak (Checkbox)'"></span>
                                </span>

                                <!-- Required Badge -->
                                <template x-if="group.is_required">
                                    <span class="px-2 py-0.5 rounded-[6px] text-[10.5px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                        Wajib Dipilih
                                    </span>
                                </template>

                                <!-- Applied Products Count Badge -->
                                <span class="px-2 py-0.5 rounded-[6px] text-[10.5px] font-medium bg-[#f8f9fa] text-[#53565a] border border-[#e9ebf0]">
                                    <i class="fas fa-utensils text-[9px] mr-1 text-[#df5826]"></i>
                                    <span x-text="(group.products_count !== undefined ? group.products_count : (group.products?.length || 0)) + ' Menu'"></span>
                                </span>
                            </div>
                            <p class="text-[12px] text-[#7f837e] mt-1" x-text="group.selection_type === 'SINGLE' ? 'Customer hanya dapat memilih 1 opsi dari daftar ini.' : 'Customer dapat memilih lebih dari satu opsi sekaligus.'"></p>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center gap-2.5 shrink-0 self-end md:self-center">
                        <!-- Toggle Switch -->
                        <label class="inline-flex items-center gap-2 cursor-pointer select-none" title="Aktif/Nonaktifkan grup">
                            <input type="checkbox" :checked="group.is_active" @change="toggleAddonStatus(group)" class="sr-only">
                            <div class="relative w-9 h-5 rounded-full transition-colors duration-200 ease-in-out p-0.5 shadow-inner"
                                 :class="group.is_active ? 'bg-[#df5826]' : 'bg-gray-300'">
                                <div class="w-4 h-4 bg-white rounded-full shadow-md transform transition-transform duration-200 ease-in-out"
                                     :class="group.is_active ? 'translate-x-4' : 'translate-x-0'"></div>
                            </div>
                            <span class="text-[11px] font-semibold" :class="group.is_active ? 'text-[#df5826]' : 'text-gray-400'" x-text="group.is_active ? 'Aktif' : 'Nonaktif'"></span>
                        </label>

                        <div class="h-4 w-px bg-gray-200"></div>

                        <!-- Edit Button -->
                        <button type="button" @click="editAddonGroup(group)" class="px-3 py-1.5 rounded-[8px] bg-white border border-[#e9ebf0] hover:border-[#df5826] text-[#53565a] hover:text-[#df5826] text-[11.5px] font-medium flex items-center gap-1.5 transition shadow-2xs cursor-pointer">
                            <i class="fas fa-edit text-xs"></i>
                            <span>Edit</span>
                        </button>

                        <!-- Delete Button -->
                        <button type="button" @click="deleteAddonGroup(group)" class="w-8 h-8 rounded-[8px] bg-white border border-[#e9ebf0] hover:border-red-500 text-gray-400 hover:text-red-500 flex items-center justify-center transition shadow-2xs cursor-pointer" title="Hapus grup">
                            <i class="fas fa-trash-alt text-xs"></i>
                        </button>
                    </div>
                </div>

                <!-- Modifiers Items List Preview (Pills) -->
                <div class="pt-3.5">
                    <p class="text-[11px] font-bold text-[#8b8e93] uppercase tracking-wider mb-2">Daftar Pilihan / Opsi (<span x-text="group.modifiers?.length || 0"></span>)</p>
                    <div class="flex flex-wrap gap-2">
                        <template x-for="item in (group.modifiers || [])" :key="item.id">
                            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-[10px] bg-[#f8f9fa] border border-[#e9ebf0] text-[12px] text-[#111827]">
                                <span class="w-1.5 h-1.5 rounded-full" :class="item.is_active ? 'bg-emerald-500' : 'bg-gray-400'"></span>
                                <span class="font-medium" x-text="item.name"></span>
                                <span class="font-bold text-[11px] px-1.5 py-0.5 rounded-[6px]"
                                      :class="Number(item.price_adjustment) > 0 ? 'bg-[#fff0e6] text-[#df5826]' : 'bg-gray-200/60 text-gray-600'"
                                      x-text="Number(item.price_adjustment) > 0 ? '+Rp' + formatRp(item.price_adjustment) : 'Gratis (Rp 0)'"></span>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Attached Products Tags Preview -->
                <template x-if="group.products && group.products.length > 0">
                    <div class="mt-3 pt-3 border-t border-dashed border-[#f1f3f5] flex items-center gap-2 text-[11.5px] text-[#7f837e] overflow-hidden">
                        <i class="fas fa-link text-[#df5826] text-xs shrink-0"></i>
                        <span class="shrink-0 font-medium text-[#111827]">Terpasang di:</span>
                        <div class="flex items-center gap-1.5 truncate">
                            <template x-for="(prod, pIdx) in group.products.slice(0, 5)" :key="prod.id">
                                <span class="inline-block px-2 py-0.5 bg-white border border-[#e9ebf0] rounded-[6px] text-[11px] font-medium text-[#53565a] truncate max-w-[120px]" x-text="prod.name"></span>
                            </template>
                            <template x-if="group.products.length > 5">
                                <span class="text-[11px] text-[#7f837e] font-semibold" x-text="'+' + (group.products.length - 5) + ' menu lainnya'"></span>
                            </template>
                        </div>
                    </div>
                </template>
            </div>
        </template>

        <!-- Empty State -->
        <template x-if="filteredModifierGroups.length === 0">
            <div class="bg-white rounded-[20px] border border-dashed border-[#d8dce2] p-10 text-center">
                <div class="w-16 h-16 rounded-full bg-[#fff6f0] text-[#df5826] flex items-center justify-center mx-auto mb-3.5 shadow-2xs">
                    <i class="fas fa-layer-group text-2xl"></i>
                </div>
                <h3 class="text-base font-bold text-[#111827]">Belum Ada Grup Add-on</h3>
                <p class="text-xs text-[#7f837e] max-w-md mx-auto mt-1 mb-5">Buat grup add-on pertama Anda (misal: "Pilihan Es" dengan opsi Normal Ice, Less Ice, No Ice) agar dapat dipilih saat pelanggan atau kasir memesan menu.</p>
                <button type="button" @click="openCreateAddonModal()" class="px-5 py-2.5 rounded-[12px] bg-[#df5826] hover:bg-[#c94d00] text-white font-bold text-xs inline-flex items-center gap-2 shadow-xs transition cursor-pointer">
                    <i class="fas fa-plus"></i>
                    <span>Buat Grup Add-on Sekarang</span>
                </button>
            </div>
        </template>
    </div>

    <!-- MODAL: CREATE / EDIT MODIFIER GROUP -->
    <div x-show="addonModal.show" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-6 font-sans">
        <!-- Backdrop -->
        <div @click="addonModal.show = false" class="absolute inset-0 bg-black/50 backdrop-blur-xs transition-opacity"></div>
        
        <!-- Modal Content -->
        <div class="relative w-full max-w-2xl mx-auto bg-white rounded-[24px] shadow-2xl flex flex-col max-h-[92vh] overflow-hidden border border-[#e9ebf0]"
             @keydown.escape.window="addonModal.show = false" x-transition>
            
            <!-- Modal Header -->
            <div class="p-6 border-b border-[#e9ebf0] flex items-center justify-between shrink-0 bg-[#fafbfc]">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-[12px] bg-[#fff6f0] text-[#df5826] flex items-center justify-center shadow-2xs shrink-0">
                        <i class="fas fa-layer-group text-base"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-[#111827] leading-tight" x-text="addonModal.id ? 'Edit Grup Add-on' : 'Tambah Grup Add-on Baru'"></h2>
                        <p class="text-xs text-[#7f837e]">Kelola nama grup, jenis pilihan, dan daftar opsi tambahannya.</p>
                    </div>
                </div>
                <button type="button" @click="addonModal.show = false" class="w-8 h-8 rounded-full bg-white border border-[#e9ebf0] text-gray-400 hover:text-gray-600 flex items-center justify-center hover:bg-gray-100 transition cursor-pointer">
                    <i class="fas fa-times text-xs"></i>
                </button>
            </div>

            <!-- Modal Scrollable Body -->
            <div class="p-6 overflow-y-auto space-y-5 custom-scroll flex-1">
                <!-- Group Name Input -->
                <div>
                    <label class="block text-xs font-bold text-[#111827] mb-1.5">Nama Grup Add-on <span class="text-red-500">*</span></label>
                    <input type="text" x-model="addonModal.name" placeholder="Contoh: Pilihan Es (Ice Level), Tingkat Manis, Extra Topping..." class="w-full bg-white border border-[#e9ebf0] rounded-[10px] px-3.5 py-2.5 text-[13px] text-[#111827] placeholder:text-[#9ca3af] focus:border-[#df5826] focus:ring-1 focus:ring-[#df5826] outline-none">
                </div>

                <!-- Selection Type Cards (Single vs Multiple) -->
                <div>
                    <label class="block text-xs font-bold text-[#111827] mb-2">Tipe Pilihan Pelanggan <span class="text-red-500">*</span></label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <!-- Single Choice (Radio) -->
                        <label class="p-3.5 rounded-[14px] border-2 cursor-pointer transition flex items-start gap-3"
                               :class="addonModal.selection_type === 'SINGLE' ? 'border-[#df5826] bg-[#fffaf5]' : 'border-[#e9ebf0] hover:border-gray-300 bg-white'">
                            <input type="radio" value="SINGLE" x-model="addonModal.selection_type" class="sr-only">
                            <div class="w-4 h-4 rounded-full border-2 mt-0.5 flex items-center justify-center shrink-0"
                                 :class="addonModal.selection_type === 'SINGLE' ? 'border-[#df5826]' : 'border-gray-300'">
                                <div class="w-2 h-2 rounded-full bg-[#df5826]" x-show="addonModal.selection_type === 'SINGLE'"></div>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-[#111827]">Pilihan Tunggal (Radio)</p>
                                <p class="text-[11px] text-[#7f837e] mt-0.5 leading-snug">Customer hanya bisa memilih 1 opsi. <strong>Cocok untuk: Normal Ice, Less Ice, No Ice.</strong></p>
                            </div>
                        </label>

                        <!-- Multiple Choice (Checkbox) -->
                        <label class="p-3.5 rounded-[14px] border-2 cursor-pointer transition flex items-start gap-3"
                               :class="addonModal.selection_type === 'MULTIPLE' ? 'border-[#df5826] bg-[#fffaf5]' : 'border-[#e9ebf0] hover:border-gray-300 bg-white'">
                            <input type="radio" value="MULTIPLE" x-model="addonModal.selection_type" class="sr-only">
                            <div class="w-4 h-4 rounded-[4px] border-2 mt-0.5 flex items-center justify-center shrink-0"
                                 :class="addonModal.selection_type === 'MULTIPLE' ? 'border-[#df5826] bg-[#df5826]' : 'border-gray-300'">
                                <i class="fas fa-check text-[9px] text-white" x-show="addonModal.selection_type === 'MULTIPLE'"></i>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-[#111827]">Pilihan Banyak (Checkbox)</p>
                                <p class="text-[11px] text-[#7f837e] mt-0.5 leading-snug">Customer bebas memilih lebih dari 1 opsi. <strong>Cocok untuk: Extra Shot, Boba, Topping Keju.</strong></p>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Target Kategori & Wajib Pilihan Strip -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-1">
                    <div>
                        <label class="block text-xs font-bold text-[#111827] mb-1.5">Target Kategori</label>
                        <select x-model="addonModal.category_target" class="w-full bg-white border border-[#e9ebf0] rounded-[10px] px-3.5 py-2 text-[12.5px] text-[#111827] focus:border-[#df5826] outline-none">
                            <option value="all">Semua Kategori (Umum)</option>
                            <option value="beverage">Khusus Minuman</option>
                            <option value="food">Khusus Makanan</option>
                        </select>
                    </div>

                    <div class="flex items-center sm:pt-6">
                        <label class="inline-flex items-center gap-2.5 cursor-pointer select-none">
                            <input type="checkbox" x-model="addonModal.is_required" class="rounded text-[#df5826] focus:ring-[#df5826] w-4 h-4">
                            <span class="text-xs font-bold text-[#111827]">Wajib dipilih oleh customer?</span>
                        </label>
                    </div>
                </div>

                <!-- Dynamic Items (Options & Prices) -->
                <div class="pt-2">
                    <div class="flex items-center justify-between mb-2.5">
                        <label class="text-xs font-bold text-[#111827]">Daftar Pilihan / Opsi <span class="text-red-500">*</span></label>
                        <button type="button" @click="addAddonItemRow()" class="text-xs font-bold text-[#df5826] hover:text-[#c94d00] flex items-center gap-1 cursor-pointer">
                            <i class="fas fa-plus text-[10px]"></i>
                            <span>Tambah Opsi</span>
                        </button>
                    </div>

                    <div class="space-y-2">
                        <template x-for="(item, idx) in addonModal.items" :key="idx">
                            <div class="flex items-center gap-2 p-2.5 rounded-[12px] bg-[#f8f9fa] border border-[#e9ebf0]">
                                <div class="flex-1">
                                    <input type="text" x-model="item.name" placeholder="Nama opsi (cth: Less Ice)" class="w-full bg-white border border-[#e9ebf0] rounded-[8px] px-3 py-1.5 text-xs text-[#111827] outline-none focus:border-[#df5826]">
                                </div>
                                <div class="w-32 relative">
                                    <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-[11px] text-gray-400 font-semibold pointer-events-none">+Rp</span>
                                    <input type="number" min="0" step="500" x-model.number="item.price_adjustment" placeholder="0" class="w-full bg-white border border-[#e9ebf0] rounded-[8px] pl-9 pr-2 py-1.5 text-xs font-bold text-[#df5826] outline-none focus:border-[#df5826]">
                                </div>
                                <button type="button" @click="removeAddonItemRow(idx)" class="w-7 h-7 rounded-[8px] bg-white border border-[#e9ebf0] text-gray-400 hover:text-red-500 hover:border-red-200 flex items-center justify-center transition shrink-0 cursor-pointer" title="Hapus opsi">
                                    <i class="fas fa-times text-xs"></i>
                                </button>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Terapkan ke Menu (Interactive Checklist) -->
                <div class="pt-3 border-t border-[#f1f3f5]" x-data="{ searchProd: '' }">
                    <div class="flex items-center justify-between mb-2">
                        <div>
                            <label class="text-xs font-bold text-[#111827]">Terapkan ke Menu Toko (Opsional)</label>
                            <p class="text-[11px] text-[#7f837e]">Centang menu yang langsung terhubung ke grup addon ini.</p>
                        </div>
                        <span class="text-xs font-bold text-[#df5826]" x-text="addonModal.product_ids.length + ' menu dipilih'"></span>
                    </div>

                    <!-- Search & Quick Selection -->
                    <div class="flex items-center gap-2 mb-2.5">
                        <div class="relative flex-1">
                            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                            <input type="text" x-model="searchProd" placeholder="Cari menu..." class="w-full bg-[#f8f9fa] border border-[#e9ebf0] rounded-[8px] pl-8 pr-3 py-1.5 text-xs text-[#111827] outline-none focus:bg-white focus:border-[#df5826]">
                        </div>
                        <button type="button" @click="addonModal.product_ids = shopProducts.map(p => p.id)" class="px-2.5 py-1.5 rounded-[8px] bg-gray-100 text-[11px] font-semibold text-gray-700 hover:bg-gray-200 shrink-0">
                            Pilih Semua
                        </button>
                        <button type="button" @click="addonModal.product_ids = []" class="px-2.5 py-1.5 rounded-[8px] bg-gray-100 text-[11px] font-semibold text-gray-700 hover:bg-gray-200 shrink-0">
                            Reset
                        </button>
                    </div>

                    <!-- Product Checklist Scroll Area -->
                    <div class="max-h-40 overflow-y-auto space-y-1 p-2 bg-[#f8f9fa] rounded-[12px] border border-[#e9ebf0] custom-scroll">
                        <template x-for="prod in shopProducts.filter(p => !searchProd || p.name.toLowerCase().includes(searchProd.toLowerCase()))" :key="prod.id">
                            <label class="flex items-center justify-between p-2 rounded-[8px] bg-white border border-[#f1f3f5] hover:border-gray-300 cursor-pointer transition text-xs">
                                <div class="flex items-center gap-2.5 truncate">
                                    <input type="checkbox" :value="prod.id" x-model.number="addonModal.product_ids" class="rounded text-[#df5826] focus:ring-[#df5826] w-3.5 h-3.5">
                                    <span class="font-medium text-[#111827] truncate" x-text="prod.name"></span>
                                </div>
                                <span class="text-[11px] text-[#7f837e] font-mono shrink-0" x-text="'Rp ' + formatRp(prod.price)"></span>
                            </label>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="p-4 px-6 border-t border-[#e9ebf0] flex items-center justify-end gap-3 shrink-0 bg-[#fafbfc]">
                <button type="button" @click="addonModal.show = false" class="px-5 py-2.5 rounded-[10px] font-semibold text-xs bg-white border border-[#e9ebf0] text-[#53565a] hover:bg-gray-50 transition cursor-pointer">
                    Batal
                </button>
                <button type="button" @click="saveAddonGroup()" :disabled="isSavingAddon" class="px-6 py-2.5 rounded-[10px] font-bold text-xs bg-[#df5826] hover:bg-[#c94d00] text-white transition shadow-xs flex items-center gap-2 cursor-pointer disabled:opacity-50">
                    <i :class="isSavingAddon ? 'fas fa-spinner fa-spin' : 'fas fa-save'"></i>
                    <span x-text="isSavingAddon ? 'Menyimpan...' : 'Simpan Grup Add-on'"></span>
                </button>
            </div>
        </div>
    </div>
</div>
