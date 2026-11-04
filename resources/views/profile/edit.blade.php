<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Profil Saya — {{ $user->shop?->name ?? 'OQARI' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <x-theme-head :shop="$user->shop" />
</head>
<body class="min-h-screen bg-[#f8f9fa] font-['DM_Sans',sans-serif] text-[#111827] antialiased">
<div class="cafe-page-shell">
    <x-cafe-sidebar />
    <div class="cafe-page-content bg-[#f8f9fa]">
        <header class="flex h-16 items-center gap-3 border-b border-[#e9ebf0] bg-white px-4 md:px-7">
            <button type="button" x-data @click="$dispatch('toggle-mobile-nav')" class="flex h-11 w-11 items-center justify-center rounded-lg text-[#7f837e] hover:bg-gray-50 md:hidden" aria-label="Buka navigasi"><i class="fas fa-bars" aria-hidden="true"></i></button>
            <span class="text-[13px] font-semibold">Profil Saya</span>
        </header>
        <main class="p-4 md:p-6 lg:p-7">
            <div class="mx-auto max-w-6xl">
                <div class="mb-6"><h1 class="text-[25px] font-semibold leading-tight tracking-tight">Profil Saya</h1><p class="mt-1.5 text-[13px] text-[#7f837e]">Kelola informasi pribadi dan keamanan akun Anda.</p></div>
                <div class="grid items-start gap-5 lg:grid-cols-[260px_minmax(0,1fr)]">
                    <aside aria-label="Ringkasan akun" class="overflow-hidden rounded-xl border border-[#e9ebf0] bg-white shadow-sm">
                        <div class="border-b border-[#e9ebf0] p-5">
                            <span class="mb-4 inline-flex h-14 w-14 items-center justify-center rounded-2xl bg-[#fff6f0] text-lg font-semibold text-[#c94d00]" aria-hidden="true">{{ mb_strtoupper(mb_substr($user->name, 0, 2)) }}</span>
                            <h2 class="break-words text-base font-semibold">{{ $user->name }}</h2>
                            <p class="mt-1 break-all text-xs leading-relaxed text-[#7f837e]">{{ $user->email }}</p>
                            <span class="mt-3 inline-flex rounded-md border border-[#ffe0cb] bg-[#fff6f0] px-2.5 py-1 text-[11px] font-medium text-[#c94d00]">{{ $user->role === 'owner' ? 'Owner' : ($user->role === 'superadmin' ? 'Super Admin' : config('access.roles.'.$user->role, $user->role)) }}</span>
                        </div>
                        <div class="space-y-4 p-5 text-xs">
                            <div><p class="text-[#9ca3af]">Kafe</p><p class="mt-1.5 break-words font-medium text-[#53565a]">{{ $user->shop?->name ?? 'Administrasi platform' }}</p></div>
                            <div><p class="text-[#9ca3af]">Pengaturan akun</p><p class="mt-1.5 leading-relaxed text-[#7f837e]">Perubahan profil hanya berlaku untuk akun Anda. Hak akses dikelola melalui Manajemen Staf.</p></div>
                        </div>
                    </aside>
                    <div class="min-w-0 space-y-5">
                        @include('profile.partials.update-profile-information-form')
                        @include('profile.partials.update-password-form')
                        @include('profile.partials.delete-user-form')
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
</body>
</html>
