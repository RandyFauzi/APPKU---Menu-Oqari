<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Oqari Super Admin')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="icon" type="image/webp" href="{{ asset('logo-oqari.webp') }}">
    <style>
        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            font-weight: 600;
            color: #4b5563; /* text-gray-600 */
            transition: all 0.2s;
        }
        .sidebar-link:hover {
            background-color: #f3f4f6; /* bg-gray-100 */
            color: #164A35;
        }
        .sidebar-link.active {
            background-color: #164A35;
            color: white;
        }
    </style>
</head>
<body class="bg-[#F8F7F3] text-gray-800 flex min-h-screen">

    <!-- Sidebar -->
    <aside class="w-64 bg-white border-r border-[#E3E1DC] flex flex-col hidden md:flex shrink-0">
        <div class="p-6 flex items-center gap-3 border-b border-[#E3E1DC]">
            <div class="w-10 h-10 bg-[#164A35] rounded-lg flex items-center justify-center text-white">
                <i class="fas fa-chess-king"></i>
            </div>
            <div class="font-black text-xl text-[#164A35]">Oqari</div>
        </div>
        
        <nav class="p-4 flex-1 space-y-1">
            <a href="{{ route('superadmin.dashboard') }}" class="sidebar-link {{ request()->routeIs('superadmin.dashboard') ? 'active' : '' }}">
                <i class="fas fa-chart-pie w-5"></i> Dashboard
            </a>
            <a href="{{ route('superadmin.shops.index') }}" class="sidebar-link {{ request()->routeIs('superadmin.shops.*') ? 'active' : '' }}">
                <i class="fas fa-store w-5"></i> Manajemen Toko
            </a>
            <a href="{{ route('superadmin.users.index') }}" class="sidebar-link {{ request()->routeIs('superadmin.users.*') ? 'active' : '' }}">
                <i class="fas fa-users w-5"></i> Manajemen Pengguna
            </a>
        </nav>

        <div class="p-4 border-t border-[#E3E1DC]">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full px-4 py-2 bg-red-50 text-red-600 font-bold rounded-xl hover:bg-red-100 transition flex items-center justify-center gap-2">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 overflow-x-hidden">
        <!-- Mobile Header (Visible only on small screens) -->
        <div class="md:hidden bg-white border-b border-[#E3E1DC] p-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-[#164A35] rounded-lg flex items-center justify-center text-white">
                    <i class="fas fa-chess-king text-sm"></i>
                </div>
                <div class="font-black text-lg text-[#164A35]">Oqari Super Admin</div>
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
