<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Keranjang - {{ $shop->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    @if(isset($shop) && $shop->logo_url)
        <link rel="icon" type="image/png" href="{{ asset('storage/' . $shop->logo_url) }}">
    @else
        <link rel="icon" type="image/webp" href="{{ asset('Pavico.webp') }}">
    @endif
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <script>
        tailwind.config = { theme: { extend: { colors: { primary: '{{ $shop->primary_color ?? "#1E5A7A" }}', secondary: '{{ $shop->primary_color ?? "#2d6a4f" }}', accent: '#f59e0b', bgbase: '#F9FAFB', textdark: '#1A202C' } } } }
    </script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body data-page="cart" class="antialiased max-w-md mx-auto bg-gray-50 min-h-screen relative shadow-xl pb-28 page-transition">

    <header class="bg-white p-4 sticky top-0 z-30 shadow-sm flex items-center gap-4 border-b border-gray-100">
        <a href="#" onclick="goBackHome(event)" class="w-8 h-8 flex items-center justify-center rounded-full bg-gray-100 text-gray-600 hover:bg-gray-200 transition-colors">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h2 class="font-bold text-gray-800 text-lg">My Cart</h2>
    </header>
    
    <div class="p-4">
        
        <!-- Data Pemesan -->
        <div id="customer-info-section" class="bg-white p-5 rounded-2xl border border-gray-100 mb-6 shadow-sm">
            <h3 class="font-bold text-gray-800 mb-3 border-b border-gray-100 pb-2">Informasi Meja & Pemesan</h3>
            
            <div class="mb-3 flex gap-4" id="order-type-container">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="radio" name="orderType" value="Dine-in" class="accent-primary w-4 h-4" checked>
                    <span class="text-sm font-bold text-gray-700">Dine In</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="radio" name="orderType" value="Takeaway" class="accent-primary w-4 h-4">
                    <span class="text-sm font-bold text-gray-700">Takeaway</span>
                </label>
            </div>

            <div class="mb-3" id="table-input-container">
                <label class="block text-xs font-bold text-gray-500 mb-1">Nomor Meja <span class="text-red-500">*</span></label>
                <!-- the id is customer-table so triggerPaymentGateway in public/js/main.js reads it correctly if customer_table doesn't exist -->
                <input type="text" id="customer-table" placeholder="Ketik nomor meja" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm text-gray-600 font-bold focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-colors">
            </div>
            
            <div class="mb-3">
                <label class="block text-xs font-bold text-gray-500 mb-1">Nama Pemesan <span class="text-red-500">*</span></label>
                <input type="text" id="customer-name" placeholder="Masukkan nama Anda" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-colors" required>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">Alamat Email / No. WA <span class="text-red-500">*</span></label>
                <!-- Retained ID as customer-email for main.js compatibility -->
                <input type="text" id="customer-email" placeholder="Untuk pengiriman struk/notifikasi" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-colors" required>
            </div>
        </div>

        <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">Daftar Pesanan</h3>
        <div id="cart-items-container"></div>

        <!-- Rincian Biaya -->
        <div id="summary-section" class="bg-white p-5 rounded-2xl border border-gray-200 mt-6 mb-4 shadow-sm hidden">
            <h3 class="font-bold text-gray-800 mb-3 border-b border-gray-100 pb-2">Order Summary</h3>
            <div class="flex justify-between text-sm text-gray-600 mb-2">
                <span>Subtotal</span>
                <span id="summary-subtotal" class="font-semibold text-gray-800">Rp 0</span>
            </div>
            <div class="flex justify-between text-sm text-gray-600 mb-2">
                <span>Pajak Restoran (PB1 10%)</span>
                <span id="summary-tax" class="font-semibold text-gray-800">Rp 0</span>
            </div>
            <div id="packaging-row" class="flex justify-between text-sm text-gray-600 mb-3" style="display:none;">
                <span>Biaya Kemasan (Takeaway)</span>
                <span id="summary-packaging" class="font-semibold text-gray-800">Rp 0</span>
            </div>
            <div class="flex justify-between items-center mt-3 pt-3 border-t border-dashed border-gray-200">
                <span class="font-bold text-gray-800">Total Tagihan</span>
                <span class="text-2xl font-extrabold text-primary" id="cart-detail-total">Rp 0</span>
            </div>
        </div>
    </div>

    <!-- Fix Bottom Bar -->
    <div class="fixed bottom-0 max-w-md w-full bg-white border-t border-gray-100 p-4 shadow-[0_-4px_10px_-1px_rgba(0,0,0,0.05)] z-40 mx-auto left-0 right-0">
        <button id="btn-trigger-pay" onclick="triggerPaymentGateway()" class="w-full bg-primary text-white py-3.5 rounded-xl font-bold text-base shadow-lg hover:shadow-xl flex items-center justify-center gap-2 active:scale-95 transition-all">
            <span id="btn-pay-text">Lanjut Pembayaran</span> <i class="fas fa-chevron-right text-xs opacity-80"></i>
        </button>
    </div>

    <!-- PAYMENT GATEWAY MODAL -->
    <div id="modal-payment" class="fixed inset-0 z-50 hidden flex flex-col justify-end max-w-md mx-auto">
        <div class="absolute inset-0 modal-overlay bg-black/50" onclick="cancelPayment()"></div>
        <div class="relative bg-white w-full rounded-t-3xl modal-content p-6 flex flex-col shadow-2xl">
            <div class="w-12 h-1.5 bg-gray-200 rounded-full mx-auto mb-5"></div>
            
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h3 class="font-bold text-xl text-gray-800">Payment Method</h3>
                    <p class="text-xs text-gray-500 mt-1 uppercase tracking-wider font-bold">{{ $shop->name }}</p>
                </div>
                <div class="bg-primary/10 text-primary px-3 py-1.5 rounded-lg text-sm font-bold border border-primary/20" id="pg-total">Rp 0</div>
            </div>
            
            <label class="flex items-center gap-4 p-4 border-2 border-primary bg-primary/5 rounded-2xl mb-3 cursor-pointer">
                <input type="radio" name="payment" checked class="accent-primary w-5 h-5">
                <div class="bg-primary p-1 rounded-md"><img src="https://upload.wikimedia.org/wikipedia/commons/a/a2/Logo_QRIS.svg" class="h-5 filter invert" alt="QRIS"></div>
                <span class="font-bold text-sm text-primary">QRIS (GoPay, Dana, dll)</span>
            </label>
            
            <label class="flex items-center gap-4 p-4 border-2 border-transparent bg-gray-50 rounded-2xl mb-8 cursor-pointer hover:bg-gray-100 transition-colors">
                <input type="radio" name="payment" class="accent-primary w-5 h-5">
                <div class="w-8 h-8 rounded-full bg-white flex items-center justify-center shadow-sm"><i class="fas fa-money-bill-wave text-gray-400"></i></div>
                <span class="font-bold text-sm text-gray-600">Bayar di Kasir (Cash)</span>
            </label>
            
            <button id="btn-pay" onclick="processSimulatedPayment()" class="w-full bg-primary text-white py-4 rounded-2xl font-bold text-lg shadow-lg hover:shadow-xl active:scale-95 transition-all flex justify-center items-center gap-2">
                Order Now
            </button>
        </div>
    </div>
    
    <div id="toast-container" class="fixed bottom-24 left-1/2 -translate-x-1/2 z-50 flex flex-col gap-2 w-full max-w-sm px-4 pointer-events-none"></div>

    @include('shop.scripts')
    
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            let qrTable = localStorage.getItem('bitten_table_qr');
            const tableInput = document.getElementById('customer-table');
            if (qrTable && tableInput) {
                tableInput.value = qrTable;
                tableInput.readOnly = true;
                tableInput.classList.add('bg-gray-100', 'text-gray-500', 'cursor-not-allowed');
                tableInput.classList.remove('bg-gray-50');
            }
            
            const orderTypes = document.querySelectorAll('input[name="orderType"]');
            const tableContainer = document.getElementById('table-input-container');
            
            orderTypes.forEach(radio => {
                radio.addEventListener('change', (e) => {
                    if(e.target.value === 'Takeaway') {
                        tableContainer.style.display = 'none';
                        tableInput.value = 'TA';
                    } else {
                        tableContainer.style.display = 'block';
                        tableInput.value = localStorage.getItem('bitten_table_qr') || '';
                    }
                });
            });
        });
    </script>
</body>
</html>
