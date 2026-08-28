<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Pesanan Berhasil - {{ $shop->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
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
<body data-page="tracking" class="antialiased bg-bgbase text-textdark">

    <div class="app-container min-h-screen relative flex flex-col justify-center items-center p-6 text-center">
        <!-- Animasi Ilustrasi -->
        <div class="mb-6 relative">
            <div class="w-32 h-32 bg-white border-4 border-primary rounded-full flex items-center justify-center mx-auto mb-4 animate-pulse shadow-[8px_8px_0_0_#8CB8C9]">
                <i class="fas fa-clipboard-list text-5xl text-primary"></i>
            </div>
            <div class="absolute top-0 right-0 w-10 h-10 bg-accent rounded-full border-4 border-bgbase flex items-center justify-center animate-bounce">
                <i class="fas fa-check text-textdark text-sm"></i>
            </div>
        </div>

        <!-- Teks Status -->
        <h1 class="text-3xl font-heading font-extrabold text-primary mb-2">Order Diterima!</h1>
        <p class="text-textdark/70 mb-8 text-sm max-w-xs mx-auto">Pesanan <b id="track-type" class="text-primary">Dine-in</b> senilai <b id="track-total">Rp 0</b> sedang disiapkan oleh tim Bitten Coffee.</p>

        <!-- Info Box -->
        <div class="bg-white rounded-xl p-4 w-full mb-8 border-2 border-primary/20 flex items-center gap-4 text-left shadow-sm">
            <div class="bg-bgbase w-12 h-12 rounded-lg border-2 border-primary/10 flex items-center justify-center shadow-sm text-primary flex-shrink-0">
                <i class="fas fa-receipt text-xl"></i>
            </div>
            <div>
                <p class="text-xs text-textdark/50 font-bold uppercase tracking-wider font-mono">No. Pesanan</p>
                <p class="font-heading font-bold text-primary text-xl">#ORD-998</p>
            </div>
        </div>

        <!-- Tombol Kembali -->
        <a href="#" onclick="goBackHome(event)" class="w-full bg-primary text-white py-4 rounded-sm font-bold text-center shadow-[4px_4px_0_0_#8CB8C9] hover:translate-y-0.5 hover:shadow-[2px_2px_0_0_#8CB8C9] active:translate-y-1 active:shadow-none transition-all block border-2 border-primary">
            Kembali ke Menu
        </a>

        <!-- RATING MODAL (Alpine.js) -->
        <div id="modal-rating" class="fixed inset-0 z-50 hidden flex flex-col justify-end max-w-[414px] mx-auto">
            <div class="absolute inset-0 bg-textdark/80 backdrop-blur-sm" onclick="closeRating()"></div>
            
            <div x-data="{ 
                rating: 0, 
                hoverRating: 0,
                googleReviewLink: 'https://g.page/r/KODE_UNIK_BITTEN/review',
                submitInternalFeedback() {
                    alert('Terima kasih atas masukannya. Kami akan segera memperbaikinya!');
                    closeRating();
                }
            }" class="relative bg-bgbase w-full rounded-t-2xl p-6 flex flex-col items-center text-center transition-all border-t-4 border-primary shadow-[0_-10px_40px_rgba(30,90,122,0.3)]">
                
                <div class="w-12 h-1.5 bg-primary/20 rounded-full mx-auto mb-6"></div>
                
                <div class="w-16 h-16 bg-white border-2 border-primary text-primary rounded-full flex items-center justify-center text-3xl mb-4 shadow-[4px_4px_0_0_#8CB8C9]">
                    <i class="fas fa-star"></i>
                </div>
                
                <h3 class="font-heading font-bold text-2xl text-primary mb-2">Bagaimana pesanan Anda?</h3>
                <p class="text-sm text-textdark/70 mb-6 font-mono">Bantu Bitten Coffee jadi lebih baik.</p>
                
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
                    <p class="text-sm font-bold text-red-500 mb-3 font-mono">Mohon maaf atas kekurangannya 🙏</p>
                    <textarea class="w-full bg-white border-2 border-primary/20 rounded-xl p-3 text-sm mb-4 focus:outline-none focus:border-primary font-mono" rows="3" placeholder="Apa yang bisa kami perbaiki?"></textarea>
                    <button @click="submitInternalFeedback()" class="w-full bg-textdark text-white py-3.5 rounded-sm font-bold border-2 border-textdark">Kirim Masukan</button>
                </div>

                <!-- Conditional UI: 4-5 Stars (Google Maps Review) -->
                <div x-show="rating >= 4" x-transition class="mt-2 w-full">
                    <p class="text-sm font-bold text-primary mb-4 bg-white p-3 rounded-lg border-2 border-primary/20 shadow-sm font-mono">Terima kasih! 💙 Dukung kami di Google Maps.</p>
                    <a :href="googleReviewLink" target="_blank" @click="closeRating()" class="w-full bg-primary text-white py-3.5 rounded-sm font-bold flex items-center justify-center gap-2 border-2 border-primary shadow-[4px_4px_0_0_#8CB8C9] active:translate-y-1 active:shadow-none transition-all">
                        <i class="fab fa-google"></i> Beri Ulasan Bintang 5
                    </a>
                </div>

                <button onclick="closeRating()" class="mt-6 text-sm text-textdark/50 font-bold underline font-mono">Lain kali saja</button>
            </div>
        </div>
    </div>
    @include('shop.scripts')
</body>
</html>
