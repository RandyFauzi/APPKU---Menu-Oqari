<div x-show="currentTab === 'receipt'" x-cloak class="flex-grow bg-[#FDFBF7] overflow-auto hide-scroll relative">
    <!-- Sticky Action Bar -->
    <div class="sticky top-0 z-40 bg-[#FDFBF7]/95 backdrop-blur-md px-8 lg:px-10 py-4 border-b border-[#E3E1DC]/50 shadow-[0_4px_20px_-10px_rgba(0,0,0,0.05)] flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <p class="text-[#777873] font-medium text-sm"><i class="fas fa-print mr-1"></i> Sesuaikan tampilan struk yang akan dicetak untuk pelanggan Anda.</p>
        </div>
        <button @click="saveSettings" type="button" class="bg-[#164A35] text-white px-8 py-3 rounded-xl font-bold text-sm shadow-[0_8px_20px_-6px_rgba(22,74,53,0.5)] hover:bg-[#1e5f44] hover:-translate-y-0.5 transition-all flex items-center gap-2 justify-center" :disabled="isSavingSettings">
            <i class="fas fa-spinner fa-spin" x-show="isSavingSettings"></i>
            <i class="fas fa-save" x-show="!isSavingSettings"></i>
            <span x-text="isSavingSettings ? 'Menyimpan...' : 'Simpan Perubahan'"></span>
        </button>
    </div>

    <div class="p-4 md:p-8 lg:p-10 pt-6 md:pt-8 lg:pt-8 grid grid-cols-1 xl:grid-cols-12 gap-8 items-start">
        <!-- Form Settings -->
        <div class="xl:col-span-7 flex flex-col gap-8">
            <!-- Section 1: Header & Footer Struk -->
            <div class="bg-white rounded-[24px] shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-[#E3E1DC] overflow-hidden">
                <div class="p-6 border-b border-[#E3E1DC] bg-[#F8F7F3]/50">
                    <h3 class="font-bold text-lg text-[#202522]">Informasi Struk</h3>
                    <p class="text-xs text-[#777873]">Tambahkan pesan teks tambahan (header & footer) pada struk pembelian pelanggan.</p>
                </div>
                <div class="p-6 flex flex-col gap-6">
                    <div>
                        <label class="block text-sm font-bold text-[#202522] mb-2">Teks Header Struk (Opsional)</label>
                        <textarea x-model="settings.receipt_header" rows="3" class="w-full border border-[#E3E1DC] rounded-xl px-4 py-3 text-sm focus:border-[#164A35] focus:ring-1 focus:ring-[#164A35] focus:outline-none transition-all" placeholder="Contoh: Jam Buka: 08:00 - 22:00&#10;Melayani Dine-in & Takeaway"></textarea>
                        <p class="text-xs text-[#777873] mt-2">Teks ini akan muncul di bagian atas struk, tepat di bawah nama toko dan alamat.</p>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-[#202522] mb-2">Teks Footer Struk (Opsional)</label>
                        <textarea x-model="settings.receipt_footer" rows="3" class="w-full border border-[#E3E1DC] rounded-xl px-4 py-3 text-sm focus:border-[#164A35] focus:ring-1 focus:ring-[#164A35] focus:outline-none transition-all" placeholder="Contoh: Terima kasih atas kunjungan Anda!&#10;Kritik & Saran: 0812xxxx"></textarea>
                        <p class="text-xs text-[#777873] mt-2">Teks ini akan muncul di bagian paling bawah struk.</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-[#F8F7F3] rounded-2xl p-5 border border-[#E3E1DC]">
                <h4 class="font-bold text-[#202522] mb-2 flex items-center"><i class="fas fa-lightbulb text-amber-500 mr-2"></i>Tips Professional</h4>
                <ul class="text-sm text-[#777873] space-y-2">
                    <li>&bull; Logo Toko akan otomatis diambil dari pengaturan **Toko Saya > Identitas & Branding**.</li>
                    <li>&bull; Nama akun Instagram akan otomatis tercetak jika Anda sudah mengisi *username* Instagram di profil toko Anda.</li>
                    <li>&bull; Jangan membuat Teks Header atau Footer terlalu panjang agar struk tidak boros kertas.</li>
                </ul>
            </div>
        </div>

        <!-- Live Preview -->
        <div class="xl:col-span-5 sticky top-[100px]">
            <div class="flex justify-between items-center mb-4">
                <h3 class="font-black text-[#202522]"><i class="fas fa-receipt mr-2 text-[#164A35]"></i>Live Preview Struk</h3>
            </div>
            
            <!-- Receipt Frame (Thermal Printer Style) -->
            <div class="relative mx-auto w-full max-w-[320px] bg-white shadow-[0_20px_60px_rgba(0,0,0,0.15)] overflow-hidden font-mono text-sm" style="border-top: 4px dashed #ccc; border-bottom: 4px dashed #ccc;">
                <div class="p-6 flex flex-col items-center justify-center text-center text-black">
                    <!-- Logo -->
                    <template x-if="settings.logoPreview || window.INITIAL_DATA.shop?.logo_url">
                        <img :src="settings.logoPreview || window.INITIAL_DATA.shop?.logo_url" alt="Logo" class="h-16 w-16 object-contain grayscale mb-2" style="filter: grayscale(100%) contrast(1.5);">
                    </template>
                    
                    <!-- Shop Name -->
                    <h2 class="font-bold text-lg uppercase" x-text="settings.name || 'NAMA TOKO'"></h2>
                    
                    <!-- Address (Placeholder or real) -->
                    <p class="text-xs mt-1">{{ $shop->address ?? 'Alamat belum diisi' }}</p>

                    <!-- Header -->
                    <template x-if="settings.receipt_header">
                        <div class="text-xs mt-3 whitespace-pre-wrap" x-text="settings.receipt_header"></div>
                    </template>

                    <div class="w-full border-b border-dashed border-gray-400 my-4"></div>

                    <!-- Dummy Transaction Info -->
                    <div class="w-full text-xs text-left mb-2">
                        <div class="flex justify-between"><span>No: TRX-0001</span><span>01/01/2026</span></div>
                        <div class="flex justify-between"><span>Meja: 4</span><span>Kasir: Admin</span></div>
                    </div>

                    <div class="w-full border-b border-dashed border-gray-400 my-2"></div>

                    <!-- Dummy Items -->
                    <div class="w-full text-xs text-left mb-2">
                        <div class="flex justify-between mb-1">
                            <span>2x Americano (Ice)</span>
                            <span>40.000</span>
                        </div>
                        <div class="flex justify-between mb-1">
                            <span>1x Caffe Latte (Hot)</span>
                            <span>25.000</span>
                        </div>
                    </div>

                    <div class="w-full border-b border-dashed border-gray-400 my-2"></div>

                    <!-- Dummy Total -->
                    <div class="w-full text-xs text-left mb-4">
                        <div class="flex justify-between font-bold">
                            <span>TOTAL</span>
                            <span>65.000</span>
                        </div>
                        <div class="flex justify-between mt-1 text-[10px]">
                            <span>Tunai</span>
                            <span>100.000</span>
                        </div>
                        <div class="flex justify-between text-[10px]">
                            <span>Kembali</span>
                            <span>35.000</span>
                        </div>
                    </div>

                    <!-- Footer -->
                    <template x-if="settings.receipt_footer">
                        <div class="text-xs whitespace-pre-wrap mt-2 text-center w-full" x-text="settings.receipt_footer"></div>
                    </template>
                    <template x-if="!settings.receipt_footer">
                        <div class="text-xs mt-2 text-center w-full">Terima kasih atas kunjungan Anda!<br>Silakan datang kembali.</div>
                    </template>
                    
                    <!-- Instagram Placeholder -->
                    <template x-if="settings.instagram_link">
                        <div class="text-xs mt-3 text-center w-full flex items-center justify-center gap-1">
                            <i class="fab fa-instagram"></i> <span x-text="settings.instagram_link"></span>
                        </div>
                    </template>

                </div>
                <!-- Paper zig-zag effect top/bottom -->
                <div class="absolute top-0 left-0 w-full h-2 bg-[url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIxMCIgaGVpZ2h0PSIxMCI+PHBvbHlnb24gcG9pbnRzPSIwLDAgNSwxMCAxMCwwIiBmaWxsPSIjRkRGQkY3Ii8+PC9zdmc+')] bg-repeat-x -mt-1 z-10"></div>
                <div class="absolute bottom-0 left-0 w-full h-2 bg-[url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIxMCIgaGVpZ2h0PSIxMCI+PHBvbHlnb24gcG9pbnRzPSIwLDEwIDUsMCAxMCwxMCIgZmlsbD0iI0ZERkJGNyIvPjwvc3ZnPg==')] bg-repeat-x -mb-1 z-10"></div>
            </div>
            
            <p class="text-xs text-center text-[#777873] mt-4"><i class="fas fa-info-circle mr-1"></i> Preview ini disesuaikan untuk printer thermal Bluetooth (58mm).</p>
        </div>
    </div>
</div>
