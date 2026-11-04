@extends('Admin.Users.layout')
@section('content')
<div class="flex flex-col sm:flex-row justify-between gap-4 sm:items-center mb-6">
    <div>
        <h1 class="text-[25px] font-semibold leading-tight tracking-tight">Manajemen Staf</h1>
        <p class="mt-1 text-[13px] text-[#7f837e]">Kelola anggota tim, akun, dan hak akses kafe Anda.</p>
    </div>
    <a class="inline-flex items-center justify-center gap-2 bg-gradient-to-r from-[#f9731f] to-[#ea580c] hover:from-[#ea580c] hover:to-[#c2410c] text-white px-4 py-3 rounded-lg text-xs font-semibold shadow-sm transition-colors" href="{{ route('admin.users.create') }}"><i class="fas fa-plus" aria-hidden="true"></i>Tambah Staf</a>
</div>
<div class="bg-white rounded-xl border border-[#e9ebf0] shadow-sm overflow-hidden">
    <div class="p-5 border-b border-[#e9ebf0] flex flex-col xl:flex-row xl:items-end justify-between gap-5">
        <div>
            <h2 class="text-sm font-semibold">Anggota tim <span class="ml-2 px-2 py-0.5 rounded-full bg-[#fff6f0] text-[#c94d00] text-xs">{{ $users->total() }} akun</span></h2>
            <p class="text-xs text-[#7f837e] mt-2">Penonaktifan akun tetap menyimpan riwayat transaksi dan absensi.</p>
        </div>
        <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-col sm:flex-row gap-3 sm:items-end">
            <div class="flex-1"><label for="q" class="block text-xs text-[#53565a] mb-1.5">Cari staf</label><input id="q" type="search" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Nama atau email" maxlength="100" class="w-full sm:w-56 text-xs py-3 rounded-lg border-[#e9ebf0] focus:border-orange-500 focus:ring-orange-500"></div>
            <div><label for="status" class="block text-xs text-[#53565a] mb-1.5">Status akun</label><select id="status" name="status" class="w-full text-xs py-3 rounded-lg border-[#e9ebf0] focus:border-orange-500 focus:ring-orange-500"><option value="">Semua status</option><option value="active" @selected(($filters['status'] ?? '') === 'active')>Aktif</option><option value="inactive" @selected(($filters['status'] ?? '') === 'inactive')>Nonaktif</option></select></div>
            <button class="px-4 py-3 text-xs font-semibold border border-[#e9ebf0] rounded-lg text-[#53565a] hover:bg-gray-50 transition-colors">Terapkan</button>
            <a class="px-3 py-3 text-xs font-medium text-[#7f837e] hover:text-[#ea580c] text-center" href="{{ route('admin.users.index') }}">Reset</a>
        </form>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left text-[13px]">
            <caption class="sr-only">Daftar staf kafe {{ auth()->user()->shop->name }}</caption>
            <thead class="bg-[#f8f9fa] text-[#7f837e] text-[11px] uppercase tracking-wider border-b border-[#e9ebf0]"><tr><th scope="col" class="px-5 py-4">Anggota tim</th><th scope="col" class="px-5 py-4">Role &amp; Izin</th><th scope="col" class="px-5 py-4">Status</th><th scope="col" class="px-5 py-4">Tindakan</th></tr></thead>
            <tbody class="divide-y divide-[#e9ebf0]">
            @forelse($users as $member)
                <tr class="hover:bg-[#f8f9fa] transition-colors">
                    <td class="px-5 py-5"><div class="flex items-center gap-3"><span class="w-10 h-10 rounded-full bg-[#fff6f0] text-[#c94d00] flex items-center justify-center text-xs font-semibold shrink-0" aria-hidden="true">{{ mb_strtoupper(mb_substr($member->name, 0, 2)) }}</span><div><p class="font-semibold">{{ $member->name }} @if($member->id === auth()->id())<span class="text-[11px] text-[#7f837e] font-normal">(Anda)</span>@endif</p><p class="text-xs text-[#7f837e] mt-1">{{ $member->email }}</p></div></div></td>
                    <td class="px-5 py-5"><span class="font-medium">{{ $member->role === 'owner' ? 'Owner' : config('access.roles.'.$member->role, $member->role) }}</span><p class="text-xs text-[#7f837e] mt-1">{{ $member->role === 'owner' ? 'Seluruh izin kafe' : ($member->permissions === null ? 'Preset role' : count($member->permissions).' izin khusus') }}</p></td>
                    <td class="px-5 py-5"><span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold {{ $member->is_active ? 'bg-[#eafbf2] text-[#00643b]' : 'bg-red-50 text-red-700' }}"><span class="w-1.5 h-1.5 rounded-full bg-current" aria-hidden="true"></span>{{ $member->is_active ? 'Aktif' : 'Nonaktif' }}</span></td>
                    <td class="px-5 py-5">
                        @if($policy->update(auth()->user(), $member))
                            <div class="flex flex-wrap gap-3 items-center">
                                <a class="inline-flex items-center gap-2 px-3 py-2.5 text-xs font-semibold border border-[#e9ebf0] rounded-lg text-[#53565a] hover:border-orange-300 hover:text-[#ea580c] whitespace-nowrap" href="{{ route('admin.users.edit', $member->id) }}"><i class="fas fa-pen" aria-hidden="true"></i>Edit Staf</a>
                                @if($member->is_active)<form method="POST" action="{{ route('admin.users.deactivate', $member->id) }}" x-data @submit="if (!confirm('Nonaktifkan akun ini? Riwayat tetap tersimpan dan akses login dihentikan.')) $event.preventDefault()">@csrf @method('PATCH')<button class="text-xs text-red-700 hover:bg-red-50 rounded-lg px-3 py-3" type="submit">Nonaktifkan</button></form>@endif
                                @if($policy->delete(auth()->user(), $member))
                                    <form method="POST" action="{{ route('admin.users.destroy', $member->id) }}" x-data @submit="if (!confirm('Hapus staf ini dari daftar? Akun akan diarsipkan dan tidak bisa login. Riwayat transaksi dan absensi tetap tersimpan.')) $event.preventDefault()">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-xs text-red-700 border border-red-200 hover:bg-red-50 rounded-lg px-3 py-3">Hapus Staf</button>
                                    </form>
                                @endif
                            </div>
                        @else
                            @if($member->id === auth()->id())
                                <a href="{{ route('profile.edit') }}" class="inline-flex items-center px-3 py-3 text-xs font-semibold text-[#53565a] border border-[#e9ebf0] rounded-lg hover:text-[#ea580c]">Edit Profil Saya</a>
                            @else
                                <span class="text-xs text-[#7f837e]">Akun dilindungi</span>
                            @endif
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="px-5 py-16 text-center"><div class="w-12 h-12 mx-auto mb-4 rounded-xl bg-[#fff6f0] text-[#f9731f] flex items-center justify-center"><i class="fas fa-users" aria-hidden="true"></i></div><p class="font-semibold">Tidak ada user sesuai pencarian.</p><p class="mt-2 text-xs text-[#7f837e]">Coba nama, email, atau status akun lainnya.</p></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-5 border-t border-[#e9ebf0] text-xs text-[#7f837e]">@if($users->hasPages()){{ $users->links() }}@else Menampilkan {{ $users->count() }} dari {{ $users->total() }} anggota tim @endif</div>
</div>
@endsection

