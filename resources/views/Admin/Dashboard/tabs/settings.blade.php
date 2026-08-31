<div x-show="currentTab === 'settings'" x-cloak class="flex-grow bg-[#FDFBF7] overflow-auto hide-scroll relative">
    
    <!-- Sticky Action Bar -->
    <div class="sticky top-0 z-40 bg-[#FDFBF7]/95 backdrop-blur-md px-8 lg:px-10 py-4 border-b border-[#E3E1DC]/50 shadow-[0_4px_20px_-10px_rgba(0,0,0,0.05)] flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <p class="text-[#777873] font-medium text-sm"><i class="fas fa-info-circle mr-1"></i> Kelola identitas, tampilan, dan operasional toko Anda.</p>
        </div>
        <button @click="saveSettings" type="button" class="bg-[#164A35] text-white px-8 py-3 rounded-xl font-bold text-sm shadow-[0_8px_20px_-6px_rgba(22,74,53,0.5)] hover:bg-[#1e5f44] hover:-translate-y-0.5 transition-all flex items-center gap-2 justify-center" :disabled="isSavingSettings">
            <i class="fas fa-spinner fa-spin" x-show="isSavingSettings"></i>
            <i class="fas fa-save" x-show="!isSavingSettings"></i>
            <span x-text="isSavingSettings ? 'Menyimpan...' : 'Simpan Perubahan'"></span>
        </button>
    </div>

    <div class="p-8 lg:p-10 pt-8 lg:pt-8 grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
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
                                <div class="flex items-center justify-between mb-3">
                                    <label class="block text-sm font-bold text-[#202522]">Banner Promo (Max 3)</label>
                                    <div @click="settings.is_banner_active = !settings.is_banner_active" class="relative inline-flex items-center cursor-pointer group p-1 -mr-1">
                                        <div class="w-10 h-[22px] rounded-full transition-colors duration-300 ease-in-out relative shadow-inner" :class="settings.is_banner_active ? 'bg-[#164A35]' : 'bg-gray-300'">
                                            <div class="w-4 h-4 bg-white rounded-full shadow-md transform transition-transform duration-300 ease-in-out absolute top-[3px] left-[3px]" :class="settings.is_banner_active ? 'translate-x-[18px]' : 'translate-x-0'"></div>
                                        </div>
                                        <span class="ml-2 text-xs font-bold transition-colors" :class="settings.is_banner_active ? 'text-[#164A35]' : 'text-gray-400'">Aktif</span>
                                    </div>
                                </div>
                                <div class="grid grid-cols-3 gap-2" :class="!settings.is_banner_active ? 'opacity-50 pointer-events-none' : ''">
                                    <template x-for="(banner, idx) in 3" :key="idx">
                                        <div class="w-full aspect-[21/9] rounded-xl border-2 border-dashed border-[#E3E1DC] flex items-center justify-center bg-[#F8F7F3] overflow-hidden relative group cursor-pointer" @click="document.getElementById('bannerInput' + idx).click()">
                                            <img x-show="settings.banners[idx]" :src="settings.banners[idx]" class="w-full h-full object-cover">
                                            <div x-show="!settings.banners[idx]" class="flex flex-col items-center text-[#777873]">
                                                <i class="fas fa-image text-xl mb-1 text-[#E3E1DC]"></i>
                                                <span class="text-[9px] font-semibold" x-text="'Banner ' + (idx+1)"></span>
                                            </div>
                                            <div class="absolute inset-0 bg-[#202522]/60 backdrop-blur-sm flex flex-col items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                                <i class="fas fa-camera text-white mb-1"></i>
                                                <button x-show="settings.banners[idx]" @click.stop="settings.banners[idx] = null; settings.bannerFiles[idx] = null; settings.bannerPaths[idx] = null" class="text-[8px] bg-red-500 text-white px-2 py-0.5 rounded-full hover:bg-red-600">Hapus</button>
                                            </div>
                                            <input type="file" :id="'bannerInput' + idx" @change="(e) => { 
                                                const file = e.target.files[0]; 
                                                if(file) { 
                                                    settings.bannerFiles[idx] = file; 
                                                    const reader = new FileReader(); 
                                                    reader.onload = (ev) => settings.banners[idx] = ev.target.result; 
                                                    reader.readAsDataURL(file); 
                                                } 
                                            }" class="hidden" accept="image/*">
                                        </div>
                                    </template>
                                </div>
                                <p class="text-[10px] text-gray-400 mt-2">Gunakan banner untuk promo atau produk terbaru.</p>
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
            <div class="relative mx-auto w-full max-w-[340px] h-[650px] bg-gray-50 rounded-[40px] shadow-[0_20px_60px_rgba(0,0,0,0.15)] border-[8px] border-[#202522] overflow-hidden flex flex-col font-sans" :style="settings.font_family === 'poppins' ? 'font-family: Poppins, sans-serif;' : (settings.font_family === 'playfair' ? 'font-family: Playfair Display, serif;' : 'font-family: Nunito, sans-serif;')">
                <!-- Notch -->
                <div class="absolute top-0 inset-x-0 h-6 bg-[#202522] rounded-b-3xl w-40 mx-auto z-50"></div>

                <!-- Template Customer Menu Real Structure -->
                <div class="flex-1 overflow-y-auto hide-scroll pb-24 relative" style="background-image: radial-gradient(circle, #e5e7eb 1.5px, transparent 1.5px); background-size: 24px 24px;">
                    
                    <!-- Toko Tutup Overlay -->
                    <div x-show="!settings.is_open" class="absolute inset-0 bg-white/80 backdrop-blur-sm z-[200] flex flex-col items-center justify-center p-6 text-center">
                        <i class="fas fa-store-slash text-5xl text-gray-400 mb-4"></i>
                        <h2 class="text-xl font-bold text-gray-800 mb-2">Toko Sedang Tutup</h2>
                        <p class="text-xs text-gray-500 font-medium">Mohon maaf, toko kami saat ini sedang tidak menerima pesanan.</p>
                    </div>

                    <!-- Header APP (Logo, Notif, Profile) -->
                    <header class="bg-white pt-7 pb-3 px-4 z-30 relative shadow-sm">
                        <div class="flex items-center gap-2">
                            <template x-if="settings.logoPreview">
                                <img :src="settings.logoPreview" alt="Logo" class="h-10 w-10 object-contain drop-shadow-sm rounded-lg shrink-0">
                            </template>
                            <template x-if="!settings.logoPreview">
                                <div class="h-10 w-10 rounded-lg flex items-center justify-center shrink-0" :style="'background-color: ' + (settings.primary_color || '#1c4532')">
                                    <i class="fas fa-store text-white text-sm"></i>
                                </div>
                            </template>
                            <div class="flex flex-col">
                                <span class="font-extrabold text-[15px] leading-tight tracking-tight uppercase" :style="'color: ' + (settings.primary_color || '#1c4532')" x-text="settings.name || 'BITTEN COFFEE'"></span>
                                <span class="text-[8px] font-bold text-gray-500 tracking-[0.2em] mt-0.5 uppercase" x-text="settings.slogan || 'COFFEE & EATERY'"></span>
                            </div>
                        </div>
                    </header>

                    <!-- Location & Search Bar -->
                    <div class="bg-white px-4 pb-5 relative z-30 border-b border-gray-100">
                        <div class="bg-white rounded-full p-1.5 flex items-center shadow-[0_2px_8px_-3px_rgba(0,0,0,0.1)] border border-gray-200">
                            <div class="flex items-center gap-2 pl-2 pr-3 border-r border-gray-200 rounded-l-full py-1.5 shrink-0 max-w-[55%]">
                                <div class="w-7 h-7 rounded-full flex items-center justify-center shrink-0" :style="'background-color: ' + (settings.primary_color ? settings.primary_color + '15' : '#1c453215')">
                                    <i class="fas fa-map-marker-alt text-[11px]" :style="'color: ' + (settings.primary_color || '#1c4532')"></i>
                                </div>
                                <div class="flex flex-col leading-tight min-w-0">
                                    <span class="text-[9px] text-gray-400 font-bold uppercase tracking-wider truncate">Deliver To / Meja</span>
                                    <span class="text-xs font-bold text-gray-800 flex items-center gap-1 truncate">
                                        (...) <i class="fas fa-chevron-down text-[8px] text-gray-400 ml-0.5"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="flex-1 flex items-center px-3 relative">
                                <i class="fas fa-search text-gray-400 text-sm absolute left-3"></i>
                                <span class="text-xs text-gray-400 font-medium pl-6 truncate">Cari menu...</span>
                            </div>
                        </div>
                    </div>

                    <!-- Promo Carousel Section -->
                    <div x-show="settings.is_banner_active" class="px-4 mt-5 mb-8">
                        <div class="relative rounded-[20px] overflow-hidden shadow-lg h-40 group flex items-center justify-center" :style="'background-color: ' + (settings.primary_color || '#1c4532')">
                            <template x-if="settings.banners.filter(b => b !== null).length > 0">
                                <!-- Just show the first active banner as preview -->
                                <img :src="settings.banners.find(b => b !== null)" class="w-full h-full object-cover">
                            </template>
                            <template x-if="settings.banners.filter(b => b !== null).length === 0">
                                <div class="text-white/50 font-bold text-sm tracking-widest uppercase">Banner Promo</div>
                            </template>
                            <div class="absolute bottom-3 w-full flex justify-center gap-1.5 z-10">
                                <template x-for="(_, i) in (settings.banners.filter(b => b !== null).length || 1)">
                                    <div class="w-2 h-2 rounded-full" :class="i === 0 ? 'bg-white' : 'bg-white/50'"></div>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- Categories Square Grid -->
                    <div class="sticky top-0 bg-white/90 backdrop-blur-md z-20 pt-3 pb-3 border-y border-gray-100 shadow-sm">
                        <div class="flex overflow-x-auto px-4 gap-3 hide-scroll">
                            <div class="flex flex-col items-center gap-1 min-w-[70px]">
                                <div class="w-14 h-14 rounded-2xl flex items-center justify-center border-2" :style="'border-color: ' + (settings.primary_color || '#1c4532') + '; background-color: ' + (settings.primary_color ? settings.primary_color + '10' : '#1c453210')">
                                    <i class="fas fa-star text-lg" :style="'color: ' + (settings.primary_color || '#1c4532')"></i>
                                </div>
                                <span class="text-[10px] font-bold" :style="'color: ' + (settings.primary_color || '#1c4532')">All Menu</span>
                            </div>
                            <div class="flex flex-col items-center gap-1 min-w-[70px]">
                                <div class="w-14 h-14 rounded-2xl flex items-center justify-center border border-gray-200 bg-white shadow-sm">
                                    <i class="fas fa-coffee text-lg text-[#D97A32]"></i>
                                </div>
                                <span class="text-[10px] font-medium text-gray-500">Coffee</span>
                            </div>
                        </div>
                    </div>

                    <!-- Menu List Header -->
                    <div class="px-4 mt-5 flex justify-between items-center mb-1">
                        <h2 class="font-extrabold text-[19px] text-gray-800">Popular Picks</h2>
                        <span class="text-[11px] font-bold cursor-pointer" :style="'color: ' + (settings.primary_color || '#1c4532')">View All &rarr;</span>
                    </div>
                    
                    <h3 class="px-4 mt-4 mb-2 font-bold text-[15px]" :style="'color: ' + (settings.primary_color || '#1c4532')">Coffee</h3>
                    <div class="px-4 mb-4"><div class="h-px bg-gray-200 w-full"></div></div>

                    <!-- Menu Items View (List vs Grid) -->
                    <div class="px-4">
                        <!-- Grid View -->
                        <div x-show="settings.theme_style === 'grid'" class="grid grid-cols-2 gap-4">
                            <div class="bg-white p-3 rounded-[20px] shadow-sm border border-gray-100 relative">
                                <div class="w-full aspect-[4/3] bg-gray-100 rounded-xl mb-3 flex flex-col items-center justify-center text-gray-300">
                                    <i class="fas fa-image text-3xl mb-1"></i>
                                    <span class="text-[8px] font-bold tracking-wider">NO IMAGE</span>
                                </div>
                                <h4 class="font-bold text-xs text-gray-800 leading-tight">Vanilla Latte</h4>
                                <p class="font-black text-[13px] mt-1" :style="'color: ' + (settings.primary_color || '#1c4532')">Rp 25.000</p>
                                <button class="absolute bottom-3 right-3 w-6 h-6 rounded-full flex items-center justify-center text-white text-[10px] shadow-sm" :style="'background-color: ' + (settings.primary_color || '#1c4532')">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                            <div class="bg-white p-3 rounded-[20px] shadow-sm border border-gray-100 relative">
                                <div class="w-full aspect-[4/3] bg-gray-100 rounded-xl mb-3 flex flex-col items-center justify-center text-gray-300">
                                    <i class="fas fa-image text-3xl mb-1"></i>
                                </div>
                                <h4 class="font-bold text-xs text-gray-800 leading-tight">Americano</h4>
                                <p class="font-black text-[13px] mt-1" :style="'color: ' + (settings.primary_color || '#1c4532')">Rp 18.000</p>
                                <button class="absolute bottom-3 right-3 w-6 h-6 rounded-full flex items-center justify-center text-white text-[10px] shadow-sm" :style="'background-color: ' + (settings.primary_color || '#1c4532')">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>

                        <!-- List View -->
                        <div x-show="settings.theme_style === 'list'" class="flex flex-col gap-4">
                            <div class="bg-white p-3 rounded-[20px] shadow-sm border border-gray-100 flex gap-3 relative">
                                <div class="w-20 h-20 bg-gray-100 rounded-xl shrink-0 flex items-center justify-center text-gray-300">
                                    <i class="fas fa-image text-2xl"></i>
                                </div>
                                <div class="flex-1 flex flex-col justify-center pr-6">
                                    <h4 class="font-bold text-sm text-gray-800 leading-tight">Vanilla Latte</h4>
                                    <p class="text-[10px] text-gray-500 mt-1 line-clamp-2">Kopi susu vanilla lezat...</p>
                                    <p class="font-black text-sm mt-1.5" :style="'color: ' + (settings.primary_color || '#1c4532')">Rp 25.000</p>
                                </div>
                                <button class="absolute bottom-3 right-3 w-6 h-6 rounded-full flex items-center justify-center text-white text-[10px] shadow-sm" :style="'background-color: ' + (settings.primary_color || '#1c4532')">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                            <div class="bg-white p-3 rounded-[20px] shadow-sm border border-gray-100 flex gap-3 relative">
                                <div class="w-20 h-20 bg-gray-100 rounded-xl shrink-0 flex items-center justify-center text-gray-300">
                                    <i class="fas fa-image text-2xl"></i>
                                </div>
                                <div class="flex-1 flex flex-col justify-center pr-6">
                                    <h4 class="font-bold text-sm text-gray-800 leading-tight">Americano</h4>
                                    <p class="text-[10px] text-gray-500 mt-1 line-clamp-2">Espresso dengan air matang</p>
                                    <p class="font-black text-sm mt-1.5" :style="'color: ' + (settings.primary_color || '#1c4532')">Rp 18.000</p>
                                </div>
                                <button class="absolute bottom-3 right-3 w-6 h-6 rounded-full flex items-center justify-center text-white text-[10px] shadow-sm" :style="'background-color: ' + (settings.primary_color || '#1c4532')">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Floating Cart Button -->
                <div class="absolute bottom-4 inset-x-4 h-12 rounded-full shadow-xl flex items-center justify-between px-5 text-white z-40" :style="'background-color: ' + (settings.primary_color || '#1c4532')">
                    <div class="flex flex-col justify-center">
                        <span class="text-[9px] text-white/70 font-semibold">Total Pesanan</span>
                        <span class="font-bold text-sm leading-tight">Rp 0</span>
                    </div>
                    <button class="bg-white px-4 py-1.5 rounded-full font-bold shadow-md flex items-center gap-2" :style="'color: ' + (settings.primary_color || '#1c4532')">
                        <i class="fas fa-shopping-cart text-[11px]"></i>
                        <span class="bg-gray-800 text-white text-[9px] px-1.5 py-0.5 rounded-full">0</span>
                    </button>
                </div>
            </div>
            
            <p class="text-center text-xs text-[#777873] mt-4"><i class="fas fa-info-circle mr-1"></i> Preview bersifat simulasi desain.</p>
        </div>
    </div>
</div>
