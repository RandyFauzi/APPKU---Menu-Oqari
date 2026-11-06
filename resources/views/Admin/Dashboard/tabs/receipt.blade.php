<div x-show="currentTab === 'receipt'" x-cloak class="flex-grow bg-ivory overflow-auto hide-scroll relative"
     x-data="{ 
        receiptTab: 'customer', 
        printerModal: { show: false, isEdit: false, index: -1, id: null, name: '', location: 'cashier', connection_type: 'browser', ip_address: '', port: 9100, paper_width: '58mm', category_ids: [], is_active: true, auto_print: false },
        openAddPrinter() {
            this.printerModal = { show: true, isEdit: false, index: -1, id: null, name: '', location: 'kitchen', connection_type: 'browser', ip_address: '192.168.1.200', port: 9100, paper_width: '58mm', category_ids: [], is_active: true, auto_print: true };
        },
        openEditPrinter(idx) {
            const p = this.settings.printers[idx];
            this.printerModal = {
                show: true, isEdit: true, index: idx, id: p.id || null,
                name: p.name || '', location: p.location || 'cashier',
                connection_type: p.connection_type || 'browser',
                ip_address: p.ip_address || '', port: p.port || 9100,
                paper_width: p.paper_width || '58mm',
                category_ids: Array.isArray(p.category_ids) ? [...p.category_ids] : [],
                is_active: p.is_active ?? true, auto_print: p.auto_print ?? false
            };
        },
        savePrinter() {
            if (!this.printerModal.name.trim()) return;
            const newPrinter = {
                id: this.printerModal.id || Date.now(),
                name: this.printerModal.name.trim(),
                location: this.printerModal.location,
                connection_type: this.printerModal.connection_type,
                ip_address: this.printerModal.ip_address,
                port: Number(this.printerModal.port) || 9100,
                paper_width: this.printerModal.paper_width,
                category_ids: this.printerModal.category_ids,
                is_active: this.printerModal.is_active,
                auto_print: this.printerModal.auto_print,
            };
            if (this.printerModal.isEdit && this.printerModal.index >= 0) {
                this.settings.printers[this.printerModal.index] = newPrinter;
            } else {
                this.settings.printers.push(newPrinter);
            }
            this.printerModal.show = false;
        },
        removePrinter(idx) {
            if (confirm('Hapus printer ini?')) {
                this.settings.printers.splice(idx, 1);
            }
        },
        toggleCategorySelection(catId) {
            const idx = this.printerModal.category_ids.indexOf(catId);
            if (idx === -1) {
                this.printerModal.category_ids.push(catId);
            } else {
                this.printerModal.category_ids.splice(idx, 1);
            }
        },
        testPrint(printer) {
            const width = printer.paper_width || '58mm';
            const type = printer.location === 'cashier' ? 'customer' : 'kitchen';
            const catQuery = (printer.category_ids && printer.category_ids.length > 0) ? '&category_id=' + printer.category_ids.join(',') : '';
            window.open('/admin/api/orders/sample/test-print?type=' + type + '&width=' + width + catQuery, '_blank');
        }
     }">

    <!-- Sticky Action Bar -->
    <div class="sticky top-0 z-40 bg-ivory/95 backdrop-blur-md px-6 lg:px-10 py-4 border-b border-line/50 shadow-[0_4px_20px_-10px_rgba(0,0,0,0.05)] flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-lg font-bold text-ink flex items-center gap-2">
                <i class="fas fa-print text-[#df5826]"></i>
                <span>Sistem Struk &amp; Multi-Printer</span>
            </h2>
            <p class="text-muted font-medium text-xs mt-0.5">Kelola struk pembayaran pelanggan, tiket pesanan dapur/bar, dan routing printer.</p>
        </div>
        <button @click="saveSettings" type="button" class="bg-[#df5826] text-white px-7 py-2.5 rounded-xl font-bold text-sm shadow-sm hover:opacity-95 transition-all flex items-center gap-2 justify-center cursor-pointer" :disabled="isSavingSettings">
            <i class="fas fa-spinner fa-spin" x-show="isSavingSettings"></i>
            <i class="fas fa-save" x-show="!isSavingSettings"></i>
            <span x-text="isSavingSettings ? 'Menyimpan...' : 'Simpan Semua Pengaturan'"></span>
        </button>
    </div>

    {{-- Main Workspace Layout --}}
    <div class="p-4 md:p-8 lg:p-10 pt-6 grid grid-cols-1 xl:grid-cols-12 gap-8 items-start">
        
        <!-- Left Column: Settings Navigation & Forms -->
        <div class="xl:col-span-7 flex flex-col gap-6">

            <!-- Sub-tab Navigation Switcher -->
            <div class="bg-white p-1.5 rounded-2xl border border-line shadow-2xs flex items-center gap-1 overflow-x-auto hide-scroll">
                <button type="button" @click="receiptTab = 'customer'; settings.receiptPreviewTab = 'customer'"
                        :class="receiptTab === 'customer' ? 'bg-[#fff6f0] text-[#df5826] font-bold border border-[#f5d5c6]' : 'text-muted hover:text-ink font-medium border border-transparent'"
                        class="px-4 py-2 rounded-xl text-xs sm:text-sm flex items-center gap-2 transition cursor-pointer whitespace-nowrap">
                    <i class="fas fa-receipt text-xs"></i>
                    <span>Struk Pelanggan</span>
                </button>
                <button type="button" @click="receiptTab = 'kitchen'; settings.receiptPreviewTab = 'kitchen'"
                        :class="receiptTab === 'kitchen' ? 'bg-[#fff6f0] text-[#df5826] font-bold border border-[#f5d5c6]' : 'text-muted hover:text-ink font-medium border border-transparent'"
                        class="px-4 py-2 rounded-xl text-xs sm:text-sm flex items-center gap-2 transition cursor-pointer whitespace-nowrap">
                    <i class="fas fa-utensils text-xs"></i>
                    <span>Struk Pesanan Dapur &amp; Bar</span>
                </button>
                <button type="button" @click="receiptTab = 'printers'"
                        :class="receiptTab === 'printers' ? 'bg-[#fff6f0] text-[#df5826] font-bold border border-[#f5d5c6]' : 'text-muted hover:text-ink font-medium border border-transparent'"
                        class="px-4 py-2 rounded-xl text-xs sm:text-sm flex items-center gap-2 transition cursor-pointer whitespace-nowrap">
                    <i class="fas fa-network-wired text-xs"></i>
                    <span>Sistem Multi-Printer</span>
                    <span class="bg-[#df5826] text-white text-[10px] font-bold px-1.5 py-0.2 rounded-full" x-text="settings.printers ? settings.printers.length : 0"></span>
                </button>
            </div>

            <!-- ═══════════════════════════════════════════════════════ -->
            <!-- TAB 1: STRUK PELANGGAN SETTINGS                         -->
            <!-- ═══════════════════════════════════════════════════════ -->
            <div x-show="receiptTab === 'customer'" class="flex flex-col gap-6">
                <div class="bg-white rounded-[20px] shadow-sm border border-line overflow-hidden">
                    <div class="p-5 border-b border-line bg-[#fbfaf8] flex items-center justify-between">
                        <div>
                            <h3 class="font-bold text-base text-ink">Format &amp; Tampilan Struk Pelanggan</h3>
                            <p class="text-xs text-muted">Struk resmi pembayaran yang diberikan kepada pembeli.</p>
                        </div>
                        <span class="text-[11px] font-semibold text-[#df5826] bg-[#fff6f0] px-2.5 py-1 rounded-full border border-[#f5d5c6]">Kasir Struk</span>
                    </div>

                    <div class="p-6 flex flex-col gap-5">
                        {{-- Paper Width Selector --}}
                        <div>
                            <label class="block text-xs font-bold text-ink uppercase tracking-wider mb-2">Ukuran Kertas Thermal</label>
                            <div class="grid grid-cols-2 gap-3 max-w-md">
                                <label class="flex items-center gap-3 p-3 rounded-xl border cursor-pointer transition"
                                       :class="(settings.receipt_settings.paper_width || '58mm') === '58mm' ? 'border-[#df5826] bg-[#fff6f0]' : 'border-line hover:border-gray-400'">
                                    <input type="radio" value="58mm" x-model="settings.receipt_settings.paper_width" class="sr-only">
                                    <i class="fas fa-scroll text-sm text-[#df5826]"></i>
                                    <div>
                                        <p class="text-xs font-bold text-ink">58 mm (Kecil)</p>
                                        <p class="text-[10.5px] text-muted">Standar printer Bluetooth portable</p>
                                    </div>
                                </label>
                                <label class="flex items-center gap-3 p-3 rounded-xl border cursor-pointer transition"
                                       :class="settings.receipt_settings.paper_width === '80mm' ? 'border-[#df5826] bg-[#fff6f0]' : 'border-line hover:border-gray-400'">
                                    <input type="radio" value="80mm" x-model="settings.receipt_settings.paper_width" class="sr-only">
                                    <i class="fas fa-receipt text-sm text-[#df5826]"></i>
                                    <div>
                                        <p class="text-xs font-bold text-ink">80 mm (Lebar)</p>
                                        <p class="text-[10.5px] text-muted">Standar printer POS desktop/LAN</p>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <hr class="border-line">

                        {{-- Toggles --}}
                        <div>
                            <label class="block text-xs font-bold text-ink uppercase tracking-wider mb-3">Informasi yang Ditampilkan</label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <label class="flex items-center justify-between p-3 rounded-xl border border-line bg-gray-50/50 cursor-pointer hover:bg-gray-50">
                                    <div class="text-xs">
                                        <span class="font-bold text-ink block">Logo Toko</span>
                                        <span class="text-muted text-[10.5px]">Cetak logo cafe di atas struk</span>
                                    </div>
                                    <input type="checkbox" x-model="settings.receipt_settings.show_logo" class="rounded text-[#df5826] focus:ring-[#df5826] w-4 h-4">
                                </label>

                                <label class="flex items-center justify-between p-3 rounded-xl border border-line bg-gray-50/50 cursor-pointer hover:bg-gray-50">
                                    <div class="text-xs">
                                        <span class="font-bold text-ink block">Alamat Cafe</span>
                                        <span class="text-muted text-[10.5px]">Cetak alamat toko di bawah nama</span>
                                    </div>
                                    <input type="checkbox" x-model="settings.receipt_settings.show_address" class="rounded text-[#df5826] focus:ring-[#df5826] w-4 h-4">
                                </label>

                                <label class="flex items-center justify-between p-3 rounded-xl border border-line bg-gray-50/50 cursor-pointer hover:bg-gray-50">
                                    <div class="text-xs">
                                        <span class="font-bold text-ink block">Nomor Telepon / WA</span>
                                        <span class="text-muted text-[10.5px]">Kontak layanan pelanggan</span>
                                    </div>
                                    <input type="checkbox" x-model="settings.receipt_settings.show_phone" class="rounded text-[#df5826] focus:ring-[#df5826] w-4 h-4">
                                </label>

                                <label class="flex items-center justify-between p-3 rounded-xl border border-line bg-gray-50/50 cursor-pointer hover:bg-gray-50">
                                    <div class="text-xs">
                                        <span class="font-bold text-ink block">Akun Instagram</span>
                                        <span class="text-muted text-[10.5px]">Cetak sosmed cafe di bagian bawah</span>
                                    </div>
                                    <input type="checkbox" x-model="settings.receipt_settings.show_social" class="rounded text-[#df5826] focus:ring-[#df5826] w-4 h-4">
                                </label>
                            </div>
                        </div>

                        <hr class="border-line">

                        {{-- WiFi on Receipt --}}
                        <div class="bg-[#fcfaf7] border border-[#f2ece2] rounded-xl p-4">
                            <label class="flex items-center justify-between cursor-pointer mb-3">
                                <div>
                                    <span class="text-xs font-bold text-ink flex items-center gap-1.5">
                                        <i class="fas fa-wifi text-[#df5826]"></i>
                                        <span>Cetak Password WiFi di Struk</span>
                                    </span>
                                    <span class="text-[10.5px] text-muted">Sangat praktis untuk pelanggan cafe yang memesan di tempat.</span>
                                </div>
                                <input type="checkbox" x-model="settings.receipt_settings.show_wifi" class="rounded text-[#df5826] focus:ring-[#df5826] w-4 h-4">
                            </label>

                            <div x-show="settings.receipt_settings.show_wifi" class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
                                <div>
                                    <label class="block text-[11px] font-semibold text-ink mb-1">Nama WiFi (SSID)</label>
                                    <input type="text" x-model="settings.receipt_settings.wifi_ssid" placeholder="Contoh: KopiSenja_FreeWiFi"
                                           class="w-full bg-white border border-line rounded-lg px-3 py-1.5 text-xs focus:border-[#df5826] outline-none">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-ink mb-1">Password WiFi</label>
                                    <input type="text" x-model="settings.receipt_settings.wifi_password" placeholder="Contoh: kopienak123"
                                           class="w-full bg-white border border-line rounded-lg px-3 py-1.5 text-xs focus:border-[#df5826] outline-none">
                                </div>
                            </div>
                        </div>

                        <hr class="border-line">

                        {{-- Header & Footer Text --}}
                        <div class="flex flex-col gap-4">
                            <div>
                                <label class="block text-xs font-bold text-ink mb-1">Teks Tambahan Header (Opsional)</label>
                                <textarea x-model="settings.receipt_header" rows="2" class="w-full border border-line rounded-xl px-3.5 py-2.5 text-xs focus:border-[#df5826] outline-none" placeholder="Contoh: Jam Buka: 08:00 - 22:00 | Free WiFi Available"></textarea>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-ink mb-1">Teks Tambahan Footer (Ucapan Terima Kasih)</label>
                                <textarea x-model="settings.receipt_footer" rows="2" class="w-full border border-line rounded-xl px-3.5 py-2.5 text-xs focus:border-[#df5826] outline-none" placeholder="Contoh: Terima kasih atas kunjungan Anda!\nKritik & Saran WA: 0812-xxxx"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ═══════════════════════════════════════════════════════ -->
            <!-- TAB 2: STRUK PESANAN CAFE (DAPUR & BAR TICKET)          -->
            <!-- ═══════════════════════════════════════════════════════ -->
            <div x-show="receiptTab === 'kitchen'" class="flex flex-col gap-6">
                <div class="bg-white rounded-[20px] shadow-sm border border-line overflow-hidden">
                    <div class="p-5 border-b border-line bg-[#fbfaf8] flex items-center justify-between">
                        <div>
                            <h3 class="font-bold text-base text-ink">Format Tiket Pesanan Dapur &amp; Bar</h3>
                            <p class="text-xs text-muted">Struk khusus persiapan masak untuk koki dapur atau barista bar.</p>
                        </div>
                        <span class="text-[11px] font-semibold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-full border border-emerald-200">Kitchen Ticket</span>
                    </div>

                    <div class="p-6 flex flex-col gap-5">
                        {{-- Title for kitchen ticket --}}
                        <div>
                            <label class="block text-xs font-bold text-ink uppercase tracking-wider mb-1.5">Judul Tiket Dapur / Bar</label>
                            <input type="text" x-model="settings.receipt_settings.kitchen_ticket_title" placeholder="Contoh: TIKET PESANAN DAPUR / BAR"
                                   class="w-full max-w-md bg-white border border-line rounded-xl px-3.5 py-2 text-xs font-bold focus:border-[#df5826] outline-none">
                            <p class="text-[11px] text-muted mt-1">Dicetak tebal di baris teratas tiket dapur.</p>
                        </div>

                        <hr class="border-line">

                        {{-- Kitchen Toggles --}}
                        <div>
                            <label class="block text-xs font-bold text-ink uppercase tracking-wider mb-3">Preferensi Tampilan Dapur</label>
                            <div class="flex flex-col gap-3">
                                
                                {{-- Hide / Show Prices in Kitchen --}}
                                <label class="flex items-center justify-between p-3.5 rounded-xl border border-line bg-gray-50/50 cursor-pointer hover:bg-gray-50">
                                    <div class="text-xs pr-4">
                                        <span class="font-bold text-ink block">Sembunyikan Harga di Tiket Dapur</span>
                                        <span class="text-muted text-[10.5px]">Rekomendasi Best Practice: koki dan barista hanya perlu fokus pada jenis menu dan kuantitas, bukan rincian biaya.</span>
                                    </div>
                                    <input type="checkbox" :checked="!settings.receipt_settings.kitchen_show_prices" @change="settings.receipt_settings.kitchen_show_prices = !$event.target.checked" class="rounded text-[#df5826] focus:ring-[#df5826] w-4 h-4">
                                </label>

                                {{-- Highlight customer notes --}}
                                <label class="flex items-center justify-between p-3.5 rounded-xl border border-line bg-gray-50/50 cursor-pointer hover:bg-gray-50">
                                    <div class="text-xs pr-4">
                                        <span class="font-bold text-ink block">Kotak Khusus Catatan Customer (Highlight)</span>
                                        <span class="text-muted text-[10.5px]">Menampilkan permintaan khusus (contoh: "tanpa gula", "ekstra pedas") dengan bingkai kotak tebal agar tidak terlewat.</span>
                                    </div>
                                    <input type="checkbox" x-model="settings.receipt_settings.kitchen_highlight_notes" class="rounded text-[#df5826] focus:ring-[#df5826] w-4 h-4">
                                </label>

                                {{-- Font Size for Kitchen --}}
                                <div class="p-3.5 rounded-xl border border-line bg-gray-50/50">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-xs font-bold text-ink">Ukuran Font Menu Dapur</span>
                                        <span class="text-[10.5px] text-muted">Kemudahan membaca dari jarak jauh</span>
                                    </div>
                                    <div class="grid grid-cols-2 gap-2">
                                        <button type="button" @click="settings.receipt_settings.kitchen_font_size = 'normal'"
                                                :class="(settings.receipt_settings.kitchen_font_size || 'normal') === 'normal' ? 'bg-white border-[#df5826] text-[#df5826] font-bold shadow-2xs' : 'bg-transparent border-gray-300 text-gray-600'"
                                                class="py-1.5 px-3 rounded-lg border text-xs text-center transition cursor-pointer">
                                            Normal (12px)
                                        </button>
                                        <button type="button" @click="settings.receipt_settings.kitchen_font_size = 'large'"
                                                :class="settings.receipt_settings.kitchen_font_size === 'large' ? 'bg-white border-[#df5826] text-[#df5826] font-bold shadow-2xs' : 'bg-transparent border-gray-300 text-gray-600'"
                                                class="py-1.5 px-3 rounded-lg border text-xs text-center transition cursor-pointer">
                                            Besar / High Visibility (14px)
                                        </button>
                                    </div>
                                </div>

                                {{-- Show Cashier name --}}
                                <label class="flex items-center justify-between p-3.5 rounded-xl border border-line bg-gray-50/50 cursor-pointer hover:bg-gray-50">
                                    <div class="text-xs pr-4">
                                        <span class="font-bold text-ink block">Tampilkan Nama Kasir / Waiter</span>
                                        <span class="text-muted text-[10.5px]">Memudahkan juru masak konfirmasi pesanan ke staf yang menginput.</span>
                                    </div>
                                    <input type="checkbox" x-model="settings.receipt_settings.kitchen_show_cashier" class="rounded text-[#df5826] focus:ring-[#df5826] w-4 h-4">
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ═══════════════════════════════════════════════════════ -->
            <!-- TAB 3: SISTEM MULTI-PRINTER (ROUTING & HARDWARE HUB)    -->
            <!-- ═══════════════════════════════════════════════════════ -->
            <div x-show="receiptTab === 'printers'" class="flex flex-col gap-6">
                <div class="bg-white rounded-[20px] shadow-sm border border-line overflow-hidden">
                    <div class="p-5 border-b border-line bg-[#fbfaf8] flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div>
                            <h3 class="font-bold text-base text-ink">Daftar Printer Stasiun Terdaftar</h3>
                            <p class="text-xs text-muted">Arahkan pesanan secara otomatis ke printer Kasir, Bar, atau Dapur sesuai kategori menu.</p>
                        </div>
                        <button type="button" @click="openAddPrinter()"
                                class="bg-[#df5826] text-white px-4 py-2 rounded-xl text-xs font-bold hover:opacity-95 shadow-sm transition flex items-center gap-1.5 shrink-0 cursor-pointer">
                            <i class="fas fa-plus"></i>
                            <span>Tambah Printer</span>
                        </button>
                    </div>

                    {{-- Printers List --}}
                    <div class="p-5 flex flex-col gap-3.5">
                        <template x-if="!settings.printers || settings.printers.length === 0">
                            <div class="text-center py-10 border border-dashed border-line rounded-xl">
                                <i class="fas fa-print text-3xl text-gray-300 mb-2"></i>
                                <p class="text-xs font-bold text-ink">Belum ada printer yang ditambahkan</p>
                                <p class="text-[11px] text-muted">Klik tombol "+ Tambah Printer" untuk menambahkan printer pertama Anda.</p>
                            </div>
                        </template>

                        <template x-for="(printer, pIdx) in (settings.printers || [])" :key="printer.id || pIdx">
                            <div class="p-4 rounded-xl border border-line hover:border-[#df5826]/50 bg-white shadow-2xs flex flex-col sm:flex-row sm:items-center justify-between gap-4 transition">
                                <div class="flex items-start gap-3.5 min-w-0">
                                    <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0"
                                         :class="printer.location === 'cashier' ? 'bg-[#fff6f0] text-[#df5826] border border-[#f5d5c6]' : (printer.location === 'bar' ? 'bg-amber-50 text-amber-700 border border-amber-200' : 'bg-emerald-50 text-emerald-700 border border-emerald-200')">
                                        <i class="fas text-sm" :class="printer.location === 'cashier' ? 'fa-cash-register' : (printer.location === 'bar' ? 'fa-coffee' : 'fa-utensils')"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <h4 class="font-bold text-xs text-ink truncate" x-text="printer.name"></h4>
                                            
                                            {{-- Location Badge --}}
                                            <span class="text-[9.5px] font-bold px-2 py-0.5 rounded-full"
                                                  :class="printer.location === 'cashier' ? 'bg-[#fff6f0] text-[#df5826]' : (printer.location === 'bar' ? 'bg-amber-50 text-amber-700' : 'bg-emerald-50 text-emerald-700')"
                                                  x-text="printer.location === 'cashier' ? 'Kasir (Struk Pelanggan)' : (printer.location === 'bar' ? 'Barista (Minuman)' : 'Dapur (Makanan)')"></span>

                                            {{-- Paper Width --}}
                                            <span class="text-[9.5px] font-semibold bg-gray-100 text-gray-700 px-1.5 py-0.5 rounded" x-text="printer.paper_width || '58mm'"></span>
                                        </div>

                                        {{-- Connectivity details --}}
                                        <div class="flex items-center gap-2 text-[10.5px] text-muted mt-1">
                                            <span x-text="printer.connection_type === 'network' ? 'IP: ' + (printer.ip_address || '192.168.1.xxx') + ':' + (printer.port || 9100) : (printer.connection_type === 'bluetooth' ? 'Bluetooth Thermal' : 'Web Browser Dialog')"></span>
                                            <span>•</span>
                                            <span x-text="(printer.category_ids && printer.category_ids.length > 0) ? printer.category_ids.length + ' Kategori ditugaskan' : 'Semua Kategori Menu'"></span>
                                        </div>
                                    </div>
                                </div>

                                {{-- Action buttons --}}
                                <div class="flex items-center gap-2 shrink-0">
                                    <button type="button" @click="testPrint(printer)"
                                            class="px-2.5 py-1.5 rounded-lg border border-line bg-gray-50 hover:bg-[#fff6f0] hover:text-[#df5826] hover:border-[#f5d5c6] text-[11px] font-semibold text-gray-700 transition flex items-center gap-1 cursor-pointer"
                                            title="Uji coba cetak struk contoh">
                                        <i class="fas fa-print text-xs"></i>
                                        <span>Test</span>
                                    </button>
                                    <button type="button" @click="openEditPrinter(pIdx)"
                                            class="px-2.5 py-1.5 rounded-lg border border-line hover:border-gray-400 text-[11px] font-semibold text-gray-700 transition cursor-pointer">
                                        Edit
                                    </button>
                                    <button type="button" @click="removePrinter(pIdx)"
                                            class="p-1.5 rounded-lg text-gray-400 hover:text-red-500 hover:bg-red-50 transition cursor-pointer">
                                        <i class="fas fa-trash-alt text-xs"></i>
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

        </div>

        <!-- ═══════════════════════════════════════════════════════════ -->
        <!-- RIGHT COLUMN: REALISTIC THERMAL LIVE PREVIEW               -->
        <!-- ═══════════════════════════════════════════════════════════ -->
        <div class="xl:col-span-5 sticky top-[90px]">
            <div class="flex justify-between items-center mb-3">
                <h3 class="font-bold text-sm text-ink flex items-center gap-1.5">
                    <i class="fas fa-eye text-[#df5826]"></i>
                    <span>Live Preview Thermal</span>
                </h3>
                
                {{-- Preview Switcher Tab --}}
                <div class="bg-white border border-line rounded-lg p-0.5 flex text-[10.5px]">
                    <button type="button" @click="settings.receiptPreviewTab = 'customer'"
                            :class="settings.receiptPreviewTab === 'customer' ? 'bg-[#fff6f0] text-[#df5826] font-bold' : 'text-muted'"
                            class="px-2.5 py-1 rounded-md transition cursor-pointer">
                        Pelanggan
                    </button>
                    <button type="button" @click="settings.receiptPreviewTab = 'kitchen'"
                            :class="settings.receiptPreviewTab === 'kitchen' ? 'bg-[#fff6f0] text-[#df5826] font-bold' : 'text-muted'"
                            class="px-2.5 py-1 rounded-md transition cursor-pointer">
                        Dapur/Bar
                    </button>
                </div>
            </div>

            <!-- Realistic Thermal Paper Container -->
            <div class="relative mx-auto w-full max-w-[310px] bg-white shadow-[0_16px_40px_rgba(0,0,0,0.12)] overflow-hidden font-mono text-xs text-black border-x border-gray-200"
                 style="border-top: 5px dashed #bbb; border-bottom: 5px dashed #bbb;">
                
                <div class="p-5 flex flex-col">

                    <!-- ────────────────────────────────────────── -->
                    <!-- PREVIEW: STRUK PELANGGAN                   -->
                    <!-- ────────────────────────────────────────── -->
                    <template x-if="settings.receiptPreviewTab === 'customer'">
                        <div class="flex flex-col">
                            {{-- Logo --}}
                            <template x-if="settings.receipt_settings.show_logo && (settings.logoPreview || window.INITIAL_DATA.shop?.logo_url)">
                                <img :src="settings.logoPreview || window.INITIAL_DATA.shop?.logo_url" alt="Logo" class="h-12 w-12 object-contain grayscale mx-auto mb-2" style="filter: grayscale(100%) contrast(1.4);">
                            </template>

                            {{-- Shop Name --}}
                            <h2 class="font-bold text-sm uppercase text-center" x-text="settings.name || 'NAMA CAFE'"></h2>
                            
                            {{-- Slogan --}}
                            <template x-if="settings.slogan">
                                <p class="text-[9.5px] text-center mt-0.5" x-text="settings.slogan"></p>
                            </template>

                            {{-- Address & Phone --}}
                            <template x-if="settings.receipt_settings.show_address">
                                <p class="text-[9.5px] text-center mt-1 leading-tight">{{ $shop->address ?? 'Jl. Utama Kopi No. 12, Jakarta' }}</p>
                            </template>
                            <template x-if="settings.receipt_settings.show_phone">
                                <p class="text-[9.5px] text-center">Telp/WA: <span x-text="settings.whatsapp_number || '0812-3456-7890'"></span></p>
                            </template>

                            {{-- Custom Header --}}
                            <template x-if="settings.receipt_header">
                                <p class="text-[9.5px] text-center mt-1.5 whitespace-pre-wrap" x-text="settings.receipt_header"></p>
                            </template>

                            <div class="w-full border-b border-dashed border-gray-400 my-2.5"></div>

                            {{-- Metadata --}}
                            <div class="w-full text-[10.5px] flex justify-between">
                                <span>No: TRX-9921</span>
                                <span>06 Nov 2026 13:40</span>
                            </div>
                            <div class="w-full text-[10.5px] flex justify-between">
                                <span>Plg: Budi Santoso</span>
                                <span>Kasir: Admin</span>
                            </div>
                            <div class="w-full text-[10.5px] flex justify-between">
                                <span>Tipe: Dine-in</span>
                                <span class="font-bold">Meja: 04</span>
                            </div>

                            <div class="w-full border-b border-dashed border-gray-400 my-2"></div>

                            {{-- Dummy Items --}}
                            <div class="w-full text-[11px] flex flex-col gap-1.5">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1 pr-2">
                                        <span class="font-bold">2x Americano (Ice)</span>
                                        <span class="block text-[9.5px] text-gray-600">Less Sugar</span>
                                    </div>
                                    <span class="shrink-0 font-semibold">50.000</span>
                                </div>
                                <div class="flex justify-between items-start">
                                    <div class="flex-1 pr-2">
                                        <span class="font-bold">1x Sate Kambing Mix Tahu</span>
                                        <span class="block text-[9.5px] text-gray-600">* Bumbu dipisah</span>
                                    </div>
                                    <span class="shrink-0 font-semibold">55.000</span>
                                </div>
                            </div>

                            <div class="w-full border-b border-dashed border-gray-400 my-2"></div>

                            {{-- Totals --}}
                            <div class="w-full text-[11px] flex flex-col gap-1">
                                <div class="flex justify-between text-gray-700">
                                    <span>Subtotal</span>
                                    <span>105.000</span>
                                </div>
                                <div class="flex justify-between text-gray-700">
                                    <span>Pajak Resto (10%)</span>
                                    <span>10.500</span>
                                </div>
                                <div class="flex justify-between font-bold text-xs pt-1 border-t border-dashed border-gray-400">
                                    <span>TOTAL</span>
                                    <span>Rp 115.500</span>
                                </div>
                                <div class="flex justify-between text-[10.5px] mt-0.5">
                                    <span>Tunai</span>
                                    <span>150.000</span>
                                </div>
                                <div class="flex justify-between text-[10.5px] font-bold">
                                    <span>Kembali</span>
                                    <span>34.500</span>
                                </div>
                            </div>

                            {{-- WiFi Box if enabled --}}
                            <template x-if="settings.receipt_settings.show_wifi">
                                <div class="w-full border border-dashed border-black p-2 my-2 text-center text-[10px]">
                                    <p class="font-bold">WIFI CAFE</p>
                                    <p>SSID: <span x-text="settings.receipt_settings.wifi_ssid || 'KopiSenja_FreeWiFi'"></span></p>
                                    <p>Password: <span x-text="settings.receipt_settings.wifi_password || 'kopienak123'"></span></p>
                                </div>
                            </template>

                            <div class="w-full border-b border-dashed border-gray-400 my-2"></div>

                            {{-- Footer text --}}
                            <div class="text-center text-[9.5px] mt-1">
                                <p class="whitespace-pre-wrap" x-text="settings.receipt_footer || 'Terima kasih atas kunjungan Anda!\nSilakan datang kembali.'"></p>
                                <template x-if="settings.receipt_settings.show_social && settings.instagram_link">
                                    <p class="mt-1">IG: <span x-text="settings.instagram_link"></span></p>
                                </template>
                            </div>
                        </div>
                    </template>

                    <!-- ────────────────────────────────────────── -->
                    <!-- PREVIEW: STRUK PESANAN DAPUR / BAR         -->
                    <!-- ────────────────────────────────────────── -->
                    <template x-if="settings.receiptPreviewTab === 'kitchen'">
                        <div class="flex flex-col">
                            <h3 class="font-black text-xs text-center tracking-wider" x-text="settings.receipt_settings.kitchen_ticket_title || 'TIKET PESANAN DAPUR'"></h3>
                            <p class="text-[9.5px] text-center text-gray-600" x-text="settings.name || 'NAMA CAFE'"></p>

                            <div class="w-full border-b border-dashed border-gray-400 my-2"></div>

                            {{-- Target Table / Fulfillment Badge --}}
                            <div class="border-2 border-black p-1 text-center font-black text-sm uppercase tracking-wider my-1">
                                MEJA 04 (DINE IN)
                            </div>

                            <div class="w-full text-[10px] flex justify-between mt-1">
                                <span>No: TRX-9921</span>
                                <span>13:40</span>
                            </div>
                            <div class="w-full text-[10px] flex justify-between">
                                <span>Plg: Budi Santoso</span>
                                <template x-if="settings.receipt_settings.kitchen_show_cashier">
                                    <span>Kasir: Admin</span>
                                </template>
                            </div>

                            <div class="w-full border-b-2 border-dashed border-black my-2"></div>

                            {{-- Kitchen Items --}}
                            <div class="w-full flex flex-col gap-2" :class="settings.receipt_settings.kitchen_font_size === 'large' ? 'text-xs' : 'text-[11px]'">
                                <div class="flex items-start">
                                    <span class="font-black w-8 shrink-0">[2x]</span>
                                    <div class="flex-1">
                                        <span class="font-bold">Americano (Ice)</span>
                                        <span class="block text-[9.5px] text-gray-700">Less Sugar</span>
                                    </div>
                                    <template x-if="settings.receipt_settings.kitchen_show_prices">
                                        <span class="shrink-0 text-[10px]">50.000</span>
                                    </template>
                                </div>

                                <div class="flex items-start">
                                    <span class="font-black w-8 shrink-0">[1x]</span>
                                    <div class="flex-1">
                                        <span class="font-bold">Sate Kambing Mix Tahu</span>
                                        <div :class="settings.receipt_settings.kitchen_highlight_notes ? 'border border-dashed border-black p-1 bg-gray-50 my-1 font-bold text-[10px]' : 'text-[9.5px] text-gray-700'">
                                            * Catatan: Bumbu dipisah, tanpa bawang
                                        </div>
                                    </div>
                                    <template x-if="settings.receipt_settings.kitchen_show_prices">
                                        <span class="shrink-0 text-[10px]">55.000</span>
                                    </template>
                                </div>
                            </div>

                            <div class="w-full border-b-2 border-dashed border-black my-2"></div>

                            <template x-if="settings.receipt_settings.kitchen_show_prices">
                                <div class="flex justify-between font-bold text-xs mb-2">
                                    <span>Total:</span>
                                    <span>Rp 115.500</span>
                                </div>
                            </template>

                            <div class="text-center text-[9px] text-gray-600">
                                <p>*** CETAK PESANAN SELESAI ***</p>
                            </div>
                        </div>
                    </template>

                </div>
            </div>
            
            <p class="text-xs text-center text-muted mt-3">
                <i class="fas fa-info-circle mr-1"></i> Preview otomatis beradaptasi dengan pengaturan yang Anda ubah.
            </p>
        </div>
    </div>

    <!-- ═══════════════════════════════════════════════════════════ -->
    <!-- MODAL: ADD / EDIT PRINTER                                   -->
    <!-- ═══════════════════════════════════════════════════════════ -->
    <div x-show="printerModal.show" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-xs" @click="printerModal.show = false"></div>
        <div class="bg-white border border-line rounded-2xl shadow-2xl max-w-md w-full relative z-10 overflow-hidden flex flex-col max-h-[90vh]">
            <div class="p-4 border-b border-line bg-[#fbfaf8] flex items-center justify-between">
                <h3 class="font-bold text-sm text-ink" x-text="printerModal.isEdit ? 'Edit Printer' : 'Tambah Printer Baru'"></h3>
                <button type="button" @click="printerModal.show = false" class="text-gray-400 hover:text-gray-600 text-sm">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="p-5 overflow-y-auto space-y-4 text-xs">
                <div>
                    <label class="block font-bold text-ink mb-1">Nama Printer *</label>
                    <input type="text" x-model="printerModal.name" placeholder="Contoh: Printer Barista 58mm"
                           class="w-full bg-white border border-line rounded-xl px-3 py-2 text-xs font-semibold focus:border-[#df5826] outline-none">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-ink mb-1">Peruntukan / Lokasi</label>
                        <select x-model="printerModal.location" class="w-full bg-white border border-line rounded-xl px-3 py-2 text-xs font-semibold focus:border-[#df5826] outline-none">
                            <option value="cashier">Kasir (Struk Pelanggan)</option>
                            <option value="kitchen">Dapur (Makanan)</option>
                            <option value="bar">Bar (Minuman)</option>
                            <option value="checker">Checker (Semua Item)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-ink mb-1">Lebar Kertas</label>
                        <select x-model="printerModal.paper_width" class="w-full bg-white border border-line rounded-xl px-3 py-2 text-xs font-semibold focus:border-[#df5826] outline-none">
                            <option value="58mm">58 mm (Standar)</option>
                            <option value="80mm">80 mm (Lebar)</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-ink mb-1">Metode Koneksi</label>
                    <select x-model="printerModal.connection_type" class="w-full bg-white border border-line rounded-xl px-3 py-2 text-xs font-semibold focus:border-[#df5826] outline-none">
                        <option value="browser">Web Browser Print (Semua Printer USB/Driver OS)</option>
                        <option value="network">Network / IP LAN (ESC/POS Printer)</option>
                        <option value="bluetooth">Bluetooth Thermal</option>
                    </select>
                </div>

                <div x-show="printerModal.connection_type === 'network'" class="grid grid-cols-3 gap-2 bg-gray-50 p-3 rounded-xl border border-line">
                    <div class="col-span-2">
                        <label class="block font-bold text-ink mb-1">IP Address</label>
                        <input type="text" x-model="printerModal.ip_address" placeholder="192.168.1.200"
                               class="w-full bg-white border border-line rounded-lg px-2.5 py-1.5 text-xs outline-none">
                    </div>
                    <div>
                        <label class="block font-bold text-ink mb-1">Port</label>
                        <input type="number" x-model.number="printerModal.port" placeholder="9100"
                               class="w-full bg-white border border-line rounded-lg px-2.5 py-1.5 text-xs outline-none">
                    </div>
                </div>

                {{-- Category Routing --}}
                <div x-show="printerModal.location !== 'cashier'">
                    <label class="block font-bold text-ink mb-1.5">Routing Kategori Menu</label>
                    <p class="text-[11px] text-muted mb-2">Pilih kategori menu yang akan dicetak pada printer ini. Jika kosong, semua item akan dicetak.</p>
                    <div class="grid grid-cols-2 gap-2 max-h-36 overflow-y-auto border border-line rounded-xl p-2.5 bg-gray-50/50">
                        @foreach($categories as $cat)
                            <label class="flex items-center gap-2 p-1.5 rounded hover:bg-white cursor-pointer text-xs">
                                <input type="checkbox" :checked="printerModal.category_ids.includes({{ $cat->id }})" @change="toggleCategorySelection({{ $cat->id }})" class="rounded text-[#df5826] focus:ring-[#df5826]">
                                <span class="truncate">{{ $cat->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="pt-2 border-t border-line flex items-center justify-between">
                    <label class="flex items-center gap-2 cursor-pointer font-bold text-ink">
                        <input type="checkbox" x-model="printerModal.auto_print" class="rounded text-[#df5826] focus:ring-[#df5826]">
                        <span>Cetak Otomatis saat Pesanan Masuk</span>
                    </label>
                </div>
            </div>

            <div class="p-4 bg-[#fbfaf8] border-t border-line flex items-center justify-end gap-2">
                <button type="button" @click="printerModal.show = false" class="px-4 py-2 rounded-xl border border-line text-xs font-semibold text-gray-600 hover:bg-gray-100 transition cursor-pointer">
                    Batal
                </button>
                <button type="button" @click="savePrinter()" class="px-5 py-2 rounded-xl bg-[#df5826] text-white text-xs font-bold hover:opacity-95 transition cursor-pointer">
                    Simpan Printer
                </button>
            </div>
        </div>
    </div>

</div>
