<section x-data @if($errors->userDeletion->isNotEmpty()) x-init="$nextTick(() => $refs.deleteDialog.showModal())" @endif>
    <details class="group rounded-xl border border-[#e9ebf0] bg-white shadow-sm">
        <summary class="flex min-h-[56px] cursor-pointer list-none items-center gap-2 px-5 text-xs font-medium text-[#7f837e] hover:text-[#53565a] md:px-6 [&::-webkit-details-marker]:hidden"><i class="fas fa-sliders text-[11px]" aria-hidden="true"></i>Pengaturan akun lanjutan<i class="fas fa-chevron-down ml-auto text-[9px] transition-transform group-open:rotate-180" aria-hidden="true"></i></summary>
        <div class="flex flex-col gap-4 border-t border-[#e9ebf0] p-5 sm:flex-row sm:items-center sm:justify-between md:px-6">
            <div><h2 class="text-sm font-semibold">Hapus Akun</h2><p class="mt-1 max-w-md text-xs leading-relaxed text-[#7f837e]">Penghapusan akun bersifat permanen. Pastikan data penting sudah disimpan sebelum melanjutkan.</p></div>
            <button type="button" @click="$refs.deleteDialog.showModal()" class="inline-flex min-h-[44px] shrink-0 items-center justify-center gap-2 rounded-lg border border-red-200 px-4 text-xs font-medium text-red-600 hover:bg-red-50"><i class="far fa-trash-can" aria-hidden="true"></i>Hapus Akun</button>
        </div>
    </details>
    <dialog x-ref="deleteDialog" aria-labelledby="delete-account-title" @click="if ($event.target === $el) $el.close()" class="m-auto w-[calc(100%-2rem)] max-w-md rounded-xl border border-[#e9ebf0] bg-white p-0 text-[#111827] shadow-xl backdrop:bg-black/40">
        <form method="post" action="{{ route('profile.destroy') }}" class="p-5 md:p-6">
            @csrf @method('delete')
            <h2 id="delete-account-title" class="text-base font-semibold">Hapus akun Anda?</h2>
            <p class="mt-2 text-xs leading-relaxed text-[#7f837e]">Akun akan dihapus permanen dan Anda akan keluar dari aplikasi. Masukkan password untuk mengonfirmasi.</p>
            <div class="mt-5">
                <label for="delete-account-password" class="mb-2 block text-xs font-medium text-[#53565a]">Password saat ini</label>
                <input id="delete-account-password" name="password" type="password" required autocomplete="current-password" class="h-11 w-full rounded-lg border-[#e9ebf0] text-[13px] focus:border-red-500 focus:ring-red-500">
                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2 text-xs" />
            </div>
            <div class="mt-5 flex justify-end gap-3 border-t border-[#e9ebf0] pt-4">
                <button type="button" @click="$refs.deleteDialog.close()" class="min-h-[44px] rounded-lg border border-[#e9ebf0] px-4 text-xs font-medium text-[#53565a] hover:bg-gray-50">Batal</button>
                <button type="submit" class="min-h-[44px] rounded-lg bg-red-600 px-4 text-xs font-semibold text-white hover:bg-red-700">Hapus Akun</button>
            </div>
        </form>
    </dialog>
</section>
