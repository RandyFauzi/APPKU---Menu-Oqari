@extends('SuperAdmin.layouts.app')

@section('title', 'Manajemen Toko - Oqari Super Admin')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-black text-gray-800">Manajemen Toko</h1>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-[#E3E1DC] overflow-hidden flex flex-col">
        <div class="p-4 border-b border-[#E3E1DC] bg-gray-50 flex flex-col sm:flex-row justify-between gap-4 sm:items-center">
            <h3 class="font-bold text-gray-800"><i class="fas fa-store mr-2"></i> Daftar toko terdaftar</h3>
            
            <form method="GET" class="flex flex-wrap gap-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama / slug..."
                       class="flex-1 min-w-[140px] px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#164A35]">
                <select name="status" class="px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#164A35]">
                    <option value="">Semua status</option>
                    <option value="active" @selected(request('status') === 'active')>Active</option>
                    <option value="trial" @selected(request('status') === 'trial')>Trial</option>
                    <option value="suspended" @selected(request('status') === 'suspended')>Suspended</option>
                </select>
                <button class="px-4 py-2 text-sm bg-[#164A35] text-white rounded-lg font-bold hover:bg-[#113a29] transition">Filter</button>
            </form>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm relative">
                <thead class="bg-white sticky top-0 shadow-sm text-xs uppercase text-gray-500 z-10">
                    <tr>
                        <th class="p-4">Toko</th>
                        <th class="p-4">Status</th>
                        <th class="p-4">Karyawan</th>
                        <th class="p-4">Total Order</th>
                        <th class="p-4">Terdaftar</th>
                        <th class="p-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($shops as $shop)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="p-4">
                            <div class="font-bold text-base text-gray-800">{{ $shop->name }}</div>
                            <a href="/{{ $shop->slug }}" target="_blank" class="text-blue-500 hover:underline text-xs flex items-center gap-1 mt-1">
                                {{ $shop->slug }} <i class="fas fa-external-link-alt text-[10px]"></i>
                            </a>
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
                        <td class="p-4 text-gray-600 font-medium">{{ number_format($shop->orders_count) }}</td>
                        <td class="p-4 text-gray-500 text-xs">{{ $shop->created_at->format('d M Y') }}</td>
                        <td class="p-4 text-right space-x-1 whitespace-nowrap">
                            <a href="{{ route('superadmin.shops.show', $shop) }}" class="inline-block px-3 py-1.5 bg-blue-50 text-blue-600 hover:bg-blue-100 rounded-lg font-medium transition text-xs">
                                <i class="fas fa-eye mr-1"></i> Detail
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="p-8 text-center text-gray-500">Tidak ada toko yang cocok dengan pencarian Anda</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-[#E3E1DC]">
            {{ $shops->links() }}
        </div>
    </div>
</div>
@endsection
