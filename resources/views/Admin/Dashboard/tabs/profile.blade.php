<div x-show="currentTab === 'profile'" x-cloak class="flex-grow p-4 md:p-8 overflow-y-auto hide-scroll space-y-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-[28px] font-bold text-primary leading-tight" style="font-family: var(--oq-font-heading);">Profile Settings</h2>
            <p class="text-[14px] text-muted mt-1">Perbarui informasi profil dan kata sandi Anda.</p>
        </div>
    </div>

    <div class="bg-white rounded-[24px] shadow-[0_10px_35px_rgba(0,0,0,0.03)] border border-line overflow-hidden">
        <div class="p-5 md:p-8 space-y-6 max-w-2xl">
            
            <div class="flex flex-col gap-5">
                <div>
                    <label class="block text-[12px] font-bold text-muted mb-2 uppercase tracking-widest">Nama Lengkap</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fas fa-user text-brand-200"></i>
                        </div>
                        <input type="text" x-model="profile.name" placeholder="Nama Lengkap" class="w-full bg-ivory border border-line rounded-[14px] pl-11 pr-4 py-3 text-[15px] font-bold text-ink focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-all">
                    </div>
                </div>

                <div>
                    <label class="block text-[12px] font-bold text-muted mb-2 uppercase tracking-widest">Alamat Email</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fas fa-envelope text-brand-200"></i>
                        </div>
                        <input type="email" x-model="profile.email" placeholder="Alamat Email" class="w-full bg-ivory border border-line rounded-[14px] pl-11 pr-4 py-3 text-[15px] font-bold text-ink focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-all">
                    </div>
                </div>
            </div>

            <hr class="border-dashed border-line my-6">

            <div class="flex flex-col gap-5">
                <p class="text-[14px] font-bold text-ink">Ubah Kata Sandi</p>
                <p class="text-[13px] text-muted -mt-4">Kosongkan jika tidak ingin mengubah kata sandi.</p>
                
                <div>
                    <label class="block text-[12px] font-bold text-muted mb-2 uppercase tracking-widest">Kata Sandi Baru</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-brand-200"></i>
                        </div>
                        <input type="password" x-model="profile.password" placeholder="Min. 8 karakter" class="w-full bg-ivory border border-line rounded-[14px] pl-11 pr-4 py-3 text-[15px] font-bold text-ink focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-all">
                    </div>
                </div>

                <div>
                    <label class="block text-[12px] font-bold text-muted mb-2 uppercase tracking-widest">Konfirmasi Kata Sandi Baru</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-brand-200"></i>
                        </div>
                        <input type="password" x-model="profile.password_confirmation" placeholder="Ulangi kata sandi" class="w-full bg-ivory border border-line rounded-[14px] pl-11 pr-4 py-3 text-[15px] font-bold text-ink focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-all">
                    </div>
                </div>
            </div>
            
            <div class="pt-6 border-t border-line flex justify-end">
                <button @click="saveProfile()" :disabled="isSavingProfile" class="px-8 py-3.5 rounded-[12px] font-bold text-[14px] bg-primary text-white hover:bg-primary-dark transition-colors shadow-sm flex items-center gap-2 disabled:opacity-70">
                    <i class="fas fa-spinner fa-spin" x-show="isSavingProfile" x-cloak></i>
                    <i class="fas fa-save" x-show="!isSavingProfile"></i>
                    Simpan Profil
                </button>
            </div>
        </div>
    </div>
</div>
