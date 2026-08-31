<div x-show="currentTab === 'logs'" x-cloak>
    <div class="flex justify-between items-center mb-8">
        <div>
            <h2 class="text-[32px] font-bold text-[#164A35]" style="font-family: 'Playfair Display', serif;">Log Aktivitas & Keamanan</h2>
            <p class="text-[#777873] mt-2">Audit trail untuk memantau semua aktivitas perubahan oleh pegawai.</p>
        </div>
        <button @click="fetchLogs" class="bg-white border border-[#E3E1DC] text-[#164A35] px-4 py-2.5 rounded-xl font-bold hover:bg-gray-50 transition-colors shadow-sm">
            <i class="fas fa-sync-alt mr-2"></i> Segarkan
        </button>
    </div>

    <div class="bg-white rounded-[24px] border border-[#E3E1DC] overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-[#F8F7F3] border-b border-[#E3E1DC]">
                        <th class="py-4 px-6 text-[13px] font-bold text-[#777873] uppercase tracking-wider">Waktu</th>
                        <th class="py-4 px-6 text-[13px] font-bold text-[#777873] uppercase tracking-wider">Aktor (Crew)</th>
                        <th class="py-4 px-6 text-[13px] font-bold text-[#777873] uppercase tracking-wider">Aktivitas</th>
                        <th class="py-4 px-6 text-[13px] font-bold text-[#777873] uppercase tracking-wider">Deskripsi Lengkap</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-if="logs.length === 0">
                        <tr><td colspan="4" class="text-center py-8 text-gray-500">Belum ada aktivitas tercatat.</td></tr>
                    </template>
                    <template x-for="log in logs" :key="log.id">
                        <tr class="border-b border-gray-50 hover:bg-gray-50/50 transition-colors">
                            <td class="py-4 px-6 text-[13px] text-[#777873]" x-text="log.time"></td>
                            <td class="py-4 px-6 font-bold text-[#164A35]" x-text="log.user"></td>
                            <td class="py-4 px-6">
                                <span class="px-2.5 py-1 rounded-md text-[11px] font-bold uppercase tracking-wider bg-gray-100 text-gray-600" x-text="log.action"></span>
                            </td>
                            <td class="py-4 px-6 text-sm text-[#202522]" x-text="log.description"></td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>
</div>
