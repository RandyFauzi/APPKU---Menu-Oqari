@extends('SuperAdmin.layouts.app')

@section('title', 'Manajemen Pengguna - Oqari Super Admin')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-black text-gray-800">Manajemen Pengguna</h1>
        <a href="{{ route('superadmin.users.create') }}" class="px-4 py-2 bg-[#164A35] text-white font-bold rounded-xl hover:bg-[#113a29] transition flex items-center gap-2">
            <i class="fas fa-plus"></i> Tambah Pengguna
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-[#E3E1DC] overflow-hidden flex flex-col">
        <div class="p-4 border-b border-[#E3E1DC] bg-gray-50 flex items-center justify-between">
            <h3 class="font-bold text-gray-800"><i class="fas fa-users mr-2"></i> Daftar semua pengguna</h3>
            
            <form method="GET" class="flex gap-2">
                <input type="text" name="user_search" value="{{ request('user_search') }}" placeholder="Cari nama / email..."
                       class="px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#164A35]">
                <button class="px-3 py-2 text-sm bg-[#164A35] text-white rounded-lg font-bold hover:bg-[#113a29] transition">Cari</button>
            </form>
        </div>
        <div class="overflow-x-auto flex-1">
            <table class="w-full text-left text-sm relative">
                <thead class="bg-white sticky top-0 shadow-sm text-xs uppercase text-gray-500 z-10">
                    <tr>
                        <th class="p-4">Nama & Email</th>
                        <th class="p-4">Role</th>
                        <th class="p-4">Toko Terkait</th>
                        <th class="p-4">Terdaftar</th>
                        <th class="p-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($users as $user)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="p-4">
                            <div class="font-bold text-gray-800">{{ $user->name }}</div>
                            <div class="text-xs text-gray-500 mt-1">{{ $user->email }}</div>
                        </td>
                        <td class="p-4">
                            <span class="px-2 py-1 rounded-lg text-[10px] font-black uppercase
                                {{ $user->role === 'superadmin' ? 'bg-purple-100 text-purple-700' : ($user->role === 'owner' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-700') }}">
                                {{ $user->role }}
                            </span>
                        </td>
                        <td class="p-4">
                            @if($user->shop)
                                <a href="{{ route('superadmin.shops.show', $user->shop) }}" class="font-bold text-[#164A35] hover:underline">{{ $user->shop->name }}</a>
                            @else
                                <span class="text-gray-400 italic">-</span>
                            @endif
                        </td>
                        <td class="p-4 text-gray-500 text-xs">{{ $user->created_at->format('d M Y') }}</td>
                        <td class="p-4 text-right whitespace-nowrap space-x-1">
                            <a href="{{ route('superadmin.users.edit', $user) }}" class="inline-block px-3 py-1.5 bg-blue-50 text-blue-600 hover:bg-blue-100 rounded-lg font-medium transition text-xs">
                                <i class="fas fa-edit mr-1"></i> Edit
                            </a>
                            @if($user->role !== 'superadmin' || App\Models\User::where('role', 'superadmin')->count() > 1)
                                <form method="POST" action="{{ route('superadmin.users.destroy', $user) }}" class="inline" onsubmit="return confirm('Yakin hapus akun {{ $user->name }} permanen?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-3 py-1.5 bg-red-50 text-red-600 hover:bg-red-100 rounded-lg font-medium transition text-xs">
                                        <i class="fas fa-trash mr-1"></i> Hapus
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="p-8 text-center text-gray-500">Tidak ada pengguna ditemukan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-[#E3E1DC]">
            {{ $users->links() }}
        </div>
    </div>
</div>
@endsection
