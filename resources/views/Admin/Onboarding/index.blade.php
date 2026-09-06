<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Setup Bisnis - OQARI</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <style>
        [x-cloak] { display: none !important; }
        .hide-scroll::-webkit-scrollbar { display: none; }
        .hide-scroll { -ms-overflow-style: none; scrollbar-width: none; }
        body { font-family: ui-sans-serif, system-ui, sans-serif; }
    </style>
</head>
<body class="bg-gray-50 h-screen w-full overflow-hidden text-gray-800" x-data="onboardingApp()">

    <!-- TOP NAV -->
    <div class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-6 shrink-0 absolute top-0 w-full z-10 shadow-sm">
        <div class="flex items-center gap-3">
            <img src="{{ asset('logo-oqari.webp') }}" class="h-8 w-8 object-cover rounded-md" onerror="this.src='https://ui-avatars.com/api/?name=O&color=16a34a&background=dcfce7'">
            <span class="font-black text-lg text-gray-800 tracking-tight">OQARI</span>
        </div>
        
        <div class="hidden sm:flex items-center gap-2 text-sm text-gray-500 font-semibold">
            <i class="fas fa-headset"></i> Butuh Bantuan?
        </div>
    </div>

    <!-- MAIN CONTAINER -->
    <div class="flex h-full pt-16">
        
        <!-- SIDEBAR PROGRESS (Hidden on Mobile) -->
        <div class="hidden md:flex flex-col w-72 bg-white border-r border-gray-200 p-8 shrink-0 relative">
            <h2 class="text-xl font-black mb-8 text-gray-800">Setup Usahamu</h2>
            
            <div class="relative flex-1">
                <div class="absolute left-3.5 top-2 bottom-6 w-0.5 bg-gray-100 -z-10"></div>
                
                <template x-for="(step, index) in steps" :key="step.id">
                    <div class="flex items-start gap-4 mb-8 relative">
                        <div class="w-7 h-7 rounded-full flex items-center justify-center shrink-0 border-2 transition-colors duration-300 bg-white"
                             :class="isStepCompleted(index) ? 'border-green-500 text-green-500' : (currentStepIndex === index ? 'border-green-500 border-[6px]' : 'border-gray-200')">
                            <i class="fas fa-check text-[10px]" x-show="isStepCompleted(index)"></i>
                        </div>
                        <div class="pt-0.5">
                            <p class="font-bold text-sm transition-colors duration-300" 
                               :class="currentStepIndex >= index ? 'text-gray-900' : 'text-gray-400'" 
                               x-text="step.title"></p>
                            <p class="text-xs text-gray-400 mt-1" x-text="step.desc"></p>
                        </div>
                    </div>
                </template>
            </div>
            
            <div class="mt-auto pt-6 border-t border-gray-100">
                <p class="text-xs text-gray-400 text-center">Progress Setup</p>
                <div class="w-full bg-gray-100 rounded-full h-2.5 mt-2 overflow-hidden">
                    <div class="bg-green-500 h-2.5 rounded-full transition-all duration-500" :style="'width: ' + progressPercent + '%'"></div>
                </div>
            </div>
        </div>

        <!-- MAIN WIZARD AREA -->
        <div class="flex-1 overflow-y-auto hide-scroll bg-gray-50 p-4 sm:p-8 relative">
            
            <!-- Mobile Progress Bar -->
            <div class="md:hidden mb-6 bg-white p-4 rounded-2xl shadow-sm border border-gray-100">
                <div class="flex justify-between text-xs font-bold text-gray-500 mb-2">
                    <span x-text="steps[currentStepIndex].title"></span>
                    <span x-text="Math.round(progressPercent) + '%'"></span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-2 overflow-hidden">
                    <div class="bg-green-500 h-2 rounded-full transition-all duration-500" :style="'width: ' + progressPercent + '%'"></div>
                </div>
            </div>

            <div class="max-w-2xl mx-auto pb-20">
                
                <!-- STEP 1: WELCOME -->
                <div x-show="currentStepId === 'welcome'" x-transition.opacity.duration.300ms>
                    <div class="bg-white rounded-3xl p-8 sm:p-12 text-center shadow-sm border border-gray-100">
                        <div class="w-24 h-24 bg-green-50 text-green-600 rounded-full flex items-center justify-center mx-auto mb-6 text-4xl shadow-inner">
                            <i class="fas fa-rocket"></i>
                        </div>
                        <h1 class="text-3xl font-black text-gray-900 mb-4">Selamat Datang di OQARI!</h1>
                        <p class="text-gray-500 mb-8 leading-relaxed">
                            Kami sangat senang kamu bergabung. Mari kita siapkan sistem kasir dan tokomu hanya dalam waktu kurang dari 3 menit agar kamu bisa <b>langsung berjualan</b> hari ini.
                        </p>
                        <button @click="nextStep()" class="bg-green-600 hover:bg-green-700 text-white font-black py-4 px-8 rounded-2xl w-full sm:w-auto shadow-lg shadow-green-200 transition-all active:scale-95">
                            Mulai Setup Sekarang <i class="fas fa-arrow-right ml-2"></i>
                        </button>
                    </div>
                </div>

                <!-- STEP 2: BUSINESS PROFILE -->
                <div x-show="currentStepId === 'business_profile'" x-cloak x-transition.opacity.duration.300ms>
                    <h2 class="text-2xl font-black text-gray-900 mb-2">Profil Usaha Kamu</h2>
                    <p class="text-gray-500 text-sm mb-8">Informasi dasar ini akan ditampilkan di struk dan sistem tokomu.</p>
                    
                    <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-gray-100 space-y-6">
                        
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Nama Usaha <span class="text-red-500">*</span></label>
                            <input type="text" x-model="form.name" class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-green-500 focus:ring-0 outline-none text-lg transition" placeholder="Contoh: Kopi Kenangan, Warung Nasi...">
                            <p x-show="errors.name" class="text-red-500 text-xs mt-1 font-semibold" x-text="errors.name"></p>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Jenis Usaha <span class="text-red-500">*</span></label>
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                                <template x-for="type in ['Cafe', 'Restaurant', 'Retail', 'F&B Outlet', 'Warung', 'Lainnya']">
                                    <label class="cursor-pointer">
                                        <input type="radio" x-model="form.business_type" :value="type" class="sr-only">
                                        <div class="border-2 rounded-xl text-center py-3 px-2 font-bold text-sm transition"
                                             :class="form.business_type === type ? 'border-green-500 bg-green-50 text-green-700' : 'border-gray-200 text-gray-600 hover:border-gray-300'">
                                            <span x-text="type"></span>
                                        </div>
                                    </label>
                                </template>
                            </div>
                            <p x-show="errors.business_type" class="text-red-500 text-xs mt-1 font-semibold" x-text="errors.business_type"></p>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Alamat Usaha (Opsional)</label>
                            <textarea x-model="form.address" rows="2" class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-green-500 outline-none transition resize-none" placeholder="Masukkan alamat lengkap..."></textarea>
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end gap-3">
                        <button @click="saveStep('business_profile')" :disabled="loading" class="bg-gray-900 hover:bg-black text-white font-black py-3 px-8 rounded-xl shadow-lg transition disabled:opacity-50">
                            <span x-show="!loading">Lanjut <i class="fas fa-chevron-right ml-1"></i></span>
                            <span x-show="loading"><i class="fas fa-spinner fa-spin"></i> Menyimpan...</span>
                        </button>
                    </div>
                </div>

                <!-- STEP 3: SALES MODE -->
                <div x-show="currentStepId === 'sales_mode'" x-cloak x-transition.opacity.duration.300ms>
                    <h2 class="text-2xl font-black text-gray-900 mb-2">Metode Penjualan</h2>
                    <p class="text-gray-500 text-sm mb-8">Bagaimana cara pelanggan membeli di tokomu? (Bisa pilih lebih dari satu)</p>
                    
                    <div class="grid gap-4 mb-8">
                        
                        <label class="flex items-start gap-4 bg-white p-6 rounded-2xl border-2 cursor-pointer transition shadow-sm"
                               :class="form.sales_modes.includes('DINE_IN') ? 'border-green-500 bg-green-50/30' : 'border-gray-100 hover:border-gray-200'">
                            <input type="checkbox" value="DINE_IN" x-model="form.sales_modes" class="mt-1 w-5 h-5 text-green-600 rounded border-gray-300 focus:ring-green-500">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-1">
                                    <i class="fas fa-utensils text-green-600"></i>
                                    <h3 class="font-bold text-gray-900">Makan di Tempat (Dine-In)</h3>
                                </div>
                                <p class="text-sm text-gray-500">Pelanggan makan di meja/lokasi. (Fitur Manajemen Meja akan otomatis disiapkan).</p>
                            </div>
                        </label>

                        <label class="flex items-start gap-4 bg-white p-6 rounded-2xl border-2 cursor-pointer transition shadow-sm"
                               :class="form.sales_modes.includes('TAKEAWAY') ? 'border-green-500 bg-green-50/30' : 'border-gray-100 hover:border-gray-200'">
                            <input type="checkbox" value="TAKEAWAY" x-model="form.sales_modes" class="mt-1 w-5 h-5 text-green-600 rounded border-gray-300 focus:ring-green-500">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-1">
                                    <i class="fas fa-shopping-bag text-green-600"></i>
                                    <h3 class="font-bold text-gray-900">Bawa Pulang (Takeaway)</h3>
                                </div>
                                <p class="text-sm text-gray-500">Pelanggan memesan untuk dibawa pulang langsung tanpa nomor meja.</p>
                            </div>
                        </label>

                        <label class="flex items-start gap-4 bg-white p-6 rounded-2xl border-2 cursor-pointer transition shadow-sm"
                               :class="form.sales_modes.includes('DELIVERY') ? 'border-green-500 bg-green-50/30' : 'border-gray-100 hover:border-gray-200'">
                            <input type="checkbox" value="DELIVERY" x-model="form.sales_modes" class="mt-1 w-5 h-5 text-green-600 rounded border-gray-300 focus:ring-green-500">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-1">
                                    <i class="fas fa-motorcycle text-green-600"></i>
                                    <h3 class="font-bold text-gray-900">Pesan Antar (Delivery)</h3>
                                </div>
                                <p class="text-sm text-gray-500">Pemesanan online melalui kurir (Grab/Gojek/Kurir Toko).</p>
                            </div>
                        </label>
                    </div>

                    <p x-show="errors.sales_modes" class="text-red-500 text-sm font-semibold mb-4" x-text="errors.sales_modes"></p>

                    <div class="mt-8 flex justify-between gap-3">
                        <button @click="backStep()" class="text-gray-500 font-bold px-4 hover:text-gray-800">Kembali</button>
                        <button @click="saveStep('sales_mode')" :disabled="loading" class="bg-gray-900 hover:bg-black text-white font-black py-3 px-8 rounded-xl shadow-lg transition disabled:opacity-50">
                            <span x-show="!loading">Lanjut <i class="fas fa-chevron-right ml-1"></i></span>
                            <span x-show="loading"><i class="fas fa-spinner fa-spin"></i> Menyimpan...</span>
                        </button>
                    </div>
                </div>

                <!-- STEP 4: MENU SETUP -->
                <div x-show="currentStepId === 'menu'" x-cloak x-transition.opacity.duration.300ms>
                    <h2 class="text-2xl font-black text-gray-900 mb-2">Tambahkan Menu Pertama</h2>
                    <p class="text-gray-500 text-sm mb-6">Agar bisa langsung mencoba sistem kasir, yuk buat satu menu pertamamu!</p>
                    
                    <template x-if="hasMenu">
                        <div class="bg-blue-50 border border-blue-200 text-blue-800 p-4 rounded-xl mb-6 flex items-start gap-3">
                            <i class="fas fa-info-circle mt-0.5 text-lg"></i>
                            <div>
                                <p class="font-bold text-sm">Menu sudah tersedia.</p>
                                <p class="text-xs">Kamu sudah memiliki menu di sistem. Kamu bisa menambah menu lagi atau langsung klik Lanjut.</p>
                            </div>
                        </div>
                    </template>

                    <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-gray-100 mb-8">
                        <div class="space-y-5">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">Nama Produk/Menu <span class="text-red-500">*</span></label>
                                <input type="text" x-model="form.menu_name" class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-green-500 outline-none font-bold text-lg" placeholder="Cth: Kopi Susu Gula Aren">
                            </div>
                            
                            <div class="flex flex-col sm:flex-row gap-5">
                                <div class="flex-1">
                                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">Kategori</label>
                                    <input type="text" x-model="form.menu_category" class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-green-500 outline-none" placeholder="Cth: Minuman">
                                </div>
                                <div class="flex-1">
                                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">Harga Jual <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 font-bold">Rp</span>
                                        <input type="number" x-model="form.menu_price" class="w-full pl-12 pr-4 py-3 rounded-xl border-2 border-gray-200 focus:border-green-500 outline-none font-bold text-lg" placeholder="0">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-6">
                            <button @click="saveMenu()" :disabled="menuLoading || !form.menu_name || !form.menu_price" class="w-full bg-green-50 hover:bg-green-100 text-green-700 font-black py-4 rounded-xl border-2 border-green-200 transition disabled:opacity-50">
                                <span x-show="!menuLoading"><i class="fas fa-plus mr-1"></i> Simpan Menu Pertama</span>
                                <span x-show="menuLoading"><i class="fas fa-spinner fa-spin"></i> Menyimpan...</span>
                            </button>
                        </div>
                    </div>

                    <div class="flex justify-between items-center gap-3">
                        <button @click="backStep()" class="text-gray-500 font-bold px-4 hover:text-gray-800">Kembali</button>
                        <button @click="saveStep('menu')" :disabled="loading || (!hasMenu && !form.menu_name)" class="bg-gray-900 hover:bg-black text-white font-black py-3 px-8 rounded-xl shadow-lg transition disabled:opacity-50">
                            <span x-show="!hasMenu && form.menu_name">Simpan & Lanjut <i class="fas fa-chevron-right ml-1"></i></span>
                            <span x-show="hasMenu">Lanjut <i class="fas fa-chevron-right ml-1"></i></span>
                        </button>
                    </div>
                </div>

                <!-- STEP 5: PAYMENT -->
                <div x-show="currentStepId === 'payment'" x-cloak x-transition.opacity.duration.300ms>
                    <h2 class="text-2xl font-black text-gray-900 mb-2">Metode Pembayaran</h2>
                    <p class="text-gray-500 text-sm mb-6">Pilih pembayaran yang diterima di tokomu saat ini.</p>
                    
                    <div class="space-y-4 mb-8">
                        <div class="bg-white p-5 rounded-2xl border-2 border-green-500 shadow-sm flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-full bg-green-100 text-green-600 flex items-center justify-center text-xl"><i class="fas fa-money-bill-wave"></i></div>
                                <div>
                                    <h3 class="font-bold text-gray-900">Uang Tunai (Cash)</h3>
                                    <p class="text-xs text-gray-500">Uang pas, kembalian, laci kasir.</p>
                                </div>
                            </div>
                            <div class="bg-green-500 text-white text-[10px] font-black px-2 py-1 rounded">AKTIF DEFAULT</div>
                        </div>

                        <label class="bg-white p-5 rounded-2xl border-2 cursor-pointer shadow-sm flex items-center justify-between transition"
                               :class="form.enable_qris ? 'border-blue-500 bg-blue-50/20' : 'border-gray-100 hover:border-gray-200'">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xl"><i class="fas fa-qrcode"></i></div>
                                <div>
                                    <h3 class="font-bold text-gray-900">QRIS / E-Wallet</h3>
                                    <p class="text-xs text-gray-500">Gopay, OVO, Dana, M-Banking</p>
                                </div>
                            </div>
                            <input type="checkbox" x-model="form.enable_qris" class="w-5 h-5 text-blue-600 rounded focus:ring-blue-500">
                        </label>
                        
                        <div class="bg-amber-50 border border-amber-200 text-amber-800 p-4 rounded-xl flex items-start gap-3 mt-6">
                            <i class="fas fa-lightbulb mt-0.5"></i>
                            <div class="text-xs">
                                <b>Tips:</b> Kamu masih bisa menambahkan EDC BCA, Mandiri, transfer bank, dan metode lainnya nanti melalui menu Pengaturan Pembayaran.
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-between items-center gap-3">
                        <button @click="backStep()" class="text-gray-500 font-bold px-4 hover:text-gray-800">Kembali</button>
                        <button @click="saveStep('payment')" :disabled="loading" class="bg-gray-900 hover:bg-black text-white font-black py-3 px-8 rounded-xl shadow-lg transition disabled:opacity-50">
                            Lanjut <i class="fas fa-chevron-right ml-1"></i>
                        </button>
                    </div>
                </div>

                <!-- STEP 6: RECEIPT -->
                <div x-show="currentStepId === 'receipt'" x-cloak x-transition.opacity.duration.300ms>
                    <h2 class="text-2xl font-black text-gray-900 mb-2">Struk Pelanggan</h2>
                    <p class="text-gray-500 text-sm mb-6">Sesuaikan tampilan struk untuk pelangganmu.</p>
                    
                    <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-gray-100 mb-8 flex flex-col md:flex-row gap-8">
                        <div class="flex-1">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Slogan / Footer Struk</label>
                            <textarea x-model="form.slogan" rows="4" class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-green-500 outline-none transition resize-none" placeholder="Contoh: Terima kasih atas kunjungannya! Follow IG kami @tokoku"></textarea>
                            <p class="text-xs text-gray-400 mt-2">Pesan ini akan dicetak di bagian paling bawah struk.</p>
                        </div>
                        
                        <!-- Preview Struk Minimalis -->
                        <div class="bg-gray-50 p-6 rounded-xl border border-gray-200 font-mono text-sm w-full md:w-64 shrink-0 shadow-inner">
                            <div class="text-center font-bold text-lg mb-1" x-text="form.name || 'Nama Toko'"></div>
                            <div class="text-center text-gray-500 text-xs mb-4" x-text="form.address || 'Alamat Toko'"></div>
                            <div class="border-t border-dashed border-gray-300 my-2"></div>
                            <div class="flex justify-between text-xs my-1"><span>1x Kopi Susu</span><span>Rp 18.000</span></div>
                            <div class="border-t border-dashed border-gray-300 my-2"></div>
                            <div class="flex justify-between font-bold"><span>TOTAL</span><span>Rp 18.000</span></div>
                            <div class="flex justify-between text-xs"><span>Tunai</span><span>Rp 20.000</span></div>
                            <div class="border-t border-dashed border-gray-300 my-2"></div>
                            <div class="text-center text-xs text-gray-600 mt-4 italic whitespace-pre-wrap" x-text="form.slogan || 'Slogan toko...'"></div>
                        </div>
                    </div>

                    <div class="flex justify-between items-center gap-3">
                        <button @click="backStep()" class="text-gray-500 font-bold px-4 hover:text-gray-800">Kembali</button>
                        <button @click="saveStep('receipt')" :disabled="loading" class="bg-green-600 hover:bg-green-700 text-white font-black py-3 px-8 rounded-xl shadow-lg shadow-green-200 transition disabled:opacity-50">
                            Selesai <i class="fas fa-check ml-1"></i>
                        </button>
                    </div>
                </div>

                <!-- STEP 7: READY -->
                <div x-show="currentStepId === 'ready'" x-cloak x-transition.opacity.duration.300ms>
                    <div class="bg-white rounded-3xl p-8 sm:p-12 text-center shadow-sm border border-gray-100 relative overflow-hidden">
                        
                        <!-- Confetti background minimal -->
                        <div class="absolute inset-0 opacity-10 pointer-events-none" style="background-image: radial-gradient(#16a34a 2px, transparent 2px); background-size: 20px 20px;"></div>
                        
                        <div class="relative z-10">
                            <div class="w-24 h-24 bg-green-500 text-white rounded-full flex items-center justify-center mx-auto mb-6 text-4xl shadow-xl shadow-green-200">
                                <i class="fas fa-check-double"></i>
                            </div>
                            <h1 class="text-3xl font-black text-gray-900 mb-2">Usaha Kamu Siap Berjualan! 🎉</h1>
                            <p class="text-gray-500 mb-8 max-w-md mx-auto">
                                Konfigurasi dasar sudah selesai. Kamu sudah siap untuk melayani pelanggan pertamamu sekarang juga!
                            </p>
                            
                            <div class="text-left bg-gray-50 p-6 rounded-2xl border border-gray-100 max-w-sm mx-auto mb-8 space-y-3">
                                <div class="flex items-center text-sm font-bold text-green-700 gap-3"><i class="fas fa-check-circle"></i> Profil Usaha & Tipe Jualan</div>
                                <div class="flex items-center text-sm font-bold text-green-700 gap-3"><i class="fas fa-check-circle"></i> Menu Pertama Dibuat</div>
                                <div class="flex items-center text-sm font-bold text-green-700 gap-3"><i class="fas fa-check-circle"></i> Pembayaran Tunai & Struk</div>
                                <div class="border-t border-gray-200 my-2"></div>
                                <div class="flex items-center text-xs font-bold text-gray-400 gap-3"><i class="far fa-circle"></i> Tambah Karyawan (Bisa nanti)</div>
                                <div class="flex items-center text-xs font-bold text-gray-400 gap-3"><i class="far fa-circle"></i> Kelola Stok (Bisa nanti)</div>
                            </div>

                            <button @click="completeOnboarding()" :disabled="loading" class="bg-green-600 hover:bg-green-700 text-white font-black py-4 px-12 rounded-2xl w-full sm:w-auto shadow-xl shadow-green-200 transition-all active:scale-95 disabled:opacity-50 text-lg">
                                BUKA POS KASIR SEKARANG <i class="fas fa-arrow-right ml-2"></i>
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        function onboardingApp() {
            return {
                shop: @json($shop),
                hasMenu: {{ $hasMenu ? 'true' : 'false' }},
                hasPaymentMethod: {{ $hasPaymentMethod ? 'true' : 'false' }},
                
                loading: false,
                menuLoading: false,
                
                steps: [
                    { id: 'welcome', title: 'Selamat Datang', desc: 'Mulai perjalananmu' },
                    { id: 'business_profile', title: 'Profil Bisnis', desc: 'Nama & tipe usaha' },
                    { id: 'sales_mode', title: 'Tipe Jualan', desc: 'Dine In, Takeaway...' },
                    { id: 'menu', title: 'Menu Pertama', desc: 'Katalog produk' },
                    { id: 'payment', title: 'Pembayaran', desc: 'Metode terima uang' },
                    { id: 'receipt', title: 'Struk', desc: 'Pengaturan cetak' },
                    { id: 'ready', title: 'Ready to Sell', desc: 'Siap berjualan!' }
                ],
                
                currentStepId: '{{ $shop->onboarding_step ?? "welcome" }}',
                
                form: {
                    name: '{{ $shop->name ?? "" }}',
                    business_type: '{{ $shop->business_type ?? "" }}',
                    address: '{{ $shop->address ?? "" }}',
                    sales_modes: {!! json_encode($shop->sales_modes ?? []) !!} || ['TAKEAWAY', 'DINE_IN'],
                    
                    menu_name: '',
                    menu_category: 'Makanan Utama',
                    menu_price: '',
                    
                    enable_qris: false,
                    slogan: '{{ $shop->slogan ?? "Terima kasih atas kunjungannya!" }}'
                },
                
                errors: {},

                get currentStepIndex() {
                    let idx = this.steps.findIndex(s => s.id === this.currentStepId);
                    return Math.max(0, idx);
                },
                
                get progressPercent() {
                    return (this.currentStepIndex / (this.steps.length - 1)) * 100;
                },

                isStepCompleted(index) {
                    return index < this.currentStepIndex;
                },

                nextStep() {
                    if (this.currentStepIndex < this.steps.length - 1) {
                        this.currentStepId = this.steps[this.currentStepIndex + 1].id;
                    }
                },

                backStep() {
                    if (this.currentStepIndex > 1) {
                        this.currentStepId = this.steps[this.currentStepIndex - 1].id;
                    }
                },

                async saveStep(stepId) {
                    this.errors = {};
                    
                    if (stepId === 'business_profile') {
                        if (!this.form.name) this.errors.name = 'Nama usaha wajib diisi';
                        if (!this.form.business_type) this.errors.business_type = 'Pilih jenis usaha';
                        if (Object.keys(this.errors).length > 0) return;
                    }
                    if (stepId === 'sales_mode') {
                        if (this.form.sales_modes.length === 0) {
                            this.errors.sales_modes = 'Pilih minimal satu tipe penjualan';
                            return;
                        }
                    }

                    if (stepId === 'menu' && !this.hasMenu && this.form.menu_name) {
                        await this.saveMenu();
                        // if save menu failed, stop
                        if (!this.hasMenu) return; 
                    }

                    this.loading = true;
                    try {
                        const res = await fetch('/admin/onboarding/step', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                step: stepId,
                                data: this.form
                            })
                        });
                        const data = await res.json();
                        if (data.success) {
                            this.currentStepId = data.next_step;
                            window.scrollTo({ top: 0, behavior: 'smooth' });
                        } else {
                            alert(data.message || 'Terjadi kesalahan saat menyimpan data.');
                        }
                    } catch (e) {
                        console.error(e);
                        alert('Koneksi terputus. Silakan coba lagi.');
                    } finally {
                        this.loading = false;
                    }
                },

                async saveMenu() {
                    this.menuLoading = true;
                    try {
                        const res = await fetch('/admin/api/menu', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                name: this.form.menu_name,
                                categoryId: this.form.menu_category,
                                price: this.form.menu_price,
                            })
                        });
                        const data = await res.json();
                        if (data.success) {
                            this.hasMenu = true;
                            this.form.menu_name = '';
                            this.form.menu_price = '';
                        } else {
                            alert(data.message || 'Gagal menyimpan menu');
                        }
                    } catch (e) {
                        console.error(e);
                        alert('Terjadi kesalahan jaringan.');
                    } finally {
                        this.menuLoading = false;
                    }
                },

                async completeOnboarding() {
                    this.loading = true;
                    try {
                        const res = await fetch('/admin/onboarding/complete', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({})
                        });
                        const data = await res.json();
                        if (data.success) {
                            // Redirect to dashboard (Dashboard will handle redirecting cashier to POS if needed)
                            window.location.href = '/admin/dashboard';
                        }
                    } catch (e) {
                        console.error(e);
                        this.loading = false;
                    }
                }
            }
        }
    </script>
</body>
</html>