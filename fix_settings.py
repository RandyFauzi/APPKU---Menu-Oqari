with open('resources/views/Admin/Dashboard/tabs/settings.blade.php', 'r', encoding='utf-8') as f:
    content = f.read()

import re

# Replace Banner upload UI
old_banner_ui = r'<!-- Banner -->.*?accept=\"image/\*\">\s*</div>'
new_banner_ui = r'''<!-- Banner -->
                            <div class="w-full md:w-2/3">
                                <div class="flex items-center justify-between mb-3">
                                    <label class="block text-sm font-bold text-[#202522]">Banner Promo (Max 3)</label>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" x-model="settings.is_banner_active" class="sr-only peer">
                                        <div class="w-9 h-5 bg-gray-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-[#164A35]"></div>
                                        <span class="ml-2 text-[10px] font-bold text-gray-500">Aktif</span>
                                    </label>
                                </div>
                                <div class="grid grid-cols-3 gap-2" :class="!settings.is_banner_active ? 'opacity-50 pointer-events-none' : ''">
                                    <template x-for="(banner, idx) in 3" :key="idx">
                                        <div class="w-full aspect-[21/9] rounded-xl border-2 border-dashed border-[#E3E1DC] flex items-center justify-center bg-[#F8F7F3] overflow-hidden relative group cursor-pointer" @click="$refs['bannerInput' + idx].click()">
                                            <img x-show="settings.banners[idx]" :src="settings.banners[idx]" class="w-full h-full object-cover">
                                            <div x-show="!settings.banners[idx]" class="flex flex-col items-center text-[#777873]">
                                                <i class="fas fa-image text-lg mb-1 text-[#E3E1DC]"></i>
                                                <span class="text-[8px] font-semibold">Banner <span x-text="idx+1"></span></span>
                                            </div>
                                            <div class="absolute inset-0 bg-[#202522]/40 backdrop-blur-sm flex flex-col items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                                <i class="fas fa-camera text-white mb-1"></i>
                                                <button x-show="settings.banners[idx]" @click.stop="settings.banners[idx] = null; settings.bannerFiles[idx] = null; settings.bannerPaths[idx] = null" class="text-[8px] bg-red-500 text-white px-2 py-0.5 rounded-full hover:bg-red-600">Hapus</button>
                                            </div>
                                            <input type="file" :x-ref="'bannerInput' + idx" @change="(e) => { 
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
                            </div>'''

content = re.sub(old_banner_ui, new_banner_ui, content, flags=re.DOTALL)

# Replace mockup banner
old_mockup_banner = r'<!-- Promo Carousel Section -->.*?<!-- Categories Square Grid -->'
new_mockup_banner = r'''<!-- Promo Carousel Section -->
                    <div x-show="settings.is_banner_active" class="px-4 mt-1 mb-8">
                        <div class="relative rounded-[20px] overflow-hidden shadow-lg h-36 group flex items-center justify-center" :style="'background-color: ' + (settings.primary_color || '#1c4532')">
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
                    <!-- Categories Square Grid -->'''

content = re.sub(old_mockup_banner, new_mockup_banner, content, flags=re.DOTALL)

with open('resources/views/Admin/Dashboard/tabs/settings.blade.php', 'w', encoding='utf-8') as f:
    f.write(content)
