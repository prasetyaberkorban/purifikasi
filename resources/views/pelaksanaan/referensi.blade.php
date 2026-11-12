<x-app-layout title="Referensi Pelaksanaan" subtitle="Master Tahap Pelaksanaan & Master PIC · Manajemen Proyek">

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

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- TABEL 1: MASTER TAHAP PELAKSANAAN -->
        <div class="rounded-2xl border border-line bg-surface p-5 sm:p-6 shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between pb-3 border-b border-line mb-4">
                    <div>
                        <h3 class="text-base font-bold text-ink flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-forest"></span>
                            Tahap Pelaksanaan
                        </h3>
                        <p class="text-xs text-ink-soft">Daftar tahapan resmi dan bobot persentase progress.</p>
                    </div>
                    <button type="button" onclick="openAddTahapModal()"
                            class="btn-primary py-1.5 px-3 text-xs font-semibold inline-flex items-center gap-1.5 shadow-sm">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        + Add Tahap
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs sm:text-sm text-ink">
                        <thead class="bg-surface-alt/70 border-b border-line text-[0.7rem] font-bold uppercase tracking-wider text-ink-soft">
                            <tr>
                                <th class="py-2.5 px-3 text-center w-12">No.</th>
                                <th class="py-2.5 px-3">Tahap Pelaksanaan</th>
                                <th class="py-2.5 px-3 text-center w-28">Bobot</th>
                                <th class="py-2.5 px-3 text-center w-20">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-line">
                            @foreach ($tahapList as $item)
                                <tr class="hover:bg-slate-50/70">
                                    <td class="py-2.5 px-3 text-center font-mono text-ink-soft">{{ $item->urutan }}</td>
                                    <td class="py-2.5 px-3 font-medium text-ink">{{ $item->tahap }}</td>
                                    <td class="py-2.5 px-3 text-center font-mono font-bold text-forest">
                                        {{ number_format($item->bobot, 2) }} ({{ round($item->bobot * 100) }}%)
                                    </td>
                                    <td class="py-2.5 px-3 text-center">
                                        <div class="inline-flex items-center gap-1">
                                            <button type="button" onclick="openEditTahapModal({{ json_encode($item) }})" class="p-1 text-ink-soft hover:text-emerald-700">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                            </button>
                                            <form method="POST" action="{{ route('pelaksanaan.ref.destroy', ['type' => 'tahap', 'id' => $item->id]) }}" onsubmit="return confirm('Hapus tahap ini?')" class="inline">
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
            <div class="mt-4 pt-3 border-t border-line text-[11px] text-ink-soft">
                Total {{ $tahapList->count() }} tahapan pelaksanaan.
            </div>
        </div>

        <!-- TABEL 2: MASTER DAFTAR PIC -->
        <div class="rounded-2xl border border-line bg-surface p-5 sm:p-6 shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between pb-3 border-b border-line mb-4">
                    <div>
                        <h3 class="text-base font-bold text-ink flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-forest"></span>
                            Daftar PIC (Penanggung Jawab)
                        </h3>
                        <p class="text-xs text-ink-soft">Master penanggung jawab yang otomatis bertambah saat input form.</p>
                    </div>
                    <button type="button" onclick="openAddPicModal()"
                            class="btn-primary py-1.5 px-3 text-xs font-semibold inline-flex items-center gap-1.5 shadow-sm">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        + Add PIC
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs sm:text-sm text-ink">
                        <thead class="bg-surface-alt/70 border-b border-line text-[0.7rem] font-bold uppercase tracking-wider text-ink-soft">
                            <tr>
                                <th class="py-2.5 px-3 text-center w-12">No.</th>
                                <th class="py-2.5 px-4">Nama PIC</th>
                                <th class="py-2.5 px-3 text-center w-24">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-line">
                            @foreach ($picList as $idx => $pic)
                                <tr class="hover:bg-slate-50/70">
                                    <td class="py-2.5 px-3 text-center font-mono text-ink-soft">{{ $idx + 1 }}</td>
                                    <td class="py-2.5 px-4 font-semibold text-ink">{{ $pic->nama }}</td>
                                    <td class="py-2.5 px-3 text-center">
                                        <div class="inline-flex items-center gap-1">
                                            <button type="button" onclick="openEditPicModal({{ json_encode($pic) }})" class="p-1 text-ink-soft hover:text-emerald-700">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                            </button>
                                            <form method="POST" action="{{ route('pelaksanaan.ref.destroy', ['type' => 'pic', 'id' => $pic->id]) }}" onsubmit="return confirm('Hapus PIC ini?')" class="inline">
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
            <div class="mt-4 pt-3 border-t border-line text-[11px] text-ink-soft">
                Total {{ $picList->count() }} orang PIC terdaftar.
            </div>
        </div>

    </div>

    <!-- MODAL TAHAP -->
    <div id="tahapModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="relative w-full max-w-md rounded-2xl bg-surface border border-line shadow-2xl p-6">
            <h3 id="tahapModalTitle" class="text-base font-bold text-ink mb-4 pb-2 border-b border-line">Tambah Tahap Pelaksanaan</h3>
            <form id="tahapForm" method="POST" action="{{ route('pelaksanaan.ref.store', ['type' => 'tahap']) }}" class="space-y-4">
                @csrf
                <input type="hidden" name="_method" id="tahapMethod" value="POST">
                <div>
                    <label class="block text-xs font-semibold text-ink mb-1">Tahap Pelaksanaan <span class="text-rose-500">*</span></label>
                    <input type="text" name="tahap" id="tahap_input_nama" required class="w-full rounded-xl border border-line bg-surface px-3 py-2 text-xs sm:text-sm text-ink">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-ink mb-1">Bobot Progress <span class="text-rose-500">*</span></label>
                        <select name="bobot" id="tahap_input_bobot" required class="w-full rounded-xl border border-line bg-surface px-3 py-2 text-xs sm:text-sm text-ink">
                            <option value="0.00">0.00 (0%)</option>
                            <option value="0.20">0.20 (20%)</option>
                            <option value="0.40">0.40 (40%)</option>
                            <option value="0.60">0.60 (60%)</option>
                            <option value="0.75">0.75 (75%)</option>
                            <option value="0.90">0.90 (90%)</option>
                            <option value="1.00">1.00 (100%)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-ink mb-1">Urutan</label>
                        <input type="number" name="urutan" id="tahap_input_urutan" min="1" max="50" class="w-full rounded-xl border border-line bg-surface px-3 py-2 text-xs sm:text-sm text-ink">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-ink mb-1">Keterangan Ringkas</label>
                    <textarea name="keterangan" id="tahap_input_keterangan" rows="2" class="w-full rounded-xl border border-line bg-surface px-3 py-2 text-xs sm:text-sm text-ink"></textarea>
                </div>
                <div class="flex justify-end gap-2 pt-3 border-t border-line">
                    <button type="button" onclick="closeTahapModal()" class="btn-secondary py-1.5 px-3 text-xs">Batal</button>
                    <button type="submit" class="btn-primary py-1.5 px-4 text-xs font-semibold">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL PIC -->
    <div id="picModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="relative w-full max-w-sm rounded-2xl bg-surface border border-line shadow-2xl p-6">
            <h3 id="picModalTitle" class="text-base font-bold text-ink mb-4 pb-2 border-b border-line">Tambah Master PIC</h3>
            <form id="picForm" method="POST" action="{{ route('pelaksanaan.ref.store', ['type' => 'pic']) }}" class="space-y-4">
                @csrf
                <input type="hidden" name="_method" id="picMethod" value="POST">
                <div>
                    <label class="block text-xs font-semibold text-ink mb-1">Nama PIC <span class="text-rose-500">*</span></label>
                    <input type="text" name="nama" id="pic_input_nama" required placeholder="Contoh: Dika, Siddiq..." class="w-full rounded-xl border border-line bg-surface px-3 py-2 text-xs sm:text-sm text-ink">
                </div>
                <div class="flex justify-end gap-2 pt-3 border-t border-line">
                    <button type="button" onclick="closePicModal()" class="btn-secondary py-1.5 px-3 text-xs">Batal</button>
                    <button type="submit" class="btn-primary py-1.5 px-4 text-xs font-semibold">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const tahapStoreRoute = "{{ route('pelaksanaan.ref.store', ['type' => 'tahap']) }}";
        const tahapUpdateRouteBase = "{{ url('manajemen-proyek/pelaksanaan/referensi/tahap') }}";

        const picStoreRoute = "{{ route('pelaksanaan.ref.store', ['type' => 'pic']) }}";
        const picUpdateRouteBase = "{{ url('manajemen-proyek/pelaksanaan/referensi/pic') }}";

        function openAddTahapModal() {
            document.getElementById('tahapModalTitle').innerText = "Tambah Tahap Pelaksanaan";
            document.getElementById('tahapForm').action = tahapStoreRoute;
            document.getElementById('tahapMethod').value = "POST";
            document.getElementById('tahap_input_nama').value = "";
            document.getElementById('tahap_input_bobot').value = "0.00";
            document.getElementById('tahap_input_urutan').value = "";
            document.getElementById('tahap_input_keterangan').value = "";
            document.getElementById('tahapModal').classList.remove('hidden');
        }

        function openEditTahapModal(item) {
            document.getElementById('tahapModalTitle').innerText = "Ubah Tahap Pelaksanaan";
            document.getElementById('tahapForm').action = `${tahapUpdateRouteBase}/${item.id}`;
            document.getElementById('tahapMethod').value = "PUT";
            document.getElementById('tahap_input_nama').value = item.tahap;
            document.getElementById('tahap_input_bobot').value = parseFloat(item.bobot || 0).toFixed(2);
            document.getElementById('tahap_input_urutan').value = item.urutan;
            document.getElementById('tahap_input_keterangan').value = item.keterangan || "";
            document.getElementById('tahapModal').classList.remove('hidden');
        }

        function closeTahapModal() {
            document.getElementById('tahapModal').classList.add('hidden');
        }

        function openAddPicModal() {
            document.getElementById('picModalTitle').innerText = "Tambah Master PIC";
            document.getElementById('picForm').action = picStoreRoute;
            document.getElementById('picMethod').value = "POST";
            document.getElementById('pic_input_nama').value = "";
            document.getElementById('picModal').classList.remove('hidden');
        }

        function openEditPicModal(item) {
            document.getElementById('picModalTitle').innerText = "Ubah Master PIC";
            document.getElementById('picForm').action = `${picUpdateRouteBase}/${item.id}`;
            document.getElementById('picMethod').value = "PUT";
            document.getElementById('pic_input_nama').value = item.nama;
            document.getElementById('picModal').classList.remove('hidden');
        }

        function closePicModal() {
            document.getElementById('picModal').classList.add('hidden');
        }
    </script>

</x-app-layout>
