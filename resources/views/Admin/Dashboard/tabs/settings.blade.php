<div x-show="currentTab === 'settings'" x-cloak class="flex-grow p-8 overflow-auto hide-scroll">
            <div class="mb-8">
                <p class="text-gray-500 font-medium">Kelola informasi toko dan branding Anda.</p>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 max-w-3xl">
                <form @submit.prevent="saveSettings" class="flex flex-col gap-6">
                    <!-- Shop Name -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Nama Toko</label>
                        <input type="text" x-model="settings.name" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none transition-all" placeholder="Contoh: Bitten Coffee" required>
                    </div>

                    <!-- Slogan -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Slogan / Tagline</label>
                        <input type="text" x-model="settings.slogan" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none transition-all" placeholder="Kopi seduh manual terbaik">
                    </div>

                    <!-- URL Slug -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">URL Slug Toko</label>
                        <div class="flex items-center">
                            <span class="bg-gray-50 border border-gray-200 border-r-0 rounded-l-xl px-4 py-3 text-sm text-gray-500 font-mono flex-shrink-0" x-text="window.location.host + '/'"></span>
                            <input type="text" x-model="settings.slug" class="w-full border border-gray-200 rounded-r-xl px-4 py-3 text-sm font-mono focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none transition-all" placeholder="bitten-coffee" required>
                        </div>
                        <p class="text-xs text-gray-400 mt-2">Ini akan menjadi alamat web unik untuk menu pelanggan Anda.</p>
                    </div>

                    <!-- Is Open Toggle -->
                    <div class="flex items-center justify-between bg-gray-50 p-4 rounded-xl border border-gray-200">
                        <div>
                            <label class="block text-sm font-bold text-gray-700">Toko Buka?</label>
                            <p class="text-xs text-gray-500 mt-1">Matikan jika sedang tutup/libur agar pelanggan tidak bisa memesan.</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" x-model="settings.is_open" class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-500"></div>
                        </label>
                    </div>

                    <!-- Logo Upload -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Logo Toko (Opsional)</label>
                        <div class="flex items-center gap-6">
                            <div class="w-24 h-24 rounded-2xl border-2 border-dashed border-gray-300 flex items-center justify-center bg-gray-50 overflow-hidden relative group">
                                <img x-show="settings.logoPreview" :src="settings.logoPreview" class="w-full h-full object-cover">
                                <i x-show="!settings.logoPreview" class="fas fa-image text-3xl text-gray-300"></i>
                                <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer" @click="$refs.logoInput.click()">
                                    <i class="fas fa-camera text-white"></i>
                                </div>
                            </div>
                            <div class="flex-grow">
                                <input type="file" x-ref="logoInput" @change="handleLogoUpload" class="hidden" accept="image/*">
                                <button type="button" @click="$refs.logoInput.click()" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg font-semibold text-sm hover:bg-gray-200 transition-colors">Pilih Gambar</button>
                                <p class="text-xs text-gray-400 mt-2">Format: JPG, PNG. Maksimal 2MB.</p>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <!-- Theme Style -->
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Layout Menu</label>
                            <select x-model="settings.theme_style" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:border-primary focus:outline-none bg-white">
                                <option value="list">List View (GoFood Style)</option>
                                <option value="grid">Grid View (Instagram Style)</option>
                            </select>
                        </div>
                        <!-- Font Family -->
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Tipografi (Font)</label>
                            <select x-model="settings.font_family" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:border-primary focus:outline-none bg-white">
                                <option value="poppins">Modern (Poppins)</option>
                                <option value="playfair">Elegan (Playfair Display)</option>
                                <option value="nunito">Fun (Nunito)</option>
                            </select>
                        </div>
                    </div>

                    <!-- Primary Color -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Warna Utama (Opsional)</label>
                        <div class="flex items-center gap-3">
                            <input type="color" x-model="settings.primary_color" class="w-12 h-12 rounded cursor-pointer border-0 p-0 bg-transparent">
                            <input type="text" x-model="settings.primary_color" class="w-32 border border-gray-200 rounded-xl px-4 py-3 text-sm font-mono focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none uppercase" placeholder="#1E5A7A">
                        </div>
                        <p class="text-xs text-gray-400 mt-2">Warna ini akan digunakan sebagai warna tombol dan aksen di halaman menu pelanggan.</p>
                    </div>

                    <div class="border-t border-gray-100 my-2"></div>

                    <!-- Social Links -->
                    <h3 class="font-bold text-gray-800">Tautan Pintar (Social Links)</h3>
                    <div class="grid grid-cols-1 gap-4">
                        <div class="flex items-center">
                            <span class="bg-gray-50 border border-gray-200 border-r-0 rounded-l-xl px-4 py-3 text-pink-500 flex-shrink-0"><i class="fab fa-instagram text-lg"></i></span>
                            <input type="text" x-model="settings.instagram_link" class="w-full border border-gray-200 rounded-r-xl px-4 py-3 text-sm focus:border-primary focus:outline-none" placeholder="https://instagram.com/username">
                        </div>
                        <div class="flex items-center">
                            <span class="bg-gray-50 border border-gray-200 border-r-0 rounded-l-xl px-4 py-3 text-green-500 flex-shrink-0"><i class="fab fa-whatsapp text-lg"></i></span>
                            <input type="text" x-model="settings.whatsapp_number" class="w-full border border-gray-200 rounded-r-xl px-4 py-3 text-sm focus:border-primary focus:outline-none" placeholder="628123456789 (Awali dengan 62)">
                        </div>
                        <div class="flex items-center">
                            <span class="bg-gray-50 border border-gray-200 border-r-0 rounded-l-xl px-4 py-3 text-red-500 flex-shrink-0"><i class="fas fa-map-marker-alt text-lg"></i></span>
                            <input type="text" x-model="settings.maps_link" class="w-full border border-gray-200 rounded-r-xl px-4 py-3 text-sm focus:border-primary focus:outline-none" placeholder="Link Google Maps lokasi toko">
                        </div>
                    </div>

                    <div class="pt-4 border-t border-gray-100 flex justify-end">
                        <button type="submit" class="bg-primary text-white px-6 py-3 rounded-xl font-bold text-sm shadow-sm hover:bg-[#154660] transition-colors flex items-center gap-2" :disabled="isSavingSettings">
                            <i class="fas fa-spinner fa-spin" x-show="isSavingSettings"></i>
                            <span x-text="isSavingSettings ? 'Menyimpan...' : 'Simpan Perubahan'"></span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- FITUR SEMENTARA DINONAKTIFKAN (GoFood / GoBiz Integration) -->
            @if(false)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 max-w-3xl mt-8">
                <div class="flex items-center gap-4 mb-6">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/e/e6/GoFood_logo.svg" alt="GoFood" class="h-8">
                    <h3 class="font-bold text-gray-800 text-xl">Integrasi GoFood (GoBiz)</h3>
                </div>
                
                <p class="text-gray-500 text-sm mb-6">Hubungkan aplikasi POS Anda dengan GoFood untuk menerima pesanan secara otomatis dan menyinkronkan katalog menu.</p>
                
                @if(optional(auth()->user()->shop)->gobiz_access_token)
                    <div class="bg-green-50 border border-green-200 rounded-xl p-4 flex items-center justify-between mb-6">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-green-100 text-green-600 flex items-center justify-center">
                                <i class="fas fa-check"></i>
                            </div>
                            <div>
                                <p class="font-bold text-green-800">Toko Terhubung dengan GoFood</p>
                                <p class="text-xs text-green-600">ID Outlet: {{ auth()->user()->shop->gobiz_outlet_id ?? 'Belum disinkronisasi' }}</p>
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('admin.integrations.gobiz.sync') }}" method="POST" class="flex flex-col gap-4">
                        @csrf
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">ID Outlet GoBiz Anda</label>
                            <input type="text" name="outlet_id" value="{{ auth()->user()->shop->gobiz_outlet_id }}" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:border-primary focus:outline-none" placeholder="Masukkan ID Outlet GoBiz" required>
                        </div>
                        <button type="submit" class="bg-green-600 text-white px-6 py-3 rounded-xl font-bold text-sm shadow-sm hover:bg-green-700 transition-colors self-start">
                            <i class="fas fa-sync-alt mr-2"></i> Sinkronisasi Katalog Menu Sekarang
                        </button>
                    </form>
                @else
                    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 flex items-center justify-between mb-6">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-yellow-100 text-yellow-600 flex items-center justify-center">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                            <div>
                                <p class="font-bold text-yellow-800">Toko Belum Terhubung</p>
                                <p class="text-xs text-yellow-600">Anda perlu melakukan otorisasi akun GoBiz Anda terlebih dahulu.</p>
                            </div>
                        </div>
                    </div>
                    
                    <a href="{{ route('admin.integrations.gobiz.connect') }}" class="bg-red-600 text-white px-6 py-3 rounded-xl font-bold text-sm shadow-sm hover:bg-red-700 transition-colors inline-block">
                        Hubungkan dengan GoFood
                    </a>
                @endif
            </div>
            @endif
        </div>