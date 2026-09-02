@extends('Admin.Layouts.admin-auth-master')

@section('title', 'Point of Sale (POS)')

@section('content')
<div class="h-screen flex flex-col bg-gray-100" x-data="posApp()">
    
    <!-- Navbar / Header POS -->
    <div class="bg-white border-b px-6 py-4 flex justify-between items-center shadow-sm">
        <h1 class="text-xl font-bold text-gray-800">POS Terminal</h1>
        
        <div class="flex items-center gap-4">
            @if($activeSession)
                <span class="text-green-600 font-semibold text-sm flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-green-500"></span>
                    Kasir Aktif (Sesi #{{ $activeSession->id }})
                </span>
                <button class="bg-red-500 text-white px-4 py-2 rounded-lg font-bold text-sm hover:bg-red-600 transition">Tutup Kasir</button>
            @else
                <span class="text-red-600 font-semibold text-sm flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-red-500"></span>
                    Kasir Ditutup
                </span>
                <button class="bg-green-500 text-white px-4 py-2 rounded-lg font-bold text-sm hover:bg-green-600 transition" @click="showOpenRegisterModal = true">Buka Kasir</button>
            @endif
        </div>
    </div>

    <!-- Main Content -->
    <div class="flex-1 flex overflow-hidden">
        <!-- Products Grid (Left) -->
        <div class="w-2/3 p-6 overflow-y-auto">
            @if(!$activeSession)
                <div class="flex flex-col items-center justify-center h-full text-center">
                    <h2 class="text-2xl font-bold text-gray-400 mb-2">Kasir Belum Dibuka</h2>
                    <p class="text-gray-500">Silakan buka kas (Open Register) terlebih dahulu untuk mulai menerima pesanan.</p>
                </div>
            @else
                <div class="grid grid-cols-4 gap-4">
                    <!-- Produk Dummy Loop (Untuk Preview Arsitektur) -->
                    @foreach($products as $product)
                        <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 hover:border-green-500 cursor-pointer transition">
                            <h3 class="font-bold text-gray-800 truncate">{{ $product->name }}</h3>
                            <p class="text-green-600 font-bold text-sm mt-2">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Cart Sidebar (Right) -->
        <div class="w-1/3 bg-white border-l flex flex-col">
            <div class="p-4 border-b">
                <h2 class="font-bold text-lg text-gray-800">Detail Pesanan</h2>
            </div>
            
            <div class="flex-1 p-4 overflow-y-auto bg-gray-50">
                <!-- Cart Items akan muncul di sini -->
                <div class="text-center text-gray-400 mt-10">Belum ada item pesanan</div>
            </div>

            <div class="p-4 border-t bg-white">
                <div class="flex justify-between mb-2">
                    <span class="text-gray-600">Subtotal</span>
                    <span class="font-bold">Rp 0</span>
                </div>
                <div class="flex justify-between mb-4">
                    <span class="text-gray-600">Tax (11%)</span>
                    <span class="font-bold">Rp 0</span>
                </div>
                <div class="flex justify-between mb-6 text-lg">
                    <span class="font-bold text-gray-800">Total Pembayaran</span>
                    <span class="font-bold text-green-600">Rp 0</span>
                </div>
                
                <button class="w-full bg-green-600 text-white py-3 rounded-xl font-bold text-lg hover:bg-green-700 transition" {{ !$activeSession ? 'disabled' : '' }} :class="{'opacity-50 cursor-not-allowed': !{{ $activeSession ? 'true' : 'false' }} }">
                    Bayar & Cetak
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function posApp() {
        return {
            showOpenRegisterModal: false,
            // Logika AlpineJS untuk keranjang dan modifier akan ditambahkan di sini
        }
    }
</script>
@endsection
