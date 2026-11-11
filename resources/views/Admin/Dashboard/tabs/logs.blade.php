<div x-show="currentTab === 'logs'" x-cloak class="flex-grow p-4 md:p-6 lg:p-7 overflow-y-auto hide-scroll bg-white font-['DM_Sans',sans-serif]">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-[25px] font-semibold text-[#111827] leading-tight tracking-tight">Log Aktivitas</h1>
            <p class="text-[13px] text-[#7f837e] mt-1">Pantau riwayat perubahan dan aktivitas tim di kafe Anda.</p>
        </div>
        <button type="button" @click="fetchLogs(logPage)" :disabled="logsLoading" :aria-busy="logsLoading" class="inline-flex items-center justify-center gap-2 px-4 py-3 bg-[#f9731f] hover:bg-[#ea580c] text-white text-xs font-semibold rounded-lg transition-colors disabled:opacity-50">
            <i class="fas fa-sync-alt" :class="logsLoading && 'animate-spin'" aria-hidden="true"></i><span x-text="logsLoading ? 'Memuat...' : 'Segarkan'">Segarkan</span>
        </button>
    </div>

    <div class="bg-white border border-[#e9ebf0] rounded-xl shadow-sm overflow-hidden">
        <div class="p-5 border-b border-[#e9ebf0] flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <h2 class="text-sm font-semibold text-[#111827]">Riwayat aktivitas <span class="ml-2 px-2 py-0.5 rounded-full bg-[#fff6f0] text-[#c94d00] text-xs" x-text="logTotal + ' catatan'"></span></h2>
                <p class="text-xs text-[#7f837e] mt-2">Aktivitas terbaru ditampilkan lebih dahulu. Waktu {{ config('app.timezone') }}.</p>
            </div>
            <form @submit.prevent="fetchLogs(1)" class="flex flex-col sm:flex-row gap-3">
                <div>
                    <label for="log-search" class="block text-xs text-[#53565a] mb-1.5">Cari aktivitas</label>
                    <input id="log-search" type="search" x-model="logSearch" maxlength="150" placeholder="Nama, aktivitas, atau deskripsi" class="w-full sm:w-64 rounded-lg border-[#e9ebf0] text-xs py-3 focus:border-orange-500 focus:ring-orange-500">
                </div>
                <div>
                    <label for="log-date" class="block text-xs text-[#53565a] mb-1.5">Tanggal</label>
                    <input id="log-date" type="date" x-model="logDate" class="w-full rounded-lg border-[#e9ebf0] text-xs py-3 focus:border-orange-500 focus:ring-orange-500">
                </div>
                <button type="submit" :disabled="logsLoading" class="self-end w-full sm:w-auto px-4 py-3 text-xs font-semibold border border-[#e9ebf0] rounded-lg text-[#53565a] hover:bg-gray-50 disabled:opacity-50">Terapkan</button>
            </form>
        </div>
        <div x-show="logsError" role="alert" class="p-5 bg-red-50 text-red-700 text-sm flex flex-wrap items-center justify-between gap-3">
            <span x-text="logsError"></span>
            <button type="button" @click="fetchLogs(logPage)" :disabled="logsLoading" class="underline font-semibold py-2">Coba lagi</button>
        </div>
        <div :aria-busy="logsLoading" class="overflow-x-auto">
            <table class="w-full text-left text-[13px]">
                <caption class="sr-only">Riwayat aktivitas pegawai kafe</caption>
                <thead class="bg-[#f8f9fa] text-[#7f837e] text-[11px] uppercase tracking-wider border-b border-[#e9ebf0]">
                    <tr><th scope="col" class="px-5 py-4">Waktu</th><th scope="col" class="px-5 py-4">Anggota tim</th><th scope="col" class="px-5 py-4">Aktivitas</th><th scope="col" class="px-5 py-4">Deskripsi</th></tr>
                </thead>
                <tbody class="divide-y divide-[#e9ebf0]">
                    <template x-if="logsLoading"><tr><td colspan="4" class="py-16 text-center text-[#7f837e]" role="status">Memuat riwayat aktivitas...</td></tr></template>
                    <template x-if="!logsLoading && !logsError && logs.length === 0">
                        <tr><td colspan="4" class="px-5 py-16 text-center">
                            <div class="w-12 h-12 mx-auto mb-4 rounded-xl bg-[#fff6f0] text-[#f9731f] flex items-center justify-center"><i class="fas fa-clock-rotate-left text-xl" aria-hidden="true"></i></div>
                            <p class="font-semibold text-[#111827]" x-text="logSearch || logDate ? 'Tidak ada aktivitas yang cocok' : 'Belum ada aktivitas tercatat'"></p>
                            <p class="text-xs text-[#7f837e] mt-2" x-text="logSearch || logDate ? 'Coba kata kunci atau tanggal lain.' : 'Aktivitas yang dicatat sistem akan muncul di sini.'"></p>
                        </td></tr>
                    </template>
                    <template x-for="log in (logsLoading || logsError ? [] : logs)" :key="log.id">
                        <tr class="hover:bg-[#f8f9fa] transition-colors">
                            <td class="px-5 py-5 whitespace-nowrap text-[#7f837e]" x-text="log.time"></td>
                            <td class="px-5 py-5 text-[#111827] font-medium"><div class="flex items-center gap-2.5"><span aria-hidden="true" class="w-8 h-8 rounded-full bg-[#fff6f0] text-[#c94d00] flex items-center justify-center shrink-0 text-xs font-semibold" x-text="log.user.slice(0,2).toUpperCase()"></span><span x-text="log.user"></span></div></td>
                            <td class="px-5 py-5"><span class="inline-block px-2.5 py-1 rounded-md bg-[#fff6f0] text-[#c94d00] text-xs font-medium break-words" x-text="log.action"></span></td>
                            <td class="px-5 py-5 text-[#53565a] min-w-[240px] break-words" x-text="log.description"></td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
        <div class="px-5 py-4 border-t border-[#e9ebf0] flex flex-wrap items-center justify-between gap-3 text-xs text-[#7f837e]">
            <p aria-live="polite" x-text="'Halaman ' + logPage + ' dari ' + logLastPage + ' · ' + logTotal + ' aktivitas'"></p>
            <div class="flex gap-2">
                <button type="button" @click="fetchLogs(logPage - 1)" :disabled="logsLoading || logPage <= 1" class="px-3 py-3 rounded-lg border border-[#e9ebf0] text-[#53565a] hover:bg-gray-50 disabled:opacity-40">Sebelumnya</button>
                <button type="button" @click="fetchLogs(logPage + 1)" :disabled="logsLoading || logPage >= logLastPage" class="px-3 py-3 rounded-lg border border-[#e9ebf0] text-[#53565a] hover:bg-gray-50 disabled:opacity-40">Berikutnya</button>
            </div>
        </div>
    </div>
</div>
