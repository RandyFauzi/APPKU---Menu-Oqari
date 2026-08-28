import re

with open('resources/views/Admin/Dashboard/tabs/menu.blade.php', 'r', encoding='utf-8') as f:
    content = f.read()

old_select_html = '''<select x-model="draftMenus[index].categoryId" class="w-full bg-[#F8F7F3] border border-transparent rounded-[11px] px-4 py-3 text-[16px] text-[#202522] focus:border-[#164A35] focus:ring-1 focus:ring-[#164A35] outline-none appearance-none font-medium cursor-pointer">
                                        <option value="Coffee">Coffee</option>
                                        <option value="Pastry">Pastry</option>
                                        <option value="Beverages">Beverages</option>
                                        <option value="Foods">Foods</option>
                                        <option value="Snacks">Snacks</option>
                                        <option value="Sweets">Sweets</option>
                                    </select>
                                    <i class="fas fa-chevron-down absolute right-4 top-4 text-xs text-[#777873] pointer-events-none"></i>'''

new_select_html = '''<div x-data="{ 
                                        open: false,
                                        options: [
                                            { id: 'Coffee', icon: 'fas fa-mug-hot', bg: 'bg-[#DDEBDD]', text: 'text-[#164A35]' },
                                            { id: 'Pastry', icon: 'fas fa-bread-slice', bg: 'bg-[#F7E5D2]', text: 'text-[#D97A32]' },
                                            { id: 'Beverages', icon: 'fas fa-glass-martini-alt', bg: 'bg-blue-100', text: 'text-blue-700' },
                                            { id: 'Foods', icon: 'fas fa-utensils', bg: 'bg-red-100', text: 'text-red-700' },
                                            { id: 'Snacks', icon: 'fas fa-cookie', bg: 'bg-yellow-100', text: 'text-yellow-700' },
                                            { id: 'Sweets', icon: 'fas fa-ice-cream', bg: 'bg-pink-100', text: 'text-pink-700' }
                                        ]
                                    }">
                                        <!-- Trigger -->
                                        <button @click="open = !open" @click.away="open = false" type="button" 
                                                class="w-full bg-[#F8F7F3] border border-[#E3E1DC] rounded-[11px] px-3 py-1.5 h-[46px] flex items-center justify-between focus:border-[#164A35] focus:ring-1 focus:ring-[#164A35] transition-colors outline-none cursor-pointer">
                                            
                                            <!-- Dynamic Badge for Selected -->
                                            <template x-for="opt in options">
                                                <template x-if="draftMenus[index].categoryId === opt.id">
                                                    <div :class="opt.bg + ' ' + opt.text" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg font-semibold text-sm">
                                                        <i :class="opt.icon"></i> <span x-text="opt.id"></span>
                                                    </div>
                                                </template>
                                            </template>
                                            
                                            <!-- Fallback if not found -->
                                            <template x-if="!options.find(o => o.id === draftMenus[index].categoryId)">
                                                <span class="text-[#777873] font-medium text-sm px-2">Select category</span>
                                            </template>

                                            <i class="fas fa-chevron-down text-xs text-[#777873] mr-1 transition-transform" :class="open ? 'rotate-180' : ''"></i>
                                        </button>
                                        
                                        <!-- Dropdown Menu -->
                                        <div x-show="open" x-cloak 
                                             x-transition:enter="transition ease-out duration-100"
                                             x-transition:enter-start="opacity-0 scale-95"
                                             x-transition:enter-end="opacity-100 scale-100"
                                             x-transition:leave="transition ease-in duration-75"
                                             x-transition:leave-start="opacity-100 scale-100"
                                             x-transition:leave-end="opacity-0 scale-95"
                                             class="absolute z-50 w-full mt-2 bg-white border border-[#E3E1DC] rounded-[12px] shadow-[0_10px_35px_rgba(0,0,0,0.08)] py-2 flex flex-col gap-1 max-h-56 overflow-y-auto">
                                            <template x-for="opt in options" :key="opt.id">
                                                <button @click="draftMenus[index].categoryId = opt.id; open = false" type="button" 
                                                        class="mx-2 px-2 py-1.5 rounded-[10px] text-left hover:bg-[#F8F7F3] transition-colors flex items-center">
                                                    <div :class="opt.bg + ' ' + opt.text" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg font-semibold text-sm">
                                                        <i :class="opt.icon"></i> <span x-text="opt.id"></span>
                                                    </div>
                                                </button>
                                            </template>
                                        </div>
                                    </div>'''

if old_select_html in content:
    content = content.replace(old_select_html, new_select_html)
    with open('resources/views/Admin/Dashboard/tabs/menu.blade.php', 'w', encoding='utf-8') as f:
        f.write(content)
    print("Updated dropdown in menu.blade.php")
else:
    print("Could not find dropdown html to replace")
