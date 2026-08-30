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
        tailwind.config = { theme: { extend: { colors: { primary: '{{ $shop->primary_color ?? "#1E5A7A" }}', bgbase: '#F4EFE6', accent: '#8CB8C9', textdark: '#2D3748' } } } }
    </script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body data-page="cart" class="antialiased bg-bgbase text-textdark">

    <div class="app-container pb-28">
        <header class="bg-bgbase p-4 sticky top-0 z-30 flex items-center gap-4 border-b-2 border-primary/10 backdrop-blur-md bg-opacity-90">
            <a href="#" onclick="goBackHome(event)" class="w-8 h-8 flex items-center justify-center rounded-sm bg-white border-2 border-primary text-primary hover:bg-primary hover:text-white transition-colors">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h2 class="font-heading font-bold text-primary text-lg">My Cart</h2>
        </header>
        
        <div class="p-4">
            

            <h3 class="text-xs font-bold text-textdark/60 uppercase tracking-widest mb-3 font-mono">Daftar Pesanan</h3>
            <div id="cart-items-container"></div>



            <!-- Rincian Biaya -->
            <div id="summary-section" class="bg-white p-5 rounded-xl border-2 border-primary/10 mt-6 mb-4 shadow-sm hidden">
                <h3 class="font-heading font-bold text-primary mb-3 border-b-2 border-primary/10 pb-2">Order Summary</h3>
                <div class="flex justify-between text-sm text-textdark/80 mb-2">
                    <span>Subtotal</span>
                    <span id="summary-subtotal" class="font-bold text-textdark">Rp 0</span>
                </div>
                <div class="flex justify-between text-sm text-textdark/80 mb-2">
                    <span>Pajak (PB1 10%)</span>
                    <span id="summary-tax" class="font-bold text-textdark">Rp 0</span>
                </div>
                <div id="packaging-row" class="flex justify-between text-sm text-textdark/80 mb-3" style="display:none;">
                    <span>Biaya Kemasan</span>
                    <span id="summary-packaging" class="font-bold text-textdark">Rp 0</span>
                </div>
                <div class="flex justify-between items-center mt-3 pt-3 border-t-2 border-dashed border-primary/20">
                    <span class="font-bold text-textdark">Total Tagihan</span>
                    <span class="text-xl font-extrabold text-primary" id="cart-detail-total">Rp 0</span>
                </div>
            </div>
        </div>

        <!-- Fix Bottom Bar -->
        <div class="fixed bottom-0 max-w-[414px] w-full bg-white border-t-2 border-primary/10 p-4 shadow-[0_-4px_10px_-1px_rgba(0,0,0,0.05)] z-40 mx-auto left-0 right-0">
            <button id="btn-trigger-pay" onclick="openCustomerInfoModal()" class="w-full bg-primary text-white py-3.5 rounded-sm font-bold text-base shadow-[4px_4px_0_0_#8CB8C9] active:translate-y-1 active:shadow-none transition-all border-2 border-primary flex items-center justify-center gap-2">
                <span id="btn-pay-text">Lanjut Pembayaran</span> <i class="fas fa-chevron-right text-xs opacity-80"></i>
            </button>
        </div>

        <!-- CUSTOMER INFO MODAL -->
        <div id="modal-customer-info" class="fixed inset-0 z-50 hidden flex flex-col justify-end max-w-[414px] mx-auto">
            <div class="absolute inset-0 modal-overlay bg-textdark/80" onclick="closeCustomerInfoModal()"></div>
            <div class="relative bg-bgbase w-full rounded-t-2xl p-6 flex flex-col shadow-2xl border-t-4 border-primary max-h-[85vh] overflow-y-auto">
                <div class="w-12 h-1.5 bg-primary/20 rounded-full mx-auto mb-5"></div>
                
                <h3 class="font-heading font-bold text-xl text-primary mb-2 text-center">Data Pemesan</h3>
                <p class="text-xs text-textdark/60 text-center mb-6">Silakan lengkapi data ini sebelum pembayaran.</p>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-textdark mb-1">Nama Pemanggil <span class="text-red-500">*</span></label>
                        <input type="text" id="customer-name" placeholder="Nama Anda" class="w-full bg-white border border-gray-200 rounded-lg px-4 py-3 text-sm focus:outline-none focus:border-primary font-bold">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-textdark mb-1">Email <span class="text-red-500">*</span></label>
                        <input type="email" id="customer-email" placeholder="contoh@email.com" class="w-full bg-white border border-gray-200 rounded-lg px-4 py-3 text-sm focus:outline-none focus:border-primary font-bold">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-textdark mb-1">Nomor Meja <span class="text-red-500">*</span></label>
                        <input type="text" id="customer-table" placeholder="Nomor Meja (Misal: 01)" class="w-full bg-white border border-gray-200 rounded-lg px-4 py-3 text-sm focus:outline-none focus:border-primary font-bold">
                    </div>
                </div>

                <button onclick="triggerPaymentGateway()" class="w-full bg-primary text-white py-4 rounded-sm font-bold text-lg mt-8 shadow-[4px_4px_0_0_#8CB8C9] active:translate-y-1 active:shadow-none transition-all flex justify-center items-center gap-2 border-2 border-primary">
                    Lanjut ke Pembayaran <i class="fas fa-chevron-right text-xs opacity-80"></i>
                </button>
            </div>
        </div>

        <!-- PAYMENT GATEWAY MODAL -->
        <div id="modal-payment" class="fixed inset-0 z-50 hidden flex flex-col justify-end max-w-[414px] mx-auto">
            <div class="absolute inset-0 modal-overlay bg-textdark/80" onclick="cancelPayment()"></div>
            <div class="relative bg-bgbase w-full rounded-t-2xl p-6 flex flex-col shadow-2xl border-t-4 border-primary">
                <div class="w-12 h-1.5 bg-primary/20 rounded-full mx-auto mb-5"></div>
                
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h3 class="font-heading font-bold text-xl text-primary">Payment Method</h3>
                        <p class="text-xs text-textdark/70 mt-1 uppercase tracking-wider font-bold">Bitten Coffee</p>
                    </div>
                    <div class="bg-primary text-white px-3 py-1.5 rounded-sm text-sm font-bold border-2 border-primary shadow-[2px_2px_0_0_#8CB8C9]" id="pg-total">Rp 0</div>
                </div>
                
                <label class="flex items-center gap-4 p-4 border-2 border-primary bg-white rounded-xl mb-8 cursor-pointer shadow-[4px_4px_0_0_#8CB8C9]">
                    <input type="radio" name="payment" checked class="accent-primary w-5 h-5">
                    <div class="bg-[#00569c] p-1 rounded"><img src="https://upload.wikimedia.org/wikipedia/commons/a/a2/Logo_QRIS.svg" class="h-5 filter invert" alt="QRIS"></div>
                    <span class="font-bold text-sm text-textdark">QRIS (GoPay, OVO, dll)</span>
                </label>
                
                <button id="btn-pay" onclick="processSimulatedPayment()" class="w-full bg-primary text-white py-4 rounded-sm font-bold text-lg shadow-[4px_4px_0_0_#8CB8C9] active:translate-y-1 active:shadow-none transition-all flex justify-center items-center gap-2 border-2 border-primary">
                    Order Now
                </button>
            </div>
        </div>
        
        <div id="toast-container" class="fixed bottom-24 left-1/2 -translate-x-1/2 z-50 flex flex-col gap-2 w-full max-w-[414px] px-4 pointer-events-none"></div>
    @include('shop.scripts')
</body>
</html>
