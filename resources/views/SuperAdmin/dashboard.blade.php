<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Oqari Super Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="icon" type="image/webp" href="{{ asset('logo-oqari.webp') }}">
</head>
<body class="bg-[#F8F7F3] text-gray-800">
<div class="min-h-screen p-4 md:p-8">
    <div class="max-w-7xl mx-auto space-y-6">
        
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between bg-white p-6 rounded-2xl shadow-sm border border-[#E3E1DC] gap-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-[#164A35] rounded-xl flex items-center justify-center text-white">
                    <i class="fas fa-chess-king text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-black text-[#164A35]">Oqari Super Admin</h1>
                    <p class="text-sm text-gray-500">Pusat kendali seluruh sistem dan penyewa (tenant)</p>
                </div>
            </div>
            <div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full sm:w-auto px-4 py-2 bg-red-50 text-red-600 font-bold rounded-xl hover:bg-red-100 transition flex items-center justify-center gap-2">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </button>
                </form>
            </div>
        </div>

        @if(session('success'))
            <div class="p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl font-medium">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl font-medium">
                {{ session('error') }}
            </div>
        @endif

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-gradient-to-br from-[#164A35] to-[#1e5a42] rounded-2xl p-6 text-white shadow-md">
                <p class="text-white/80 font-medium text-sm">Total Toko (Tenants)</p>
                <h3 class="text-3xl font-black mt-2">{{ number_format($totalShops) }}</h3>
            </div>
            <div class="bg-gradient-to-br from-[#D97A32] to-[#e88a42] rounded-2xl p-6 text-white shadow-md">
                <p class="text-white/80 font-medium text-sm">Total Pengguna (Owners/Staff)</p>
                <h3 class="text-3xl font-black mt-2">{{ number_format($totalUsers) }}</h3>
            </div>
            <div class="bg-gradient-to-br from-[#1E5A7A] to-[#287095] rounded-2xl p-6 text-white shadow-md">
                <p class="text-white/80 font-medium text-sm">Total Transaksi Platform</p>
                <h3 class="text-3xl font-black mt-2">{{ number_format($totalOrders) }}</h3>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Shops Table -->
            <div class="bg-white rounded-2xl shadow-sm border border-[#E3E1DC] overflow-hidden flex flex-col">
                <div class="p-4 border-b border-[#E3E1DC] bg-gray-50 shrink-0">
                    <h3 class="font-bold text-gray-800"><i class="fas fa-store mr-2"></i> Daftar Toko</h3>
                </div>
                <div class="overflow-x-auto max-h-[500px] flex-1">
                    <table class="w-full text-left text-sm relative">
                        <thead class="bg-white sticky top-0 shadow-sm text-xs uppercase text-gray-500 z-10">
                            <tr>
                                <th class="p-4">Nama Toko</th>
                                <th class="p-4">Slug</th>
                                <th class="p-4">Users</th>
                                <th class="p-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($shops as $shop)
                            <tr class="hover:bg-gray-50">
                                <td class="p-4 font-bold">{{ $shop->name }}</td>
                                <td class="p-4"><a href="/{{ $shop->slug }}" target="_blank" class="text-blue-500 hover:underline">{{ $shop->slug }}</a></td>
                                <td class="p-4"><span class="bg-gray-100 px-2 py-1 rounded-md text-xs font-bold">{{ $shop->users_count }}</span></td>
                                <td class="p-4 text-right">
                                    <form method="POST" action="{{ route('superadmin.shops.delete', $shop->id) }}" onsubmit="return confirm('Hapus toko beserta semua datanya?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700 p-2"><i class="fas fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="p-4 text-center text-gray-500">Belum ada toko</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Users Table -->
            <div class="bg-white rounded-2xl shadow-sm border border-[#E3E1DC] overflow-hidden flex flex-col">
                <div class="p-4 border-b border-[#E3E1DC] bg-gray-50 shrink-0">
                    <h3 class="font-bold text-gray-800"><i class="fas fa-users mr-2"></i> Daftar Pengguna</h3>
                </div>
                <div class="overflow-x-auto max-h-[500px] flex-1">
                    <table class="w-full text-left text-sm relative">
                        <thead class="bg-white sticky top-0 shadow-sm text-xs uppercase text-gray-500 z-10">
                            <tr>
                                <th class="p-4">Nama & Email</th>
                                <th class="p-4">Role</th>
                                <th class="p-4">Toko</th>
                                <th class="p-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($users as $user)
                            <tr class="hover:bg-gray-50">
                                <td class="p-4">
                                    <div class="font-bold">{{ $user->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $user->email }}</div>
                                </td>
                                <td class="p-4">
                                    <span class="px-2 py-1 rounded-lg text-[10px] font-black uppercase
                                        {{ $user->role === 'superadmin' ? 'bg-purple-100 text-purple-700' : ($user->role === 'owner' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-700') }}">
                                        {{ $user->role }}
                                    </span>
                                </td>
                                <td class="p-4 font-semibold text-gray-700">{{ $user->shop ? $user->shop->name : '-' }}</td>
                                <td class="p-4 text-right">
                                    @if($user->role !== 'superadmin')
                                    <form method="POST" action="{{ route('superadmin.users.delete', $user->id) }}" onsubmit="return confirm('Hapus akun ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700 p-2"><i class="fas fa-trash"></i></button>
                                    </form>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="p-4 text-center text-gray-500">Belum ada pengguna</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>
</body>
</html>
