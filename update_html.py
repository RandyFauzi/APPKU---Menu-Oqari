import re

with open('resources/views/Admin/Dashboard/dashboard.blade.php', 'r', encoding='utf-8') as f:
    content = f.read()

# Replace the HTML Modal
html_pattern = r"<!-- MODAL: INCOMING ORDER ALERT -->.*?<!-- Footer Actions -->.*?</div>\s*</div>\s*</div>"
new_html = """<!-- MODAL: INCOMING ORDER ALERT -->
        <div x-show="activeIncomingOrder" x-cloak class="fixed inset-0 z-[200] flex items-center justify-center p-4 font-sans sm:items-center sm:p-0">
            <!-- Backdrop -->
            <div class="absolute inset-0 bg-black/25 backdrop-blur-[2px] transition-opacity"></div>
            
            <!-- Modal Content -->
            <div class="relative bg-[#F8F7F3] w-full sm:w-[520px] rounded-[24px] shadow-[0_20px_60px_rgba(0,0,0,0.15)] flex flex-col border border-[#E3E1DC] overflow-hidden" 
                 x-transition:enter="transition ease-out duration-300 transform"
                 x-transition:enter-start="opacity-0 translate-y-8 scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                 x-transition:leave="transition ease-in duration-200 transform"
                 x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 scale-95">

                <!-- Header -->
                <div class="p-6 bg-[#F8F7F3] border-b border-[#E3E1DC] flex justify-between items-start">
                    <div class="flex gap-4">
                        <div class="w-12 h-12 rounded-[14px] bg-[#D97A32] text-white flex items-center justify-center shadow-sm shrink-0">
                            <i class="fas fa-bell text-xl animate-pulse"></i>
                        </div>
                        <div class="flex flex-col justify-center">
                            <h2 class="text-[18px] font-bold text-[#D97A32] leading-tight mb-1">Pesanan Baru Masuk!</h2>
                            <p class="text-[13px] text-[#777873]">Baru saja</p>
                        </div>
                    </div>
                    
                    <!-- Timer Badge -->
                    <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-[10px] text-[15px] font-mono font-bold shrink-0 transition-colors"
                         :class="{
                            'bg-[#D97A32] text-white': incomingTimer > 15,
                            'bg-red-500 text-white animate-pulse': incomingTimer <= 15
                         }">
                        <i class="fas fa-stopwatch text-sm"></i>
                        <span x-text="formatTime(incomingTimer)"></span>
                    </div>
                </div>

                <!-- Body -->
                <div class="px-6 py-5 bg-white relative">
                    <!-- Queue info -->
                    <div x-show="incomingOrderQueue.length > 0" class="absolute top-0 inset-x-0 bg-[#F7E5D2] text-[#D97A32] text-[12px] font-bold py-1 text-center">
                        <span x-text="incomingOrderQueue.length"></span> pesanan menunggu antrean
                    </div>

                    <!-- Order Number & Type -->
                    <div class="flex gap-4 items-center mt-2 mb-6">
                        <div class="w-12 h-12 rounded-[14px] bg-[#F8F7F3] border border-[#E3E1DC] text-[#202522] flex items-center justify-center shrink-0">
                            <i class="fas fa-shopping-bag text-lg"></i>
                        </div>
                        <div>
                            <h3 class="text-[18px] font-bold text-[#202522] mb-1">Pesanan #<span x-text="activeIncomingOrder?.id"></span></h3>
                            <p class="text-[13px] text-[#777873]">Takeaway • <span x-text="activeIncomingOrder?.customer"></span></p>
                        </div>
                    </div>

                    <div class="border-t border-dashed border-[#E3E1DC] my-4"></div>

                    <!-- Items -->
                    <div class="flex flex-col gap-4 max-h-[30vh] overflow-y-auto hide-scroll py-2">
                        <template x-for="(item, idx) in activeIncomingOrder?.items" :key="idx">
                            <div class="flex items-center gap-4">
                                <img :src="item.image || 'https://images.unsplash.com/photo-1497935586351-b67a49e012bf?w=100&h=100&fit=crop'" class="w-12 h-12 rounded-[12px] object-cover bg-gray-100 shrink-0">
                                <div class="flex-grow min-w-0">
                                    <h4 class="text-[14px] font-bold text-[#202522] truncate" x-text="item.name"></h4>
                                </div>
                                <div class="text-[13px] font-bold text-[#777873] w-8 text-center shrink-0" x-text="item.qty + 'x'"></div>
                                <div class="text-[14px] font-bold text-[#202522] w-24 text-right shrink-0" x-text="formatRp(item.price * item.qty)"></div>
                            </div>
                        </template>
                    </div>

                    <div class="border-t border-dashed border-[#E3E1DC] my-4"></div>

                    <!-- Total & Payment -->
                    <div class="flex justify-between items-end mb-2 mt-4">
                        <div>
                            <p class="text-[13px] font-bold text-[#202522] mb-1">Total Pesanan</p>
                            <p class="text-[26px] font-bold text-[#164A35]" x-text="activeIncomingOrder ? formatRp(activeIncomingOrder.total) : '0'"></p>
                        </div>
                        
                        <!-- Payment Badge -->
                        <div class="bg-[#DDEBDD] rounded-[12px] px-4 py-2.5 flex items-start gap-2.5">
                            <i class="fas fa-check-circle text-[#164A35] mt-0.5"></i>
                            <div>
                                <p class="text-[13px] font-bold text-[#164A35] leading-none mb-1.5">Pembayaran Lunas</p>
                                <p class="text-[11px] font-medium text-[#164A35]/80 leading-none">Dibayar via QRIS</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer Actions -->
                <div class="p-6 bg-[#F8F7F3] border-t border-[#E3E1DC]">
                    <!-- State: Confirm Reject -->
                    <div x-show="incomingOrderState === 'reject_confirm'" class="flex flex-col gap-4">
                        <p class="text-center text-[15px] font-bold text-[#202522]">Tolak pesanan #<span x-text="activeIncomingOrder?.id"></span>?</p>
                        <div class="flex gap-3">
                            <button @click="incomingOrderState = 'new'" class="flex-1 py-3.5 rounded-[14px] font-bold text-[14px] bg-white border border-[#E3E1DC] text-[#777873] hover:bg-gray-50 transition-colors">Batal</button>
                            <button @click="confirmRejectOrder(false)" class="flex-1 py-3.5 rounded-[14px] font-bold text-[14px] bg-red-50 text-red-600 border border-red-200 hover:bg-red-100 transition-colors">Ya, Tolak Pesanan</button>
                        </div>
                    </div>

                    <!-- State: New/Normal -->
                    <div x-show="incomingOrderState === 'new'" class="flex gap-4">
                        <button @click="incomingOrderState = 'reject_confirm'" class="w-[140px] shrink-0 py-4 rounded-[14px] font-bold text-[14px] bg-transparent border border-[#E3E1DC] text-[#777873] hover:text-red-500 hover:border-red-200 hover:bg-red-50 transition-colors">
                            Tolak Pesanan
                        </button>
                        <button @click="acceptOrder()" class="flex-1 py-4 rounded-[14px] font-bold text-[15px] bg-[#164A35] text-white hover:bg-[#0f3526] transition-colors shadow-[0_4px_12px_rgba(22,74,53,0.2)] flex items-center justify-center gap-2">
                            <i class="fas fa-coffee"></i> Terima & Siapkan
                        </button>
                    </div>

                    <!-- State: Accepting -->
                    <div x-show="incomingOrderState === 'accepting'" class="flex justify-center items-center py-4">
                        <i class="fas fa-spinner fa-spin text-[#164A35] text-2xl"></i>
                        <span class="ml-3 font-bold text-[#164A35]">Menerima pesanan...</span>
                    </div>

                    <!-- State: Accepted -->
                    <div x-show="incomingOrderState === 'accepted'" class="flex flex-col justify-center items-center py-2">
                        <div class="w-10 h-10 bg-[#DDEBDD] text-[#164A35] rounded-full flex items-center justify-center mb-2">
                            <i class="fas fa-check text-xl"></i>
                        </div>
                        <span class="font-bold text-[#164A35]">Pesanan Diterima!</span>
                        <span class="text-[13px] text-[#777873]">Sedang disiapkan...</span>
                    </div>
                    
                    <!-- State: Rejected -->
                    <div x-show="incomingOrderState === 'rejected'" class="flex flex-col justify-center items-center py-2">
                        <div class="w-10 h-10 bg-red-100 text-red-600 rounded-full flex items-center justify-center mb-2">
                            <i class="fas fa-times text-xl"></i>
                        </div>
                        <span class="font-bold text-red-600">Pesanan Ditolak</span>
                    </div>

                    <!-- Timeout Notice -->
                    <div class="text-center mt-5" x-show="['new', 'reject_confirm'].includes(incomingOrderState)">
                        <p class="text-[12px] font-medium text-[#777873] flex items-center justify-center gap-1.5">
                            <i class="far fa-clock"></i> Pesanan otomatis ditolak dalam <span x-text="incomingTimer" class="font-mono font-bold"></span> detik
                        </p>
                    </div>
                </div>
            </div>
        </div>"""

content = re.sub(html_pattern, new_html, content, flags=re.DOTALL)

with open('resources/views/Admin/Dashboard/dashboard.blade.php', 'w', encoding='utf-8') as f:
    f.write(content)
print("Updated HTML for Incoming Order Modal")
