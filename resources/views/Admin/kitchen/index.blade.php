<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Kitchen Display — OQARI</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        window.OQARI_REALTIME = {
            shopId: @json(auth()->check() ? auth()->user()->shop_id : null)
        };
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <x-theme-head :shop="$shop ?? null" />
</head>
<body class="bg-gray-900 min-h-screen font-sans" x-data="kitchenApp()" @refresh-live-orders.window="refresh()">
    <div class="oq-frieze fixed inset-x-0 top-0 z-[200] pointer-events-none" aria-hidden="true"></div>

{{-- TOPBAR --}}
<div class="flex items-center justify-between px-6 py-3 bg-gray-800 border-b border-gray-700">
    <div class="flex items-center gap-3">
        <i class="fas fa-fire-burner text-orange-400 text-xl"></i>
        <span class="text-white font-black text-lg">KITCHEN DISPLAY</span>
    </div>
    <div class="flex items-center gap-4">
        <span class="text-xs text-gray-400">Auto-refresh setiap 5 detik</span>
        <div class="flex items-center gap-2 text-sm text-gray-300">
            <div class="w-7 h-7 rounded-full bg-orange-500 flex items-center justify-center font-bold text-xs">
                {{ substr(Auth::user()->name, 0, 1) }}
            </div>
            <span>{{ Auth::user()->name }}</span>
        </div>

        @can('record-attendance')
            <a href="{{ route('admin.attendance.index') }}" class="block px-3 py-3 rounded-xl font-bold text-sm text-gold-400 hover:bg-gray-100">Absensi Karyawan</a>
        @endcan
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button class="text-xs text-gray-500 hover:text-red-400"><i class="fas fa-sign-out-alt"></i></button>
        </form>
    </div>
</div>

