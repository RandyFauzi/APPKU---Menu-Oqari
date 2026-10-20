<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Oqari Super Admin')</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="icon" type="image/webp" href="{{ asset('logo-oqari.webp') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <x-theme-head :shop="$shop ?? null" />
</head>
<body class="bg-ivory text-gray-800 flex h-screen overflow-hidden">
    <div class="oq-frieze fixed inset-x-0 top-0 z-[200] pointer-events-none" aria-hidden="true"></div>

    <!-- Sidebar -->
    <x-cafe-sidebar />

    <!-- Main Content -->
    <main class="flex-1 h-full overflow-y-auto overflow-x-hidden">
        <!-- Mobile Header (Visible only on small screens) -->
        <div class="md:hidden bg-white border-b border-line p-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <img src="{{ asset('logo-oqari.webp') }}" alt="Oqari Logo" class="w-8 h-8 object-contain">
                <div class="font-black text-lg text-primary">Oqari Super Admin</div>
            </div>
            <!-- Menu button could go here, but for now we keep it simple -->
            <form method="POST" action="{{ route('logout') }}" class="m-0">
                @csrf
                <button type="submit" class="text-red-500 font-bold">Logout</button>
            </form>
        </div>

        <div class="p-4 md:p-8 max-w-7xl mx-auto space-y-6">
            @if(session('success'))
                <div class="p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl font-medium flex items-center gap-2">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl font-medium flex items-center gap-2">
                    <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                </div>
            @endif
            
            @if($errors->any())
                <div class="p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl font-medium flex flex-col gap-1">
                    @foreach($errors->all() as $error)
                        <div><i class="fas fa-exclamation-circle"></i> {{ $error }}</div>
                    @endforeach
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    @stack('scripts')
</body>
</html>
