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
        </div>