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

                    <!-- URL Slug -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">URL Slug Toko</label>
                        <div class="flex items-center">
                            <span class="bg-gray-50 border border-gray-200 border-r-0 rounded-l-xl px-4 py-3 text-sm text-gray-500 font-mono flex-shrink-0" x-text="window.location.host + '/'"></span>
                            <input type="text" x-model="settings.slug" class="w-full border border-gray-200 rounded-r-xl px-4 py-3 text-sm font-mono focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none transition-all" placeholder="bitten-coffee" required>
                        </div>
                        <p class="text-xs text-gray-400 mt-2">Ini akan menjadi alamat web unik untuk menu pelanggan Anda.</p>
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

                    <!-- Primary Color -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Warna Utama (Opsional)</label>
                        <div class="flex items-center gap-3">
                            <input type="color" x-model="settings.primary_color" class="w-12 h-12 rounded cursor-pointer border-0 p-0 bg-transparent">
                            <input type="text" x-model="settings.primary_color" class="w-32 border border-gray-200 rounded-xl px-4 py-3 text-sm font-mono focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none uppercase" placeholder="#1E5A7A">
                        </div>
                        <p class="text-xs text-gray-400 mt-2">Warna ini akan digunakan sebagai warna tombol dan aksen di halaman menu pelanggan.</p>
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