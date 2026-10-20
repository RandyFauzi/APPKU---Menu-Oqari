<div x-show="currentTab === 'logs'" x-cloak class="flex-grow p-4 md:p-8 bg-ivory overflow-y-auto hide-scroll font-sans">
    <div class="max-w-5xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <p class="text-muted text-[16px]">Audit trail untuk memantau semua aktivitas perubahan oleh pegawai.</p>
            <button @click="fetchLogs" class="bg-white border border-line text-primary px-4 py-2.5 rounded-xl font-bold hover:bg-gray-50 transition-colors shadow-sm">
                <i class="fas fa-sync-alt mr-2"></i> Segarkan
            </button>
        </div>

    <div class="bg-white rounded-[24px] border border-line overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-ivory border-b border-line">
                        <th class="py-4 px-6 text-[13px] font-bold text-muted uppercase tracking-wider">Waktu</th>
                        <th class="py-4 px-6 text-[13px] font-bold text-muted uppercase tracking-wider">Aktor (Crew)</th>
                        <th class="py-4 px-6 text-[13px] font-bold text-muted uppercase tracking-wider">Aktivitas</th>
                        <th class="py-4 px-6 text-[13px] font-bold text-muted uppercase tracking-wider">Deskripsi Lengkap</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-if="logs.length === 0">
                        <tr><td colspan="4" class="text-center py-8 text-gray-500">Belum ada aktivitas tercatat.</td></tr>
                    </template>
                    <template x-for="log in logs" :key="log.id">
                        <tr class="border-b border-gray-50 hover:bg-gray-50/50 transition-colors">
                            <td class="py-4 px-6 text-[13px] text-muted" x-text="log.time"></td>
                            <td class="py-4 px-6 font-bold text-primary" x-text="log.user"></td>
                            <td class="py-4 px-6">
                                <span class="px-2.5 py-1 rounded-md text-[11px] font-bold uppercase tracking-wider bg-gray-100 text-gray-600" x-text="log.action"></span>
                            </td>
                            <td class="py-4 px-6 text-sm text-ink" x-text="log.description"></td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>
    </div>
</div>
