<div x-show="currentTab === 'settings'" x-cloak class="flex-grow bg-ivory overflow-auto hide-scroll relative">
    
    <!-- Sticky Action Bar -->
    <div class="sticky top-0 z-40 bg-ivory/95 backdrop-blur-md px-8 lg:px-10 py-4 border-b border-line/50 shadow-[0_4px_20px_-10px_rgba(0,0,0,0.05)] flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <p class="text-muted font-medium text-sm"><i class="fas fa-info-circle mr-1"></i> Kelola identitas, tampilan, dan operasional toko Anda.</p>
        </div>
        <button @click="saveSettings" type="button" class="bg-primary text-white px-8 py-3 rounded-xl font-bold text-sm shadow-[0_8px_20px_-6px_rgba(30,90,122,0.5)] hover:bg-brand-700 hover:-translate-y-0.5 transition-all flex items-center gap-2 justify-center" :disabled="isSavingSettings">
            <i class="fas fa-spinner fa-spin" x-show="isSavingSettings"></i>
            <i class="fas fa-save" x-show="!isSavingSettings"></i>
            <span x-text="isSavingSettings ? 'Menyimpan...' : 'Simpan Perubahan'"></span>
        </button>
    </div>

    <div class="p-4 md:p-8 lg:p-10 pt-6 md:pt-8 lg:pt-8 grid grid-cols-1 xl:grid-cols-12 gap-8 items-start">
        <!-- Kiri: Form Settings -->
        <div class="xl:col-span-8 flex flex-col gap-8">
            <form id="settingsForm" @submit.prevent="saveSettings" class="flex flex-col gap-8">
                
                <!-- Section 1: Identitas & Branding -->
                <div class="bg-white rounded-[24px] shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-line overflow-hidden">
                    <div class="p-6 border-b border-line bg-ivory/50">
                        <h3 class="font-bold text-lg text-ink">Identitas & Branding</h3>
                        <p class="text-xs text-muted">Pengaturan profil utama toko Anda.</p>
                    </div>
                    <div class="p-6 flex flex-col gap-6">
                        <div class="flex flex-col md:flex-row gap-6">
                            <!-- Logo -->
                            <div class="w-full md:w-1/3">
                                <label class="block text-sm font-bold text-ink mb-3">Logo Toko</label>
                                <div class="w-full aspect-square rounded-2xl border-2 border-dashed border-line flex items-center justify-center bg-ivory overflow-hidden relative group cursor-pointer" @click="$refs.logoInput.click()">
                                    <img x-show="settings.logoPreview" :src="settings.logoPreview" class="w-full h-full object-cover">
                                    <div x-show="!settings.logoPreview" class="flex flex-col items-center text-muted">
                                        <i class="fas fa-cloud-upload-alt text-3xl mb-2 text-line"></i>
                                        <span class="text-xs font-semibold">Upload Logo</span>
                                    </div>
                                    <div class="absolute inset-0 bg-ink/40 backdrop-blur-sm flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                        <i class="fas fa-camera text-white text-xl"></i>
                                    </div>
                                </div>
                                <input type="file" x-ref="logoInput" @change="handleLogoUpload" class="hidden" accept="image/*">
                            </div>
                            <!-- Banner -->
                            <div class="w-full md:w-2/3">
                                <div class="flex items-center justify-between mb-3">
                                    <label class="block text-sm font-bold text-ink">Banner Promo (Max 3)</label>
                                    <div @click="settings.is_banner_active = !settings.is_banner_active" class="relative inline-flex items-center cursor-pointer group p-1 -mr-1">
                                        <div class="w-10 h-[22px] rounded-full transition-colors duration-300 ease-in-out relative shadow-inner" :class="settings.is_banner_active ? 'bg-primary' : 'bg-gray-300'">
                                            <div class="w-4 h-4 bg-white rounded-full shadow-md transform transition-transform duration-300 ease-in-out absolute top-[3px] left-[3px]" :class="settings.is_banner_active ? 'translate-x-[18px]' : 'translate-x-0'"></div>
                                        </div>
                                        <span class="ml-2 text-xs font-bold transition-colors" :class="settings.is_banner_active ? 'text-primary' : 'text-gray-400'">Aktif</span>
                                    </div>
                                </div>
                                <div class="grid grid-cols-3 gap-2" :class="!settings.is_banner_active ? 'opacity-50 pointer-events-none' : ''">
                                    <template x-for="(banner, idx) in 3" :key="idx">
                                        <div class="w-full aspect-[21/9] rounded-xl border-2 border-dashed border-line flex items-center justify-center bg-ivory overflow-hidden relative group cursor-pointer" @click="document.getElementById('bannerInput' + idx).click()">
                                            <img x-show="settings.banners[idx]" :src="settings.banners[idx]" class="w-full h-full object-cover">
                                            <div x-show="!settings.banners[idx]" class="flex flex-col items-center text-muted">
                                                <i class="fas fa-image text-xl mb-1 text-line"></i>
                                                <span class="text-[9px] font-semibold" x-text="'Banner ' + (idx+1)"></span>
                                            </div>
                                            <div class="absolute inset-0 bg-ink/60 backdrop-blur-sm flex flex-col items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
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
                            <label class="block text-sm font-bold text-ink mb-2">Nama Toko</label>
                            <input type="text" x-model="settings.name" class="w-full border border-line rounded-xl px-4 py-3 text-sm focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none transition-all" placeholder="Contoh: Bitten Coffee" required>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-ink mb-2">Slogan / Tagline</label>
                            <input type="text" x-model="settings.slogan" class="w-full border border-line rounded-xl px-4 py-3 text-sm focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none transition-all" placeholder="Kopi seduh manual terbaik">
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-ink mb-2">URL Slug Toko</label>
                            <div class="flex items-center">
                                <span class="bg-ivory border border-line border-r-0 rounded-l-xl px-4 py-3 text-sm text-muted font-mono flex-shrink-0" x-text="window.location.host + '/'"></span>
                                <input type="text" x-model="settings.slug" class="w-full border border-line rounded-r-xl px-4 py-3 text-sm font-mono bg-ivory text-muted cursor-not-allowed focus:outline-none" readonly>
                            </div>
                            <p class="text-xs text-muted mt-2"><i class="fas fa-lock mr-1"></i> Alamat unik menu pelanggan Anda bersifat permanen untuk mencegah rusaknya QR Code.</p>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Tampilan & UI -->
                <div class="bg-white rounded-[24px] shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-line">
                    <div class="p-6 border-b border-line bg-ivory/50 rounded-t-[24px]">
                        <h3 class="font-bold text-lg text-ink">Tampilan (UI/UX)</h3>
                        <p class="text-xs text-muted">Ubah layout, tipografi, dan warna menu agar selaras dengan citra cafe Anda.</p>
                    </div>
                    <div class="p-6 flex flex-col gap-6">
                        <!-- Opsi Layout Menu -->
                        <div>
                            <label class="block text-sm font-bold text-ink mb-2">Opsi Layout Menu Pelanggan</label>
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                                <!-- List -->
                                <button type="button" @click="settings.theme_style = 'list'" :class="settings.theme_style === 'list' ? 'border-primary bg-primary/5 ring-2 ring-primary/20' : 'border-line hover:border-gray-300 bg-white'" class="p-3.5 rounded-2xl border text-left flex flex-col justify-between transition-all group">
                                    <div class="flex items-center justify-between mb-2">
                                        <div class="w-8 h-8 rounded-xl flex items-center justify-center bg-gray-100 group-hover:bg-primary/10 transition-colors">
                                            <i class="fas fa-list text-xs" :class="settings.theme_style === 'list' ? 'text-primary' : 'text-gray-500'"></i>
                                        </div>
                                        <span x-show="settings.theme_style === 'list'" class="w-2.5 h-2.5 rounded-full bg-primary"></span>
                                    </div>
                                    <div>
                                        <span class="block text-xs font-bold text-ink">List View</span>
                                        <span class="block text-[10px] text-muted leading-tight mt-0.5">Klasik GoFood</span>
                                    </div>
                                </button>
                                <!-- Grid -->
                                <button type="button" @click="settings.theme_style = 'grid'" :class="settings.theme_style === 'grid' ? 'border-primary bg-primary/5 ring-2 ring-primary/20' : 'border-line hover:border-gray-300 bg-white'" class="p-3.5 rounded-2xl border text-left flex flex-col justify-between transition-all group">
                                    <div class="flex items-center justify-between mb-2">
                                        <div class="w-8 h-8 rounded-xl flex items-center justify-center bg-gray-100 group-hover:bg-primary/10 transition-colors">
                                            <i class="fas fa-th-large text-xs" :class="settings.theme_style === 'grid' ? 'text-primary' : 'text-gray-500'"></i>
                                        </div>
                                        <span x-show="settings.theme_style === 'grid'" class="w-2.5 h-2.5 rounded-full bg-primary"></span>
                                    </div>
                                    <div>
                                        <span class="block text-xs font-bold text-ink">Grid 2-Kolom</span>
                                        <span class="block text-[10px] text-muted leading-tight mt-0.5">Instagram Card</span>
                                    </div>
                                </button>
                                <!-- Compact -->
                                <button type="button" @click="settings.theme_style = 'compact'" :class="settings.theme_style === 'compact' ? 'border-primary bg-primary/5 ring-2 ring-primary/20' : 'border-line hover:border-gray-300 bg-white'" class="p-3.5 rounded-2xl border text-left flex flex-col justify-between transition-all group">
                                    <div class="flex items-center justify-between mb-2">
                                        <div class="w-8 h-8 rounded-xl flex items-center justify-center bg-gray-100 group-hover:bg-primary/10 transition-colors">
                                            <i class="fas fa-bars text-xs" :class="settings.theme_style === 'compact' ? 'text-primary' : 'text-gray-500'"></i>
                                        </div>
                                        <span x-show="settings.theme_style === 'compact'" class="w-2.5 h-2.5 rounded-full bg-primary"></span>
                                    </div>
                                    <div>
                                        <span class="block text-xs font-bold text-ink">Compact List</span>
                                        <span class="block text-[10px] text-muted leading-tight mt-0.5">Ringkas Cepat</span>
                                    </div>
                                </button>
                                <!-- Cards -->
                                <button type="button" @click="settings.theme_style = 'cards'" :class="settings.theme_style === 'cards' ? 'border-primary bg-primary/5 ring-2 ring-primary/20' : 'border-line hover:border-gray-300 bg-white'" class="p-3.5 rounded-2xl border text-left flex flex-col justify-between transition-all group">
                                    <div class="flex items-center justify-between mb-2">
                                        <div class="w-8 h-8 rounded-xl flex items-center justify-center bg-gray-100 group-hover:bg-primary/10 transition-colors">
                                            <i class="fas fa-id-card text-xs" :class="settings.theme_style === 'cards' ? 'text-primary' : 'text-gray-500'"></i>
                                        </div>
                                        <span x-show="settings.theme_style === 'cards'" class="w-2.5 h-2.5 rounded-full bg-primary"></span>
                                    </div>
                                    <div>
                                        <span class="block text-xs font-bold text-ink">Hero Cards</span>
                                        <span class="block text-[10px] text-muted leading-tight mt-0.5">Artisan Showcase</span>
                                    </div>
                                </button>
                            </div>
                        </div>

                        <!-- Tipografi Pilihan (Custom Dropdown UI) -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Dropdown 1: Tipografi Utama (Teks) -->
                            <div x-data="{ 
                                    open: false, 
                                    search: '', 
                                    get filteredFonts() {
                                        if (!this.search.trim()) return fontOptions;
                                        const q = this.search.toLowerCase();
                                        return fontOptions.filter(f => f.name.toLowerCase().includes(q) || f.category.toLowerCase().includes(q));
                                    }
                                 }" 
                                 @click.outside="open = false" 
                                 class="relative">
                                <label class="block text-sm font-bold text-ink mb-2">Tipografi Utama (Teks Menu & Konten)</label>
                                
                                <!-- Trigger Button -->
                                <button type="button" 
                                        @click="open = !open" 
                                        class="w-full bg-white border rounded-[14px] px-3.5 py-2.5 text-left flex items-center justify-between transition-all cursor-pointer shadow-xs outline-none"
                                        :class="open ? 'border-primary ring-2 ring-primary/15 shadow-sm' : 'border-line hover:border-gray-400 hover:bg-[#fafbfc]'">
                                    <div class="flex items-center gap-2.5 min-w-0">
                                        <span class="w-8 h-8 rounded-lg bg-ivory border border-line flex items-center justify-center text-xs font-bold shrink-0 text-ink shadow-2xs" 
                                              :style="'font-family: ' + getFontFamilyCss(settings.font_family)">
                                            Aa
                                        </span>
                                        <div class="flex flex-col min-w-0">
                                            <div class="flex items-center gap-1.5">
                                                <span class="text-xs font-bold text-ink truncate" x-text="getFontName(settings.font_family)"></span>
                                                <span class="text-[9px] font-semibold px-1.5 py-0.5 rounded-md bg-ivory text-muted border border-line shrink-0" x-text="getFontCategory(settings.font_family)"></span>
                                            </div>
                                            <span class="text-[11px] text-muted truncate mt-0.5" :style="'font-family: ' + getFontFamilyCss(settings.font_family)">Contoh Teks Menu Cafe</span>
                                        </div>
                                    </div>
                                    <div class="w-6 h-6 rounded-full flex items-center justify-center shrink-0 transition-colors ml-1"
                                         :class="open ? 'bg-primary/10 text-primary' : 'text-gray-400'">
                                        <i class="fas fa-chevron-down text-[11px] transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
                                    </div>
                                </button>

                                <!-- Popover Menu -->
                                <div x-show="open" 
                                     x-transition:enter="transition ease-out duration-150"
                                     x-transition:enter-start="opacity-0 translate-y-1 scale-[0.98]"
                                     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                     x-transition:leave="transition ease-in duration-100"
                                     x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                     x-transition:leave-end="opacity-0 translate-y-1 scale-[0.98]"
                                     x-cloak
                                     class="absolute left-0 right-0 top-full mt-2 z-50 bg-white border border-[#e5e7eb] rounded-[18px] shadow-[0_24px_60px_-12px_rgba(15,23,42,0.25),0_6px_20px_rgba(15,23,42,0.08)] p-3 min-w-[280px]">
                                    
                                    <!-- Header inside Dropdown -->
                                    <div class="flex items-center justify-between pb-2 mb-2 border-b border-[#f1f3f5] text-[11px]">
                                        <div class="flex items-center gap-1.5 font-bold text-[#111827] uppercase tracking-wider text-[10px]">
                                            <i class="fas fa-font text-primary"></i>
                                            <span>Daftar Tipografi</span>
                                        </div>
                                        <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-[#f8f9fa] border border-[#e9ebf0] text-[#7f837e]" 
                                              x-text="filteredFonts.length + ' font'"></span>
                                    </div>

                                    <!-- Search input inside dropdown -->
                                    <div class="relative mb-2">
                                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-xs text-gray-400 pointer-events-none"></i>
                                        <input type="text" 
                                               x-model="search" 
                                               placeholder="Cari nama font / gaya..." 
                                               style="padding-left: 34px; padding-right: 28px;"
                                               class="w-full bg-[#f8f9fa] border border-[#e5e7eb] focus:border-primary focus:bg-white focus:ring-2 focus:ring-primary/10 rounded-[10px] py-1.5 text-xs text-[#111827] placeholder:text-[#9ca3af] outline-none transition-all">
                                        <button type="button" 
                                                x-show="search.length > 0" 
                                                @click="search = ''" 
                                                class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 w-4 h-4 rounded-full flex items-center justify-center hover:bg-gray-200 transition text-[10px]">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>

                                    <!-- Scrollable Options List -->
                                    <div class="max-h-60 overflow-y-auto custom-scroll space-y-1 pr-1">
                                        <template x-for="font in filteredFonts" :key="font.id">
                                            <button type="button" 
                                                    @click="settings.font_family = font.id; open = false; search = ''" 
                                                    class="w-full text-left p-2.5 rounded-xl transition-all flex items-center justify-between group cursor-pointer"
                                                    :class="settings.font_family === font.id ? 'bg-primary/5 border border-primary/20 text-primary font-bold' : 'hover:bg-[#f8f9fa] text-[#111827] border border-transparent'">
                                                <div class="flex items-center gap-2.5 min-w-0">
                                                    <span class="w-8 h-8 rounded-lg flex items-center justify-center text-sm font-semibold shrink-0 transition-colors"
                                                          :class="settings.font_family === font.id ? 'bg-primary text-white shadow-xs' : 'bg-ivory text-muted group-hover:text-ink border border-line'"
                                                          :style="'font-family: ' + font.family">
                                                        Aa
                                                    </span>
                                                    <div class="flex flex-col min-w-0">
                                                        <div class="flex items-center gap-1.5">
                                                            <span class="text-xs font-bold truncate" x-text="font.name"></span>
                                                            <span class="text-[9px] px-1.5 py-0.5 rounded font-medium"
                                                                  :class="settings.font_family === font.id ? 'bg-primary/10 text-primary' : 'bg-gray-100 text-gray-500'"
                                                                  x-text="font.category"></span>
                                                        </div>
                                                        <span class="text-[11px] truncate mt-0.5 font-normal"
                                                              :class="settings.font_family === font.id ? 'text-primary/80' : 'text-gray-500'"
                                                              :style="'font-family: ' + font.family"
                                                              x-text="font.preview"></span>
                                                    </div>
                                                </div>
                                                <div class="shrink-0 ml-2">
                                                    <i class="fas fa-check-circle text-primary text-sm" x-show="settings.font_family === font.id"></i>
                                                </div>
                                            </button>
                                        </template>
                                    </div>
                                </div>
                            </div>

                            <!-- Dropdown 2: Font Judul Toko -->
                            <div x-data="{ 
                                    open: false, 
                                    search: '', 
                                    get filteredFonts() {
                                        if (!this.search.trim()) return fontOptions;
                                        const q = this.search.toLowerCase();
                                        return fontOptions.filter(f => f.name.toLowerCase().includes(q) || f.category.toLowerCase().includes(q));
                                    }
                                 }" 
                                 @click.outside="open = false" 
                                 class="relative">
                                <label class="block text-sm font-bold text-ink mb-2">Font Judul Toko (Nama Cafe & Brand)</label>
                                
                                <!-- Trigger Button -->
                                <button type="button" 
                                        @click="open = !open" 
                                        class="w-full bg-white border rounded-[14px] px-3.5 py-2.5 text-left flex items-center justify-between transition-all cursor-pointer shadow-xs outline-none"
                                        :class="open ? 'border-primary ring-2 ring-primary/15 shadow-sm' : 'border-line hover:border-gray-400 hover:bg-[#fafbfc]'">
                                    <div class="flex items-center gap-2.5 min-w-0">
                                        <span class="w-8 h-8 rounded-lg bg-ivory border border-line flex items-center justify-center text-xs font-bold shrink-0 text-ink shadow-2xs" 
                                              :style="'font-family: ' + getFontFamilyCss(settings.title_font_family)">
                                            Aa
                                        </span>
                                        <div class="flex flex-col min-w-0">
                                            <div class="flex items-center gap-1.5">
                                                <span class="text-xs font-bold text-ink truncate" x-text="getFontName(settings.title_font_family)"></span>
                                                <span class="text-[9px] font-semibold px-1.5 py-0.5 rounded-md bg-ivory text-muted border border-line shrink-0" x-text="getFontCategory(settings.title_font_family)"></span>
                                            </div>
                                            <span class="text-[11px] text-muted truncate mt-0.5 font-bold uppercase" :style="'font-family: ' + getFontFamilyCss(settings.title_font_family)" x-text="settings.name || 'BITTEN COFFEE'"></span>
                                        </div>
                                    </div>
                                    <div class="w-6 h-6 rounded-full flex items-center justify-center shrink-0 transition-colors ml-1"
                                         :class="open ? 'bg-primary/10 text-primary' : 'text-gray-400'">
                                        <i class="fas fa-chevron-down text-[11px] transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
                                    </div>
                                </button>

                                <!-- Popover Menu -->
                                <div x-show="open" 
                                     x-transition:enter="transition ease-out duration-150"
                                     x-transition:enter-start="opacity-0 translate-y-1 scale-[0.98]"
                                     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                     x-transition:leave="transition ease-in duration-100"
                                     x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                     x-transition:leave-end="opacity-0 translate-y-1 scale-[0.98]"
                                     x-cloak
                                     class="absolute left-0 right-0 top-full mt-2 z-50 bg-white border border-[#e5e7eb] rounded-[18px] shadow-[0_24px_60px_-12px_rgba(15,23,42,0.25),0_6px_20px_rgba(15,23,42,0.08)] p-3 min-w-[280px]">
                                    
                                    <!-- Header inside Dropdown -->
                                    <div class="flex items-center justify-between pb-2 mb-2 border-b border-[#f1f3f5] text-[11px]">
                                        <div class="flex items-center gap-1.5 font-bold text-[#111827] uppercase tracking-wider text-[10px]">
                                            <i class="fas fa-heading text-primary"></i>
                                            <span>Daftar Font Judul</span>
                                        </div>
                                        <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-[#f8f9fa] border border-[#e9ebf0] text-[#7f837e]" 
                                              x-text="filteredFonts.length + ' font'"></span>
                                    </div>

                                    <!-- Search input inside dropdown -->
                                    <div class="relative mb-2">
                                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-xs text-gray-400 pointer-events-none"></i>
                                        <input type="text" 
                                               x-model="search" 
                                               placeholder="Cari nama font / gaya..." 
                                               style="padding-left: 34px; padding-right: 28px;"
                                               class="w-full bg-[#f8f9fa] border border-[#e5e7eb] focus:border-primary focus:bg-white focus:ring-2 focus:ring-primary/10 rounded-[10px] py-1.5 text-xs text-[#111827] placeholder:text-[#9ca3af] outline-none transition-all">
                                        <button type="button" 
                                                x-show="search.length > 0" 
                                                @click="search = ''" 
                                                class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 w-4 h-4 rounded-full flex items-center justify-center hover:bg-gray-200 transition text-[10px]">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>

                                    <!-- Scrollable Options List -->
                                    <div class="max-h-60 overflow-y-auto custom-scroll space-y-1 pr-1">
                                        <template x-for="font in filteredFonts" :key="font.id">
                                            <button type="button" 
                                                    @click="settings.title_font_family = font.id; open = false; search = ''" 
                                                    class="w-full text-left p-2.5 rounded-xl transition-all flex items-center justify-between group cursor-pointer"
                                                    :class="settings.title_font_family === font.id ? 'bg-primary/5 border border-primary/20 text-primary font-bold' : 'hover:bg-[#f8f9fa] text-[#111827] border border-transparent'">
                                                <div class="flex items-center gap-2.5 min-w-0">
                                                    <span class="w-8 h-8 rounded-lg flex items-center justify-center text-sm font-semibold shrink-0 transition-colors"
                                                          :class="settings.title_font_family === font.id ? 'bg-primary text-white shadow-xs' : 'bg-ivory text-muted group-hover:text-ink border border-line'"
                                                          :style="'font-family: ' + font.family">
                                                        Aa
                                                    </span>
                                                    <div class="flex flex-col min-w-0">
                                                        <div class="flex items-center gap-1.5">
                                                            <span class="text-xs font-bold truncate" x-text="font.name"></span>
                                                            <span class="text-[9px] px-1.5 py-0.5 rounded font-medium"
                                                                  :class="settings.title_font_family === font.id ? 'bg-primary/10 text-primary' : 'bg-gray-100 text-gray-500'"
                                                                  x-text="font.category"></span>
                                                        </div>
                                                        <span class="text-[12px] truncate mt-0.5 font-bold uppercase"
                                                              :class="settings.title_font_family === font.id ? 'text-primary' : 'text-gray-800'"
                                                              :style="'font-family: ' + font.family"
                                                              x-text="settings.name || 'BITTEN COFFEE'"></span>
                                                    </div>
                                                </div>
                                                <div class="shrink-0 ml-2">
                                                    <i class="fas fa-check-circle text-primary text-sm" x-show="settings.title_font_family === font.id"></i>
                                                </div>
                                            </button>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Warna Aksen Cafe / Brand -->
                        <div class="space-y-3">
                            <label class="block text-sm font-bold text-ink">Warna Aksen Cafe / Brand</label>
                            <div class="flex items-center gap-3">
                                <input type="color" 
                                       x-model="settings.primary_color" 
                                       class="w-10 h-10 rounded-xl cursor-pointer border border-line p-0 bg-white shadow-xs hover:border-gray-400 transition-all">
                                <input type="text" 
                                       x-model="settings.primary_color" 
                                       placeholder="#1E5A7A"
                                       @blur="if (settings.primary_color && !settings.primary_color.startsWith('#')) settings.primary_color = '#' + settings.primary_color"
                                       class="w-32 border border-line rounded-xl px-3 py-2 text-xs font-mono font-bold uppercase focus:border-primary focus:outline-none bg-white shadow-xs">
                                <button type="button" 
                                        @click="settings.primary_color = '#1E5A7A'" 
                                        class="text-xs text-muted hover:text-ink underline">
                                    Reset ke Standar (#1E5A7A)
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 3: Latar Belakang Menu Pelanggan -->
                <div class="bg-white rounded-[24px] shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-line overflow-hidden">
                    <div class="p-6 border-b border-line bg-ivory/50">
                        <h3 class="font-bold text-lg text-ink">Latar Belakang Menu Pelanggan</h3>
                        <p class="text-xs text-muted">Atur tekstur pola atau foto suasana cafe sebagai latar belakang halaman menu.</p>
                    </div>
                    <div class="p-6 flex flex-col gap-6">
                        <!-- Tipe Background Selector -->
                        <div>
                            <label class="block text-sm font-bold text-ink mb-2">Tipe Latar Belakang</label>
                            <div class="grid grid-cols-3 gap-3">
                                <button type="button" @click="settings.background_type = 'pattern'" :class="settings.background_type === 'pattern' ? 'border-primary bg-primary/5 ring-2 ring-primary/20 text-primary font-bold' : 'border-line text-ink bg-white hover:border-gray-300'" class="py-3 px-4 rounded-xl border flex items-center justify-center gap-2 text-xs transition-all">
                                    <i class="fas fa-shapes"></i>
                                    <span>Pola Tekstur</span>
                                </button>
                                <button type="button" @click="settings.background_type = 'image'" :class="settings.background_type === 'image' ? 'border-primary bg-primary/5 ring-2 ring-primary/20 text-primary font-bold' : 'border-line text-ink bg-white hover:border-gray-300'" class="py-3 px-4 rounded-xl border flex items-center justify-center gap-2 text-xs transition-all">
                                    <i class="fas fa-image"></i>
                                    <span>Foto Kustom</span>
                                </button>
                                <button type="button" @click="settings.background_type = 'solid'" :class="settings.background_type === 'solid' ? 'border-primary bg-primary/5 ring-2 ring-primary/20 text-primary font-bold' : 'border-line text-ink bg-white hover:border-gray-300'" class="py-3 px-4 rounded-xl border flex items-center justify-center gap-2 text-xs transition-all">
                                    <i class="fas fa-paint-roller"></i>
                                    <span>Warna Polos</span>
                                </button>
                            </div>
                        </div>

                        <!-- Pilihan Pola Tekstur (Pattern) -->
                        <div x-show="settings.background_type === 'pattern'" class="space-y-3">
                            <label class="block text-xs font-bold text-ink">Pilihan Motif Pattern Estetik</label>
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                                <!-- Dots -->
                                <div @click="settings.background_pattern = 'dots'" :class="settings.background_pattern === 'dots' ? 'border-primary ring-2 ring-primary/20 font-bold' : 'border-line hover:border-gray-300'" class="cursor-pointer rounded-2xl border p-3 flex flex-col items-center gap-2 bg-white transition-all">
                                    <div class="w-full h-14 rounded-xl border border-gray-100" style="background-color: #fdfbf7; background-image: radial-gradient(circle, #cbd5e1 1.5px, transparent 1.5px); background-size: 14px 14px;"></div>
                                    <span class="text-xs text-ink">Polkadot Halus</span>
                                </div>
                                <!-- Grid -->
                                <div @click="settings.background_pattern = 'grid'" :class="settings.background_pattern === 'grid' ? 'border-primary ring-2 ring-primary/20 font-bold' : 'border-line hover:border-gray-300'" class="cursor-pointer rounded-2xl border p-3 flex flex-col items-center gap-2 bg-white transition-all">
                                    <div class="w-full h-14 rounded-xl border border-gray-100" style="background-color: #fdfbf7; background-image: linear-gradient(to right, rgba(0,0,0,0.06) 1px, transparent 1px), linear-gradient(to bottom, rgba(0,0,0,0.06) 1px, transparent 1px); background-size: 14px 14px;"></div>
                                    <span class="text-xs text-ink">Grid Minimalis</span>
                                </div>
                                <!-- Cafe / Beans -->
                                <div @click="settings.background_pattern = 'cafe'" :class="settings.background_pattern === 'cafe' ? 'border-primary ring-2 ring-primary/20 font-bold' : 'border-line hover:border-gray-300'" class="cursor-pointer rounded-2xl border p-3 flex flex-col items-center gap-2 bg-white transition-all">
                                    <div class="w-full h-14 rounded-xl border border-gray-100 flex items-center justify-center" style="background-color: #fdfbf7; background-image: radial-gradient(#8d6e63 1.5px, transparent 1.5px), radial-gradient(#bcaaa4 1px, transparent 1px); background-size: 20px 20px, 10px 10px; background-position: 0 0, 10px 10px;">
                                        <i class="fas fa-coffee text-muted text-xs opacity-40"></i>
                                    </div>
                                    <span class="text-xs text-ink">Coffee Texture</span>
                                </div>
                                <!-- Waves -->
                                <div @click="settings.background_pattern = 'waves'" :class="settings.background_pattern === 'waves' ? 'border-primary ring-2 ring-primary/20 font-bold' : 'border-line hover:border-gray-300'" class="cursor-pointer rounded-2xl border p-3 flex flex-col items-center gap-2 bg-white transition-all">
                                    <div class="w-full h-14 rounded-xl border border-gray-100" style="background-color: #fdfbf7; background-image: repeating-radial-gradient(circle at 0 0, transparent 0, #fdfbf7 10px), repeating-linear-gradient(#f1f5f955, #e2e8f055);"></div>
                                    <span class="text-xs text-ink">Gelombang Lembut</span>
                                </div>
                                <!-- Stripes -->
                                <div @click="settings.background_pattern = 'stripes'" :class="settings.background_pattern === 'stripes' ? 'border-primary ring-2 ring-primary/20 font-bold' : 'border-line hover:border-gray-300'" class="cursor-pointer rounded-2xl border p-3 flex flex-col items-center gap-2 bg-white transition-all">
                                    <div class="w-full h-14 rounded-xl border border-gray-100" style="background-color: #fdfbf7; background-image: repeating-linear-gradient(45deg, transparent, transparent 8px, rgba(0, 0, 0, 0.04) 8px, rgba(0, 0, 0, 0.04) 16px);"></div>
                                    <span class="text-xs text-ink">Garis Diagonal</span>
                                </div>
                                <!-- Moroccan / Tile -->
                                <div @click="settings.background_pattern = 'moroccan'" :class="settings.background_pattern === 'moroccan' ? 'border-primary ring-2 ring-primary/20 font-bold' : 'border-line hover:border-gray-300'" class="cursor-pointer rounded-2xl border p-3 flex flex-col items-center gap-2 bg-white transition-all">
                                    <div class="w-full h-14 rounded-xl border border-gray-100" style="background-color: #fdfbf7; background-image: radial-gradient(#00000010 2px, transparent 2px), radial-gradient(#00000010 2px, transparent 2px); background-size: 16px 16px; background-position: 0 0, 8px 8px;"></div>
                                    <span class="text-xs text-ink">Ornamen Ubin</span>
                                </div>
                            </div>
                        </div>

                        <!-- Pilihan Gambar Kustom (Image) -->
                        <div x-show="settings.background_type === 'image'" class="space-y-4">
                            <div>
                                <label class="block text-xs font-bold text-ink mb-2">Upload Foto Latar Menu</label>
                                <div class="w-full aspect-[21/9] rounded-2xl border-2 border-dashed border-line flex items-center justify-center bg-ivory overflow-hidden relative group cursor-pointer" @click="$refs.bgImageInput.click()">
                                    <img x-show="settings.backgroundImagePreview" :src="settings.backgroundImagePreview" class="w-full h-full object-cover">
                                    <div x-show="!settings.backgroundImagePreview" class="flex flex-col items-center text-muted">
                                        <i class="fas fa-image text-3xl mb-2 text-line"></i>
                                        <span class="text-xs font-semibold">Upload Foto Cafe / Suasana Menu</span>
                                        <span class="text-[10px] text-gray-400 mt-1">Format: JPG, PNG, WebP (Maks. 5MB)</span>
                                    </div>
                                    <div class="absolute inset-0 bg-ink/50 backdrop-blur-sm flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity gap-3">
                                        <span class="text-xs text-white bg-white/20 px-3 py-1.5 rounded-full"><i class="fas fa-camera mr-1"></i> Ganti Foto</span>
                                        <button x-show="settings.backgroundImagePreview" type="button" @click.stop="removeBgImage()" class="text-xs bg-red-500 text-white px-3 py-1.5 rounded-full hover:bg-red-600"><i class="fas fa-trash mr-1"></i> Hapus</button>
                                    </div>
                                </div>
                                <input type="file" x-ref="bgImageInput" @change="handleBgImageUpload" class="hidden" accept="image/*">
                            </div>

                            <!-- Overlay Opacity Slider -->
                            <div class="p-4 rounded-2xl bg-ivory/60 border border-line space-y-2">
                                <div class="flex items-center justify-between">
                                    <label class="text-xs font-bold text-ink">Kecerahan Lapisan Pelindung (Overlay Dimming)</label>
                                    <span class="text-xs font-bold text-primary font-mono" x-text="settings.background_overlay_opacity + '%'"></span>
                                </div>
                                <input type="range" min="30" max="95" step="5" x-model="settings.background_overlay_opacity" class="w-full accent-primary cursor-pointer">
                                <p class="text-[11px] text-muted">Lapisan transparan menjaga kontras agar tulisan menu tetap tajam dan mudah dibaca oleh pelanggan.</p>
                            </div>
                        </div>

                        <!-- Pilihan Warna Polos (Solid) -->
                        <div x-show="settings.background_type === 'solid'" class="space-y-3">
                            <label class="block text-xs font-bold text-ink">Pilih Warna Latar Belakang</label>
                            <div class="flex items-center gap-3">
                                <input type="color" x-model="settings.background_color" class="w-10 h-10 rounded-xl cursor-pointer border border-line p-0">
                                <input type="text" x-model="settings.background_color" class="w-32 border border-line rounded-xl px-3 py-2 text-xs font-mono font-bold uppercase focus:border-primary focus:outline-none bg-white">
                                <button type="button" @click="settings.background_color = '#fdfbf7'" class="text-xs text-muted hover:text-ink underline">Reset ke Ivory (#fdfbf7)</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 4: Sistem Setting Best Seller / Highlight -->
                <div class="bg-white rounded-[24px] shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-line overflow-hidden">
                    <div class="p-6 border-b border-line bg-ivory/50 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="font-bold text-lg text-ink">Menu Unggulan & Best Seller</h3>
                                <span class="bg-amber-100 text-amber-800 text-xs px-2.5 py-0.5 rounded-full font-bold" x-text="settings.best_seller_ids.length + ' Ditandai'"></span>
                            </div>
                            <p class="text-xs text-muted">Pilih produk favorit yang akan dipromosikan di rak sorotan menu pelanggan.</p>
                        </div>
                    </div>
                    <div class="p-6 flex flex-col gap-4">
                        <!-- Search & Filter Controls -->
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="relative flex-1">
                                <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-muted text-xs"></i>
                                <input type="text" x-model="bestSellerSearch" placeholder="Cari nama menu..." class="w-full border border-line rounded-xl pl-9 pr-4 py-2 text-xs focus:border-primary focus:outline-none bg-white">
                            </div>
                            <div class="flex items-center gap-2">
                                <button type="button" @click="bestSellerFilter = 'all'" :class="bestSellerFilter === 'all' ? 'bg-ink text-white font-bold' : 'bg-ivory text-muted hover:text-ink border border-line'" class="px-3 py-1.5 rounded-xl text-xs transition-colors">
                                    Semua (<span x-text="allShopProducts.length"></span>)
                                </button>
                                <button type="button" @click="bestSellerFilter = 'selected'" :class="bestSellerFilter === 'selected' ? 'bg-amber-600 text-white font-bold' : 'bg-ivory text-muted hover:text-ink border border-line'" class="px-3 py-1.5 rounded-xl text-xs transition-colors flex items-center gap-1.5">
                                    <i class="fas fa-star text-[10px]"></i>
                                    <span>Ditandai</span> (<span x-text="settings.best_seller_ids.length"></span>)
                                </button>
                            </div>
                        </div>

                        <!-- Product List -->
                        <div class="border border-line rounded-2xl overflow-hidden max-h-72 overflow-y-auto divide-y divide-line">
                            <template x-for="prod in filteredBestSellerProducts" :key="prod.id">
                                <div class="p-3 flex items-center justify-between gap-3 hover:bg-ivory/50 transition-colors" :class="settings.best_seller_ids.includes(prod.id) ? 'bg-amber-50/50' : 'bg-white'">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <img :src="prod.image_url || '/Assests/null%20image.webp'" class="w-10 h-10 rounded-xl object-cover border border-line shrink-0" onerror="this.onerror=null; this.src='/Assests/null%20image.webp'">
                                        <div class="truncate">
                                            <div class="flex items-center gap-1.5">
                                                <span class="text-xs font-bold text-ink truncate" x-text="prod.name"></span>
                                                <span x-show="settings.best_seller_ids.includes(prod.id)" class="text-[10px] text-amber-600 font-extrabold flex items-center gap-0.5 shrink-0"><i class="fas fa-star text-[9px]"></i> Best Seller</span>
                                            </div>
                                            <span class="text-[11px] text-muted" x-text="formatRp(prod.price) + (prod.category?.name ? ' • ' + prod.category.name : '')"></span>
                                        </div>
                                    </div>
                                    <button type="button" @click="toggleBestSeller(prod.id)" :class="settings.best_seller_ids.includes(prod.id) ? 'bg-amber-500 text-white hover:bg-amber-600 shadow-sm' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'" class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all shrink-0 flex items-center gap-1.5">
                                        <i class="fas" :class="settings.best_seller_ids.includes(prod.id) ? 'fa-check' : 'fa-plus'"></i>
                                        <span x-text="settings.best_seller_ids.includes(prod.id) ? 'Terpilih' : 'Tandai'"></span>
                                    </button>
                                </div>
                            </template>
                            <div x-show="filteredBestSellerProducts.length === 0" class="p-8 text-center text-xs text-muted">
                                <i class="fas fa-utensils text-2xl text-line mb-2"></i>
                                <p>Tidak ada menu yang sesuai pencarian.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 5: Operasional & Kontak -->
                <div class="bg-white rounded-[24px] shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-line overflow-hidden">
                    <div class="p-6 border-b border-line bg-ivory/50">
                        <h3 class="font-bold text-lg text-ink">Operasional & Tautan Media Sosial</h3>
                        <p class="text-xs text-muted">Atur jam buka operasional dan pilih sosmed yang ingin ditampilkan di menu pelanggan.</p>
                    </div>
                    
                    <!-- Jam Operasional -->
                    <div class="p-6 border-b border-line">
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="text-sm font-bold text-ink">Jadwal Operasional</h4>
                            <a href="{{ route('admin.operating-hours.index') }}" class="text-xs font-bold text-primary hover:underline flex items-center gap-1.5">
                                <span>Atur Jam Buka & Hari Libur</span>
                                <i class="fas fa-arrow-right text-[10px]"></i>
                            </a>
                        </div>
                        <div class="space-y-3">
                            <template x-for="(dayStr, key) in {'monday':'Senin', 'tuesday':'Selasa', 'wednesday':'Rabu', 'thursday':'Kamis', 'friday':'Jumat', 'saturday':'Sabtu', 'sunday':'Minggu'}" :key="key">
                                <div class="flex items-center justify-between p-3 rounded-xl border border-line bg-white hover:border-primary/30 transition-colors" x-data="{ get dayItem() { return (settings.operating_hours?.weekly ? settings.operating_hours.weekly[key] : (settings.operating_hours?.[key] || { open: '08:00', close: '22:00', is_closed: false })); } }">
                                    <div class="flex items-center gap-3 w-1/3">
                                        <input type="checkbox" :checked="!dayItem.is_closed" @change="dayItem.is_closed = !$event.target.checked" class="w-4 h-4 text-primary rounded border-gray-300 focus:ring-primary">
                                        <span class="text-sm font-semibold text-ink" x-text="dayStr"></span>
                                    </div>
                                    <div class="flex items-center gap-2 w-2/3 justify-end" x-show="!dayItem.is_closed">
                                        <input type="time" x-model="dayItem.open" class="border border-line rounded-lg px-2 py-1.5 text-xs font-mono focus:border-primary focus:outline-none">
                                        <span class="text-muted text-xs">s/d</span>
                                        <input type="time" x-model="dayItem.close" class="border border-line rounded-lg px-2 py-1.5 text-xs font-mono focus:border-primary focus:outline-none">
                                    </div>
                                    <div class="w-2/3 text-right pr-4" x-show="dayItem.is_closed">
                                        <span class="text-xs font-bold text-red-500 bg-red-50 px-3 py-1 rounded-full">Tutup</span>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Tautan Sosial Media & Toggles -->
                    <div class="p-6 flex flex-col gap-4">
                        <div>
                            <h4 class="text-sm font-bold text-ink">Tautan & Visibilitas Media Sosial</h4>
                            <p class="text-xs text-muted">Centang "Tampil di Menu" agar ikon tampil pada footer menu pelanggan.</p>
                        </div>

                        <!-- TikTok -->
                        <div class="flex flex-col sm:flex-row sm:items-center gap-3 p-3.5 rounded-2xl border border-line bg-white">
                            <div class="flex items-center gap-3 flex-1">
                                <span class="w-10 h-10 rounded-xl bg-black text-white flex items-center justify-center shrink-0">
                                    <i class="fab fa-tiktok text-lg"></i>
                                </span>
                                <div class="flex-1">
                                    <div class="flex items-center">
                                        <span class="bg-ivory border border-line border-r-0 rounded-l-xl px-3 py-2 text-muted font-mono text-xs shrink-0">tiktok.com/@</span>
                                        <input type="text" x-model="settings.tiktok_link" class="w-full border border-line rounded-r-xl px-3 py-2 text-xs focus:border-primary focus:outline-none" placeholder="username_toko">
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center justify-between sm:justify-end gap-2 pl-12 sm:pl-0 border-t sm:border-t-0 pt-2 sm:pt-0 border-line">
                                <label class="text-xs font-semibold text-ink cursor-pointer flex items-center gap-2">
                                    <input type="checkbox" x-model="settings.social_settings.show_tiktok" class="w-4 h-4 text-primary rounded border-gray-300 focus:ring-primary">
                                    <span>Tampil di Menu</span>
                                </label>
                            </div>
                        </div>

                        <!-- Instagram -->
                        <div class="flex flex-col sm:flex-row sm:items-center gap-3 p-3.5 rounded-2xl border border-line bg-white">
                            <div class="flex items-center gap-3 flex-1">
                                <span class="w-10 h-10 rounded-xl bg-pink-500 text-white flex items-center justify-center shrink-0">
                                    <i class="fab fa-instagram text-lg"></i>
                                </span>
                                <div class="flex-1">
                                    <div class="flex items-center">
                                        <span class="bg-ivory border border-line border-r-0 rounded-l-xl px-3 py-2 text-muted font-mono text-xs shrink-0">instagram.com/</span>
                                        <input type="text" x-model="settings.instagram_link" class="w-full border border-line rounded-r-xl px-3 py-2 text-xs focus:border-primary focus:outline-none" placeholder="username_toko">
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center justify-between sm:justify-end gap-2 pl-12 sm:pl-0 border-t sm:border-t-0 pt-2 sm:pt-0 border-line">
                                <label class="text-xs font-semibold text-ink cursor-pointer flex items-center gap-2">
                                    <input type="checkbox" x-model="settings.social_settings.show_instagram" class="w-4 h-4 text-primary rounded border-gray-300 focus:ring-primary">
                                    <span>Tampil di Menu</span>
                                </label>
                            </div>
                        </div>

                        <!-- WhatsApp -->
                        <div class="flex flex-col sm:flex-row sm:items-center gap-3 p-3.5 rounded-2xl border border-line bg-white">
                            <div class="flex items-center gap-3 flex-1">
                                <span class="w-10 h-10 rounded-xl bg-green-500 text-white flex items-center justify-center shrink-0">
                                    <i class="fab fa-whatsapp text-lg"></i>
                                </span>
                                <div class="flex-1">
                                    <input type="text" x-model="settings.whatsapp_number" class="w-full border border-line rounded-xl px-3 py-2 text-xs focus:border-primary focus:outline-none" placeholder="Nomor WhatsApp (Awali 62, contoh: 6281234567890)">
                                </div>
                            </div>
                            <div class="flex items-center justify-between sm:justify-end gap-2 pl-12 sm:pl-0 border-t sm:border-t-0 pt-2 sm:pt-0 border-line">
                                <label class="text-xs font-semibold text-ink cursor-pointer flex items-center gap-2">
                                    <input type="checkbox" x-model="settings.social_settings.show_whatsapp" class="w-4 h-4 text-primary rounded border-gray-300 focus:ring-primary">
                                    <span>Tampil di Menu</span>
                                </label>
                            </div>
                        </div>

                        <!-- Google Maps -->
                        <div class="flex flex-col sm:flex-row sm:items-center gap-3 p-3.5 rounded-2xl border border-line bg-white">
                            <div class="flex items-center gap-3 flex-1">
                                <span class="w-10 h-10 rounded-xl bg-red-500 text-white flex items-center justify-center shrink-0">
                                    <i class="fas fa-map-marker-alt text-lg"></i>
                                </span>
                                <div class="flex-1">
                                    <input type="text" x-model="settings.maps_link" class="w-full border border-line rounded-xl px-3 py-2 text-xs focus:border-primary focus:outline-none" placeholder="Link tautan Google Maps lokasi cafe">
                                </div>
                            </div>
                            <div class="flex items-center justify-between sm:justify-end gap-2 pl-12 sm:pl-0 border-t sm:border-t-0 pt-2 sm:pt-0 border-line">
                                <label class="text-xs font-semibold text-ink cursor-pointer flex items-center gap-2">
                                    <input type="checkbox" x-model="settings.social_settings.show_maps" class="w-4 h-4 text-primary rounded border-gray-300 focus:ring-primary">
                                    <span>Tampil di Menu</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 6: Pengaturan Diskon Kasir & Shortcut Dinamis -->
                <div class="bg-white rounded-[24px] shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-line overflow-hidden">
                    <div class="p-6 border-b border-line bg-ivory/50 flex items-center justify-between">
                        <div>
                            <h3 class="font-bold text-lg text-ink">Pengaturan Diskon Kasir (POS)</h3>
                            <p class="text-xs text-muted">Aktifkan fitur diskon, atur shortcut cepat untuk kasir, dan izinkan input manual.</p>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-orange-100 text-[#df5826] flex items-center justify-center text-lg">
                            <i class="fas fa-percent"></i>
                        </div>
                    </div>
                    <div class="p-6 flex flex-col gap-6">
                        <!-- Toggles Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Toggle 1: Fitur Diskon Aktif -->
                            <div class="p-4 rounded-2xl border border-line bg-ivory/30 flex items-center justify-between gap-4">
                                <div>
                                    <h4 class="font-bold text-sm text-ink mb-1">Fitur Diskon di Kasir</h4>
                                    <p class="text-xs text-muted">Tampilkan tombol diskon dan voucher pada menu kasir POS.</p>
                                </div>
                                <div @click="settings.discount_settings.is_enabled = !settings.discount_settings.is_enabled" class="relative inline-flex items-center cursor-pointer group p-1 shrink-0">
                                    <div class="w-11 h-6 rounded-full transition-colors duration-300 relative shadow-inner" :class="settings.discount_settings.is_enabled ? 'bg-primary' : 'bg-gray-300'">
                                        <div class="w-4 h-4 bg-white rounded-full shadow-md transform transition-transform duration-300 absolute top-1 left-1" :class="settings.discount_settings.is_enabled ? 'translate-x-5' : 'translate-x-0'"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Toggle 2: Diskon Manual Fleksibel -->
                            <div class="p-4 rounded-2xl border border-line bg-ivory/30 flex items-center justify-between gap-4" :class="!settings.discount_settings.is_enabled ? 'opacity-50 pointer-events-none' : ''">
                                <div>
                                    <h4 class="font-bold text-sm text-ink mb-1">Izinkan Diskon Manual Bebas</h4>
                                    <p class="text-xs text-muted">Kasir dapat menginput angka nominal atau persentase bebas.</p>
                                </div>
                                <div @click="settings.discount_settings.allow_custom = !settings.discount_settings.allow_custom" class="relative inline-flex items-center cursor-pointer group p-1 shrink-0">
                                    <div class="w-11 h-6 rounded-full transition-colors duration-300 relative shadow-inner" :class="settings.discount_settings.allow_custom ? 'bg-primary' : 'bg-gray-300'">
                                        <div class="w-4 h-4 bg-white rounded-full shadow-md transform transition-transform duration-300 absolute top-1 left-1" :class="settings.discount_settings.allow_custom ? 'translate-x-5' : 'translate-x-0'"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Shortcut Presets Manager -->
                        <div :class="!settings.discount_settings.is_enabled ? 'opacity-50 pointer-events-none' : ''">
                            <div class="flex items-center justify-between mb-3">
                                <div>
                                    <h4 class="font-bold text-sm text-ink">Shortcut Preset Diskon Cepat (POS)</h4>
                                    <p class="text-xs text-muted">Tombol shortcut 1-tap yang langsung muncul di layar kasir untuk mempermudah transaksi.</p>
                                </div>
                            </div>

                            <!-- Preset list -->
                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3 mb-4">
                                <template x-for="(preset, pIdx) in (settings.discount_settings.presets || [])" :key="preset.id || pIdx">
                                    <div class="p-3 rounded-xl border border-line bg-white shadow-2xs flex items-center justify-between gap-2 group hover:border-primary transition">
                                        <div class="truncate">
                                            <span class="font-bold text-xs text-ink block truncate" x-text="preset.name"></span>
                                            <span class="text-[10px] font-semibold text-primary" x-text="preset.type === 'percentage' ? preset.value + '%' : 'Rp ' + Number(preset.value).toLocaleString('id-ID')"></span>
                                        </div>
                                        <button type="button" @click="removeDiscountPreset(pIdx)" class="text-muted hover:text-red-500 p-1 rounded-full opacity-60 group-hover:opacity-100 transition cursor-pointer" title="Hapus shortcut">
                                            <i class="fas fa-trash-alt text-[10px]"></i>
                                        </button>
                                    </div>
                                </template>
                            </div>

                            <!-- Add new preset inline form -->
                            <div class="p-4 rounded-2xl border border-dashed border-line bg-ivory/40 flex flex-col md:flex-row items-center gap-3">
                                <div class="w-full md:w-1/3">
                                    <input type="text" x-model="newPreset.name" class="w-full border border-line rounded-xl px-3.5 py-2 text-xs focus:border-primary focus:outline-none bg-white" placeholder="Nama Shortcut (misal: Diskon 25%, VIP)">
                                </div>
                                <div class="w-full md:w-1/4">
                                    <select x-model="newPreset.type" class="w-full border border-line rounded-xl px-3.5 py-2 text-xs focus:border-primary focus:outline-none bg-white">
                                        <option value="percentage">Persentase (%)</option>
                                        <option value="fixed">Nominal Tetap (Rp)</option>
                                    </select>
                                </div>
                                <div class="w-full md:w-1/4">
                                    <input type="number" x-model.number="newPreset.value" min="1" class="w-full border border-line rounded-xl px-3.5 py-2 text-xs focus:border-primary focus:outline-none bg-white" placeholder="Nilai (misal: 25 atau 10000)">
                                </div>
                                <div class="w-full md:w-auto shrink-0">
                                    <button type="button" @click="addDiscountPreset" class="w-full md:w-auto px-4 py-2 bg-ink hover:bg-black text-white font-bold text-xs rounded-xl transition flex items-center justify-center gap-1.5 shadow-2xs cursor-pointer">
                                        <i class="fas fa-plus text-[10px]"></i>
                                        <span>Tambah Shortcut</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 7: Kelola Voucher & Kupon Promo -->
                <div class="bg-white rounded-[24px] shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-line overflow-hidden">
                    <div class="p-6 border-b border-line bg-ivory/50 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div>
                            <h3 class="font-bold text-lg text-ink">Kelola Voucher & Kupon Promosi</h3>
                            <p class="text-xs text-muted">Buat kode kupon promo khusus pelanggan (contoh: HEMAT20, KOPIASIK).</p>
                        </div>
                        <button type="button" @click="openCreateVoucherModal()" class="px-5 py-2.5 rounded-xl bg-primary hover:bg-brand-700 text-white font-bold text-xs flex items-center justify-center gap-2 shadow-sm transition shrink-0 cursor-pointer">
                            <i class="fas fa-plus"></i>
                            <span>Buat Voucher Baru</span>
                        </button>
                    </div>

                    <div class="p-6">
                        <!-- Loading State -->
                        <div x-show="isLoadingVouchers" class="py-12 text-center text-muted">
                            <i class="fas fa-spinner fa-spin text-2xl text-primary mb-2"></i>
                            <p class="text-xs">Memuat daftar voucher...</p>
                        </div>

                        <!-- Empty State -->
                        <div x-show="!isLoadingVouchers && vouchers.length === 0" class="py-12 text-center text-muted border-2 border-dashed border-line rounded-2xl bg-ivory/30">
                            <div class="w-12 h-12 rounded-full bg-primary/10 text-primary flex items-center justify-center mx-auto mb-3">
                                <i class="fas fa-ticket-alt text-xl"></i>
                            </div>
                            <h4 class="font-bold text-sm text-ink mb-1">Belum Ada Voucher</h4>
                            <p class="text-xs text-muted max-w-sm mx-auto mb-4">Buat kode voucher pertama Anda untuk menarik lebih banyak pembeli atau beri reward khusus pelanggan setia.</p>
                            <button type="button" @click="openCreateVoucherModal()" class="px-4 py-2 rounded-xl bg-ink text-white font-bold text-xs hover:bg-black transition cursor-pointer">
                                + Buat Voucher Sekarang
                            </button>
                        </div>

                        <!-- Vouchers Table -->
                        <div x-show="!isLoadingVouchers && vouchers.length > 0" class="overflow-x-auto -mx-6 px-6">
                            <table class="w-full text-left text-xs border-collapse">
                                <thead>
                                    <tr class="border-b border-line text-muted uppercase text-[10px] font-bold">
                                        <th class="py-3 px-3">Kode & Nama Voucher</th>
                                        <th class="py-3 px-3">Besaran Diskon</th>
                                        <th class="py-3 px-3">Min. Belanja</th>
                                        <th class="py-3 px-3">Kuota / Terpakai</th>
                                        <th class="py-3 px-3">Masa Berlaku</th>
                                        <th class="py-3 px-3 text-center">Status</th>
                                        <th class="py-3 px-3 text-right">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-line">
                                    <template x-for="v in vouchers" :key="v.id">
                                        <tr class="hover:bg-ivory/50 transition group">
                                            <td class="py-3 px-3">
                                                <div class="flex items-center gap-2">
                                                    <span class="font-mono font-black text-xs px-2 py-0.5 rounded bg-gray-100 text-ink border border-line tracking-wider" x-text="v.code"></span>
                                                    <span class="font-bold text-ink" x-text="v.name"></span>
                                                </div>
                                            </td>
                                            <td class="py-3 px-3 font-semibold text-emerald-700">
                                                <span x-text="v.discount_type === 'percentage' ? v.discount_value + '%' : 'Rp ' + Number(v.discount_value).toLocaleString('id-ID')"></span>
                                                <span x-show="v.max_discount" class="text-[10px] text-muted block" x-text="'Maks Rp ' + Number(v.max_discount).toLocaleString('id-ID')"></span>
                                            </td>
                                            <td class="py-3 px-3 text-muted">
                                                <span x-text="v.min_spend > 0 ? 'Rp ' + Number(v.min_spend).toLocaleString('id-ID') : 'Tanpa Min.'"></span>
                                            </td>
                                            <td class="py-3 px-3">
                                                <span class="font-bold text-ink" x-text="v.used_count"></span>
                                                <span class="text-muted" x-text="v.usage_limit ? ' / ' + v.usage_limit : ' (Tanpa batas)'"></span>
                                            </td>
                                            <td class="py-3 px-3 text-muted">
                                                <span x-text="v.expires_at ? new Date(v.expires_at).toLocaleDateString('id-ID', {day: 'numeric', month: 'short', year: 'numeric'}) : 'Selamanya'"></span>
                                            </td>
                                            <td class="py-3 px-3 text-center">
                                                <button type="button" @click="toggleVoucher(v)"
                                                    :class="v.is_active ? 'bg-emerald-100 text-emerald-800 border-emerald-300' : 'bg-gray-100 text-gray-500 border-gray-300'"
                                                    class="px-2.5 py-0.5 rounded-full text-[10px] font-bold border transition cursor-pointer">
                                                    <span x-text="v.is_active ? 'Aktif' : 'Nonaktif'"></span>
                                                </button>
                                            </td>
                                            <td class="py-3 px-3 text-right">
                                                <div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" @click="openEditVoucherModal(v)" class="p-1.5 text-muted hover:text-primary transition cursor-pointer" title="Edit Voucher">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button type="button" @click="deleteVoucher(v)" class="p-1.5 text-muted hover:text-red-500 transition cursor-pointer" title="Hapus Voucher">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Kanan: Live Preview (Mobile Frame) -->
        <div class="xl:col-span-4 xl:sticky xl:top-8 mt-8 xl:mt-0 flex flex-col items-center">
            <div class="flex justify-between items-center mb-4">
                <h3 class="font-black text-ink"><i class="fas fa-mobile-alt mr-2 text-primary"></i>Live Preview</h3>
            </div>
            
            <!-- Mobile Frame -->
            <div class="relative mx-auto w-full max-w-[340px] h-[650px] bg-gray-50 rounded-[40px] shadow-[0_20px_60px_rgba(0,0,0,0.15)] border-[8px] border-ink overflow-hidden flex flex-col font-sans" :style="'font-family: ' + getFontFamilyCss(settings.font_family)">
                <!-- Notch -->
                <div class="absolute top-0 inset-x-0 h-6 bg-ink rounded-b-3xl w-40 mx-auto z-50"></div>

                <!-- Template Customer Menu Real Structure with Dynamic Background -->
                <div class="flex-1 overflow-y-auto hide-scroll pb-24 relative" 
                     :style="settings.background_type === 'pattern' ? 
                                (settings.background_pattern === 'grid' ? 'background-color: #fdfbf7; background-image: linear-gradient(to right, rgba(0,0,0,0.06) 1px, transparent 1px), linear-gradient(to bottom, rgba(0,0,0,0.06) 1px, transparent 1px); background-size: 14px 14px;' :
                                (settings.background_pattern === 'cafe' ? 'background-color: #fdfbf7; background-image: radial-gradient(#8d6e63 1.5px, transparent 1.5px), radial-gradient(#bcaaa4 1px, transparent 1px); background-size: 20px 20px, 10px 10px; background-position: 0 0, 10px 10px;' :
                                (settings.background_pattern === 'waves' ? 'background-color: #fdfbf7; background-image: repeating-radial-gradient(circle at 0 0, transparent 0, #fdfbf7 10px), repeating-linear-gradient(#f1f5f955, #e2e8f055);' :
                                (settings.background_pattern === 'stripes' ? 'background-color: #fdfbf7; background-image: repeating-linear-gradient(45deg, transparent, transparent 8px, rgba(0, 0, 0, 0.04) 8px, rgba(0, 0, 0, 0.04) 16px);' :
                                (settings.background_pattern === 'moroccan' ? 'background-color: #fdfbf7; background-image: radial-gradient(#00000010 2px, transparent 2px), radial-gradient(#00000010 2px, transparent 2px); background-size: 16px 16px; background-position: 0 0, 8px 8px;' :
                                'background-color: #fdfbf7; background-image: radial-gradient(circle, #cbd5e1 1.5px, transparent 1.5px); background-size: 14px 14px;'))))) :
                             (settings.background_type === 'solid' ? ('background-color: ' + (settings.background_color || '#fdfbf7')) : 
                             ('background-image: url(' + (settings.backgroundImagePreview || '') + '); background-size: cover; background-position: center;'))">
                    
                    <!-- Image Background Dimming Layer -->
                    <div x-show="settings.background_type === 'image' && settings.backgroundImagePreview" class="absolute inset-0 pointer-events-none" :style="'background-color: rgba(253, 251, 247, ' + ((settings.background_overlay_opacity || 85) / 100) + ')'"></div>

                    <!-- Toko Tutup Overlay -->
                    <div x-show="!settings.is_open" class="absolute inset-0 bg-white/80 backdrop-blur-sm z-[200] flex flex-col items-center justify-center p-6 text-center">
                        <i class="fas fa-store-slash text-5xl text-gray-400 mb-4"></i>
                        <h2 class="text-xl font-bold text-gray-800 mb-2">Toko Sedang Tutup</h2>
                        <p class="text-xs text-gray-500 font-medium">Mohon maaf, toko kami saat ini sedang tidak menerima pesanan.</p>
                    </div>

                    <!-- Header APP (Logo, Slogan) -->
                    <header class="bg-white/90 backdrop-blur-md pt-7 pb-3 px-4 z-30 relative shadow-sm">
                        <div class="flex items-center gap-2">
                            <template x-if="settings.logoPreview">
                                <img :src="settings.logoPreview" alt="Logo" class="h-10 w-10 object-contain drop-shadow-sm rounded-lg shrink-0">
                            </template>
                            <template x-if="!settings.logoPreview">
                                <div class="h-10 w-10 rounded-lg flex items-center justify-center shrink-0" :style="'background-color: ' + (settings.primary_color || '#1e5a7a')">
                                    <i class="fas fa-store text-white text-sm"></i>
                                </div>
                            </template>
                            <div class="flex flex-col">
                                <span class="font-extrabold text-[15px] leading-tight tracking-tight uppercase" :style="'color: ' + (settings.primary_color || '#1e5a7a') + '; font-family: ' + getFontFamilyCss(settings.title_font_family)" x-text="settings.name || 'BITTEN COFFEE'"></span>
                                <span class="text-[8px] font-bold text-gray-500 tracking-[0.2em] mt-0.5 uppercase" x-text="settings.slogan || 'COFFEE & EATERY'"></span>
                            </div>
                        </div>
                    </header>

                    <!-- Location & Search Bar -->
                    <div class="bg-white/90 backdrop-blur-md px-4 pb-4 relative z-30 border-b border-gray-100">
                        <div class="bg-white rounded-full p-1.5 flex items-center shadow-[0_2px_8px_-3px_rgba(0,0,0,0.1)] border border-gray-200">
                            <div class="flex items-center gap-2 pl-2 pr-3 border-r border-gray-200 rounded-l-full py-1.5 shrink-0 max-w-[55%]">
                                <div class="w-7 h-7 rounded-full flex items-center justify-center shrink-0" :style="'background-color: ' + (settings.primary_color ? settings.primary_color + '15' : '#1c453215')">
                                    <i class="fas fa-map-marker-alt text-[11px]" :style="'color: ' + (settings.primary_color || '#1e5a7a')"></i>
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
                    <div x-show="settings.is_banner_active" class="px-4 mt-4 mb-4 relative z-10">
                        <div class="relative rounded-[20px] overflow-hidden shadow-lg h-36 group flex items-center justify-center" :style="'background-color: ' + (settings.primary_color || '#1e5a7a')">
                            <template x-if="settings.banners.filter(b => b !== null).length > 0">
                                <img :src="settings.banners.find(b => b !== null)" class="w-full h-full object-cover">
                            </template>
                            <template x-if="settings.banners.filter(b => b !== null).length === 0">
                                <div class="text-white/70 font-bold text-xs tracking-widest uppercase">Banner Promo</div>
                            </template>
                            <div class="absolute inset-x-0 bottom-0 h-1/2" :style="'background: linear-gradient(to top, ' + (settings.primary_color || '#1e5a7a') + ' 0%, transparent 100%)'"></div>
                            <div class="absolute bottom-2.5 w-full flex justify-center gap-1.5 z-10">
                                <template x-for="(_, i) in (settings.banners.filter(b => b !== null).length || 1)">
                                    <div class="w-1.5 h-1.5 rounded-full" :class="i === 0 ? 'bg-white' : 'bg-white/50'"></div>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- Best Seller Highlight Shelf (Simulasi Live Preview) -->
                    <div x-show="settings.best_seller_ids.length > 0" class="px-4 mt-3 mb-2 relative z-10">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center gap-1.5">
                                <span class="text-xs">⭐</span>
                                <h3 class="text-xs font-black text-ink tracking-tight uppercase">Menu Unggulan</h3>
                            </div>
                            <span class="text-[10px] font-bold text-amber-600 bg-amber-50 px-2 py-0.5 rounded-full">Pilihan Cafe</span>
                        </div>
                        <div class="flex gap-2.5 overflow-x-auto hide-scroll pb-1">
                            <template x-for="bsId in settings.best_seller_ids.slice(0, 3)" :key="bsId">
                                <div class="w-32 shrink-0 bg-white p-2 rounded-2xl shadow-sm border border-amber-200/80 flex flex-col justify-between relative">
                                    <div class="w-full h-16 bg-amber-50 rounded-xl mb-1.5 flex items-center justify-center text-amber-500 overflow-hidden">
                                        <template x-if="allShopProducts.find(p => p.id === bsId)?.image_url">
                                            <img :src="allShopProducts.find(p => p.id === bsId)?.image_url" class="w-full h-full object-cover">
                                        </template>
                                        <template x-if="!allShopProducts.find(p => p.id === bsId)?.image_url">
                                            <i class="fas fa-coffee text-xl"></i>
                                        </template>
                                    </div>
                                    <span class="text-[11px] font-bold text-ink truncate" x-text="allShopProducts.find(p => p.id === bsId)?.name || 'Produk Favorit'"></span>
                                    <span class="text-[10px] font-black" :style="'color: ' + (settings.primary_color || '#1e5a7a')" x-text="formatRp(allShopProducts.find(p => p.id === bsId)?.price || 25000)"></span>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Categories Bar -->
                    <div class="sticky top-0 bg-white/95 backdrop-blur-md z-20 pt-2.5 pb-2.5 border-y border-gray-100 shadow-sm">
                        <div class="flex overflow-x-auto px-4 gap-2.5 hide-scroll">
                            <div class="flex flex-col items-center gap-1 min-w-[60px]">
                                <div class="w-12 h-12 rounded-2xl flex items-center justify-center border-2" :style="'border-color: ' + (settings.primary_color || '#1e5a7a') + '; background-color: ' + (settings.primary_color ? settings.primary_color + '10' : '#1c453210')">
                                    <i class="fas fa-star text-sm" :style="'color: ' + (settings.primary_color || '#1e5a7a')"></i>
                                </div>
                                <span class="text-[9px] font-bold" :style="'color: ' + (settings.primary_color || '#1e5a7a')">All</span>
                            </div>
                            <div class="flex flex-col items-center gap-1 min-w-[60px]">
                                <div class="w-12 h-12 rounded-2xl flex items-center justify-center border border-gray-200 bg-white shadow-sm">
                                    <i class="fas fa-coffee text-sm text-gold-600"></i>
                                </div>
                                <span class="text-[9px] font-medium text-gray-500">Coffee</span>
                            </div>
                        </div>
                    </div>

                    <!-- Menu Items View (List vs Grid vs Compact vs Cards) -->
                    <div class="p-4 relative z-10">
                        <!-- Grid View (2 Kolom) -->
                        <div x-show="settings.theme_style === 'grid'" class="grid grid-cols-2 gap-3">
                            <div class="bg-white p-2.5 rounded-[18px] shadow-sm border border-gray-100 relative">
                                <div class="w-full aspect-[4/3] bg-gray-100 rounded-xl mb-2 flex flex-col items-center justify-center text-gray-300">
                                    <i class="fas fa-coffee text-2xl"></i>
                                </div>
                                <h4 class="font-bold text-xs text-gray-800 leading-tight">Vanilla Latte</h4>
                                <p class="font-black text-xs mt-1" :style="'color: ' + (settings.primary_color || '#1e5a7a')">Rp 25.000</p>
                                <button class="absolute bottom-2.5 right-2.5 w-6 h-6 rounded-full flex items-center justify-center text-white text-[10px] shadow-sm" :style="'background-color: ' + (settings.primary_color || '#1e5a7a')">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                            <div class="bg-white p-2.5 rounded-[18px] shadow-sm border border-gray-100 relative">
                                <div class="w-full aspect-[4/3] bg-gray-100 rounded-xl mb-2 flex flex-col items-center justify-center text-gray-300">
                                    <i class="fas fa-coffee text-2xl"></i>
                                </div>
                                <h4 class="font-bold text-xs text-gray-800 leading-tight">Americano</h4>
                                <p class="font-black text-xs mt-1" :style="'color: ' + (settings.primary_color || '#1e5a7a')">Rp 18.000</p>
                                <button class="absolute bottom-2.5 right-2.5 w-6 h-6 rounded-full flex items-center justify-center text-white text-[10px] shadow-sm" :style="'background-color: ' + (settings.primary_color || '#1e5a7a')">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>

                        <!-- List View (Horizontal) -->
                        <div x-show="settings.theme_style === 'list'" class="flex flex-col gap-3">
                            <div class="bg-white p-2.5 rounded-[18px] shadow-sm border border-gray-100 flex gap-3 relative">
                                <div class="w-16 h-16 bg-gray-100 rounded-xl shrink-0 flex items-center justify-center text-gray-300">
                                    <i class="fas fa-coffee text-xl"></i>
                                </div>
                                <div class="flex-1 flex flex-col justify-center pr-6">
                                    <h4 class="font-bold text-xs text-gray-800 leading-tight">Vanilla Latte</h4>
                                    <p class="text-[9px] text-gray-500 mt-0.5 line-clamp-1">Kopi susu vanilla lezat...</p>
                                    <p class="font-black text-xs mt-1" :style="'color: ' + (settings.primary_color || '#1e5a7a')">Rp 25.000</p>
                                </div>
                                <button class="absolute bottom-2.5 right-2.5 w-6 h-6 rounded-full flex items-center justify-center text-white text-[10px] shadow-sm" :style="'background-color: ' + (settings.primary_color || '#1e5a7a')">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                            <div class="bg-white p-2.5 rounded-[18px] shadow-sm border border-gray-100 flex gap-3 relative">
                                <div class="w-16 h-16 bg-gray-100 rounded-xl shrink-0 flex items-center justify-center text-gray-300">
                                    <i class="fas fa-coffee text-xl"></i>
                                </div>
                                <div class="flex-1 flex flex-col justify-center pr-6">
                                    <h4 class="font-bold text-xs text-gray-800 leading-tight">Americano</h4>
                                    <p class="text-[9px] text-gray-500 mt-0.5 line-clamp-1">Espresso dengan air matang</p>
                                    <p class="font-black text-xs mt-1" :style="'color: ' + (settings.primary_color || '#1e5a7a')">Rp 18.000</p>
                                </div>
                                <button class="absolute bottom-2.5 right-2.5 w-6 h-6 rounded-full flex items-center justify-center text-white text-[10px] shadow-sm" :style="'background-color: ' + (settings.primary_color || '#1e5a7a')">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Compact View (Dense List) -->
                        <div x-show="settings.theme_style === 'compact'" class="flex flex-col gap-2">
                            <div class="bg-white px-3 py-2 rounded-xl shadow-sm border border-gray-100 flex items-center justify-between">
                                <div class="flex items-center gap-2 min-w-0">
                                    <div class="w-8 h-8 bg-gray-100 rounded-lg shrink-0 flex items-center justify-center text-gray-400 text-xs">
                                        <i class="fas fa-coffee"></i>
                                    </div>
                                    <div class="truncate">
                                        <h4 class="font-bold text-xs text-gray-800 truncate">Vanilla Latte</h4>
                                        <p class="font-bold text-[10px]" :style="'color: ' + (settings.primary_color || '#1e5a7a')">Rp 25.000</p>
                                    </div>
                                </div>
                                <button class="w-6 h-6 rounded-lg flex items-center justify-center text-white text-[9px] shadow-sm shrink-0" :style="'background-color: ' + (settings.primary_color || '#1e5a7a')">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                            <div class="bg-white px-3 py-2 rounded-xl shadow-sm border border-gray-100 flex items-center justify-between">
                                <div class="flex items-center gap-2 min-w-0">
                                    <div class="w-8 h-8 bg-gray-100 rounded-lg shrink-0 flex items-center justify-center text-gray-400 text-xs">
                                        <i class="fas fa-coffee"></i>
                                    </div>
                                    <div class="truncate">
                                        <h4 class="font-bold text-xs text-gray-800 truncate">Americano</h4>
                                        <p class="font-bold text-[10px]" :style="'color: ' + (settings.primary_color || '#1e5a7a')">Rp 18.000</p>
                                    </div>
                                </div>
                                <button class="w-6 h-6 rounded-lg flex items-center justify-center text-white text-[9px] shadow-sm shrink-0" :style="'background-color: ' + (settings.primary_color || '#1e5a7a')">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Hero Cards View -->
                        <div x-show="settings.theme_style === 'cards'" class="flex flex-col gap-3">
                            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden relative">
                                <div class="w-full h-24 bg-gray-100 flex items-center justify-center text-gray-300">
                                    <i class="fas fa-coffee text-3xl"></i>
                                </div>
                                <div class="p-3">
                                    <h4 class="font-bold text-sm text-gray-800">Vanilla Latte</h4>
                                    <p class="text-[10px] text-gray-500 line-clamp-1 mt-0.5">Kopi susu vanilla manis pas dengan double shot espresso.</p>
                                    <div class="flex items-center justify-between mt-2">
                                        <span class="font-black text-sm" :style="'color: ' + (settings.primary_color || '#1e5a7a')">Rp 25.000</span>
                                        <button class="px-3 py-1 rounded-xl text-white text-xs font-bold shadow-sm" :style="'background-color: ' + (settings.primary_color || '#1e5a7a')">
                                            Tambah
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer Media Sosial di Menu -->
                    <div class="px-4 py-4 mt-2 flex flex-col items-center justify-center text-center opacity-80 relative z-10">
                        <div class="flex gap-2.5 mb-2">
                            <!-- TikTok -->
                            <div x-show="settings.social_settings.show_tiktok && settings.tiktok_link" class="w-7 h-7 rounded-full bg-black text-white flex items-center justify-center text-xs shadow-sm">
                                <i class="fab fa-tiktok"></i>
                            </div>
                            <!-- Instagram -->
                            <div x-show="settings.social_settings.show_instagram && settings.instagram_link" class="w-7 h-7 rounded-full bg-pink-100 text-pink-500 flex items-center justify-center text-xs shadow-sm">
                                <i class="fab fa-instagram"></i>
                            </div>
                            <!-- WhatsApp -->
                            <div x-show="settings.social_settings.show_whatsapp && settings.whatsapp_number" class="w-7 h-7 rounded-full bg-green-100 text-green-500 flex items-center justify-center text-xs shadow-sm">
                                <i class="fab fa-whatsapp"></i>
                            </div>
                            <!-- Maps -->
                            <div x-show="settings.social_settings.show_maps && settings.maps_link" class="w-7 h-7 rounded-full bg-red-100 text-red-500 flex items-center justify-center text-xs shadow-sm">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                        </div>
                        <p class="text-[9px] text-gray-400 font-medium">Powered by Menu Oqari</p>
                    </div>
                </div>

                <!-- Floating Cart Button -->
                <div class="absolute bottom-4 inset-x-4 h-12 rounded-full shadow-xl flex items-center justify-between px-5 text-white z-40" :style="'background-color: ' + (settings.primary_color || '#1e5a7a')">
                    <div class="flex flex-col justify-center">
                        <span class="text-[9px] text-white/70 font-semibold">Total Pesanan</span>
                        <span class="font-bold text-sm leading-tight">Rp 0</span>
                    </div>
                    <button class="bg-white px-4 py-1.5 rounded-full font-bold shadow-md flex items-center gap-2" :style="'color: ' + (settings.primary_color || '#1e5a7a')">
                        <i class="fas fa-shopping-cart text-[11px]"></i>
                        <span class="bg-gray-800 text-white text-[9px] px-1.5 py-0.5 rounded-full">0</span>
                    </button>
                </div>
            </div>
            
            <p class="text-center text-xs text-muted mt-4"><i class="fas fa-info-circle mr-1"></i> Preview bersifat simulasi desain.</p>
        </div>
    </div>
</div>
