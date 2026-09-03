<div x-data="orderNotification()" 
     @new-order-received.window="handleNewOrder($event.detail)"
     x-show="show"
     x-cloak
     class="fixed bottom-4 right-4 z-[9999] bg-white rounded-xl shadow-2xl border border-gray-100 p-5 w-80 transform transition-all"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
>
    <div class="flex items-start gap-4">
        <div class="bg-[#164A35] text-white p-3 rounded-full animate-bounce">
            <i class="fas fa-bell text-xl"></i>
        </div>
        <div class="flex-1">
            <h4 class="font-bold text-[#164A35] text-lg">PESANAN BARU</h4>
            <p class="text-sm font-semibold text-gray-700 mt-1">Order <span x-text="'#'+orderData?.id"></span></p>
            <p class="text-xs text-gray-500" x-text="orderData?.customer_name || 'Customer'"></p>
            
            <div class="mt-3 space-y-1">
                <template x-for="item in orderData?.items || []">
                    <p class="text-xs text-gray-600"><span x-text="item.quantity"></span>x <span x-text="item.product?.name"></span></p>
                </template>
            </div>
            
            <div class="mt-3 pt-3 border-t border-gray-100 flex items-center justify-between">
                <p class="font-bold text-sm text-[#164A35]" x-text="'Rp ' + (orderData?.total_amount || 0).toLocaleString('id-ID')"></p>
            </div>
            
            <div class="mt-4 flex flex-col gap-2">
                <button @click="acknowledgeOrder()" class="w-full bg-[#164A35] text-white text-xs font-bold py-2 rounded-lg hover:bg-opacity-90">
                    TERIMA PESANAN
                </button>
                <button @click="viewDetails()" class="w-full bg-gray-100 text-gray-700 text-xs font-bold py-2 rounded-lg hover:bg-gray-200">
                    Lihat Detail
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('orderNotification', () => ({
        show: false,
        orderData: null,
        audio: null,
        
        init() {
            // Check if audio file exists, if not we'll handle gracefully
            this.audio = new Audio('/Assest/notif_orderan_masuk.mp3');
            this.audio.loop = true;
        },
        
        handleNewOrder(order) {
            this.orderData = order;
            this.show = true;
            
            // Play sound looping
            if(this.audio) {
                this.audio.play().catch(e => console.warn('Autoplay prevented', e));
            }
            
            // Dispatch event to update Live Orders counter in other components
            window.dispatchEvent(new CustomEvent('refresh-live-orders'));
        },
        
        acknowledgeOrder() {
            if(this.audio) {
                this.audio.pause();
                this.audio.currentTime = 0;
            }
            this.show = false;
        },
        
        viewDetails() {
            this.acknowledgeOrder();
            window.location.href = '/admin/dashboard?tab=orders';
        }
    }));
});
</script>
