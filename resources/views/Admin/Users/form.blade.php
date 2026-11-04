@extends('Admin.Users.layout')
@section('content')
@php
    $selectedRole = old('role', $target?->role ?? 'crew');
    $mode = old('permission_mode', $target && $target->permissions !== null ? 'custom' : 'role');
    $selectedPermissions = old('permissions', $target?->permissions ?? $target?->effectivePermissions() ?? config('access.defaults.crew'));
@endphp
<div class="mx-auto max-w-5xl">
    <a href="{{ route('admin.users.index') }}" class="mb-4 inline-flex min-h-[44px] items-center gap-2 text-xs font-medium text-[#7f837e] transition-colors hover:text-[#ea580c]"><i class="fas fa-arrow-left text-[11px]" aria-hidden="true"></i>Kembali ke daftar staf</a>
    <div class="mb-6">
        <h1 class="text-[25px] font-semibold leading-tight tracking-tight text-[#111827]">{{ $target ? 'Edit Staf' : 'Tambah Staf' }}</h1>
        <p class="mt-1.5 text-[13px] text-[#7f837e]">{{ $target ? 'Perbarui informasi akun dan hak akses anggota tim.' : 'Lengkapi informasi akun untuk menambahkan anggota tim baru.' }}</p>
    </div>

    <form method="POST" action="{{ $target ? route('admin.users.update', $target->id) : route('admin.users.store') }}" class="space-y-5" x-data="{ mode: {{ Illuminate\Support\Js::from($mode) }}, selectedRole: {{ Illuminate\Support\Js::from($selectedRole) }} }">
        @csrf
        @if($target) @method('PUT') @endif
        <section aria-labelledby="staff-identity-title" class="overflow-hidden rounded-xl border border-[#e9ebf0] bg-white shadow-sm">
            <div class="flex items-center gap-3 border-b border-[#e9ebf0] px-5 py-4 md:px-6">
                <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-[#fff6f0] text-[#ea580c]"><i class="far fa-user text-sm" aria-hidden="true"></i></span>
                <div><h2 id="staff-identity-title" class="text-sm font-semibold">Informasi Staf</h2><p class="mt-1 text-xs text-[#7f837e]">Identitas dan kredensial untuk masuk ke akun.</p></div>
            </div>
            <div class="grid gap-5 p-5 md:grid-cols-2 md:p-6">
                <div>
                    <label for="name" class="mb-2 block text-xs font-medium text-[#53565a]">Nama lengkap <span class="text-[#ea580c]">*</span></label>
                    <input id="name" name="name" value="{{ old('name', $target?->name) }}" placeholder="Nama anggota tim" required maxlength="255" autocomplete="name" class="h-11 w-full rounded-lg border-[#e9ebf0] text-[13px] placeholder:text-[#9ca3af] focus:border-orange-500 focus:ring-orange-500">
                    @error('name')<p class="mt-1.5 text-xs text-red-700">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="email" class="mb-2 block text-xs font-medium text-[#53565a]">Email login <span class="text-[#ea580c]">*</span></label>
                    <input id="email" name="email" type="email" value="{{ old('email', $target?->email) }}" placeholder="nama@email.com" required maxlength="255" autocomplete="off" class="h-11 w-full rounded-lg border-[#e9ebf0] text-[13px] placeholder:text-[#9ca3af] focus:border-orange-500 focus:ring-orange-500">
                    @error('email')<p class="mt-1.5 text-xs text-red-700">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="password" class="mb-2 block text-xs font-medium text-[#53565a]">Password @if(!$target)<span class="text-[#ea580c]">*</span>@else<span class="font-normal text-[#9ca3af]">(opsional)</span>@endif</label>
                    <input id="password" type="password" name="password" placeholder="{{ $target ? 'Kosongkan jika tidak diubah' : 'Buat password akun' }}" autocomplete="new-password" minlength="12" maxlength="255" @required(!$target) class="h-11 w-full rounded-lg border-[#e9ebf0] text-[13px] placeholder:text-[#9ca3af] focus:border-orange-500 focus:ring-orange-500" aria-describedby="password-help">
                    <p id="password-help" class="mt-2 text-[11px] leading-relaxed text-[#7f837e]">Minimal 12 karakter, dengan huruf besar, huruf kecil, dan angka.</p>
                    @error('password')<p class="mt-1.5 text-xs text-red-700">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="password_confirmation" class="mb-2 block text-xs font-medium text-[#53565a]">Konfirmasi password @if(!$target)<span class="text-[#ea580c]">*</span>@endif</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" placeholder="Ulangi password" autocomplete="new-password" maxlength="255" @required(!$target) class="h-11 w-full rounded-lg border-[#e9ebf0] text-[13px] placeholder:text-[#9ca3af] focus:border-orange-500 focus:ring-orange-500">
                </div>
            </div>
        </section>

        <section aria-labelledby="staff-access-title" class="overflow-hidden rounded-xl border border-[#e9ebf0] bg-white shadow-sm">
            <div class="flex items-center gap-3 border-b border-[#e9ebf0] px-5 py-4 md:px-6">
                <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-[#fff6f0] text-[#ea580c]"><i class="fas fa-shield-halved text-sm" aria-hidden="true"></i></span>
                <div><h2 id="staff-access-title" class="text-sm font-semibold">Role &amp; Hak Akses</h2><p class="mt-1 text-xs text-[#7f837e]">Tentukan peran dan status akun staf.</p></div>
            </div>
            <div class="space-y-5 p-5 md:p-6">
                <div class="grid gap-5 md:grid-cols-2">
                    <div>
                        <label for="role" class="mb-2 block text-xs font-medium text-[#53565a]">Role <span class="text-[#ea580c]">*</span></label>
                        <select id="role" name="role" x-model="selectedRole" required class="h-11 w-full rounded-lg border-[#e9ebf0] text-[13px] focus:border-orange-500 focus:ring-orange-500">
                            @foreach($roles as $key => $label)<option value="{{ $key }}" @selected($selectedRole === $key)>{{ $label }}</option>@endforeach
                        </select>
                        @error('role')<p class="mt-1.5 text-xs text-red-700">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="is_active" class="mb-2 block text-xs font-medium text-[#53565a]">Status akun <span class="text-[#ea580c]">*</span></label>
                        <select id="is_active" name="is_active" class="h-11 w-full rounded-lg border-[#e9ebf0] text-[13px] focus:border-orange-500 focus:ring-orange-500">
                            <option value="1" @selected((string) old('is_active', $target?->is_active ?? true) === '1')>Aktif</option>
                            <option value="0" @selected((string) old('is_active', $target?->is_active ?? true) === '0' || old('is_active', $target?->is_active ?? true) === false)>Nonaktif</option>
                        </select>
                        @error('is_active')<p class="mt-1.5 text-xs text-red-700">{{ $message }}</p>@enderror
                    </div>
                </div>

                <fieldset class="flex flex-col gap-2 sm:flex-row sm:gap-5">
                    <legend class="sr-only">Metode hak akses</legend>
                    <label class="inline-flex min-h-[44px] cursor-pointer items-center gap-2 text-[13px] text-[#53565a]"><input class="text-orange-500 focus:ring-orange-500" type="radio" name="permission_mode" value="role" x-model="mode" @checked($mode === 'role')>Gunakan preset role</label>
                    <label class="inline-flex min-h-[44px] cursor-pointer items-center gap-2 text-[13px] text-[#53565a]"><input class="text-orange-500 focus:ring-orange-500" type="radio" name="permission_mode" value="custom" x-model="mode" @checked($mode === 'custom')>Atur izin khusus</label>
                </fieldset>

                <details class="group rounded-lg bg-[#f8f9fa] px-3.5">
                    <summary class="flex min-h-[44px] cursor-pointer list-none items-center gap-2 text-xs font-medium text-[#7f837e] transition-colors hover:text-[#ea580c] [&::-webkit-details-marker]:hidden">
                        <i class="far fa-circle-question" aria-hidden="true"></i>Panduan role &amp; hak akses<i class="fas fa-chevron-down ml-auto text-[9px] transition-transform group-open:rotate-180" aria-hidden="true"></i>
                    </summary>
                    <div class="space-y-3 border-t border-[#e9ebf0] py-3 text-xs leading-relaxed text-[#7f837e]">
                        @foreach($roles as $key => $label)
                            <div x-show="selectedRole === '{{ $key }}'" @if($selectedRole !== $key) x-cloak @endif>
                                <p class="font-semibold text-[#53565a]">{{ $label }}</p>
                                <p class="mt-1">{{ config('access.role_descriptions.'.$key) }}</p>
                                <div x-show="mode === 'role'" class="mt-2"><p class="font-medium text-[#53565a]">Izin preset</p><p class="mt-1">{{ implode(', ', array_map(fn($ability) => config('access.permissions.'.$ability.'.label'), (new App\Models\User(['role' => $key, 'shop_id' => auth()->user()->shop_id]))->effectivePermissions())) }}</p></div>
                            </div>
                        @endforeach
                        <p>Preset mengikuti role yang dipilih. Izin khusus menggantikan preset sepenuhnya; tanpa pilihan berarti tidak ada akses modul.</p>
                        <p>Izin kelola staf hanya dapat diberikan Owner kepada Admin Cafe atau Manager. Pengelola yang didelegasikan hanya dapat mengatur akun staf sesuai kewenangannya.</p>
                        <p>Owner dan akun sendiri dilindungi. Tutup shift kasir dan selesaikan absensi sebelum mengubah akses atau password. Perubahan akses meminta staf login kembali.</p>
                    </div>
                </details>

                <fieldset x-show="mode === 'custom'" x-cloak :disabled="mode !== 'custom'" class="space-y-5 border-t border-[#e9ebf0] pt-5">
                    <legend class="sr-only">Daftar izin khusus</legend>
                    @foreach($groups as $group => $permissions)
                        <div>
                            <h3 class="mb-3 text-xs font-semibold text-[#53565a]">{{ $group }}</h3>
                            <div class="grid gap-2.5 md:grid-cols-2">
                                @foreach($permissions as $key => $permission)
                                    <label class="flex cursor-pointer items-start gap-3 rounded-lg border border-[#e9ebf0] p-3 transition-colors hover:bg-[#fff6f0]">
                                        <input class="mt-0.5 rounded border-gray-300 text-orange-500 focus:ring-orange-500" type="checkbox" name="permissions[]" value="{{ $key }}" @checked(in_array($key, $selectedPermissions ?? [], true))>
                                        <span class="text-xs leading-relaxed text-[#53565a]">{{ $permission['label'] }}@if(config('access.dependencies.'.$key))<span class="mt-1 block text-[11px] text-[#7f837e]">Membutuhkan: {{ implode(', ', array_map(fn($ability) => config('access.permissions.'.$ability.'.label'), config('access.dependencies.'.$key))) }}</span>@endif</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </fieldset>
                @error('permissions')<p class="text-xs text-red-700">{{ $message }}</p>@enderror
                @error('permissions.*')<p class="text-xs text-red-700">{{ $message }}</p>@enderror
            </div>
        </section>

        <div class="flex flex-col-reverse gap-3 border-t border-[#e9ebf0] pt-5 sm:flex-row sm:justify-end">
            <a href="{{ route('admin.users.index') }}" class="inline-flex min-h-[44px] items-center justify-center rounded-lg border border-[#e9ebf0] bg-white px-5 text-xs font-medium text-[#53565a] transition-colors hover:bg-[#f8f9fa]">Batal</a>
            <button type="submit" class="inline-flex min-h-[44px] items-center justify-center gap-2 rounded-lg bg-gradient-to-r from-[#f9731f] to-[#ea580c] px-5 text-xs font-semibold text-white shadow-sm transition-colors hover:from-[#ea580c] hover:to-[#c2410c]"><i class="fas fa-check text-[11px]" aria-hidden="true"></i>{{ $target ? 'Simpan Perubahan' : 'Tambah Staf' }}</button>
        </div>
    </form>
</div>
@endsection
