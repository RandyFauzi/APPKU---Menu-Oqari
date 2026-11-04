@extends('Admin.Users.layout')
@section('content')
<div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between mb-6">
    <div>
        <h1 class="text-[25px] font-semibold leading-tight tracking-tight">Manajemen Staf</h1>
        <p class="mt-1.5 text-[13px] text-[#7f837e]">Kelola anggota tim, akun, dan hak akses kafe Anda.</p>
    </div>
    <a href="{{ route('admin.users.create') }}" class="inline-flex min-h-[44px] items-center justify-center gap-2 rounded-lg bg-gradient-to-r from-[#f9731f] to-[#ea580c] px-4 text-xs font-semibold text-white shadow-sm transition-colors hover:from-[#ea580c] hover:to-[#c2410c] sm:self-center">
        <i class="fas fa-plus" aria-hidden="true"></i>Tambah Staf
    </a>
</div>

<section aria-labelledby="staff-list-title" class="overflow-hidden rounded-xl border border-[#e9ebf0] bg-white shadow-sm">
    <div class="flex flex-col gap-4 border-b border-[#e9ebf0] px-5 py-5 lg:flex-row lg:items-center lg:justify-between lg:px-6">
        <div class="flex shrink-0 items-center gap-2.5">
            <h2 id="staff-list-title" class="text-sm font-semibold">Anggota tim</h2>
            <span class="rounded-md bg-[#f3f4f6] px-2 py-1 text-[11px] font-medium text-[#53565a]">{{ $users->total() }} akun</span>
        </div>
        <form method="GET" action="{{ route('admin.users.index') }}" class="grid grid-cols-2 items-center gap-2.5 sm:flex sm:flex-wrap">
            <div class="relative col-span-2 sm:flex-1">
                <label for="q" class="sr-only">Cari staf berdasarkan nama atau email</label>
                <i class="fas fa-search pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-xs text-[#9ca3af]" aria-hidden="true"></i>
                <input id="q" type="search" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Cari nama atau email..." maxlength="100" class="h-11 w-full rounded-lg border-[#e9ebf0] bg-white pl-10 pr-3 text-xs focus:border-orange-500 focus:ring-orange-500 sm:min-w-[230px] lg:w-64">
            </div>
            <div>
                <label for="status" class="sr-only">Status akun</label>
                <select id="status" name="status" class="h-11 w-full rounded-lg border-[#e9ebf0] text-xs text-[#53565a] focus:border-orange-500 focus:ring-orange-500 sm:w-36">
                    <option value="">Semua status</option>
                    <option value="active" @selected(($filters['status'] ?? '') === 'active')>Aktif</option>
                    <option value="inactive" @selected(($filters['status'] ?? '') === 'inactive')>Nonaktif</option>
                </select>
            </div>
            <button type="submit" class="inline-flex h-11 items-center justify-center gap-2 rounded-lg border border-[#e9ebf0] bg-[#f8f9fa] px-4 text-xs font-semibold text-[#53565a] transition-colors hover:bg-gray-100">Terapkan</button>
            <a href="{{ route('admin.users.index') }}" class="inline-flex h-11 items-center justify-center px-2 text-xs font-medium text-[#7f837e] hover:text-[#ea580c]">Reset</a>
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full min-w-[860px] table-fixed text-left text-[13px]">
            <caption class="sr-only">Daftar staf kafe {{ auth()->user()->shop->name }}</caption>
            <colgroup><col><col class="w-[22%]"><col class="w-[120px]"><col class="w-[244px]"></colgroup>
            <thead class="border-b border-[#e9ebf0] bg-[#f8f9fa] text-[10px] font-semibold uppercase tracking-wider text-[#7f837e]">
                <tr>
                    <th scope="col" class="px-6 py-3.5">Anggota tim</th>
                    <th scope="col" class="px-6 py-3.5">Role &amp; Izin</th>
                    <th scope="col" class="px-4 py-3.5">Status</th>
                    <th scope="col" class="px-6 py-3.5 text-right">Tindakan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#e9ebf0]">
                @forelse($users as $member)
                    <tr class="transition-colors hover:bg-[#fafbfc]">
                        <td class="px-6 py-5 align-middle">
                            <div class="flex min-w-0 items-center gap-3">
                                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl text-xs font-semibold {{ $member->role === 'owner' ? 'bg-[#fff6f0] text-[#c94d00]' : 'bg-[#f3f4f6] text-[#53565a]' }}" aria-hidden="true">{{ mb_strtoupper(mb_substr($member->name, 0, 2)) }}</span>
                                <div class="min-w-0">
                                    <div class="flex min-w-0 items-center gap-2">
                                        <p class="truncate font-semibold text-[#111827]" title="{{ $member->name }}">{{ $member->name }}</p>
                                        @if($member->id === auth()->id())<span class="shrink-0 rounded border border-[#e9ebf0] px-1.5 py-0.5 text-[10px] font-medium text-[#7f837e]">Anda</span>@endif
                                    </div>
                                    <p class="mt-1 truncate text-xs text-[#7f837e]" title="{{ $member->email }}">{{ $member->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-5 align-middle">
                            <span class="inline-flex rounded-md border px-2 py-1 text-[11px] font-medium {{ $member->role === 'owner' ? 'border-[#ffe0cb] bg-[#fff6f0] text-[#c94d00]' : 'border-[#e9ebf0] bg-white text-[#53565a]' }}">{{ $member->role === 'owner' ? 'Owner' : config('access.roles.'.$member->role, $member->role) }}</span>
                            <p class="mt-1.5 text-[11px] text-[#7f837e]">{{ $member->role === 'owner' ? 'Seluruh izin kafe' : ($member->permissions === null ? 'Preset role' : count($member->permissions).' izin khusus') }}</p>
                        </td>
                        <td class="px-4 py-5 align-middle">
                            <span class="inline-flex items-center gap-1.5 whitespace-nowrap rounded-full px-2.5 py-1 text-[11px] font-medium {{ $member->is_active ? 'bg-[#eafbf2] text-[#00643b]' : 'bg-[#f3f4f6] text-[#6b7280]' }}"><span class="h-1.5 w-1.5 rounded-full bg-current" aria-hidden="true"></span>{{ $member->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                        </td>
                        <td class="px-6 py-5 align-middle">
                            @if($policy->update(auth()->user(), $member))
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('admin.users.edit', $member->id) }}" class="inline-flex h-11 items-center justify-center gap-2 whitespace-nowrap rounded-lg border border-[#e9ebf0] bg-white px-3 text-xs font-medium text-[#53565a] transition-colors hover:border-orange-300 hover:bg-[#fff6f0] hover:text-[#c94d00]"><i class="fas fa-pen text-[11px]" aria-hidden="true"></i>Edit Staf</a>
                                    @if($member->is_active)
                                        <form method="POST" action="{{ route('admin.users.deactivate', $member->id) }}" x-data @submit="if (!confirm('Nonaktifkan akun ini? Riwayat tetap tersimpan dan akses login dihentikan.')) $event.preventDefault()">
                                            @csrf @method('PATCH')
                                            <button type="submit" title="Nonaktifkan staf" aria-label="Nonaktifkan {{ $member->name }}" class="inline-flex h-11 w-11 items-center justify-center rounded-lg text-[#7f837e] transition-colors hover:bg-amber-50 hover:text-amber-700"><i class="fas fa-user-slash text-[13px]" aria-hidden="true"></i></button>
                                        </form>
                                    @endif
                                    @if($archivingAvailable && $policy->delete(auth()->user(), $member))
                                        <form method="POST" action="{{ route('admin.users.destroy', $member->id) }}" x-data @submit="if (!confirm('Hapus staf ini dari daftar? Akun akan diarsipkan dan tidak bisa login. Riwayat transaksi dan absensi tetap tersimpan.')) $event.preventDefault()">
                                            @csrf @method('DELETE')
                                            <button type="submit" title="Hapus Staf" aria-label="Hapus staf {{ $member->name }}" class="inline-flex h-11 w-11 items-center justify-center rounded-lg text-red-500 transition-colors hover:bg-red-50 hover:text-red-700"><i class="far fa-trash-can text-[13px]" aria-hidden="true"></i></button>
                                        </form>
                                    @endif
                                </div>
                            @elseif($member->id === auth()->id())
                                <div class="flex justify-end"><a href="{{ route('profile.edit') }}" class="inline-flex h-11 items-center justify-center gap-2 whitespace-nowrap rounded-lg border border-[#e9ebf0] px-3 text-xs font-medium text-[#53565a] transition-colors hover:bg-[#f8f9fa]"><i class="far fa-user text-[11px]" aria-hidden="true"></i>Edit Profil Saya</a></div>
                            @else
                                <p class="text-right text-xs text-[#7f837e]"><i class="fas fa-lock mr-1.5 text-[10px]" aria-hidden="true"></i>Akun dilindungi</p>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-6 py-16 text-center"><div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-[#fff6f0] text-[#f9731f]"><i class="fas fa-users" aria-hidden="true"></i></div><p class="font-semibold">Tidak ada user sesuai pencarian.</p><p class="mt-2 text-xs text-[#7f837e]">Coba nama, email, atau status akun lainnya.</p></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="flex flex-col gap-3 border-t border-[#e9ebf0] px-6 py-4 text-[11px] text-[#7f837e]">
        @if($users->hasPages()){{ $users->links() }}@else<p>Menampilkan <span class="font-medium text-[#53565a]">{{ $users->count() }}</span> dari {{ $users->total() }} anggota tim</p>@endif
        <p class="flex items-center gap-2"><i class="fas fa-shield-halved text-[#9ca3af]" aria-hidden="true"></i>Riwayat transaksi dan absensi tetap tersimpan saat akun dinonaktifkan atau dihapus.</p>
    </div>
</section>
@endsection
