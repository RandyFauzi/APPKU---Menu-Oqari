@extends('SuperAdmin.layouts.app')

@section('title', 'Edit Toko - Oqari Super Admin')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <!-- Header Navigation -->
    <div class="flex items-center gap-4">
        <a href="{{ route('superadmin.shops.show', $shop) }}" class="w-10 h-10 bg-white border border-[#E3E1DC] rounded-xl flex items-center justify-center text-gray-500 hover:bg-gray-50 transition">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-2xl font-black text-gray-800">Edit Profil Toko</h1>
            <p class="text-sm text-gray-500">{{ $shop->name }}</p>
        </div>
    </div>

    <!-- Edit Form -->
    <div class="bg-white rounded-2xl shadow-sm border border-[#E3E1DC] overflow-hidden">
        <form action="{{ route('superadmin.shops.update', $shop) }}" method="POST" class="p-6 space-y-6">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Nama Toko <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name', $shop->name) }}" required
                       class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#164A35]">
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Slug URL <span class="text-red-500">*</span></label>
                <div class="flex">
                    <span class="inline-flex items-center px-4 rounded-l-lg border border-r-0 border-gray-200 bg-gray-50 text-gray-500 text-sm">
                        appku.site/
                    </span>
                    <input type="text" name="slug" value="{{ old('slug', $shop->slug) }}" required
                           class="flex-1 px-4 py-2 border border-gray-200 rounded-r-lg focus:outline-none focus:ring-2 focus:ring-[#164A35]">
                </div>
                <p class="text-xs text-gray-500 mt-1">Gunakan huruf kecil, angka, dan strip (-). Contoh: mada-coffee</p>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Nomor WhatsApp</label>
                <input type="text" name="whatsapp_number" value="{{ old('whatsapp_number', $shop->whatsapp_number) }}"
                       class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#164A35]">
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Slogan</label>
                <input type="text" name="slogan" value="{{ old('slogan', $shop->slogan) }}"
                       class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#164A35]">
            </div>

            <div class="pt-4 border-t border-gray-100 flex justify-end">
                <button type="submit" class="px-6 py-2 bg-[#164A35] text-white font-bold rounded-lg hover:bg-[#113a29] transition">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
