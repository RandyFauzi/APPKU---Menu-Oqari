<div x-show="currentTab === 'settings'" x-cloak class="flex-grow p-8 lg:p-10 bg-[#FDFBF7] overflow-auto hide-scroll">
    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-3xl font-black text-[#202522] tracking-tight">Toko & Branding</h2>
            <p class="text-[#777873] font-medium mt-1">Kelola identitas, tampilan, dan operasional toko Anda.</p>
        </div>
        <button @click="saveSettings" type="button" class="bg-[#164A35] text-white px-6 py-3 rounded-xl font-bold text-sm shadow-sm hover:bg-[#1e5f44] transition-colors flex items-center gap-2 justify-center" :disabled="isSavingSettings">
            <i class="fas fa-spinner fa-spin" x-show="isSavingSettings"></i>
            <span x-text="isSavingSettings ? 'Menyimpan...' : 'Simpan Perubahan'"></span>
        </button>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        <!-- Kiri: Form Settings -->
        <div class="lg:col-span-8 flex flex-col gap-8">
            <form id="settingsForm" @submit.prevent="saveSettings" class="flex flex-col gap-8">
                
                <!-- Section 1: Identitas & Branding -->
                <div class="bg-white rounded-[24px] shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-[#E3E1DC] overflow-hidden">
                    <div class="p-6 border-b border-[#E3E1DC] bg-[#F8F7F3]/50">
                        <h3 class="font-bold text-lg text-[#202522]">Identitas & Branding</h3>
                        <p class="text-xs text-[#777873]">Pengaturan profil utama toko Anda.</p>
                    </div>
                    <div class="p-6 flex flex-col gap-6">
                        <div class="flex flex-col md:flex-row gap-6">
                            <!-- Logo -->
                            <div class="w-full md:w-1/3">
                                <label class="block text-sm font-bold text-[#202522] mb-3">Logo Toko</label>
                                <div class="w-full aspect-square rounded-2xl border-2 border-dashed border-[#E3E1DC] flex items-center justify-center bg-[#F8F7F3] overflow-hidden relative group cursor-pointer" @click="$refs.logoInput.click()">
                                    <img x-show="settings.logoPreview" :src="settings.logoPreview" class="w-full h-full object-cover">
                                    <div x-show="!settings.logoPreview" class="flex flex-col items-center text-[#777873]">
                                        <i class="fas fa-cloud-upload-alt text-3xl mb-2 text-[#E3E1DC]"></i>
                                        <span class="text-xs font-semibold">Upload Logo</span>
                                    </div>
                                    <div class="absolute inset-0 bg-[#202522]/40 backdrop-blur-sm flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                        <i class="fas fa-camera text-white text-xl"></i>
                                    </div>
                                </div>
                                <input type="file" x-ref="logoInput" @change="handleLogoUpload" class="hidden" accept="image/*">
                            </div>
                            <!-- Banner -->
                            <div class="w-full md:w-2/3">
                                <label class="block text-sm font-bold text-[#202522] mb-3">Cover / Banner Header</label>
                                <div class="w-full aspect-[21/9] rounded-2xl border-2 border-dashed border-[#E3E1DC] flex items-center justify-center bg-[#F8F7F3] overflow-hidden relative group cursor-pointer" @click="$refs.bannerInput.click()">
                                    <img x-show="settings.bannerPreview" :src="settings.bannerPreview" class="w-full h-full object-cover">
                                    <div x-show="!settings.bannerPreview" class="flex flex-col items-center text-[#777873]">
                                        <i class="fas fa-image text-3xl mb-2 text-[#E3E1DC]"></i>
                                        <span class="text-xs font-semibold">Upload Cover Menu</span>
                                    </div>
                                    <div class="absolute inset-0 bg-[#202522]/40 backdrop-blur-sm flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                        <i class="fas fa-camera text-white text-xl"></i>
                                    </div>
                                </div>
                                <input type="file" x-ref="bannerInput" @change="(e) => { 
                                    const file = e.target.files[0]; 
                                    if(file) { 
                                        settings.bannerFile = file; 
                                        const reader = new FileReader(); 
                                        reader.onload = (ev) => settings.bannerPreview = ev.target.result; 
                                        reader.readAsDataURL(file); 
                                    } 
                                }" class="hidden" accept="image/*">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-[#202522] mb-2">Nama Toko</label>
                            <input type="text" x-model="settings.name" class="w-full border border-[#E3E1DC] rounded-xl px-4 py-3 text-sm focus:border-[#164A35] focus:ring-1 focus:ring-[#164A35] focus:outline-none transition-all" placeholder="Contoh: Bitten Coffee" required>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-[#202522] mb-2">Slogan / Tagline</label>
                            <input type="text" x-model="settings.slogan" class="w-full border border-[#E3E1DC] rounded-xl px-4 py-3 text-sm focus:border-[#164A35] focus:ring-1 focus:ring-[#164A35] focus:outline-none transition-all" placeholder="Kopi seduh manual terbaik">
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-[#202522] mb-2">URL Slug Toko</label>
                            <div class="flex items-center">
                                <span class="bg-[#F8F7F3] border border-[#E3E1DC] border-r-0 rounded-l-xl px-4 py-3 text-sm text-[#777873] font-mono flex-shrink-0" x-text="window.location.host + '/'"></span>
                                <input type="text" x-model="settings.slug" class="w-full border border-[#E3E1DC] rounded-r-xl px-4 py-3 text-sm font-mono focus:border-[#164A35] focus:ring-1 focus:ring-[#164A35] focus:outline-none transition-all" placeholder="bitten-coffee" required>
                            </div>
                            <p class="text-xs text-[#777873] mt-2">Alamat web unik untuk menu pelanggan Anda.</p>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Tampilan & UI -->
                <div class="bg-white rounded-[24px] shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-[#E3E1DC] overflow-hidden">
                    <div class="p-6 border-b border-[#E3E1DC] bg-[#F8F7F3]/50">
                        <h3 class="font-bold text-lg text-[#202522]">Tampilan (UI/UX)</h3>
                        <p class="text-xs text-[#777873]">Ubah warna dan gaya menu agar sesuai dengan brand Anda.</p>
                    </div>
                    <div class="p-6 flex flex-col gap-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-bold text-[#202522] mb-2">Layout Menu</label>
                                <select x-model="settings.theme_style" class="w-full border border-[#E3E1DC] rounded-xl px-4 py-3 text-sm focus:border-[#164A35] focus:outline-none bg-white">
                                    <option value="list">List View (Klasik GoFood)</option>
                                    <option value="grid">Grid View (Modern Instagram)</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-[#202522] mb-2">Tipografi (Font)</label>
                                <select x-model="settings.font_family" class="w-full border border-[#E3E1DC] rounded-xl px-4 py-3 text-sm focus:border-[#164A35] focus:outline-none bg-white">
                                    <option value="poppins">Modern (Poppins)</option>
                                    <option value="playfair">Elegan (Playfair Display)</option>
                                    <option value="nunito">Fun (Nunito)</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-[#202522] mb-3">Warna Utama Brand</label>
                            <div class="flex items-center gap-4">
                                <!-- Circular color picker wrapper for modern UI -->
                                <div class="relative w-14 h-14 rounded-full overflow-hidden border-4 border-white shadow-[0_4px_12px_rgba(0,0,0,0.1)] flex-shrink-0 cursor-pointer" :style="'background-color: ' + (settings.primary_color || '#164A35')">
                                    <input type="color" x-model="settings.primary_color" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                                </div>
                                <input type="text" x-model="settings.primary_color" class="w-32 border border-[#E3E1DC] rounded-xl px-4 py-3 text-sm font-mono focus:border-[#164A35] focus:ring-1 focus:ring-[#164A35] focus:outline-none uppercase font-bold" placeholder="#1E5A7A">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 3: Operasional & Kontak -->
                <div class="bg-white rounded-[24px] shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-[#E3E1DC] overflow-hidden">
                    <div class="p-6 border-b border-[#E3E1DC] bg-[#F8F7F3]/50">
                        <h3 class="font-bold text-lg text-[#202522]">Operasional & Kontak</h3>
                        <p class="text-xs text-[#777873]">Atur jam buka otomatis dan sosial media.</p>
                    </div>
                    
                    <!-- Toko Buka Toggle -->
                    <div class="p-6 border-b border-[#E3E1DC]">
                        <div class="flex items-center justify-between bg-[#F8F7F3] p-5 rounded-2xl border border-[#E3E1DC]">
                            <div>
                                <label class="block text-sm font-bold text-[#202522]">Toko Buka Sekarang?</label>
                                <p class="text-xs text-[#777873] mt-1">Matikan untuk menutup toko secara manual darurat.</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" x-model="settings.is_open" class="sr-only peer">
                                <div class="w-14 h-7 bg-gray-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-[#164A35]"></div>
                            </label>
                        </div>
                    </div>

                    <!-- Jam Operasional -->
                    <div class="p-6 border-b border-[#E3E1DC]">
                        <h4 class="text-sm font-bold text-[#202522] mb-4">Jadwal Operasional Otomatis</h4>
                        <div class="space-y-3">
                            <template x-for="(dayStr, key) in {'monday':'Senin', 'tuesday':'Selasa', 'wednesday':'Rabu', 'thursday':'Kamis', 'friday':'Jumat', 'saturday':'Sabtu', 'sunday':'Minggu'}" :key="key">
                                <div class="flex items-center justify-between p-3 rounded-xl border border-[#E3E1DC] bg-white hover:border-[#164A35]/30 transition-colors">
                                    <div class="flex items-center gap-3 w-1/3">
                                        <input type="checkbox" :checked="!settings.operating_hours[key].is_closed" @change="settings.operating_hours[key].is_closed = !$event.target.checked" class="w-4 h-4 text-[#164A35] rounded border-gray-300 focus:ring-[#164A35]">
                                        <span class="text-sm font-semibold text-[#202522]" x-text="dayStr"></span>
                                    </div>
                                    <div class="flex items-center gap-2 w-2/3 justify-end" x-show="!settings.operating_hours[key].is_closed">
                                        <input type="time" x-model="settings.operating_hours[key].open" class="border border-[#E3E1DC] rounded-lg px-2 py-1.5 text-xs font-mono focus:border-[#164A35] focus:outline-none">
                                        <span class="text-[#777873] text-xs">s/d</span>
                                        <input type="time" x-model="settings.operating_hours[key].close" class="border border-[#E3E1DC] rounded-lg px-2 py-1.5 text-xs font-mono focus:border-[#164A35] focus:outline-none">
                                    </div>
                                    <div class="w-2/3 text-right pr-4" x-show="settings.operating_hours[key].is_closed">
                                        <span class="text-xs font-bold text-red-500 bg-red-50 px-3 py-1 rounded-full">Tutup</span>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <div class="p-6 flex flex-col gap-5">
                        <h4 class="text-sm font-bold text-[#202522]">Tautan & Sosial Media</h4>
                        <div class="flex items-center">
                            <span class="bg-[#F8F7F3] border border-[#E3E1DC] border-r-0 rounded-l-xl px-4 py-3 text-pink-500 flex-shrink-0 w-12 flex justify-center"><i class="fab fa-instagram text-lg"></i></span>
                            <input type="text" x-model="settings.instagram_link" class="w-full border border-[#E3E1DC] rounded-r-xl px-4 py-3 text-sm focus:border-[#164A35] focus:outline-none" placeholder="https://instagram.com/username">
                        </div>
                        <div class="flex items-center">
                            <span class="bg-[#F8F7F3] border border-[#E3E1DC] border-r-0 rounded-l-xl px-4 py-3 text-green-500 flex-shrink-0 w-12 flex justify-center"><i class="fab fa-whatsapp text-lg"></i></span>
                            <input type="text" x-model="settings.whatsapp_number" class="w-full border border-[#E3E1DC] rounded-r-xl px-4 py-3 text-sm focus:border-[#164A35] focus:outline-none" placeholder="628123456789 (Awali 62)">
                        </div>
                        <div class="flex items-center">
                            <span class="bg-[#F8F7F3] border border-[#E3E1DC] border-r-0 rounded-l-xl px-4 py-3 text-red-500 flex-shrink-0 w-12 flex justify-center"><i class="fas fa-map-marker-alt text-lg"></i></span>
                            <input type="text" x-model="settings.maps_link" class="w-full border border-[#E3E1DC] rounded-r-xl px-4 py-3 text-sm focus:border-[#164A35] focus:outline-none" placeholder="Link Google Maps lokasi toko">
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Kanan: Live Preview Mockup HP -->
        <div class="lg:col-span-4 sticky top-8">
            <div class="flex justify-between items-center mb-4">
                <h3 class="font-black text-[#202522]"><i class="fas fa-mobile-alt mr-2 text-[#164A35]"></i>Live Preview</h3>
            </div>
            
            <!-- Mobile Frame -->
            <div class="relative mx-auto w-full max-w-[340px] h-[650px] bg-white rounded-[40px] shadow-[0_20px_60px_rgba(0,0,0,0.15)] border-[8px] border-[#202522] overflow-hidden flex flex-col font-sans" :style="settings.font_family === 'poppins' ? 'font-family: Poppins, sans-serif;' : (settings.font_family === 'playfair' ? 'font-family: Playfair Display, serif;' : 'font-family: Nunito, sans-serif;')">
                <!-- Notch -->
                <div class="absolute top-0 inset-x-0 h-6 bg-[#202522] rounded-b-3xl w-40 mx-auto z-50"></div>

                <!-- Preview Header (Banner) -->
                <div class="h-40 bg-gray-200 relative shrink-0">
                    <img x-show="settings.bannerPreview" :src="settings.bannerPreview" class="w-full h-full object-cover">
                    <div x-show="!settings.bannerPreview" class="w-full h-full bg-gradient-to-br from-gray-300 to-gray-400"></div>
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent"></div>
                    
                    <!-- Logo & Title overlay -->
                    <div class="absolute bottom-0 left-0 p-4 flex items-end gap-3 w-full">
                        <div class="w-14 h-14 rounded-full bg-white border-2 border-white shadow-md overflow-hidden shrink-0">
                            <img x-show="settings.logoPreview" :src="settings.logoPreview" class="w-full h-full object-cover">
                            <div x-show="!settings.logoPreview" class="w-full h-full flex items-center justify-center" :style="'background-color: ' + (settings.primary_color || '#164A35')">
                                <i class="fas fa-store text-white text-lg"></i>
                            </div>
                        </div>
                        <div class="text-white pb-1 flex-1 pr-2">
                            <h2 class="font-black text-lg leading-tight drop-shadow-md truncate" x-text="settings.name || 'Nama Toko'"></h2>
                            <p class="text-[10px] font-medium opacity-90 drop-shadow-md line-clamp-1" x-text="settings.slogan || 'Slogan toko Anda'"></p>
                        </div>
                    </div>
                </div>

                <!-- Preview Content -->
                <div class="flex-1 bg-[#FDFBF7] overflow-y-auto hide-scroll p-4 pb-20">
                    <!-- Status & Socials -->
                    <div class="flex items-center justify-between mb-5">
                        <span class="px-3 py-1 rounded-full text-[10px] font-bold" :class="settings.is_open ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'" x-text="settings.is_open ? 'Buka Sekarang' : 'Sedang Tutup'"></span>
                        <div class="flex gap-2 text-gray-400">
                            <i x-show="settings.instagram_link" class="fab fa-instagram"></i>
                            <i x-show="settings.whatsapp_number" class="fab fa-whatsapp"></i>
                            <i x-show="settings.maps_link" class="fas fa-map-marker-alt"></i>
                        </div>
                    </div>

                    <!-- Dummy Menu Items -->
                    <h3 class="font-bold text-sm mb-3 text-[#202522]">Rekomendasi Menu</h3>
                    
                    <!-- List View Preview -->
                    <div x-show="settings.theme_style === 'list'" class="space-y-3">
                        <template x-for="i in 3">
                            <div class="bg-white p-3 rounded-xl shadow-[0_2px_10px_rgba(0,0,0,0.02)] border border-[#E3E1DC] flex gap-3">
                                <div class="w-16 h-16 bg-[#F8F7F3] rounded-lg shrink-0 flex items-center justify-center text-[#E3E1DC]"><i class="fas fa-coffee"></i></div>
                                <div class="flex-1">
                                    <h4 class="font-bold text-xs text-[#202522]">Signature Coffee</h4>
                                    <p class="text-[10px] text-[#777873] mt-1">Kopi andalan dengan susu rahasia...</p>
                                    <p class="font-black text-sm mt-1" :style="'color: ' + (settings.primary_color || '#164A35')">Rp 25.000</p>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Grid View Preview -->
                    <div x-show="settings.theme_style === 'grid'" class="grid grid-cols-2 gap-3">
                        <template x-for="i in 4">
                            <div class="bg-white p-2 rounded-xl shadow-[0_2px_10px_rgba(0,0,0,0.02)] border border-[#E3E1DC]">
                                <div class="w-full aspect-square bg-[#F8F7F3] rounded-lg mb-2 flex items-center justify-center text-[#E3E1DC]"><i class="fas fa-coffee"></i></div>
                                <h4 class="font-bold text-xs text-[#202522] truncate">Signature</h4>
                                <p class="font-black text-sm mt-0.5" :style="'color: ' + (settings.primary_color || '#164A35')">Rp 25.000</p>
                            </div>
                        </template>
                    </div>
                </div>
                
                <!-- Floating Cart Button -->
                <div class="absolute bottom-4 inset-x-4 h-12 rounded-full shadow-lg flex items-center justify-center text-white font-bold text-sm" :style="'background-color: ' + (settings.primary_color || '#164A35')">
                    <i class="fas fa-shopping-cart mr-2"></i> Keranjang (0)
                </div>
            </div>
            
            <p class="text-center text-xs text-[#777873] mt-4"><i class="fas fa-info-circle mr-1"></i> Preview bersifat simulasi desain.</p>
        </div>
    </div>
</div>