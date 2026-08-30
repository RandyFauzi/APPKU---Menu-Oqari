import re

with open('resources/views/Admin/Dashboard/tabs/settings.blade.php', 'r', encoding='utf-8') as f:
    content = f.read()

# Replace mockup banner entirely
old_mockup = r'<!-- Kanan: Live Preview Mockup HP -->.*?</div>\s*</div>\s*</div>'
new_mockup = r'''<!-- Kanan: Live Preview Mockup HP -->
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
                    <header class="bg-white pt-7 pb-3 px-4 z-30 relative">
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
                    <div class="bg-white px-4 pb-5 relative z-30">
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
                    <div x-show="settings.is_banner_active" class="px-4 mt-1 mb-8">
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
                    <div class="sticky top-0 bg-gray-50/90 backdrop-blur-md z-20 pt-3 pb-3 border-y border-gray-200/50">
                        <div class="flex overflow-x-auto px-4 gap-3 hide-scroll">
                            <div class="flex flex-col items-center gap-1 min-w-[70px]">
                                <div class="w-14 h-14 rounded-2xl flex items-center justify-center border-2" :style="'border-color: ' + (settings.primary_color || '#1c4532') + '; background-color: ' + (settings.primary_color ? settings.primary_color + '15' : '#1c453215')">
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
                    <div class="px-4 mb-4"><div class="h-px bg-gray-300 w-full"></div></div>

                    <!-- Menu Items View (List vs Grid) -->
                    <div class="px-4">
                        <!-- Grid View -->
                        <div x-show="settings.theme_style === 'grid'" class="grid grid-cols-2 gap-4">
                            <div class="bg-[#EAEAEA] p-3 rounded-[20px] relative">
                                <div class="w-full aspect-[4/3] bg-gray-300 rounded-xl mb-3 flex flex-col items-center justify-center text-gray-400">
                                    <i class="fas fa-image text-3xl mb-1"></i>
                                    <span class="text-[8px] font-bold tracking-wider">NO IMAGE</span>
                                </div>
                                <h4 class="font-bold text-xs text-gray-800 leading-tight">Vanilla Latte</h4>
                                <p class="font-black text-[13px] mt-1" :style="'color: ' + (settings.primary_color || '#1c4532')">Rp 25.000</p>
                                <button class="absolute bottom-3 right-3 w-6 h-6 rounded-full flex items-center justify-center text-white text-[10px] shadow-sm" :style="'background-color: ' + (settings.primary_color || '#1c4532')">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                            <div class="bg-[#EAEAEA] p-3 rounded-[20px] relative">
                                <div class="w-full aspect-[4/3] bg-gray-300 rounded-xl mb-3 flex flex-col items-center justify-center text-gray-400">
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
                            <div class="bg-[#EAEAEA] p-3 rounded-[20px] flex gap-3 relative">
                                <div class="w-20 h-20 bg-gray-300 rounded-xl shrink-0 flex items-center justify-center text-gray-400">
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
                            <div class="bg-[#EAEAEA] p-3 rounded-[20px] flex gap-3 relative">
                                <div class="w-20 h-20 bg-gray-300 rounded-xl shrink-0 flex items-center justify-center text-gray-400">
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
</div>'''

content = re.sub(old_mockup, new_mockup, content, flags=re.DOTALL)

with open('resources/views/Admin/Dashboard/tabs/settings.blade.php', 'w', encoding='utf-8') as f:
    f.write(content)
