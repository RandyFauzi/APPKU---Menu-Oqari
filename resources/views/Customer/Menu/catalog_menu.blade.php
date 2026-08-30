<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>{{ $shop->name }} - Menu</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <script>
        tailwind.config = { 
            theme: { 
                extend: { 
                    colors: { 
                        primary: '{{ $shop->primary_color }}', 
                        bgbase: '#F8F9FA', 
                        textdark: '#1A202C' 
                    }, 
                    fontFamily: { sans: ['Inter', 'sans-serif'], heading: ['Inter', 'sans-serif'] } 
                } 
            } 
        }
    </script>
    <style>
        .app-container { max-w: 414px; margin: 0 auto; background: white; min-height: 100vh; position: relative; box-shadow: 0 0 20px rgba(0,0,0,0.1); }
        .hide-scroll::-webkit-scrollbar { display: none; }
    </style>
</head>
<body class="bg-gray-100 antialiased font-sans">
    <div class="app-container pb-24">
        <!-- Header -->
        <header class="bg-white px-5 pt-6 pb-4 sticky top-0 z-30 flex justify-between items-center border-b border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-full flex items-center justify-center font-bold text-white text-xl" style="background-color: {{ $shop->primary_color }};">
                    {{ substr($shop->name, 0, 1) }}
                </div>
                <div class="flex flex-col">
                    <h1 class="text-xl font-heading font-extrabold text-primary leading-tight tracking-tight">{{ strtoupper($shop->name) }}</h1>
                    <p class="text-[10px] text-gray-500 font-bold uppercase tracking-widest">{{ $shop->theme_style }} theme</p>
                </div>
            </div>
        </header>

        <!-- Location -->
        <div class="px-5 mt-5 mb-5">
            <div class="bg-gray-50 rounded-2xl px-5 py-4 flex items-center justify-between border border-gray-100">
                <div class="flex items-center gap-3">
                    <i class="fas fa-map-marker-alt text-primary text-xl"></i>
                    <div>
                        <p class="text-[10px] text-gray-400 font-bold uppercase">Deliver to / Table</p>
                        <p class="text-sm font-bold text-textdark">Meja 01, {{ $shop->name }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Menu Section -->
        <div class="px-5 mb-2">
            <div class="flex justify-between items-center mb-5">
                <h2 class="font-heading font-bold text-xl text-textdark">Menu Kami</h2>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                @forelse($products as $product)
                <div class="bg-white rounded-2xl p-3 border border-gray-100 flex flex-col justify-between shadow-sm hover:shadow-md transition-shadow relative">
                    <div class="w-full bg-gray-50 rounded-xl mb-3 aspect-square flex items-center justify-center overflow-hidden">
                        <i class="fas fa-coffee text-4xl text-gray-200"></i>
                    </div>
                    <div>
                        <span class="text-[9px] text-gray-500 font-bold uppercase tracking-wider mb-1 block">{{ $product->category_name }}</span>
                        <h3 class="font-bold text-sm text-textdark leading-tight mb-1">{{ $product->name }}</h3>
                        <p class="font-black text-primary text-sm mt-2">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                    </div>
                    <button class="absolute bottom-3 right-3 w-8 h-8 rounded-full bg-primary text-white flex items-center justify-center shadow-md hover:scale-105 transition-transform">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
                @empty
                <p class="text-gray-400 text-sm italic col-span-2 text-center py-10">Belum ada menu yang ditambahkan.</p>
                @endforelse
            </div>
        </div>
    </div>
</body>
</html>
