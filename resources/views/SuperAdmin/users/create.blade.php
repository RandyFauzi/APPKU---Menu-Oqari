@extends('SuperAdmin.layouts.app')

@section('title', 'Tambah Pengguna Baru - Oqari Super Admin')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <!-- Header Navigation -->
    <div class="flex items-center gap-4">
        <a href="{{ url()->previous() }}" class="w-10 h-10 bg-white border border-[#E3E1DC] rounded-xl flex items-center justify-center text-gray-500 hover:bg-gray-50 transition">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-2xl font-black text-gray-800">Tambah Pengguna Baru</h1>
            <p class="text-sm text-gray-500">Buat akun untuk super admin, pemilik toko, atau staf</p>
        </div>
    </div>

    <!-- Create Form -->
    <div class="bg-white rounded-2xl shadow-sm border border-[#E3E1DC] overflow-hidden">
        <form action="{{ route('superadmin.users.store') }}" method="POST" class="p-6 space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Nama Lengkap <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                           class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#164A35]">
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Email <span class="text-red-500">*</span></label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                           class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#164A35]">
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Password <span class="text-red-500">*</span></label>
                    <input type="password" name="password" required minlength="8"
                           class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#164A35]">
                    <p class="text-xs text-gray-500 mt-1">Minimal 8 karakter.</p>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Role <span class="text-red-500">*</span></label>
                    <select name="role" required class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#164A35]">
                        <option value="">-- Pilih Role --</option>
                        <option value="superadmin" @selected(old('role') === 'superadmin')>Super Admin (Platform)</option>
                        <option value="owner" @selected(old('role') === 'owner')>Owner (Pemilik Toko)</option>
                        <option value="admin" @selected(old('role') === 'admin')>Admin (Manajer Toko)</option>
                        <option value="crew" @selected(old('role') === 'crew')>Crew / Pelayan</option>
                        <option value="kitchen" @selected(old('role') === 'kitchen')>Kitchen / Dapur</option>
                        <option value="cashier" @selected(old('role') === 'cashier')>Cashier / Kasir</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Pilih Toko Terkait</label>
                <select name="shop_id" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#164A35]">
                    <option value="">-- Tidak ada (Khusus Super Admin) --</option>
                    @foreach($shops as $shopOption)
                        <option value="{{ $shopOption->id }}" @selected(old('shop_id', request('shop_id')) == $shopOption->id)>
                            {{ $shopOption->name }}
                        </option>
                    @endforeach
                </select>
                <p class="text-xs text-gray-500 mt-1">Biarkan kosong jika role adalah Super Admin.</p>
            </div>

            <div class="pt-4 border-t border-gray-100 flex justify-end">
                <button type="submit" class="px-6 py-2 bg-[#164A35] text-white font-bold rounded-lg hover:bg-[#113a29] transition">
                    Buat Pengguna
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
