<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Jadwal Saya — {{ auth()->user()->shop->name }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <x-theme-head :shop="auth()->user()->shop" />
</head>
<body class="min-h-screen bg-[#f8f9fa] text-[#111827] font-['DM_Sans',sans-serif] antialiased">
<div class="cafe-page-shell">
    <x-cafe-sidebar />
    <div class="cafe-page-content bg-[#f8f9fa]">
        <header class="h-[64px] bg-white border-b border-[#e9ebf0] px-4 md:px-7 flex items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <button type="button" x-data @click="$dispatch('toggle-mobile-nav')" class="md:hidden w-11 h-11 rounded-lg text-gray-500 hover:bg-gray-50" aria-label="Buka navigasi"><i class="fas fa-bars" aria-hidden="true"></i></button>
                <span class="text-[13px] font-semibold">Jadwal Saya</span>
            </div>
            <div class="flex items-center gap-2.5 min-w-0">
                <span class="w-8 h-8 rounded-full bg-[#fff6f0] text-[#c94d00] flex items-center justify-center text-xs font-semibold shrink-0" aria-hidden="true">{{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 2)) }}</span>
                <span class="text-xs text-[#53565a] truncate">{{ auth()->user()->name }}</span>
            </div>
        </header>
        <main class="p-4 md:p-6 lg:p-7">
            @if(session('success'))<p role="status" class="p-4 mb-5 rounded-lg bg-green-50 text-green-800 border border-green-200 text-sm">{{ session('success') }}</p>@endif
            @if($errors->any())<div role="alert" class="p-4 mb-5 rounded-lg bg-red-50 text-red-800 border border-red-200 text-sm"><ul class="list-disc pl-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
            @yield('content')
        </main>
    </div>
</div>
</body>
</html>
