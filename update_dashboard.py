import re

with open('resources/views/Admin/Dashboard/dashboard.blade.php', 'r', encoding='utf-8') as f:
    content = f.read()

# Replace Promo Card
old_promo = '''        <!-- Promo Card -->
        <div class="bg-[#F3EBE1] rounded-2xl p-5 relative overflow-hidden flex flex-col gap-4 mt-auto mb-2">
            <h4 class="font-heading font-extrabold text-xl leading-tight z-10 w-3/4 text-brewlytext">Great coffee builds stronger communities.</h4>
            <div class="z-10 bg-white w-8 h-8 rounded-full flex items-center justify-center shadow-sm cursor-pointer hover:scale-105 transition-transform"><i class="fas fa-arrow-right text-xs"></i></div>
            <img src="https://images.unsplash.com/photo-1551030173-122aabc4489c?w=200&fit=crop" class="absolute -bottom-6 -right-6 w-28 h-28 object-cover rounded-full opacity-80 border-4 border-[#F3EBE1]">
        </div>'''

new_promo = '''        <!-- Promo Card -->
        <div class="bg-[#F8F7F3] rounded-2xl p-5 relative overflow-hidden flex flex-col gap-3 mt-auto mb-2 border border-[#E3E1DC]">
            <h4 class="font-bold text-[16px] leading-tight z-10 text-[#164A35]">Brew Better Days</h4>
            <p class="text-[12px] text-[#777873] z-10 leading-snug w-4/5">Track, analyze and grow your cafe effortlessly.</p>
            <img src="https://images.unsplash.com/photo-1550133730-695473e544be?w=100&fit=crop" class="absolute -bottom-4 -right-4 w-20 h-20 object-cover rounded-full opacity-70 border-4 border-white shadow-sm">
        </div>'''

if old_promo in content:
    content = content.replace(old_promo, new_promo)
else:
    print("Could not find Promo Card")

# Replace Header
old_header = '''        <!-- Top Header for Main Area -->
        <header class="h-24 flex justify-between items-center px-10 shrink-0 border-b border-brewlyborder/50">
            <div>
                <h2 class="font-sans font-bold text-3xl text-brewlytext" x-text="tabs.find(t => t.id === currentTab)?.name"></h2>
            </div>
            <div class="flex items-center gap-4">
                <div class="relative">
                    <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input type="text" x-model="searchQuery" placeholder="Search orders or menu..." class="bg-gray-50 border border-gray-200 rounded-full pl-11 pr-4 py-2 text-sm focus:outline-none focus:border-brewlygreen focus:ring-1 focus:ring-brewlygreen w-64 transition-all">
                </div>
            </div>
        </header>'''

new_header = '''        <!-- Top Header for Main Area -->
        <header class="h-24 flex justify-between items-center px-10 shrink-0 border-b border-brewlyborder/50 bg-white relative z-10">
            <div x-show="currentTab !== 'analytics'">
                <h2 class="font-sans font-bold text-3xl text-[#164A35]" x-text="tabs.find(t => t.id === currentTab)?.name"></h2>
            </div>
            
            <div class="flex items-center gap-6 ml-auto">
                <div class="relative cursor-pointer group" x-show="currentTab !== 'analytics'">
                    <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input type="text" x-model="searchQuery" placeholder="Search..." class="bg-gray-50 border border-gray-200 rounded-full pl-11 pr-4 py-2 text-sm focus:outline-none focus:border-brewlygreen focus:ring-1 focus:ring-brewlygreen w-64 transition-all">
                </div>

                <div class="relative cursor-pointer group">
                    <i class="fas fa-bell text-[#777873] text-xl group-hover:text-[#164A35] transition-colors"></i>
                    <span class="absolute -top-1 -right-1.5 bg-red-500 text-white text-[9px] font-bold w-4 h-4 flex items-center justify-center rounded-full border-2 border-white">3</span>
                </div>
                
                <div class="flex items-center gap-3 cursor-pointer group pl-6 border-l border-gray-200">
                    <img src="https://ui-avatars.com/api/?name=Admin&background=164A35&color=fff" class="w-10 h-10 rounded-full object-cover">
                    <div class="flex flex-col">
                        <span class="text-[14px] font-bold text-[#202522] leading-tight w-24 truncate" x-text="settings.name || 'Admin'"></span>
                        <span class="text-[12px] font-semibold text-[#777873]">Admin</span>
                    </div>
                    <i class="fas fa-chevron-down text-[10px] text-[#777873] ml-1 group-hover:text-[#164A35] transition-colors"></i>
                </div>
            </div>
        </header>'''

if old_header in content:
    content = content.replace(old_header, new_header)
else:
    print("Could not find Top Header")

old_nav = "currentTab === tab.id ? 'bg-brewlypeach text-brewlyorange font-bold' : 'text-brewlymuted hover:bg-gray-100 hover:text-brewlytext font-medium'"
new_nav = "currentTab === tab.id ? 'bg-[#DDEBDD] text-[#164A35] font-bold' : 'text-brewlymuted hover:bg-gray-100 hover:text-[#164A35] font-medium'"
content = content.replace(old_nav, new_nav)

with open('resources/views/Admin/Dashboard/dashboard.blade.php', 'w', encoding='utf-8') as f:
    f.write(content)
print("Updated dashboard layout elements")
