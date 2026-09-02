@extends('SuperAdmin.layouts.app')

@section('title', 'Dashboard - Oqari Super Admin')

@section('content')
<div class="space-y-6">
    <div class="flex items-center gap-2 mb-6">
        <h1 class="text-2xl font-black text-gray-800">Dashboard</h1>
    </div>

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

    <!-- Activity Log -->
    <div class="bg-white rounded-2xl shadow-sm border border-[#E3E1DC] overflow-hidden">
        <div class="p-4 border-b border-[#E3E1DC] bg-gray-50 flex items-center justify-between">
            <h3 class="font-bold text-gray-800"><i class="fas fa-clock-rotate-left mr-2"></i> Aktivitas Super Admin Terbaru</h3>
        </div>
        <div class="divide-y divide-gray-100">
            @forelse($recentActivity as $log)
            <div class="p-4 text-sm flex justify-between">
                <span>
                    <span class="font-bold">{{ $log->actor->name ?? 'System' }}</span> 
                    &middot; {{ str_replace('.', ' ', $log->action) }} 
                    &middot; <span class="text-gray-500">{{ $log->target_label }}</span>
                </span>
                <span class="text-gray-400 text-xs">{{ $log->created_at->diffForHumans() }}</span>
            </div>
            @empty
            <div class="p-4 text-center text-gray-500 text-sm">Belum ada aktivitas tercatat</div>
            @endforelse
        </div>
    </div>
</div>
@endsection
