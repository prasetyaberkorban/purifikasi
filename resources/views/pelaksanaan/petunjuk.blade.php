<x-app-layout title="Petunjuk Pra-Kontrak" subtitle="Panduan Penggunaan & Standar Bobot Tahap Proses · Rekap Tahapan Pelaksanaan 2026">

    @include('pelaksanaan._tabs')

    @if (session('status'))
        <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800 shadow-sm flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>{{ session('status') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900">&times;</button>
        </div>
    @endif

    <div class="space-y-8">

        <!-- TABEL 1: PANDUAN PENGGUNAAN -->
        <div class="rounded-2xl border border-line bg-surface p-5 sm:p-6 shadow-sm">
            <div class="flex items-center justify-between pb-3 border-b border-line mb-4">
                <div>
                    <h3 class="text-base font-bold text-ink flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-forest"></span>
                        Panduan Penggunaan
                    </h3>
                    <p class="text-xs text-ink-soft">Instruksi pemakaian sheet/modul, arti pewarnaan, dan formula otomatis.</p>
                </div>
                <button type="button" onclick="openAddPanduanModal()"
                        class="btn-primary py-1.5 px-3 text-xs font-semibold inline-flex items-center gap-1.5 shadow-sm">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    + Add Petunjuk
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs sm:text-sm text-ink">
                    <thead class="bg-surface-alt/70 border-b border-line text-[0.7rem] font-bold uppercase tracking-wider text-ink-soft">
                        <tr>
                            <th class="py-2.5 px-3 text-center w-12">No.</th>
                            <th class="py-2.5 px-3 w-44">Bagian / Warna</th>
                            <th class="py-2.5 px-4 w-72">Fungsi</th>
                            <th class="py-2.5 px-4">Cara Penggunaan</th>
                            <th class="py-2.5 px-3 text-center w-20">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line">
                        @foreach ($panduanList as $idx => $p)
                            <tr class="hover:bg-slate-50/70">
                                <td class="py-3 px-3 text-center font-mono text-ink-soft">{{ $idx + 1 }}</td>
                                <td class="py-3 px-3 font-semibold text-forest">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-800 border border-slate-200">
                                        {{ $p->bagian_warna }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 font-medium text-ink">{{ $p->fungsi }}</td>
                                <td class="py-3 px-4 text-xs text-ink-soft leading-relaxed">{{ $p->cara_penggunaan }}</td>
                                <td class="py-3 px-3 text-center">
                                    <div class="inline-flex items-center gap-1">
                                        <button type="button" onclick="openEditPanduanModal({{ json_encode($p) }})" class="p-1 text-ink-soft hover:text-emerald-700">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </button>
                                        <form method="POST" action="{{ route('pelaksanaan.ref.destroy', ['type' => 'panduan', 'id' => $p->id]) }}" onsubmit="return confirm('Hapus petunjuk ini?')" class="inline">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="p-1 text-ink-soft hover:text-rose-700">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- TABEL 2: STANDAR BOBOT TAHAP PROSES -->
        <div class="rounded-2xl border border-line bg-surface p-5 sm:p-6 shadow-sm">
            <div class="flex items-center justify-between pb-3 border-b border-line mb-4">
                <div>
                    <h3 class="text-base font-bold text-ink flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-forest"></span>
                        Standar Bobot Tahap Proses
                    </h3>
                    <p class="text-xs text-ink-soft">Nilai persentase bobot kumulatif tiap tahapan proses.</p>
                </div>
                <button type="button" onclick="openAddBobotModal()"
                        class="btn-primary py-1.5 px-3 text-xs font-semibold inline-flex items-center gap-1.5 shadow-sm">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    + Add Bobot
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs sm:text-sm text-ink">
                    <thead class="bg-surface-alt/70 border-b border-line text-[0.7rem] font-bold uppercase tracking-wider text-ink-soft">
                        <tr>
                            <th class="py-2.5 px-3 text-center w-12">No.</th>
                            <th class="py-2.5 px-4">Tahap Proses</th>
                            <th class="py-2.5 px-4 text-center w-36">Bobot Desimal</th>
                            <th class="py-2.5 px-4 text-center w-36">Persentase</th>
                            <th class="py-2.5 px-3 text-center w-20">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line">
                        @foreach ($bobotList as $idx => $b)
                            <tr class="hover:bg-slate-50/70">
                                <td class="py-2.5 px-3 text-center font-mono text-ink-soft">{{ $idx + 1 }}</td>
                                <td class="py-2.5 px-4 font-semibold text-ink">{{ $b->tahap }}</td>
                                <td class="py-2.5 px-4 text-center font-mono font-bold text-forest">
                                    {{ number_format($b->bobot, 2) }}
                                </td>
                                <td class="py-2.5 px-4 text-center font-mono text-xs font-semibold text-ink-soft">
                                    {{ round($b->bobot * 100) }}%
                                </td>
                                <td class="py-2.5 px-3 text-center">
                                    <div class="inline-flex items-center gap-1">
                                        <button type="button" onclick="openEditBobotModal({{ json_encode($b) }})" class="p-1 text-ink-soft hover:text-emerald-700">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </button>
                                        <form method="POST" action="{{ route('pelaksanaan.ref.destroy', ['type' => 'bobot-proses', 'id' => $b->id]) }}" onsubmit="return confirm('Hapus standar bobot ini?')" class="inline">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="p-1 text-ink-soft hover:text-rose-700">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <!-- MODAL PANDUAN -->
    <div id="panduanModal" class="fixed inset-0 z-50 hidden overflow-y-auto backdrop-blur-sm flex items-center justify-center p-4" style="background-color: rgba(15, 23, 42, 0.75) !important;">
        <div class="relative w-full max-w-lg rounded-2xl shadow-2xl p-6" style="background-color: #ffffff !important; color: #0f172a !important; border: 1px solid #e2e8f0 !important;">
            <h3 id="panduanModalTitle" class="text-base font-bold text-slate-900 mb-4 pb-2 border-b border-slate-200">Tambah Panduan Penggunaan</h3>
            <form id="panduanForm" method="POST" action="{{ route('pelaksanaan.ref.store', ['type' => 'panduan']) }}" class="space-y-4">
                @csrf
                <input type="hidden" name="_method" id="panduanMethod" value="POST">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Bagian / Warna <span class="text-rose-500">*</span></label>
                    <input type="text" name="bagian_warna" id="panduan_input_bagian" required placeholder="Contoh: Hijau, Deadline..." class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-xs sm:text-sm text-slate-900 focus:border-forest focus:outline-none focus:ring-1 focus:ring-forest">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Fungsi <span class="text-rose-500">*</span></label>
                    <input type="text" name="fungsi" id="panduan_input_fungsi" required class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-xs sm:text-sm text-slate-900 focus:border-forest focus:outline-none focus:ring-1 focus:ring-forest">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Cara Penggunaan <span class="text-rose-500">*</span></label>
                    <textarea name="cara_penggunaan" id="panduan_input_cara" rows="3" required class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-xs sm:text-sm text-slate-900 focus:border-forest focus:outline-none focus:ring-1 focus:ring-forest"></textarea>
                </div>
                <div class="flex justify-end gap-2 pt-3 border-t border-slate-200">
                    <button type="button" onclick="closePanduanModal()" class="btn-secondary py-1.5 px-3 text-xs">Batal</button>
                    <button type="submit" class="btn-primary py-1.5 px-4 text-xs font-semibold shadow-sm">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL BOBOT -->
    <div id="bobotModal" class="fixed inset-0 z-50 hidden overflow-y-auto backdrop-blur-sm flex items-center justify-center p-4" style="background-color: rgba(15, 23, 42, 0.75) !important;">
        <div class="relative w-full max-w-sm rounded-2xl shadow-2xl p-6" style="background-color: #ffffff !important; color: #0f172a !important; border: 1px solid #e2e8f0 !important;">
            <h3 id="bobotModalTitle" class="text-base font-bold text-slate-900 mb-4 pb-2 border-b border-slate-200">Tambah Standar Bobot</h3>
            <form id="bobotForm" method="POST" action="{{ route('pelaksanaan.ref.store', ['type' => 'bobot-proses']) }}" class="space-y-4">
                @csrf
                <input type="hidden" name="_method" id="bobotMethod" value="POST">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Tahap Proses <span class="text-rose-500">*</span></label>
                    <input type="text" name="tahap" id="bobot_input_tahap" required class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-xs sm:text-sm text-slate-900 focus:border-forest focus:outline-none focus:ring-1 focus:ring-forest">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Bobot (0.00 - 1.00) <span class="text-rose-500">*</span></label>
                    <input type="number" step="0.01" min="0" max="1" name="bobot" id="bobot_input_bobot" required class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-xs sm:text-sm text-slate-900 focus:border-forest focus:outline-none focus:ring-1 focus:ring-forest">
                </div>
                <div class="flex justify-end gap-2 pt-3 border-t border-slate-200">
                    <button type="button" onclick="closeBobotModal()" class="btn-secondary py-1.5 px-3 text-xs">Batal</button>
                    <button type="submit" class="btn-primary py-1.5 px-4 text-xs font-semibold shadow-sm">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const panduanStoreRoute = "{{ route('pelaksanaan.ref.store', ['type' => 'panduan']) }}";
        const panduanUpdateRouteBase = "{{ url('manajemen-proyek/pelaksanaan/referensi/panduan') }}";

        const bobotStoreRoute = "{{ route('pelaksanaan.ref.store', ['type' => 'bobot-proses']) }}";
        const bobotUpdateRouteBase = "{{ url('manajemen-proyek/pelaksanaan/referensi/bobot-proses') }}";

        function openAddPanduanModal() {
            document.getElementById('panduanModalTitle').innerText = "Tambah Panduan Penggunaan";
            document.getElementById('panduanForm').action = panduanStoreRoute;
            document.getElementById('panduanMethod').value = "POST";
            document.getElementById('panduan_input_bagian').value = "";
            document.getElementById('panduan_input_fungsi').value = "";
            document.getElementById('panduan_input_cara').value = "";
            document.getElementById('panduanModal').classList.remove('hidden');
        }

        function openEditPanduanModal(item) {
            document.getElementById('panduanModalTitle').innerText = "Ubah Panduan Penggunaan";
            document.getElementById('panduanForm').action = `${panduanUpdateRouteBase}/${item.id}`;
            document.getElementById('panduanMethod').value = "PUT";
            document.getElementById('panduan_input_bagian').value = item.bagian_warna;
            document.getElementById('panduan_input_fungsi').value = item.fungsi;
            document.getElementById('panduan_input_cara').value = item.cara_penggunaan;
            document.getElementById('panduanModal').classList.remove('hidden');
        }

        function closePanduanModal() {
            document.getElementById('panduanModal').classList.add('hidden');
        }

        function openAddBobotModal() {
            document.getElementById('bobotModalTitle').innerText = "Tambah Standar Bobot";
            document.getElementById('bobotForm').action = bobotStoreRoute;
            document.getElementById('bobotMethod').value = "POST";
            document.getElementById('bobot_input_tahap').value = "";
            document.getElementById('bobot_input_bobot').value = "0.00";
            document.getElementById('bobotModal').classList.remove('hidden');
        }

        function openEditBobotModal(item) {
            document.getElementById('bobotModalTitle').innerText = "Ubah Standar Bobot";
            document.getElementById('bobotForm').action = `${bobotUpdateRouteBase}/${item.id}`;
            document.getElementById('bobotMethod').value = "PUT";
            document.getElementById('bobot_input_tahap').value = item.tahap;
            document.getElementById('bobot_input_bobot').value = parseFloat(item.bobot || 0).toFixed(2);
            document.getElementById('bobotModal').classList.remove('hidden');
        }

        function closeBobotModal() {
            document.getElementById('bobotModal').classList.add('hidden');
        }
    </script>

</x-app-layout>
