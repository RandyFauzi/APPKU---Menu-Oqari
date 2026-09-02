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

    <div class="bg-white rounded-[20px] shadow-sm border border-[#E3E1DC] overflow-hidden">
        <div class="p-5 border-b border-[#E3E1DC] bg-white flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <h3 class="font-bold text-gray-800 text-lg flex items-center gap-2">
                <div class="w-8 h-8 rounded-full bg-[#F8F7F3] text-[#164A35] flex items-center justify-center text-sm">
                    <i class="fas fa-users"></i>
                </div>
                Daftar Pengguna
            </h3>
            
            <form method="GET" class="flex gap-2">
                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input type="text" name="user_search" value="{{ request('user_search') }}" placeholder="Cari nama / email..."
                           class="pl-9 pr-4 py-2.5 text-sm border border-[#E3E1DC] bg-[#F8F7F3] rounded-xl focus:outline-none focus:border-[#164A35] focus:ring-1 focus:ring-[#164A35] w-full sm:w-64 transition-all">
                </div>
                <button type="submit" class="px-4 py-2.5 text-sm bg-[#164A35] text-white rounded-xl font-bold hover:bg-[#113a29] transition shadow-sm">Cari</button>
            </form>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-[#F8F7F3] text-xs uppercase text-gray-500 font-bold border-b border-[#E3E1DC]">
                    <tr>
                        <th class="py-4 px-5">Profil</th>
                        <th class="py-4 px-5">Role</th>
                        <th class="py-4 px-5">Toko Terkait</th>
                        <th class="py-4 px-5">Terdaftar</th>
                        <th class="py-4 px-5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E3E1DC]/50">
                    @forelse($users as $user)
                    <tr class="hover:bg-[#F8F7F3]/50 transition duration-150">
                        <td class="py-4 px-5">
                            <div class="flex items-center gap-3">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=E7F2EB&color=164A35" class="w-10 h-10 rounded-full object-cover border border-[#E3E1DC]">
                                <div>
                                    <div class="font-bold text-gray-800">{{ $user->name }}</div>
                                    <div class="text-xs text-gray-500 mt-0.5">{{ $user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-5">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-bold uppercase tracking-wider
                                {{ $user->role === 'superadmin' ? 'bg-purple-100 text-purple-700' : ($user->role === 'owner' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-700') }}">
                                {{ $user->role }}
                            </span>
                        </td>
                        <td class="py-4 px-5">
                            @if($user->shop)
                                <a href="{{ route('superadmin.shops.show', $user->shop) }}" class="font-semibold text-[#164A35] hover:text-[#113a29] hover:underline flex items-center gap-1.5">
                                    <i class="fas fa-store text-xs opacity-70"></i> {{ $user->shop->name }}
                                </a>
                            @else
                                <span class="text-gray-400 italic text-sm">-</span>
                            @endif
                        </td>
                        <td class="py-4 px-5 text-gray-500 text-xs font-medium">{{ $user->created_at->format('d M Y') }}</td>
                        <td class="py-4 px-5 text-right whitespace-nowrap">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('superadmin.users.edit', $user) }}" class="p-2 text-blue-600 bg-blue-50 hover:bg-blue-100 rounded-lg transition" title="Edit">
                                    <i class="fas fa-pen text-sm"></i>
                                </a>
                                @if($user->role !== 'superadmin' || App\Models\User::where('role', 'superadmin')->count() > 1)
                                    <form method="POST" action="{{ route('superadmin.users.destroy', $user) }}" class="inline" onsubmit="return confirm('Yakin hapus akun {{ $user->name }} permanen?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-red-600 bg-red-50 hover:bg-red-100 rounded-lg transition" title="Hapus">
                                            <i class="fas fa-trash-alt text-sm"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-12 px-5 text-center">
                            <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-3">
                                <i class="fas fa-users-slash text-2xl text-gray-400"></i>
                            </div>
                            <p class="text-gray-500 font-medium">Belum ada pengguna yang terdaftar.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="p-4 border-t border-[#E3E1DC] bg-white">
            {{ $users->links() }}
        </div>
    </div>
</div>
@endsection
