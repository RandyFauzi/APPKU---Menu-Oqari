<section aria-labelledby="password-title" class="overflow-hidden rounded-xl border border-[#e9ebf0] bg-white shadow-sm">
    <header class="flex items-center gap-3 border-b border-[#e9ebf0] px-5 py-4 md:px-6">
        <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-[#fff6f0] text-[#ea580c]"><i class="fas fa-lock text-sm" aria-hidden="true"></i></span>
        <div><h2 id="password-title" class="text-sm font-semibold">Ubah Password</h2><p class="mt-1 text-xs text-[#7f837e]">Gunakan password yang kuat dan berbeda dari akun lain.</p></div>
    </header>
    <form method="post" action="{{ route('password.update') }}" class="space-y-5 p-5 md:p-6">
        @csrf @method('put')
        <div>
            <label for="update_password_current_password" class="mb-2 block text-xs font-medium text-[#53565a]">Password saat ini <span class="text-[#ea580c]">*</span></label>
            <input id="update_password_current_password" name="current_password" type="password" required autocomplete="current-password" class="h-11 w-full rounded-lg border-[#e9ebf0] text-[13px] focus:border-orange-500 focus:ring-orange-500">
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2 text-xs" />
        </div>
        <div class="grid gap-5 md:grid-cols-2">
            <div>
                <label for="update_password_password" class="mb-2 block text-xs font-medium text-[#53565a]">Password baru <span class="text-[#ea580c]">*</span></label>
                <input id="update_password_password" name="password" type="password" required autocomplete="new-password" class="h-11 w-full rounded-lg border-[#e9ebf0] text-[13px] focus:border-orange-500 focus:ring-orange-500">
                <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2 text-xs" />
            </div>
            <div>
                <label for="update_password_password_confirmation" class="mb-2 block text-xs font-medium text-[#53565a]">Konfirmasi password baru <span class="text-[#ea580c]">*</span></label>
                <input id="update_password_password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password" class="h-11 w-full rounded-lg border-[#e9ebf0] text-[13px] focus:border-orange-500 focus:ring-orange-500">
            </div>
        </div>
        <div class="flex flex-wrap items-center justify-end gap-3 border-t border-[#e9ebf0] pt-4">
            @if(session('status') === 'password-updated')<p role="status" class="text-xs text-[#00643b]"><i class="fas fa-check mr-1.5" aria-hidden="true"></i>Password berhasil diperbarui.</p>@endif
            <button type="submit" class="inline-flex min-h-[44px] w-full items-center justify-center gap-2 rounded-lg bg-gradient-to-r from-[#f9731f] to-[#ea580c] px-4 text-xs font-semibold text-white shadow-sm hover:from-[#ea580c] hover:to-[#c2410c] sm:w-auto"><i class="fas fa-check text-[11px]" aria-hidden="true"></i>Simpan Password</button>
        </div>
    </form>
</section>
