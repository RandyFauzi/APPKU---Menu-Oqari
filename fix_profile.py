import re

with open('resources/views/Admin/Dashboard/dashboard.blade.php', 'r', encoding='utf-8') as f:
    content = f.read()

old_profile = '''                <div class="flex items-center gap-3 cursor-pointer group pl-6 border-l border-gray-200">
                    <img src="https://ui-avatars.com/api/?name=Admin&background=164A35&color=fff" class="w-10 h-10 rounded-full object-cover">
                    <div class="flex flex-col">
                        <span class="text-[14px] font-bold text-[#202522] leading-tight w-24 truncate" x-text="settings.name || 'Admin'"></span>
                        <span class="text-[12px] font-semibold text-[#777873]">Admin</span>
                    </div>
                    <i class="fas fa-chevron-down text-[10px] text-[#777873] ml-1 group-hover:text-[#164A35] transition-colors"></i>
                </div>'''

new_profile = '''                <div class="relative pl-6 border-l border-gray-200" x-data="{ open: false }">
                    <div @click="open = !open" @click.away="open = false" class="flex items-center gap-3 cursor-pointer group">
                        <img src="https://ui-avatars.com/api/?name=Admin&background=164A35&color=fff" class="w-10 h-10 rounded-full object-cover">
                        <div class="flex flex-col">
                            <span class="text-[14px] font-bold text-[#202522] leading-tight w-24 truncate" x-text="settings.name || 'Admin'"></span>
                            <span class="text-[12px] font-semibold text-[#777873]">Admin</span>
                        </div>
                        <i class="fas fa-chevron-down text-[10px] text-[#777873] ml-1 group-hover:text-[#164A35] transition-colors"></i>
                    </div>
                    
                    <!-- Dropdown Menu -->
                    <div x-show="open" x-cloak x-transition.opacity class="absolute right-0 mt-3 w-48 bg-white rounded-[16px] shadow-[0_10px_40px_rgba(0,0,0,0.08)] border border-[#E3E1DC] overflow-hidden z-50">
                        <div class="p-2">
                            <button @click="currentTab = 'profile'; open = false" class="w-full text-left px-4 py-2.5 rounded-[10px] text-sm font-semibold text-[#202522] hover:bg-[#F8F7F3] hover:text-[#164A35] transition-colors flex items-center gap-3">
                                <i class="fas fa-cog w-4"></i> Settings
                            </button>
                            <div class="h-px bg-gray-100 my-1 mx-2"></div>
                            <button @click="logout()" class="w-full text-left px-4 py-2.5 rounded-[10px] text-sm font-bold text-red-500 hover:bg-red-50 transition-colors flex items-center gap-3">
                                <i class="fas fa-sign-out-alt w-4"></i> Logout
                            </button>
                        </div>
                    </div>
                </div>'''

if old_profile in content:
    content = content.replace(old_profile, new_profile)
    with open('resources/views/Admin/Dashboard/dashboard.blade.php', 'w', encoding='utf-8') as f:
        f.write(content)
    print("Fixed Profile Dropdown")
else:
    print("Could not find Profile block")
