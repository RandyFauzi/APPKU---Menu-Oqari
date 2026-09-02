<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Pesanan Berhasil - {{ $shop->name }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    @if(isset($shop) && $shop->logo_url)
        <link rel="icon" type="image/png" href="{{ asset('uploads/' . $shop->logo_url) }}">
    @else
        <link rel="icon" type="image/webp" href="{{ asset('logo-oqari.webp') }}">
    @endif
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <style>
        :root {
            --color-primary: {{ $shop->primary_color ?? '#1c4532' }};
            --color-secondary: {{ $shop->primary_color ?? '#2d6a4f' }};
        }
    </style>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body data-page="tracking" class="antialiased max-w-md mx-auto bg-white min-h-screen relative flex flex-col justify-center items-center p-6 text-center shadow-xl">

    <!-- Animasi Ilustrasi -->
    <div class="mb-6 relative">
        <div class="w-32 h-32 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-4 animate-pulse">
            <i class="fas fa-clipboard-list text-6xl text-primary"></i>
        </div>
        <div class="absolute top-0 right-0 w-8 h-8 bg-accent rounded-full border-4 border-white flex items-center justify-center animate-bounce">
            <i class="fas fa-check text-white text-xs"></i>
        </div>
    </div>

    <!-- Teks Status -->
    <h1 class="text-2xl font-extrabold text-gray-800 mb-2">Orderan Sedang Diproses!</h1>
    <p class="text-gray-500 mb-8 text-sm">Pesanan <b id="track-type" class="text-primary">Dine-in</b> senilai <b id="track-total">Rp 0</b> telah diterima. Barista dan tim dapur {{ $shop->name }} langsung menyiapkan pesanan Anda.</p>

    <!-- Info Box -->
    <div class="bg-gray-50 rounded-2xl p-4 w-full mb-8 border border-gray-100 flex items-center gap-4 text-left">
        <div class="bg-white w-12 h-12 rounded-xl flex items-center justify-center shadow-sm text-primary flex-shrink-0">
            <i class="fas fa-receipt text-xl"></i>
        </div>
        <div>
            <p class="text-xs text-gray-400 font-bold uppercase tracking-wider">No. Pesanan</p>
            <p class="font-bold text-gray-800 text-lg">#ORD-<span id="display-order-number">Menunggu...</span></p>
        </div>
    </div>

    <!-- Tombol Kembali -->
    <a href="#" onclick="goBackHome(event)" class="w-full bg-primary text-white py-4 rounded-xl font-bold text-center shadow-lg hover:shadow-xl hover:bg-secondary transition-colors active:scale-95 block">
        Kembali ke Menu
    </a>

    <!-- RATING MODAL (Alpine.js) -->
    <div id="modal-rating" class="fixed inset-0 z-50 hidden flex flex-col justify-end max-w-md mx-auto">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeRating()"></div>
        
        <div x-data="{ 
            rating: 0, 
            hoverRating: 0,
            googleReviewLink: 'https://g.page/r/KODE_UNIK_TOKO/review',
            submitInternalFeedback() {
                alert('Terima kasih atas masukannya. Kami akan segera memperbaikinya!');
                closeRating();
            }
        }" class="relative bg-white w-full rounded-t-3xl p-6 flex flex-col items-center text-center transition-all shadow-[0_-10px_40px_rgba(0,0,0,0.1)]">
            
            <div class="w-12 h-1.5 bg-gray-200 rounded-full mx-auto mb-6"></div>
            
            <div class="w-16 h-16 bg-white border border-gray-100 text-primary rounded-full flex items-center justify-center text-3xl mb-4 shadow-sm">
                <i class="fas fa-star"></i>
            </div>
            
            <h3 class="font-bold text-2xl text-gray-800 mb-2">Bagaimana pesanan Anda?</h3>
            <p class="text-sm text-gray-500 mb-6">Bantu {{ $shop->name }} jadi lebih baik.</p>
            
            <!-- Stars (1 to 5) -->
            <div class="flex justify-center gap-3 mb-4">
                <template x-for="i in 5">
                    <button 
                        @click="rating = i" 
                        @mouseover="hoverRating = i" 
                        @mouseleave="hoverRating = 0"
                        class="text-4xl transition-transform transform hover:scale-110 focus:outline-none"
                        :class="(hoverRating >= i || rating >= i) ? 'text-yellow-400' : 'text-gray-300'">
                        ★
                    </button>
                </template>
            </div>

            <!-- Conditional UI: 1-3 Stars (Internal Feedback) -->
            <div x-show="rating > 0 && rating <= 3" x-transition class="mt-2 w-full">
                <p class="text-sm font-bold text-red-500 mb-3">Mohon maaf atas kekurangannya 🙏</p>
                <textarea class="w-full bg-gray-50 border border-gray-200 rounded-xl p-3 text-sm mb-4 focus:outline-none focus:border-primary" rows="3" placeholder="Apa yang bisa kami perbaiki?"></textarea>
                <button @click="submitInternalFeedback()" class="w-full bg-gray-800 text-white py-3.5 rounded-xl font-bold">Kirim Masukan</button>
            </div>

            <!-- Conditional UI: 4-5 Stars (Google Maps Review) -->
            <div x-show="rating >= 4" x-transition class="mt-2 w-full">
                <p class="text-sm font-bold text-primary mb-4 bg-primary/10 p-3 rounded-lg">Terima kasih! 💙 Dukung kami di Google Maps.</p>
                <a :href="googleReviewLink" target="_blank" @click="closeRating()" class="w-full bg-primary text-white py-3.5 rounded-xl font-bold flex items-center justify-center gap-2 shadow-lg hover:bg-secondary active:scale-95 transition-all">
                    <i class="fab fa-google"></i> Beri Ulasan Bintang 5
                </a>
            </div>

            <button onclick="closeRating()" class="mt-6 text-sm text-gray-400 font-bold underline">Lain kali saja</button>
        </div>
    </div>

    @include('shop.scripts')
    
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Check if there is an order number generated
            const lastOrderStr = localStorage.getItem('gw_last_order');
            if (lastOrderStr) {
                try {
                    const order = JSON.parse(lastOrderStr);
                    // Just take a random 3 digits from order ID or if it has an id
                    const displayId = order.id ? order.id.toString().slice(-3) : Math.floor(Math.random() * 900) + 100;
                    document.getElementById('display-order-number').innerText = displayId;
                } catch(e) {}
            } else {
                document.getElementById('display-order-number').innerText = Math.floor(Math.random() * 900) + 100;
            }
        });
    </script>
</body>
</html>
