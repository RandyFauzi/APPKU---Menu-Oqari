@extends('SuperAdmin.layouts.app')

@section('title', $shop->name . ' - Detail Toko')

@section('content')
<div class="space-y-6">
    <!-- Header Navigation -->
    <div class="flex items-center gap-4">
        <a href="{{ route('superadmin.shops.index') }}" class="w-10 h-10 bg-white border border-[#E3E1DC] rounded-xl flex items-center justify-center text-gray-500 hover:bg-gray-50 transition">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-2xl font-black text-gray-800">{{ $shop->name }}</h1>
            <p class="text-sm text-gray-500">Detail dan informasi toko</p>
        </div>
        <div class="ml-auto flex gap-2">
            <a href="{{ route('superadmin.shops.edit', $shop) }}" class="px-4 py-2 bg-white border border-[#E3E1DC] text-gray-700 font-bold rounded-xl hover:bg-gray-50 transition">
                <i class="fas fa-edit mr-2"></i> Edit Profil Toko
            </a>
            
            @if($shop->isSuspended())
                <form method="POST" action="{{ route('superadmin.shops.activate', $shop) }}" class="inline">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-green-50 border border-green-200 text-green-700 font-bold rounded-xl hover:bg-green-100 transition">
                        <i class="fas fa-play mr-2"></i> Aktifkan Toko
                    </button>
                </form>
            @else
                <button type="button" onclick="openSuspendModal()" class="px-4 py-2 bg-amber-50 border border-amber-200 text-amber-700 font-bold rounded-xl hover:bg-amber-100 transition">
                    <i class="fas fa-pause mr-2"></i> Suspend Toko
                </button>
            @endif
            
            <button type="button" onclick="openDeleteModal()" class="px-4 py-2 bg-red-50 border border-red-200 text-red-700 font-bold rounded-xl hover:bg-red-100 transition">
                <i class="fas fa-trash mr-2"></i> Hapus Permanen
            </button>
        </div>
    </div>

    <!-- Info Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        <!-- Kolom Kiri: Profil -->
        <div class="space-y-6">
            <div class="bg-white rounded-2xl shadow-sm border border-[#E3E1DC] p-6 space-y-4">
                <div class="w-24 h-24 bg-gray-100 rounded-2xl mx-auto flex items-center justify-center overflow-hidden border border-gray-200">
                    @if($shop->logo_url)
                        <img src="{{ asset('storage/' . $shop->logo_url) }}" alt="Logo" class="w-full h-full object-cover">
                    @else
                        <i class="fas fa-store text-3xl text-gray-400"></i>
                    @endif
                </div>
                
                <div class="text-center">
                    <h2 class="font-black text-xl text-gray-800">{{ $shop->name }}</h2>
                    <a href="/{{ $shop->slug }}" target="_blank" class="text-sm text-blue-500 hover:underline flex items-center justify-center gap-1 mt-1">
                        appku.site/{{ $shop->slug }} <i class="fas fa-external-link-alt text-[10px]"></i>
                    </a>
                </div>

                <div class="pt-4 border-t border-gray-100 space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Status</span>
                        @if($shop->status === 'active')
                            <span class="font-bold text-green-600">Active</span>
                        @elseif($shop->status === 'trial')
                            <span class="font-bold text-blue-600">Trial</span>
                        @else
                            <span class="font-bold text-red-600">Suspended</span>
                        @endif
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Total Pengguna</span>
                        <span class="font-bold text-gray-800">{{ $shop->users_count }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Total Order</span>
                        <span class="font-bold text-gray-800">{{ number_format($shop->orders_count) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Terdaftar Pada</span>
                        <span class="font-bold text-gray-800">{{ $shop->created_at->format('d M Y') }}</span>
                    </div>
                </div>
            </div>

            <!-- Kontak Info -->
            <div class="bg-white rounded-2xl shadow-sm border border-[#E3E1DC] p-6 space-y-4">
                <h3 class="font-bold text-gray-800 border-b pb-2 border-gray-100">Informasi Kontak</h3>
                
                <div class="space-y-3 text-sm">
                    <div>
                        <span class="text-gray-500 block mb-1">WhatsApp</span>
                        <div class="font-medium text-gray-800">
                            @if($shop->whatsapp_number)
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $shop->whatsapp_number) }}" target="_blank" class="text-green-600 hover:underline">
                                    <i class="fab fa-whatsapp mr-1"></i> {{ $shop->whatsapp_number }}
                                </a>
                            @else
                                -
                            @endif
                        </div>
                    </div>
                    <div>
                        <span class="text-gray-500 block mb-1">Slogan</span>
                        <div class="font-medium text-gray-800">{{ $shop->slogan ?? '-' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Daftar Karyawan -->
        <div class="md:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl shadow-sm border border-[#E3E1DC] overflow-hidden flex flex-col h-full">
                <div class="p-4 border-b border-[#E3E1DC] bg-gray-50 flex items-center justify-between">
                    <h3 class="font-bold text-gray-800"><i class="fas fa-users mr-2"></i> Daftar Pengguna / Karyawan Toko</h3>
                    <a href="{{ route('superadmin.users.create') }}?shop_id={{ $shop->id }}" class="px-3 py-1.5 bg-[#164A35] text-white rounded-lg text-xs font-bold hover:bg-[#113a29] transition">
                        <i class="fas fa-plus mr-1"></i> Tambah Karyawan
                    </a>
                </div>
                <div class="overflow-x-auto flex-1">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-white sticky top-0 shadow-sm text-xs uppercase text-gray-500">
                            <tr>
                                <th class="p-4">Nama & Email</th>
                                <th class="p-4">Role</th>
                                <th class="p-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($users as $user)
                            <tr class="hover:bg-gray-50">
                                <td class="p-4">
                                    <div class="font-bold text-gray-800">{{ $user->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $user->email }}</div>
                                </td>
                                <td class="p-4">
                                    <span class="px-2 py-1 rounded-lg text-[10px] font-black uppercase
                                        {{ $user->role === 'owner' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-700' }}">
                                        {{ $user->role }}
                                    </span>
                                </td>
                                <td class="p-4 text-right">
                                    <a href="{{ route('superadmin.users.edit', $user) }}?redirect_to={{ urlencode(request()->fullUrl()) }}" class="text-blue-500 hover:text-blue-700 p-2"><i class="fas fa-edit"></i></a>
                                    
                                    <form method="POST" action="{{ route('superadmin.users.destroy', $user) }}" class="inline" onsubmit="return confirm('Yakin hapus {{ $user->name }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700 p-2"><i class="fas fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="p-8 text-center text-gray-500">Toko ini belum memiliki pengguna.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Suspend Modal -->
<div id="suspendModal" class="hidden fixed inset-0 bg-black/40 flex items-center justify-center p-4 z-50">
    <div class="bg-white rounded-2xl p-6 max-w-sm w-full space-y-4 shadow-xl">
        <h3 class="font-black text-lg">Suspend Toko?</h3>
        <p class="text-sm text-gray-500">Toko tidak bisa diakses oleh pelanggan maupun staf-nya. Data tetap utuh.</p>
        <form method="POST" action="{{ route('superadmin.shops.suspend', $shop) }}">
            @csrf
            <input type="text" name="reason" required placeholder="Alasan suspend (wajib diisi)"
                   class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg mb-3 focus:outline-none focus:ring-2 focus:ring-amber-500">
            <div class="flex gap-2 justify-end">
                <button type="button" onclick="closeSuspendModal()" class="px-4 py-2 text-sm rounded-lg border hover:bg-gray-50 font-medium">Batal</button>
                <button type="submit" class="px-4 py-2 text-sm rounded-lg bg-amber-500 text-white font-bold hover:bg-amber-600 transition">Suspend</button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Modal -->
<div id="deleteModal" class="hidden fixed inset-0 bg-black/40 flex items-center justify-center p-4 z-50">
    <div class="bg-white rounded-2xl p-6 max-w-sm w-full space-y-4 shadow-xl">
        <h3 class="font-black text-lg text-red-600">Hapus Toko Permanen?</h3>
        <p class="text-sm text-gray-500">Semua order, produk, dan staf akan ikut terhapus dan <b>tidak bisa dikembalikan</b>.</p>
        <form method="POST" action="{{ route('superadmin.shops.destroy', $shop) }}">
            @csrf
            @method('DELETE')
            <input type="text" name="confirm_slug" required placeholder='Ketik "{{ $shop->slug }}" untuk konfirmasi'
                   class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg mb-3 focus:outline-none focus:ring-2 focus:ring-red-500">
            <div class="flex gap-2 justify-end">
                <button type="button" onclick="closeDeleteModal()" class="px-4 py-2 text-sm rounded-lg border hover:bg-gray-50 font-medium">Batal</button>
                <button type="submit" class="px-4 py-2 text-sm rounded-lg bg-red-600 text-white font-bold hover:bg-red-700 transition">Hapus Permanen</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function openSuspendModal() { document.getElementById('suspendModal').classList.remove('hidden'); }
    function closeSuspendModal() { document.getElementById('suspendModal').classList.add('hidden'); }
    function openDeleteModal() { document.getElementById('deleteModal').classList.remove('hidden'); }
    function closeDeleteModal() { document.getElementById('deleteModal').classList.add('hidden'); }
</script>
@endpush
