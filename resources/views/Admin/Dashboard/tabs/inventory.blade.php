<!-- Tab Inventori & Bahan Baku -->
<div x-show="currentTab === 'inventory'" x-cloak class="flex-grow p-4 md:p-8 lg:p-10 pt-6 overflow-y-auto hide-scroll bg-brewlybg">
    <div class="flex flex-col gap-8 max-w-7xl mx-auto">
        
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-primary" style="font-family: var(--oq-font-heading);">Manajemen Stok & Bahan Baku</h1>
                <p class="text-muted text-sm mt-1">Pantau stok bahan racikan, harga modal (HPP), dan riwayat mutasi bahan secara real-time.</p>
            </div>
            <div class="flex items-center gap-3">
                <button @click="openStockAdjustModal()" class="px-4 py-2.5 rounded-xl border border-line bg-white text-ink font-semibold text-sm hover:bg-ivory transition shadow-sm flex items-center gap-2">
                    <i class="fas fa-sliders-h text-primary"></i> Sesuaikan Stok / Opname
                </button>
                <button @click="openAddMaterialModal()" class="px-5 py-2.5 rounded-xl bg-primary text-white font-bold text-sm hover:bg-brand-700 transition shadow-[0_4px_12px_rgba(100,29,53,0.2)] flex items-center gap-2">
                    <i class="fas fa-plus"></i> Tambah Bahan Baku
                </button>
            </div>
        </div>

        <!-- Metric Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Total Bahan -->
            <div class="bg-gradient-to-br from-primary to-brand-700 rounded-[24px] p-6 text-white shadow-[0_12px_32px_rgba(100,29,53,0.15)] relative overflow-hidden group">
                <div class="absolute -right-6 -top-6 w-32 h-32 bg-white opacity-5 rounded-full blur-xl group-hover:scale-125 transition-transform"></div>
                <div class="flex items-center gap-4 relative z-10">
                    <div class="w-12 h-12 rounded-2xl bg-white/10 flex items-center justify-center backdrop-blur-md border border-white/10">
                        <i class="fas fa-boxes-stacked text-xl text-white"></i>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-white/80 uppercase tracking-wider">Total Item Bahan</p>
                        <h3 class="text-2xl font-black text-white mt-0.5" x-text="inventoryMetrics.total_materials">0</h3>
                    </div>
                </div>
                <div class="text-xs text-white/80 border-t border-white/10 pt-3 mt-4 flex items-center gap-2">
                    <i class="fas fa-check-circle text-xs text-[#9FE2BF]"></i>
                    <span>Bahan aktif untuk menu & racikan</span>
                </div>
            </div>

            <!-- Bahan Menipis -->
            <div class="bg-gradient-to-br from-gold-600 to-[#c76922] rounded-[24px] p-6 text-white shadow-[0_12px_32px_rgba(217,122,50,0.15)] relative overflow-hidden group">
                <div class="absolute -right-6 -top-6 w-32 h-32 bg-white opacity-5 rounded-full blur-xl group-hover:scale-125 transition-transform"></div>
                <div class="flex items-center gap-4 relative z-10">
                    <div class="w-12 h-12 rounded-2xl bg-white/10 flex items-center justify-center backdrop-blur-md border border-white/10">
                        <i class="fas fa-exclamation-triangle text-xl text-white"></i>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-white/80 uppercase tracking-wider">Stok Menipis (Alert)</p>
                        <h3 class="text-2xl font-black text-white mt-0.5" x-text="inventoryMetrics.low_stock_count">0</h3>
                    </div>
                </div>
                <div class="text-xs text-white/80 border-t border-white/10 pt-3 mt-4 flex items-center gap-2">
                    <i class="fas fa-bell text-xs text-white"></i>
                    <span>Perlu segera direstok dari supplier</span>
                </div>
            </div>

            <!-- Valuasi Nilai Stok -->
            <div class="bg-gradient-to-br from-primary to-primary-dark rounded-[24px] p-6 text-white shadow-[0_12px_32px_rgba(100,29,53,0.15)] relative overflow-hidden group">
                <div class="absolute -right-6 -top-6 w-32 h-32 bg-white opacity-5 rounded-full blur-xl group-hover:scale-125 transition-transform"></div>
                <div class="flex items-center gap-4 relative z-10">
                    <div class="w-12 h-12 rounded-2xl bg-white/10 flex items-center justify-center backdrop-blur-md border border-white/10">
                        <i class="fas fa-wallet text-xl text-white"></i>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-white/80 uppercase tracking-wider">Nilai Modal Aset Bahan</p>
                        <h3 class="text-2xl font-black text-white mt-0.5" x-text="formatRp(inventoryMetrics.total_asset_value)">Rp 0</h3>
                    </div>
                </div>
                <div class="text-xs text-white/80 border-t border-white/10 pt-3 mt-4 flex items-center gap-2">
                    <i class="fas fa-coins text-xs text-white/80"></i>
                    <span>Estimasi modal tertahan di gudang</span>
                </div>
            </div>
        </div>

        <!-- Filter & Search & Tabs Internal -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-line pb-4">
            <div class="flex gap-2">
                <button @click="inventorySubTab = 'materials'" :class="inventorySubTab === 'materials' ? 'bg-primary text-white' : 'bg-white border border-line text-muted hover:text-ink'" class="px-5 py-2 rounded-xl text-sm font-bold transition flex items-center gap-2">
                    <i class="fas fa-boxes-stacked"></i> Daftar Bahan Baku (<span x-text="rawMaterials.length"></span>)
                </button>
                <button @click="inventorySubTab = 'mutations'" :class="inventorySubTab === 'mutations' ? 'bg-primary text-white' : 'bg-white border border-line text-muted hover:text-ink'" class="px-5 py-2 rounded-xl text-sm font-bold transition flex items-center gap-2">
                    <i class="fas fa-history"></i> Riwayat Mutasi Stok
                </button>
            </div>

            <div class="flex items-center gap-3" x-show="inventorySubTab === 'materials'">
                <div class="relative">
                    <i class="fas fa-search absolute left-3.5 top-3 text-gray-400 text-sm"></i>
                    <input type="text" x-model="materialSearch" placeholder="Cari bahan..." class="pl-9 pr-4 py-2 bg-white border border-line rounded-xl text-sm outline-none focus:border-primary w-64">
                </div>
                <button @click="fetchInventory()" class="p-2 rounded-xl border border-line bg-white text-gray-600 hover:text-primary transition" title="Refresh">
                    <i class="fas fa-sync-alt" :class="isLoadingInventory ? 'fa-spin' : ''"></i>
                </button>
            </div>
        </div>

        <!-- VIEW 1: TABEL DAFTAR BAHAN BAKU -->
        <div x-show="inventorySubTab === 'materials'" class="bg-white border border-line rounded-[24px] overflow-hidden shadow-sm flex flex-col">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-ink">
                    <thead>
                        <tr class="border-b border-line text-xs font-bold uppercase tracking-wider text-muted bg-ivory">
                            <th class="p-4 pl-6">Nama Bahan</th>
                            <th class="p-4">SKU / Kode</th>
                            <th class="p-4">Satuan</th>
                            <th class="p-4">Harga Beli / Unit</th>
                            <th class="p-4">Sisa Stok</th>
                            <th class="p-4">Status</th>
                            <th class="p-4 text-center">Menu Terkait</th>
                            <th class="p-4 pr-6 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm">
                        <template x-for="item in filteredMaterials" :key="item.id">
                            <tr class="hover:bg-ivory transition group">
                                <td class="p-4 pl-6 font-bold text-ink flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-brand-100 text-primary flex items-center justify-center text-xs font-bold shrink-0">
                                        <i class="fas fa-cube"></i>
                                    </div>
                                    <span x-text="item.name"></span>
                                </td>
                                <td class="p-4 text-xs font-mono text-muted" x-text="item.sku || '-'"></td>
                                <td class="p-4 font-medium capitalize" x-text="item.unit_label || stockUnitLabel(item.unit)"></td>
                                <td class="p-4 font-mono font-semibold" x-text="formatStock(item.cost_per_unit) + ' / ' + (item.unit_label || stockUnitLabel(item.unit))"></td>
                                <td class="p-4 font-bold font-mono">
                                    <span x-text="formatStock(item.current_stock)"></span>
                                    <span class="text-xs font-normal text-gray-400" x-text="item.unit_label || stockUnitLabel(item.unit)"></span>
                                </td>
                                <td class="p-4">
                                    <template x-if="Number(item.current_stock) <= 0">
                                        <span class="px-2.5 py-1 bg-red-100 text-red-700 text-xs font-bold rounded-full flex items-center gap-1.5 w-max">
                                            <span class="w-1.5 h-1.5 rounded-full bg-red-600"></span> Habis
                                        </span>
                                    </template>
                                    <template x-if="Number(item.current_stock) > 0 && Number(item.current_stock) <= Number(item.minimum_stock)">
                                        <span class="px-2.5 py-1 bg-amber-100 text-amber-700 text-xs font-bold rounded-full flex items-center gap-1.5 w-max">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-600"></span> Menipis
                                        </span>
                                    </template>
                                    <template x-if="Number(item.current_stock) > Number(item.minimum_stock)">
                                        <span class="px-2.5 py-1 bg-brand-100 text-primary text-xs font-bold rounded-full flex items-center gap-1.5 w-max">
                                            <span class="w-1.5 h-1.5 rounded-full bg-primary"></span> Aman
                                        </span>
                                    </template>
                                </td>
                                <td class="p-4 text-center font-bold text-xs text-muted">
                                    <span class="px-2.5 py-1 bg-gray-100 rounded-lg" x-text="(item.recipes_count || 0) + ' menu'"></span>
                                </td>
                                <td class="p-4 pr-6 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <button @click="quickRestock(item)" class="px-2.5 py-1.5 rounded-lg border border-line bg-white text-primary font-bold text-xs hover:bg-brand-100 transition flex items-center gap-1.5" title="Restok">
                                            <i class="fas fa-plus-circle"></i> Restok
                                        </button>
                                        <button @click="editMaterial(item)" class="w-8 h-8 rounded-lg border border-line bg-white text-gray-500 hover:text-primary hover:border-primary transition flex items-center justify-center" title="Edit">
                                            <i class="fas fa-edit text-xs"></i>
                                        </button>
                                        <button @click="deleteMaterial(item.id)" class="w-8 h-8 rounded-lg border border-line bg-white text-gray-500 hover:text-red-600 hover:border-red-600 transition flex items-center justify-center" title="Hapus">
                                            <i class="fas fa-trash text-xs"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </template>

                        <template x-if="filteredMaterials.length === 0">
                            <tr>
                                <td colspan="8" class="p-12 text-center text-muted">
                                    <div class="w-16 h-16 rounded-full bg-ivory border border-line flex items-center justify-center text-gray-400 mx-auto mb-3">
                                        <i class="fas fa-box-open text-2xl"></i>
                                    </div>
                                    <p class="font-bold text-ink">Belum ada data bahan baku</p>
                                    <p class="text-xs text-muted mt-1">Tambahkan bahan racikan seperti Kopi, Susu, Sirup untuk mulai kalkulasi HPP otomatis.</p>
                                    <button @click="openAddMaterialModal()" class="mt-4 px-4 py-2 bg-primary text-white rounded-xl text-xs font-bold hover:bg-brand-700 transition">
                                        + Tambah Bahan Pertama
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- VIEW 2: RIWAYAT MUTASI STOK -->
        <div x-show="inventorySubTab === 'mutations'" class="bg-white border border-line rounded-[24px] overflow-hidden shadow-sm flex flex-col">
            <div class="p-5 border-b border-line bg-ivory flex justify-between items-center">
                <h3 class="font-bold text-ink">100 Mutasi Stok Terakhir</h3>
                <span class="text-xs text-muted font-medium">Audit trail keluar-masuk barang otomatis</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-ink">
                    <thead>
                        <tr class="border-b border-line text-xs font-bold uppercase tracking-wider text-muted bg-ivory">
                            <th class="p-4 pl-6">Waktu</th>
                            <th class="p-4">Nama Bahan</th>
                            <th class="p-4">Tipe</th>
                            <th class="p-4">Jumlah</th>
                            <th class="p-4">Sisa Akhir</th>
                            <th class="p-4">Keterangan</th>
                            <th class="p-4 pr-6">Petugas</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm">
                        <template x-for="mut in stockMutations" :key="mut.id">
                            <tr class="hover:bg-ivory transition">
                                <td class="p-4 pl-6 text-xs text-muted font-mono" x-text="new Date(mut.created_at).toLocaleString('id-ID')"></td>
                                <td class="p-4 font-bold text-ink" x-text="mut.raw_material ? mut.raw_material.name : '-'"></td>
                                <td class="p-4">
                                    <template x-if="mut.type === 'in'">
                                        <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-brand-100 text-primary">Restok Masuk</span>
                                    </template>
                                    <template x-if="mut.type === 'out_order'">
                                        <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-blue-50 text-blue-700">Terpakai Order</span>
                                    </template>
                                    <template x-if="mut.type === 'adjustment'">
                                        <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-purple-50 text-purple-700">Stok Opname</span>
                                    </template>
                                    <template x-if="mut.type === 'waste'">
                                        <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-red-50 text-red-700">Rusak/Expired</span>
                                    </template>
                                </td>
                                <td class="p-4 font-bold font-mono">
                                    <span :class="Number(mut.amount) > 0 ? 'text-green-600' : 'text-red-600'" x-text="(Number(mut.amount) > 0 ? '+' : '') + formatStock(mut.amount)"></span>
                                    <span class="text-xs text-gray-400 font-normal" x-text="mut.unit_label || stockUnitLabel(mut.unit || mut.raw_material?.unit)"></span>
                                </td>
                                <td class="p-4 font-mono text-xs font-bold text-ink">
                                    <span x-text="formatStock(mut.balance_after)"></span>
                                    <span class="text-[10px] text-gray-400 font-normal" x-text="mut.unit_label || stockUnitLabel(mut.unit || mut.raw_material?.unit)"></span>
                                </td>
                                <td class="p-4 text-xs text-muted" x-text="mut.notes || '-'"></td>
                                <td class="p-4 pr-6 text-xs text-ink font-medium" x-text="mut.user ? mut.user.name : 'Sistem'"></td>
                            </tr>
                        </template>

                        <template x-if="stockMutations.length === 0">
                            <tr>
                                <td colspan="7" class="p-8 text-center text-gray-400 text-xs">Belum ada catatan mutasi stok.</td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <!-- MODAL: TAMBAH / EDIT BAHAN BAKU -->
    <div x-show="showMaterialModal" x-cloak class="fixed inset-0 z-[120] flex items-center justify-center p-4 font-sans">
        <div @click="showMaterialModal = false" class="absolute inset-0 bg-ink/30 backdrop-blur-sm transition-opacity"></div>
        <div class="relative bg-white w-full max-w-lg rounded-[28px] p-8 shadow-[0_10px_35px_rgba(0,0,0,0.05)] flex flex-col border border-line" @keydown.escape.window="showMaterialModal = false" x-transition>
            
            <div class="flex justify-between items-start mb-6 pb-4 border-b border-dashed border-line">
                <div class="flex gap-4 items-center">
                    <div class="w-12 h-12 rounded-[14px] bg-primary text-white flex items-center justify-center shadow-sm">
                        <i class="fas fa-cube text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-primary" style="font-family: var(--oq-font-heading);" x-text="materialForm.id ? 'Edit Bahan Baku' : 'Tambah Bahan Baku'"></h2>
                        <p class="text-xs text-muted">Tentukan takaran, satuan, dan harga modal beli.</p>
                    </div>
                </div>
                <button @click="showMaterialModal = false" class="text-gray-400 hover:text-gray-700 w-8 h-8 flex items-center justify-center rounded-full bg-gray-50">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="flex flex-col gap-4">
                <div>
                    <label class="block text-xs font-bold text-muted uppercase tracking-wider mb-1">Nama Bahan Baku *</label>
                    <input type="text" x-model="materialForm.name" placeholder="cth: Susu Fresh Milk Greenfields, Biji Arabika Gayo" class="w-full bg-ivory border border-line rounded-xl px-4 py-3 text-sm font-bold text-ink focus:border-primary outline-none">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="stock-unit" class="block text-xs font-bold text-muted uppercase tracking-wider mb-1">Satuan Dasar *</label>
                        <select id="stock-unit" x-model="materialForm.unit" :disabled="editingUnitLocked" aria-describedby="stock-unit-help" class="w-full bg-ivory border border-gold-400 rounded-xl px-4 py-3 text-sm font-bold text-primary disabled:opacity-70">
                            <template x-for="(label, unit) in stockUnits" :key="unit"><option :value="unit" x-text="label"></option></template>
                            <template x-if="materialForm.unit && !stockUnits[materialForm.unit]"><option :value="materialForm.unit" x-text="materialForm.unit + ' (satuan lama)'"></option></template>
                        </select>
                        <p id="stock-unit-help" class="text-xs text-gray-600 mt-2" x-text="editingUnitLocked ? 'Satuan dikunci karena bahan sudah memiliki stok, resep, atau mutasi.' : 'Satuan berlaku untuk saldo awal, sisa stok, minimum stok, harga modal, dan resep.'"></p>
                        <p class="text-xs text-gray-600 mt-2">Dus, Botol, dan Pack memakai isi kemasan masing-masing bahan. Tidak dikonversi otomatis ke Pcs.</p>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-muted uppercase tracking-wider mb-1">SKU / Kode Bahan</label>
                        <input type="text" x-model="materialForm.sku" placeholder="cth: RM-COF-01" class="w-full bg-ivory border border-line rounded-xl px-4 py-3 text-sm font-bold text-ink focus:border-primary outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-muted uppercase tracking-wider mb-1">Harga Beli / Satuan (Rp) *</label>
                        <input type="number" step="0.000001" min="0" x-model="materialForm.cost_per_unit" placeholder="0" class="w-full bg-ivory border border-line rounded-xl px-4 py-3 text-sm font-bold text-ink focus:border-primary outline-none">
                        <span class="text-[10px] text-gray-400 mt-0.5 block">Modal per 1 <span x-text="stockUnitLabel(materialForm.unit)"></span></span>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-muted uppercase tracking-wider mb-1">Batas Stok Minimum *</label>
                        <input type="number" step="0.000001" min="0" x-model="materialForm.minimum_stock" :aria-label="'Batas stok minimum dalam ' + stockUnitLabel(materialForm.unit)" placeholder="0" class="w-full bg-ivory border border-line rounded-xl px-4 py-3 text-sm font-bold text-ink focus:border-primary outline-none">
                        <span class="text-[10px] text-gray-400 mt-0.5 block">Alert jika di bawah batas ini (<span x-text="stockUnitLabel(materialForm.unit)"></span>)</span>
                    </div>
                </div>

                <div x-show="!materialForm.id">
                    <label class="block text-xs font-bold text-muted uppercase tracking-wider mb-1">Saldo Stok Awal</label>
                    <input type="number" step="0.000001" min="0" x-model="materialForm.current_stock" :aria-label="'Stok awal dalam ' + stockUnitLabel(materialForm.unit)" placeholder="0" class="w-full bg-ivory border border-line rounded-xl px-4 py-3 text-sm font-bold text-ink focus:border-primary outline-none">
                </div>
            </div>

            <div class="mt-8 pt-4 border-t border-line flex justify-end gap-3">
                <button @click="showMaterialModal = false" class="px-5 py-2.5 rounded-xl border border-line text-gray-600 font-bold text-sm hover:bg-gray-50 transition">
                    Batal
                </button>
                <button @click="saveMaterial()" :disabled="isSavingMaterial" class="px-6 py-2.5 rounded-xl bg-primary text-white font-bold text-sm hover:bg-brand-700 transition flex items-center gap-2">
                    <i class="fas fa-spinner fa-spin" x-show="isSavingMaterial"></i>
                    <span x-text="isSavingMaterial ? 'Menyimpan...' : 'Simpan Bahan'"></span>
                </button>
            </div>
        </div>
    </div>

    <!-- MODAL: PENYESUAIAN STOK (RESTOK / OPNAME / WASTE) -->
    <div x-show="showAdjustModal" x-cloak class="fixed inset-0 z-[120] flex items-center justify-center p-4 font-sans">
        <div @click="showAdjustModal = false" class="absolute inset-0 bg-ink/30 backdrop-blur-sm transition-opacity"></div>
        <div class="relative bg-white w-full max-w-lg rounded-[28px] p-8 shadow-[0_10px_35px_rgba(0,0,0,0.05)] flex flex-col border border-line" @keydown.escape.window="showAdjustModal = false" x-transition>
            
            <div class="flex justify-between items-start mb-6 pb-4 border-b border-dashed border-line">
                <div class="flex gap-4 items-center">
                    <div class="w-12 h-12 rounded-[14px] bg-gold-600 text-white flex items-center justify-center shadow-sm">
                        <i class="fas fa-sliders-h text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-primary" style="font-family: var(--oq-font-heading);">Penyesuaian Stok</h2>
                        <p class="text-xs text-muted">Restok belanja, stok opname, atau catat barang tumpah/rusak.</p>
                    </div>
                </div>
                <button @click="showAdjustModal = false" class="text-gray-400 hover:text-gray-700 w-8 h-8 flex items-center justify-center rounded-full bg-gray-50">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="flex flex-col gap-4">
                <div>
                    <label class="block text-xs font-bold text-muted uppercase tracking-wider mb-1">Pilih Bahan Baku *</label>
                    <select x-model="adjustForm.raw_material_id" class="w-full bg-ivory border border-line rounded-xl px-4 py-3 text-sm font-bold text-ink focus:border-primary outline-none">
                        <option value="" disabled>-- Pilih Bahan --</option>
                        <template x-for="m in rawMaterials" :key="m.id">
                            <option :value="m.id" x-text="m.name + ' (Sisa saat ini: ' + formatStock(m.current_stock) + ' ' + (m.unit_label || stockUnitLabel(m.unit)) + ')'"></option>
                        </template>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-muted uppercase tracking-wider mb-1">Tipe Mutasi *</label>
                    <div class="grid grid-cols-3 gap-2">
                        <button type="button" @click="adjustForm.type = 'in'" :class="adjustForm.type === 'in' ? 'bg-primary text-white font-bold' : 'bg-ivory border border-line text-gray-700 font-semibold'" class="py-2.5 rounded-xl text-xs transition">
                            <i class="fas fa-plus-circle mr-1"></i> Restok Masuk
                        </button>
                        <button type="button" @click="adjustForm.type = 'adjustment'" :class="adjustForm.type === 'adjustment' ? 'bg-primary text-white font-bold' : 'bg-ivory border border-line text-gray-700 font-semibold'" class="py-2.5 rounded-xl text-xs transition">
                            <i class="fas fa-clipboard-check mr-1"></i> Stok Opname
                        </button>
                        <button type="button" @click="adjustForm.type = 'waste'" :class="adjustForm.type === 'waste' ? 'bg-red-600 text-white font-bold' : 'bg-ivory border border-line text-gray-700 font-semibold'" class="py-2.5 rounded-xl text-xs transition">
                            <i class="fas fa-trash-alt mr-1"></i> Rusak / Basi
                        </button>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-muted uppercase tracking-wider mb-1">
                        <span x-text="adjustForm.type === 'adjustment' ? 'Jumlah Riil Stok Fisik di Gudang *' : 'Jumlah Bahan *'"></span>
                    </label>
                    <input type="number" step="0.000001" min="0" :min="adjustForm.type === 'adjustment' ? 0 : 0.000001" x-model="adjustForm.amount" placeholder="0" class="w-full bg-ivory border border-line rounded-xl px-4 py-3 text-sm font-bold text-ink focus:border-primary outline-none">
                </div>

                <p class="text-sm font-semibold text-primary" x-text="'Semua jumlah dalam ' + getMaterialUnit(adjustForm.raw_material_id) + '. Stok opname boleh 0.'"></p>
                <div x-show="adjustForm.type === 'in'">
                    <label class="block text-xs font-bold text-muted uppercase tracking-wider mb-1">Harga Beli Baru / Satuan (Opsional)</label>
                    <input type="number" step="0.000001" min="0" x-model="adjustForm.new_cost_per_unit" placeholder="Kosongkan jika harga modal tidak berubah" class="w-full bg-ivory border border-line rounded-xl px-4 py-3 text-sm font-bold text-ink focus:border-primary outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-muted uppercase tracking-wider mb-1">Catatan / Alasan</label>
                    <input type="text" x-model="adjustForm.notes" placeholder="cth: Belanja dari Toko Jaya, Gelas retak, Opname akhir bulan" class="w-full bg-ivory border border-line rounded-xl px-4 py-3 text-sm text-ink focus:border-primary outline-none">
                </div>
            </div>

            <div class="mt-8 pt-4 border-t border-line flex justify-end gap-3">
                <button @click="showAdjustModal = false" class="px-5 py-2.5 rounded-xl border border-line text-gray-600 font-bold text-sm hover:bg-gray-50 transition">
                    Batal
                </button>
                <button @click="submitStockAdjustment()" :disabled="isSavingAdjustment" class="px-6 py-2.5 rounded-xl bg-primary text-white font-bold text-sm hover:bg-brand-700 transition flex items-center gap-2">
                    <i class="fas fa-spinner fa-spin" x-show="isSavingAdjustment"></i>
                    <span x-text="isSavingAdjustment ? 'Memproses...' : 'Terapkan Penyesuaian'"></span>
                </button>
            </div>
        </div>
    </div>

</div>
