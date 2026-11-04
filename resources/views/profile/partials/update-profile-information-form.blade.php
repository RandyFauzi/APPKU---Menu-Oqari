<section aria-labelledby="profile-information-title" class="overflow-hidden rounded-xl border border-[#e9ebf0] bg-white shadow-sm">
    <header class="flex items-center gap-3 border-b border-[#e9ebf0] px-5 py-4 md:px-6">
        <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-[#fff6f0] text-[#ea580c]"><i class="far fa-user" aria-hidden="true"></i></span>
        <div><h2 id="profile-information-title" class="text-sm font-semibold">Informasi Profil</h2><p class="mt-1 text-xs text-[#7f837e]">Perbarui nama dan alamat email untuk login.</p></div>
    </header>
    <form id="send-verification" method="post" action="{{ route('verification.send') }}">@csrf</form>
    <form method="post" action="{{ route('profile.update') }}" class="space-y-5 p-5 md:p-6">
        @csrf @method('patch')
        <div class="grid gap-5 md:grid-cols-2">
            <div>
                <label for="name" class="mb-2 block text-xs font-medium text-[#53565a]">Nama lengkap <span class="text-[#ea580c]">*</span></label>
                <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required maxlength="255" autocomplete="name" class="h-11 w-full rounded-lg border-[#e9ebf0] text-[13px] focus:border-orange-500 focus:ring-orange-500">
                <x-input-error class="mt-2 text-xs" :messages="$errors->get('name')" />
            </div>
            <div>
                <label for="email" class="mb-2 block text-xs font-medium text-[#53565a]">Email login <span class="text-[#ea580c]">*</span></label>
                <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required maxlength="255" autocomplete="username" class="h-11 w-full rounded-lg border-[#e9ebf0] text-[13px] focus:border-orange-500 focus:ring-orange-500">
                <x-input-error class="mt-2 text-xs" :messages="$errors->get('email')" />
            </div>
        </div>
        @if($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
            <div class="rounded-lg bg-amber-50 p-3 text-xs leading-relaxed text-amber-800">
                <p>Email belum diverifikasi. <button form="send-verification" type="submit" class="min-h-[44px] font-semibold underline">Kirim ulang verifikasi</button></p>
                @if(session('status') === 'verification-link-sent')<p role="status" class="mt-1">Tautan verifikasi telah dikirim ke email Anda.</p>@endif
            </div>
        @endif
        <div class="flex flex-wrap items-center justify-end gap-3 border-t border-[#e9ebf0] pt-4">
            @if(session('status') === 'profile-updated')<p role="status" class="text-xs text-[#00643b]"><i class="fas fa-check mr-1.5" aria-hidden="true"></i>Profil berhasil disimpan.</p>@endif
            <button type="submit" class="inline-flex min-h-[44px] w-full items-center justify-center gap-2 rounded-lg bg-gradient-to-r from-[#f9731f] to-[#ea580c] px-4 text-xs font-semibold text-white shadow-sm hover:from-[#ea580c] hover:to-[#c2410c] sm:w-auto"><i class="fas fa-check text-[11px]" aria-hidden="true"></i>Simpan Profil</button>
        </div>
    </form>
</section>