{{-- KANBAN COLUMNS --}}
<div class="grid grid-cols-3 gap-4 p-4 h-[calc(100vh-3.5rem)]">

    {{-- NEW --}}
    <div class="flex flex-col">
        <div class="flex items-center gap-2 mb-3">
            <div class="w-3 h-3 rounded-full bg-blue-400 animate-pulse"></div>
            <h2 class="text-blue-400 font-black text-sm uppercase tracking-widest">New</h2>
            <span class="bg-blue-400/20 text-blue-400 text-xs font-bold px-2 py-0.5 rounded-full"
                  x-text="orders.filter(o => o.status === 'CONFIRMED').length"></span>
        </div>
        <div class="flex-1 overflow-y-auto space-y-3 pr-1">
            <template x-for="order in orders.filter(o => o.status === 'CONFIRMED')" :key="order.id">
                <div class="bg-gray-800 rounded-2xl p-4 border border-blue-500/30">
                    <div class="flex justify-between items-center mb-3">
                        <span class="text-white font-black text-lg">#<span x-text="order.id"></span></span>
                        <span class="text-xs text-gray-400" x-text="order.time"></span>
                    </div>
                    <div class="space-y-2 mb-4">
                        <template x-for="item in order.items" :key="item.id">
                            <div class="bg-gray-700/50 rounded-xl p-3">
                                <p class="text-white font-bold text-sm">
                                    <span class="text-blue-400 font-black" x-text="item.qty + '×'"></span>
                                    <span x-text="' ' + item.name"></span>
                                </p>
                                <p x-show="item.variant" class="text-gray-400 text-xs mt-0.5" x-text="'• ' + item.variant"></p>
                                <template x-for="mod in (item.modifiers ?? [])" :key="mod.id ?? mod.name">
                                    <p class="text-gray-400 text-xs" x-text="'• ' + mod.name"></p>
                                </template>
                                <p x-show="item.notes" class="text-amber-400 text-sm whitespace-pre-wrap break-words mt-1" x-text="'📝 ' + item.notes"></p>
                            </div>
                        </template>
                    </div>
                    <button @click="advance(order.id, 'PREPARING')"
                        class="w-full py-2.5 bg-blue-500 hover:bg-blue-400 text-white font-black rounded-xl transition text-sm">
                        <i class="fas fa-play mr-1"></i> MULAI
                    </button>
                </div>
            </template>
        </div>
    </div>

    {{-- PREPARING --}}
    <div class="flex flex-col">
        <div class="flex items-center gap-2 mb-3">
            <div class="w-3 h-3 rounded-full bg-amber-400 animate-pulse"></div>
            <h2 class="text-amber-400 font-black text-sm uppercase tracking-widest">Preparing</h2>
            <span class="bg-amber-400/20 text-amber-400 text-xs font-bold px-2 py-0.5 rounded-full"
                  x-text="orders.filter(o => o.status === 'PREPARING').length"></span>
        </div>
        <div class="flex-1 overflow-y-auto space-y-3 pr-1">
            <template x-for="order in orders.filter(o => o.status === 'PREPARING')" :key="order.id">
                <div class="bg-gray-800 rounded-2xl p-4 border border-amber-500/30">
                    <div class="flex justify-between items-center mb-3">
                        <span class="text-white font-black text-lg">#<span x-text="order.id"></span></span>
                        <span class="text-xs text-gray-400" x-text="order.time"></span>
                    </div>
                    <div class="space-y-2 mb-4">
                        <template x-for="item in order.items" :key="item.id">
                            <div class="bg-gray-700/50 rounded-xl p-3">
                                <p class="text-white font-bold text-sm">
                                    <span class="text-amber-400 font-black" x-text="item.qty + '×'"></span>
                                    <span x-text="' ' + item.name"></span>
                                </p>
                                <p x-show="item.variant" class="text-gray-400 text-xs mt-0.5" x-text="'• ' + item.variant"></p>
                                <template x-for="mod in (item.modifiers ?? [])" :key="mod.id ?? mod.name">
                                    <p class="text-gray-400 text-xs" x-text="'• ' + mod.name"></p>
                                </template>
                                <p x-show="item.notes" class="text-amber-400 text-sm whitespace-pre-wrap break-words mt-1" x-text="'📝 ' + item.notes"></p>
                            </div>
                        </template>
                    </div>
                    <button @click="advance(order.id, 'READY')"
                        class="w-full py-2.5 bg-amber-500 hover:bg-amber-400 text-white font-black rounded-xl transition text-sm">
                        <i class="fas fa-check mr-1"></i> SIAP
                    </button>
                </div>
            </template>
        </div>
    </div>

    {{-- READY --}}
    <div class="flex flex-col">
        <div class="flex items-center gap-2 mb-3">
            <div class="w-3 h-3 rounded-full bg-green-400"></div>
            <h2 class="text-green-400 font-black text-sm uppercase tracking-widest">Ready</h2>
            <span class="bg-green-400/20 text-green-400 text-xs font-bold px-2 py-0.5 rounded-full"
                  x-text="orders.filter(o => o.status === 'READY').length"></span>
        </div>
        <div class="flex-1 overflow-y-auto space-y-3 pr-1">
            <template x-for="order in orders.filter(o => o.status === 'READY')" :key="order.id">
                <div class="bg-gray-800 rounded-2xl p-4 border border-gold-600/30 opacity-75">
                    <div class="flex justify-between items-center mb-3">
                        <span class="text-white font-black text-lg">#<span x-text="order.id"></span></span>
                        <span class="text-xs text-gray-400" x-text="order.time"></span>
                    </div>
                    <div class="space-y-2 mb-4">
                        <template x-for="item in order.items" :key="item.id">
                            <div class="bg-gray-700/30 rounded-xl p-3">
                                <p class="text-gray-400 font-bold text-sm line-through">
                                    <span x-text="item.qty + '× ' + item.name"></span>
                                </p>
                            </div>
                        </template>
                    </div>
                    <button @click="advance(order.id, 'COMPLETED')"
                        class="w-full py-2.5 bg-brand-800 hover:bg-green-500 text-white font-black rounded-xl transition text-sm">
                        <i class="fas fa-hand-holding mr-1"></i> DIAMBIL
                    </button>
                </div>
            </template>
        </div>
    </div>

</div>

<script>
function kitchenApp() {
    return {
        orders: {{ Illuminate\Support\Js::from($kitchenOrders) }},

        init() {
            // Auto-refresh every 5 seconds
            setInterval(() => this.refresh(), 5000);
        },

        async refresh() {
            try {
                const res = await fetch('/admin/kitchen/orders', {
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
                });
                this.orders = await res.json();
            } catch(e) { /* silent fail — will retry */ }
        },

        async advance(orderId, newStatus) {
            try {
                const res = await fetch(`/admin/kitchen/orders/${orderId}/status`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                    body: JSON.stringify({ status: newStatus }),
                });
                const data = await res.json();
                if (data.success) {
                    const order = this.orders.find(o => o.id === orderId);
                    if (order) {
                        if (newStatus === 'COMPLETED') {
                            this.orders = this.orders.filter(o => o.id !== orderId);
                        } else {
                            order.status = newStatus;
                        }
                    }
                }
            } catch(e) { alert('Gagal update status'); }
        },
    };
}
</script>
<x-order-notification />
<script>
    window.SHOP_ID = {{ Auth::user()->shop_id ?? 'null' }};
</script>
</body>
</html>
