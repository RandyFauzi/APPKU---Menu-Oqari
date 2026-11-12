<div x-data="orderNotification()" 
     @new-order-received.window="handleNewOrder($event.detail)"
     x-show="show"
     x-cloak
     class="fixed bottom-5 right-5 z-[9999] w-[350px] max-w-[calc(100vw-32px)] bg-white rounded-[20px] shadow-[0_20px_50px_rgba(0,0,0,0.16)] border border-[#e6e5e0] p-4.5 transform transition-all select-none"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 translate-y-4 scale-95"
     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100 translate-y-0 scale-100"
     x-transition:leave-end="opacity-0 translate-y-4 scale-95"
>
    <!-- Top Bar: Status Badge + Close Button -->
    <div class="flex items-center justify-between pb-3 border-b border-[#f0f0ee]">
        <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-[10px] bg-[#fff6f0] border border-[#fbd4c0] text-[#df5826] flex items-center justify-center shrink-0 text-xs shadow-2xs">
                <i class="fas fa-bell animate-pulse"></i>
            </div>
            <div>
                <div class="flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-[#df5826] animate-ping"></span>
                    <h4 class="text-[12px] font-extrabold text-[#111827] uppercase tracking-wider">Pesanan Baru Masuk</h4>
                </div>
                <p class="text-[10.5px] text-[#7f837e]">Baru saja diterima</p>
            </div>
        </div>

        <button type="button" 
                @click="acknowledgeOrder()" 
                class="w-7 h-7 rounded-full text-gray-400 hover:text-gray-700 hover:bg-gray-100 flex items-center justify-center transition cursor-pointer" 
                title="Tutup Notifikasi">
            <i class="fas fa-times text-xs"></i>
        </button>
    </div>

    <!-- Order Info Card -->
    <div class="my-3 bg-[#fafaf9] rounded-[14px] p-3 border border-[#f0f0ee] space-y-2">
        <!-- Order # & Fulfillment Badge -->
        <div class="flex items-center justify-between">
            <span class="text-[14px] font-extrabold text-[#111827]">
                Order <span x-text="'#' + (orderData?.id || '')"></span>
            </span>
            <span class="px-2 py-0.5 rounded-[7px] text-[10.5px] font-bold bg-white text-gray-700 border border-[#e6e5e0] shadow-2xs flex items-center gap-1">
                <i :class="orderData?.fulfillment_type === 'DELIVERY' ? 'fas fa-truck' : (orderData?.fulfillment_type === 'TAKEAWAY' ? 'fas fa-bag-shopping' : 'fas fa-utensils')" class="text-[10px] text-[#df5826]"></i>
                <span x-text="orderData?.table || 'Dine In'"></span>
            </span>
        </div>

        <!-- Customer Name -->
        <div class="flex items-center gap-1.5 text-[11.5px] text-gray-600 font-medium">
            <i class="far fa-user text-gray-400 text-[10.5px]"></i>
            <span class="truncate" x-text="orderData?.customer_name || 'Walk-in Customer'"></span>
        </div>

        <!-- Items Preview (Compact) -->
        <div class="pt-2 border-t border-dashed border-[#e6e5e0] space-y-1">
            <template x-for="(item, idx) in (orderData?.items || []).slice(0, 2)" :key="idx">
                <div class="flex items-center justify-between text-[11.5px] text-gray-700">
                    <span class="truncate pr-2">
                        <strong class="text-[#111827]" x-text="(item.quantity || item.qty || 1) + 'x'"></strong>
                        <span x-text="item.name || item.product_name"></span>
                    </span>
                    <span class="text-gray-500 font-medium shrink-0" x-text="formatItemPrice((item.price || item.unit_price || 0) * (item.quantity || item.qty || 1))"></span>
                </div>
            </template>
            <template x-if="(orderData?.items || []).length > 2">
                <p class="text-[10.5px] text-[#df5826] font-semibold pt-0.5" x-text="'+ ' + (orderData.items.length - 2) + ' menu lainnya...'"></p>
            </template>
            <template x-if="!(orderData?.items && orderData.items.length > 0)">
                <p class="text-[11px] text-gray-500 font-medium flex items-center gap-1">
                    <i class="fas fa-layer-group text-gray-400 text-[10px]"></i>
                    <span x-text="(orderData?.items_count || 1) + ' menu dalam pesanan'"></span>
                </p>
            </template>
        </div>
    </div>

    <!-- Bottom: Total & Action Button -->
    <div class="flex items-center justify-between pt-1 gap-3">
        <div class="min-w-0">
            <span class="text-[10px] uppercase font-bold text-gray-400 block tracking-wider leading-none mb-1">Total Pesanan</span>
            <span class="text-[17px] font-extrabold text-[#111827] leading-none" x-text="formattedTotal"></span>
        </div>

        <button type="button" 
                @click="viewDetails()" 
                class="px-4 py-2.5 rounded-[12px] bg-[#df5826] hover:bg-[#c94d00] active:scale-[0.98] text-white text-[12px] font-bold transition flex items-center gap-1.5 shadow-xs cursor-pointer shrink-0">
            <span>Buka Pesanan</span>
            <i class="fas fa-arrow-right text-[10px]"></i>
        </button>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('orderNotification', () => ({
        show: false,
        orderData: null,
        audio: null,
        
        init() {
            try {
                this.audio = new Audio('/Assest/notif_orderan_masuk.mp3');
                this.audio.loop = true;
            } catch (e) {
                console.warn('Audio init error', e);
            }
        },
        
        handleNewOrder(order) {
            if (!order) return;
            this.orderData = order;
            this.show = true;
            
            // Play sound looping
            if (this.audio) {
                this.audio.currentTime = 0;
                this.audio.play().catch(e => console.warn('Autoplay prevented', e));
            }
            
            // Dispatch event to update Live Orders counter in other components
            window.dispatchEvent(new CustomEvent('refresh-live-orders'));
        },
        
        acknowledgeOrder() {
            if (this.audio) {
                this.audio.pause();
                this.audio.currentTime = 0;
            }
            this.show = false;
        },
        
        viewDetails() {
            const orderId = this.orderData?.id;
            this.acknowledgeOrder();
            if (window.location.pathname.includes('/kitchen')) {
                return;
            }
            const targetUrl = '{{ route('admin.orders.index') }}' + (orderId ? '?order=' + orderId : '');
            window.location.href = targetUrl;
        },

        get formattedTotal() {
            const raw = this.orderData?.total ?? this.orderData?.total_amount ?? this.orderData?.grand_total ?? 0;
            const num = typeof raw === 'number' ? raw : parseFloat(raw) || 0;
            return 'Rp ' + Math.round(num).toLocaleString('id-ID');
        },

        formatItemPrice(val) {
            const num = typeof val === 'number' ? val : parseFloat(val) || 0;
            return 'Rp ' + Math.round(num).toLocaleString('id-ID');
        }
    }));
});
</script>
