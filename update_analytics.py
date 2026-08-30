import re

with open('resources/views/Admin/Dashboard/tabs/analytics.blade.php', 'r', encoding='utf-8') as f:
    content = f.read()

# 1. Remove Banner "Kinerja cafe"
banner_pattern = r'<div class="bg-\[#DDEBDD\].*?Lihat Analisis Lengkap &rarr;.*?</div>\s*</div>'
content = re.sub(banner_pattern, '', content, flags=re.DOTALL)

# 2. Modify "Menu Terlaris"
old_card = """<div class="bg-white rounded-[28px] p-8 border border-[#E3E1DC] shadow-[0_4px_20px_rgba(0,0,0,0.02)] relative group cursor-pointer hover:border-[#F7E5D2] transition-colors">
                <div class="flex justify-between items-start mb-6">
                    <h3 class="text-[18px] font-bold text-[#202522]">Menu Terlaris Hari Ini</h3>
                    <div class="w-8 h-8 rounded-full bg-[#F8F7F3] flex items-center justify-center text-[#777873] group-hover:bg-[#D97A32] group-hover:text-white transition-colors">
                        <i class="fas fa-arrow-right text-[12px] -rotate-45"></i>
                    </div>
                </div>
                
                <div class="flex gap-5 items-center">
                    <div class="flex-grow">
                        <div class="inline-flex px-2 py-1 rounded-md bg-[#F7E5D2] text-[#D97A32] text-[11px] font-bold uppercase tracking-wider mb-3">
                            <i class="fas fa-star mr-1"></i> #1 Terlaris
                        </div>
                        <h4 class="text-[22px] font-bold text-[#164A35] mb-2 leading-tight" x-text="data.topProduct.name">Es Kopi Aren</h4>
                        <p class="text-[13px] text-[#777873] font-medium leading-relaxed mb-4" x-text="data.topProduct.desc">Perpaduan espresso, gula aren, dan susu segar.</p>
                        
                        <div class="flex items-center gap-3">
                            <span class="font-bold text-[#202522] text-[15px]" x-text="data.topProduct.sold + ' porsi'">132 porsi</span>
                            <span class="text-green-600 text-[13px] font-bold flex items-center gap-1"><i class="fas fa-arrow-up text-[10px]"></i> <span x-text="data.topProduct.change + '%'">22%</span></span>
                        </div>
                    </div>"""

new_card = """<div class="bg-white rounded-[28px] p-8 border border-[#E3E1DC] shadow-[0_4px_20px_rgba(0,0,0,0.02)] relative">
                <div class="flex justify-between items-start mb-6">
                    <h3 class="text-[18px] font-bold text-[#202522]">Menu Terlaris Hari Ini</h3>
                </div>
                
                <div class="flex gap-5 items-center">
                    <div class="flex-grow">
                        <div class="inline-flex px-2 py-1 rounded-md bg-[#F7E5D2] text-[#D97A32] text-[11px] font-bold uppercase tracking-wider mb-3">
                            <i class="fas fa-star mr-1"></i> #1 Terlaris
                        </div>
                        <h4 class="text-[22px] font-bold text-[#164A35] mb-2 leading-tight">Es Kopi Aren</h4>
                        <p class="text-[13px] text-[#777873] font-medium leading-relaxed mb-4">Perpaduan espresso, gula aren, dan susu segar.</p>
                        
                        <div class="flex items-center gap-3">
                            <span class="font-bold text-[#202522] text-[15px]">132 porsi</span>
                            <span class="text-green-600 text-[13px] font-bold flex items-center gap-1"><i class="fas fa-arrow-up text-[10px]"></i> <span>22%</span></span>
                        </div>
                    </div>"""

content = content.replace(old_card, new_card)

with open('resources/views/Admin/Dashboard/tabs/analytics.blade.php', 'w', encoding='utf-8') as f:
    f.write(content)
print("Updated analytics.blade.php")
