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
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full sm:w-auto px-4 py-2 bg-red-50 text-red-600 font-bold rounded-xl hover:bg-red-100 transition flex items-center justify-center gap-2">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </button>
            </form>
        </div>

        @if(session('success'))
            <div class="p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl font-medium">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl font-medium">{{ session('error') }}</div>
        @endif

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="bg-gradient-to-br from-[#164A35] to-[#1e5a42] rounded-2xl p-6 text-white shadow-md">
                <p class="text-white/80 font-medium text-sm">Toko aktif</p>
                <h3 class="text-3xl font-black mt-2">{{ number_format($activeShops) }} <span class="text-base font-medium text-white/70">/ {{ number_format($totalShops) }}</span></h3>
            </div>
            <div class="bg-gradient-to-br from-[#D97A32] to-[#e88a42] rounded-2xl p-6 text-white shadow-md">
                <p class="text-white/80 font-medium text-sm">Total pengguna</p>
                <h3 class="text-3xl font-black mt-2">{{ number_format($totalUsers) }}</h3>
            </div>
            <div class="bg-gradient-to-br from-[#1E5A7A] to-[#287095] rounded-2xl p-6 text-white shadow-md">
                <p class="text-white/80 font-medium text-sm">Order &middot; 30 hari terakhir</p>
                <h3 class="text-3xl font-black mt-2">{{ number_format($ordersLast30d) }}</h3>
            </div>
            <div class="bg-gradient-to-br from-[#6B4AA6] to-[#8562c4] rounded-2xl p-6 text-white shadow-md">
                <p class="text-white/80 font-medium text-sm">Omzet platform &middot; 30 hari</p>
                <h3 class="text-2xl font-black mt-2">Rp{{ number_format($revenueLast30d, 0, ',', '.') }}</h3>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Shops Table -->
            <div class="bg-white rounded-2xl shadow-sm border border-[#E3E1DC] overflow-hidden flex flex-col">
                <div class="p-4 border-b border-[#E3E1DC] bg-gray-50 shrink-0 space-y-3">
                    <h3 class="font-bold text-gray-800"><i class="fas fa-store mr-2"></i> Daftar toko</h3>
                    <form method="GET" class="flex flex-wrap gap-2">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama / slug toko..."
                               class="flex-1 min-w-[140px] px-3 py-2 text-sm border border-gray-200 rounded-lg">
                        <select name="status" class="px-3 py-2 text-sm border border-gray-200 rounded-lg">
                            <option value="">Semua status</option>
                            <option value="active" @selected(request('status') === 'active')>Active</option>
                            <option value="trial" @selected(request('status') === 'trial')>Trial</option>
                            <option value="suspended" @selected(request('status') === 'suspended')>Suspended</option>
                        </select>
                        <button class="px-3 py-2 text-sm bg-[#164A35] text-white rounded-lg font-bold">Filter</button>
                    </form>
                </div>
                <div class="overflow-x-auto flex-1">
                    <table class="w-full text-left text-sm relative">
                        <thead class="bg-white sticky top-0 shadow-sm text-xs uppercase text-gray-500 z-10">
                            <tr>
                                <th class="p-4">Toko</th>
                                <th class="p-4">Status</th>
                                <th class="p-4">Users</th>
                                <th class="p-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($shops as $shop)
                            <tr class="hover:bg-gray-50">
                                <td class="p-4">
                                    <div class="font-bold">{{ $shop->name }}</div>
                                    <a href="/{{ $shop->slug }}" target="_blank" class="text-blue-500 hover:underline text-xs">{{ $shop->slug }}</a>
                                    @if($shop->isDormant())
                                        <div class="text-[10px] text-amber-600 font-bold mt-1"><i class="fas fa-triangle-exclamation"></i> Tidak ada order 14 hari+</div>
                                    @endif
                                </td>
                                <td class="p-4">
                                    @if($shop->status === 'active')
                                        <span class="px-2 py-1 rounded-lg text-[10px] font-black uppercase bg-green-100 text-green-700">Active</span>
                                    @elseif($shop->status === 'trial')
                                        <span class="px-2 py-1 rounded-lg text-[10px] font-black uppercase bg-blue-100 text-blue-700">Trial</span>
                                    @else
                                        <span class="px-2 py-1 rounded-lg text-[10px] font-black uppercase bg-red-100 text-red-700">Suspended</span>
                                    @endif
                                </td>
                                <td class="p-4"><span class="bg-gray-100 px-2 py-1 rounded-md text-xs font-bold">{{ $shop->users_count }}</span></td>
                                <td class="p-4 text-right space-x-1 whitespace-nowrap">
                                    @if($shop->isSuspended())
                                        <form method="POST" action="{{ route('superadmin.shops.activate', $shop) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="text-green-600 hover:text-green-800 p-2" title="Aktifkan kembali"><i class="fas fa-play"></i></button>
                                        </form>
                                    @else
                                        <button type="button" onclick="openSuspendModal({{ $shop->id }}, '{{ $shop->name }}')"
                                                class="text-amber-600 hover:text-amber-800 p-2" title="Suspend"><i class="fas fa-pause"></i></button>
                                    @endif
                                    <button type="button" onclick="openDeleteModal('{{ route('superadmin.shops.delete', $shop) }}', '{{ $shop->slug }}', '{{ $shop->name }}')"
                                            class="text-red-500 hover:text-red-700 p-2" title="Hapus permanen"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="p-4 text-center text-gray-500">Tidak ada toko yang cocok</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-4 border-t border-[#E3E1DC]">{{ $shops->links() }}</div>
            </div>

            <!-- Users Table -->
            <div class="bg-white rounded-2xl shadow-sm border border-[#E3E1DC] overflow-hidden flex flex-col">
                <div class="p-4 border-b border-[#E3E1DC] bg-gray-50 shrink-0 space-y-3">
                    <h3 class="font-bold text-gray-800"><i class="fas fa-users mr-2"></i> Daftar pengguna</h3>
                    <form method="GET" class="flex gap-2">
                        <input type="text" name="user_search" value="{{ request('user_search') }}" placeholder="Cari nama / email..."
                               class="flex-1 px-3 py-2 text-sm border border-gray-200 rounded-lg">
                        <button class="px-3 py-2 text-sm bg-[#164A35] text-white rounded-lg font-bold">Cari</button>
                    </form>
                </div>
                <div class="overflow-x-auto flex-1">
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
                                        {{ $user->role === 'superadmin' ? 'bg-purple-100 text-purple-700' : ($user->role === 'admin' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-700') }}">
                                        {{ $user->role }}
                                    </span>
                                </td>
                                <td class="p-4 font-semibold text-gray-700">{{ $user->shop?->name ?? '-' }}</td>
                                <td class="p-4 text-right">
                                    @if($user->role !== 'superadmin')
                                    <form method="POST" action="{{ route('superadmin.users.delete', $user->id) }}" onsubmit="return confirm('Hapus akun {{ $user->name }}?')">
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
                <div class="p-4 border-t border-[#E3E1DC]">{{ $users->links() }}</div>
            </div>
        </div>

        <!-- Activity Log -->
        <div class="bg-white rounded-2xl shadow-sm border border-[#E3E1DC] overflow-hidden">
            <div class="p-4 border-b border-[#E3E1DC] bg-gray-50">
                <h3 class="font-bold text-gray-800"><i class="fas fa-clock-rotate-left mr-2"></i> Aktivitas superadmin terbaru</h3>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($recentActivity as $log)
                <div class="p-4 text-sm flex justify-between">
                    <span><span class="font-bold">{{ $log->actor->name ?? 'System' }}</span> &middot; {{ str_replace('.', ' ', $log->action) }} &middot; <span class="text-gray-500">{{ $log->target_label }}</span></span>
                    <span class="text-gray-400 text-xs">{{ $log->created_at->diffForHumans() }}</span>
                </div>
                @empty
                <div class="p-4 text-center text-gray-500 text-sm">Belum ada aktivitas tercatat</div>
                @endforelse
            </div>
        </div>

    </div>
</div>

<!-- Suspend Modal -->
<div id="suspendModal" class="hidden fixed inset-0 bg-black/40 flex items-center justify-center p-4 z-50">
    <div class="bg-white rounded-2xl p-6 max-w-sm w-full space-y-4">
        <h3 class="font-black text-lg">Suspend <span id="suspendShopName"></span>?</h3>
        <p class="text-sm text-gray-500">Toko tidak bisa diakses staff-nya sampai diaktifkan kembali. Data tidak dihapus.</p>
        <form id="suspendForm" method="POST">
            @csrf
            <input type="text" name="reason" required placeholder="Alasan suspend (wajib diisi)"
                   class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg mb-3">
            <div class="flex gap-2 justify-end">
                <button type="button" onclick="closeModal('suspendModal')" class="px-4 py-2 text-sm rounded-lg border">Batal</button>
                <button type="submit" class="px-4 py-2 text-sm rounded-lg bg-amber-500 text-white font-bold">Suspend toko</button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Modal -->
<div id="deleteModal" class="hidden fixed inset-0 bg-black/40 flex items-center justify-center p-4 z-50">
    <div class="bg-white rounded-2xl p-6 max-w-sm w-full space-y-4">
        <h3 class="font-black text-lg text-red-600">Hapus <span id="deleteShopName"></span> permanen?</h3>
        <p class="text-sm text-gray-500">Semua order, produk, dan staff toko ini akan ikut terhapus dan <b>tidak bisa dikembalikan</b>. Ketik slug toko untuk konfirmasi.</p>
        <form id="deleteForm" method="POST">
            @csrf
            @method('DELETE')
            <input type="text" name="confirm_slug" id="confirmSlugInput" required placeholder="Ketik slug toko di sini"
                   class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg mb-3">
            <div class="flex gap-2 justify-end">
                <button type="button" onclick="closeModal('deleteModal')" class="px-4 py-2 text-sm rounded-lg border">Batal</button>
                <button type="submit" class="px-4 py-2 text-sm rounded-lg bg-red-600 text-white font-bold">Hapus permanen</button>
            </div>
        </form>
    </div>
</div>

<script>
function openSuspendModal(shopId, shopName) {
    document.getElementById('suspendShopName').textContent = shopName;
    document.getElementById('suspendForm').action = `/superadmin/shops/${shopId}/suspend`;
    document.getElementById('suspendModal').classList.remove('hidden');
}
function openDeleteModal(actionUrl, slug, shopName) {
    document.getElementById('deleteShopName').textContent = shopName;
    document.getElementById('deleteForm').action = actionUrl;
    document.getElementById('confirmSlugInput').placeholder = `Ketik "${slug}" untuk konfirmasi`;
    document.getElementById('deleteModal').classList.remove('hidden');
}
function closeModal(id) {
    document.getElementById(id).classList.add('hidden');
}
</script>
</body>
</html>
