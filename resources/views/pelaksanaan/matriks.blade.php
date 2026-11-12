<x-app-layout title="Matriks Pelaksanaan" subtitle="Rekap Tahapan Pelaksanaan 2026 · Manajemen Proyek">

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

    <!-- Header Summary KPI & Info -->
    <div class="mb-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <div class="rounded-2xl border border-line bg-surface p-4 shadow-sm flex items-center gap-4">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-forest/10 text-forest">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
            </div>
            <div>
                <p class="text-xs font-medium text-ink-soft">Total Proyek Pelaksanaan</p>
                <p class="text-xl sm:text-2xl font-bold text-ink">{{ $totalProjects }} <span class="text-xs font-normal text-ink-soft">Proyek</span></p>
            </div>
        </div>

        <div class="rounded-2xl border border-line bg-surface p-4 shadow-sm flex items-center gap-4">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-500/10 text-emerald-700">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
            </div>
            <div>
                <p class="text-xs font-medium text-ink-soft">Tanggal Update Terakhir</p>
                <p class="text-base sm:text-lg font-bold text-ink">{{ \Carbon\Carbon::parse($lastUpdate)->translatedFormat('d M Y') }}</p>
            </div>
        </div>

        <div class="rounded-2xl border border-line bg-surface p-4 shadow-sm flex items-center gap-4 sm:col-span-2 lg:col-span-1">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-sky-500/10 text-sky-700">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div>
                <p class="text-xs font-medium text-ink-soft">Status Tracking</p>
                <p class="text-xs font-medium text-ink-soft mt-0.5">
                    <span class="inline-flex items-center gap-1 text-emerald-700 font-semibold"><span class="w-2 h-2 rounded-full bg-emerald-500"></span> Aman</span> |
                    <span class="inline-flex items-center gap-1 text-amber-700 font-semibold"><span class="w-2 h-2 rounded-full bg-amber-500"></span> 0-14 Hari</span> |
                    <span class="inline-flex items-center gap-1 text-rose-700 font-semibold"><span class="w-2 h-2 rounded-full bg-rose-500"></span> Terlambat</span>
                </p>
            </div>
        </div>
    </div>

    <!-- Filter & Tombol Tambah Proyek -->
    <div class="mb-6 flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <form method="GET" action="{{ route('pelaksanaan.matriks') }}" class="flex flex-wrap items-center gap-2.5 flex-1">
            <div class="relative min-w-[200px] flex-1">
                <input type="text" name="q" value="{{ $filters['q'] ?? '' }}"
                       placeholder="Cari nama project, kode, pic..."
                       class="w-full rounded-xl border border-line bg-surface px-3 py-2 pl-9 text-xs sm:text-sm text-ink placeholder:text-ink-soft focus:border-leaf focus:outline-none focus:ring-1 focus:ring-leaf">
                <svg class="w-4 h-4 text-ink-soft absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>

            <select name="pengelola" class="rounded-xl border border-line bg-surface px-3 py-2 text-xs sm:text-sm text-ink focus:border-leaf focus:outline-none">
                <option value="">Semua Pengelola</option>
                @foreach ($pengelolaOptions as $p)
                    <option value="{{ $p }}" {{ ($filters['pengelola'] ?? '') === $p ? 'selected' : '' }}>{{ $p }}</option>
                @endforeach
            </select>

            <select name="status_pekerjaan" class="rounded-xl border border-line bg-surface px-3 py-2 text-xs sm:text-sm text-ink focus:border-leaf focus:outline-none max-w-[200px]">
                <option value="">Semua Tahap/Status</option>
                @foreach ($refTahap as $st)
                    <option value="{{ $st->tahap }}" {{ ($filters['status_pekerjaan'] ?? '') === $st->tahap ? 'selected' : '' }}>{{ $st->tahap }}</option>
                @endforeach
            </select>

            <select name="pic" class="rounded-xl border border-line bg-surface px-3 py-2 text-xs sm:text-sm text-ink focus:border-leaf focus:outline-none">
                <option value="">Semua PIC</option>
                @foreach ($refPic as $p)
                    <option value="{{ $p }}" {{ ($filters['pic'] ?? '') === $p ? 'selected' : '' }}>{{ $p }}</option>
                @endforeach
            </select>

            <button type="submit" class="btn-primary py-2 px-3 text-xs sm:text-sm">Filter</button>
            @if (!empty(array_filter($filters)))
                <a href="{{ route('pelaksanaan.matriks') }}" class="btn-secondary py-2 px-3 text-xs sm:text-sm">Reset</a>
            @endif
        </form>

        <div class="flex items-center gap-2">
            <button type="button" onclick="openAddModal()"
                    class="btn-primary inline-flex items-center gap-2 px-4 py-2 text-xs sm:text-sm font-semibold whitespace-nowrap shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                + Add Proyek
            </button>
        </div>
    </div>

    <!-- Tabel Matriks Pelaksanaan -->
    <div class="rounded-2xl border border-line bg-surface shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs sm:text-sm text-ink">
                <thead class="bg-surface-alt/70 border-b border-line text-[0.75rem] font-bold uppercase tracking-wider text-ink-soft">
                    <tr>
                        <th class="py-3 px-3 text-center w-12">No.</th>
                        <th class="py-3 px-3">Kode Project</th>
                        <th class="py-3 px-4 min-w-[260px]">Nama Project</th>
                        <th class="py-3 px-3">Pengelola</th>
                        <th class="py-3 px-4">Status Pekerjaan</th>
                        <th class="py-3 px-3 text-center">Progress</th>
                        <th class="py-3 px-3">PIC</th>
                        <th class="py-3 px-3 whitespace-nowrap">Kontrak Awal</th>
                        <th class="py-3 px-3 whitespace-nowrap">Kontrak Akhir</th>
                        <th class="py-3 px-3 text-center whitespace-nowrap">Remaining Days</th>
                        <th class="py-3 px-3 text-center w-24">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    @forelse ($projects as $index => $item)
                        @php
                            $days = $item->calculated_remaining_days;
                        @endphp
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-3 px-3 text-center text-ink-soft font-mono">
                                {{ $projects->firstItem() + $index }}
                            </td>
                            <td class="py-3 px-3 font-mono font-medium text-ink-soft whitespace-nowrap">
                                {{ $item->kode_project ?: '—' }}
                            </td>
                            <td class="py-3 px-4 font-semibold text-forest leading-snug">
                                {{ $item->nama_project }}
                            </td>
                            <td class="py-3 px-3 whitespace-nowrap">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $item->pengelola === 'Bandung' ? 'bg-indigo-50 text-indigo-700 border border-indigo-200' : 'bg-emerald-50 text-emerald-700 border border-emerald-200' }}">
                                    {{ $item->pengelola }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-xs font-medium text-ink whitespace-nowrap">
                                {{ $item->status_pekerjaan }}
                            </td>
                            <td class="py-3 px-3 text-center whitespace-nowrap font-mono font-semibold">
                                {{ round($item->progress_pekerjaan * 100) }}%
                            </td>
                            <td class="py-3 px-3 text-xs font-semibold text-slate-700 whitespace-nowrap">
                                {{ $item->pic ?: '—' }}
                            </td>
                            <td class="py-3 px-3 font-mono text-xs text-ink-soft whitespace-nowrap">
                                {{ $item->kontrak_awal ? $item->kontrak_awal->translatedFormat('d M Y') : '—' }}
                            </td>
                            <td class="py-3 px-3 font-mono text-xs text-ink-soft whitespace-nowrap">
                                {{ $item->kontrak_akhir ? $item->kontrak_akhir->translatedFormat('d M Y') : '—' }}
                            </td>
                            <td class="py-3 px-3 text-center whitespace-nowrap">
                                @if ($days !== null)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold font-mono border {{ $item->remaining_days_badge_class }}">
                                        {{ $days }} Hari
                                    </span>
                                @else
                                    <span class="text-ink-soft font-mono text-xs">—</span>
                                @endif
                            </td>
                            <td class="py-3 px-3 text-center whitespace-nowrap">
                                <div class="inline-flex items-center gap-1.5">
                                    <button type="button" onclick="openEditModal({{ json_encode($item) }})"
                                            class="p-1.5 rounded-lg text-ink-soft hover:text-emerald-700 hover:bg-emerald-50 transition-colors" title="Ubah Proyek">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </button>
                                    <form method="POST" action="{{ route('pelaksanaan.projects.destroy', $item) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus proyek ini?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 rounded-lg text-ink-soft hover:text-rose-700 hover:bg-rose-50 transition-colors" title="Hapus Proyek">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="py-12 text-center text-ink-soft">
                                <svg class="w-12 h-12 mx-auto text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Tidak ada proyek ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($projects->hasPages())
            <div class="p-4 border-t border-line">
                {{ $projects->links() }}
            </div>
        @endif
    </div>

    <!-- MODAL POP-UP ADD / EDIT PROYEK PELAKSANAAN -->
    <div id="projectModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="relative w-full max-w-2xl rounded-2xl bg-surface border border-line shadow-2xl p-6 sm:p-8 animate-in fade-in zoom-in-95 duration-150">
            <div class="flex items-center justify-between pb-4 border-b border-line mb-6">
                <div>
                    <h3 id="modalTitle" class="text-lg font-bold text-ink">Tambah Proyek Pelaksanaan</h3>
                    <p class="text-xs text-ink-soft mt-0.5">Isi rincian data tahapan pelaksanaan proyek.</p>
                </div>
                <button type="button" onclick="closeModal()" class="rounded-lg p-1.5 text-ink-soft hover:bg-slate-100 hover:text-ink">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <form id="projectForm" method="POST" action="{{ route('pelaksanaan.projects.store') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-ink mb-1">Kode Project (Opsional)</label>
                        <input type="text" name="kode_project" id="input_kode_project" placeholder="Contoh: ENG-2026-001"
                               class="w-full rounded-xl border border-line bg-surface px-3 py-2 text-xs sm:text-sm text-ink focus:border-leaf focus:outline-none focus:ring-1 focus:ring-leaf">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-ink mb-1">Pengelola <span class="text-rose-500">*</span></label>
                        <select name="pengelola" id="input_pengelola" required
                                class="w-full rounded-xl border border-line bg-surface px-3 py-2 text-xs sm:text-sm text-ink focus:border-leaf focus:outline-none focus:ring-1 focus:ring-leaf">
                            @foreach ($pengelolaOptions as $p)
                                <option value="{{ $p }}">{{ $p }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-ink mb-1">Nama Project <span class="text-rose-500">*</span></label>
                    <textarea name="nama_project" id="input_nama_project" rows="2" required placeholder="Tuliskan nama lengkap pekerjaan / kajian..."
                              class="w-full rounded-xl border border-line bg-surface px-3 py-2 text-xs sm:text-sm text-ink focus:border-leaf focus:outline-none focus:ring-1 focus:ring-leaf"></textarea>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-ink mb-1">Status Pekerjaan <span class="text-rose-500">*</span></label>
                        <select name="status_pekerjaan" id="input_status_pekerjaan" required
                                class="w-full rounded-xl border border-line bg-surface px-3 py-2 text-xs sm:text-sm text-ink focus:border-leaf focus:outline-none focus:ring-1 focus:ring-leaf">
                            @foreach ($refTahap as $st)
                                <option value="{{ $st->tahap }}" data-bobot="{{ $st->bobot }}">{{ $st->tahap }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-ink mb-1">Progress Pekerjaan <span class="text-rose-500">*</span></label>
                        <select name="progress_pekerjaan" id="input_progress_pekerjaan" required
                                class="w-full rounded-xl border border-line bg-surface px-3 py-2 text-xs sm:text-sm text-ink focus:border-leaf focus:outline-none focus:ring-1 focus:ring-leaf">
                            @foreach ($progressOptions as $pr)
                                <option value="{{ $pr['value'] }}">{{ $pr['label'] }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-xs font-semibold text-ink">PIC (Penanggung Jawab)</label>
                        <span class="text-[11px] text-ink-soft">Ketik nama baru atau pilih dari daftar</span>
                    </div>
                    <input type="text" name="pic" id="input_pic" list="picOptionsList" placeholder="Ketik nama PIC..."
                           class="w-full rounded-xl border border-line bg-surface px-3 py-2 text-xs sm:text-sm text-ink focus:border-leaf focus:outline-none focus:ring-1 focus:ring-leaf">
                    <datalist id="picOptionsList">
                        @foreach ($refPic as $picName)
                            <option value="{{ $picName }}"></option>
                        @endforeach
                    </datalist>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-ink mb-1">Kontrak Awal</label>
                        <input type="date" name="kontrak_awal" id="input_kontrak_awal"
                               class="w-full rounded-xl border border-line bg-surface px-3 py-2 text-xs sm:text-sm text-ink focus:border-leaf focus:outline-none focus:ring-1 focus:ring-leaf">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-ink mb-1">Kontrak Akhir</label>
                        <input type="date" name="kontrak_akhir" id="input_kontrak_akhir" onchange="calculateRemainingDaysPreview()"
                               class="w-full rounded-xl border border-line bg-surface px-3 py-2 text-xs sm:text-sm text-ink focus:border-leaf focus:outline-none focus:ring-1 focus:ring-leaf">
                    </div>
                </div>

                <div class="rounded-xl bg-slate-50 border border-slate-200 p-3 flex items-center justify-between">
                    <div>
                        <div class="text-xs font-semibold text-slate-700">Estimasi Sisa Hari (Remaining Days):</div>
                        <div class="text-[11px] text-slate-500">Dihitung otomatis dari selisih Kontrak Akhir dengan hari ini.</div>
                    </div>
                    <span id="previewRemainingDays" class="text-xs font-bold font-mono px-2.5 py-1 rounded-full bg-slate-200 text-slate-700">
                        —
                    </span>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-line mt-6">
                    <button type="button" onclick="closeModal()" class="btn-secondary px-4 py-2 text-xs sm:text-sm font-semibold">
                        Batal
                    </button>
                    <button type="submit" class="btn-primary px-5 py-2 text-xs sm:text-sm font-semibold">
                        Simpan Data
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const storeRoute = "{{ route('pelaksanaan.projects.store') }}";
        const updateRouteBase = "{{ url('manajemen-proyek/pelaksanaan/matriks') }}";

        function openAddModal() {
            document.getElementById('modalTitle').innerText = "Tambah Proyek Pelaksanaan";
            document.getElementById('projectForm').action = storeRoute;
            document.getElementById('formMethod').value = "POST";

            document.getElementById('input_kode_project').value = "";
            document.getElementById('input_nama_project').value = "";
            document.getElementById('input_pengelola').value = "Semarang";
            document.getElementById('input_status_pekerjaan').selectedIndex = 0;
            document.getElementById('input_progress_pekerjaan').value = "0.00";
            document.getElementById('input_pic').value = "";
            document.getElementById('input_kontrak_awal').value = "";
            document.getElementById('input_kontrak_akhir').value = "";
            document.getElementById('previewRemainingDays').innerText = "—";
            document.getElementById('previewRemainingDays').className = "text-xs font-bold font-mono px-2.5 py-1 rounded-full bg-slate-200 text-slate-700";

            document.getElementById('projectModal').classList.remove('hidden');
        }

        function openEditModal(project) {
            document.getElementById('modalTitle').innerText = "Ubah Proyek Pelaksanaan";
            document.getElementById('projectForm').action = `${updateRouteBase}/${project.id}`;
            document.getElementById('formMethod').value = "PUT";

            document.getElementById('input_kode_project').value = project.kode_project || "";
            document.getElementById('input_nama_project').value = project.nama_project || "";
            document.getElementById('input_pengelola').value = project.pengelola || "Semarang";
            document.getElementById('input_status_pekerjaan').value = project.status_pekerjaan || "";
            
            // Format float progress value (misal 0.75)
            const prog = parseFloat(project.progress_pekerjaan || 0).toFixed(2);
            document.getElementById('input_progress_pekerjaan').value = prog;

            document.getElementById('input_pic').value = project.pic || "";
            
            // Format dates YYYY-MM-DD
            if (project.kontrak_awal) {
                document.getElementById('input_kontrak_awal').value = project.kontrak_awal.substring(0, 10);
            } else {
                document.getElementById('input_kontrak_awal').value = "";
            }

            if (project.kontrak_akhir) {
                document.getElementById('input_kontrak_akhir').value = project.kontrak_akhir.substring(0, 10);
            } else {
                document.getElementById('input_kontrak_akhir').value = "";
            }

            calculateRemainingDaysPreview();

            document.getElementById('projectModal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('projectModal').classList.add('hidden');
        }

        function calculateRemainingDaysPreview() {
            const endDateStr = document.getElementById('input_kontrak_akhir').value;
            const previewEl = document.getElementById('previewRemainingDays');

            if (!endDateStr) {
                previewEl.innerText = "—";
                previewEl.className = "text-xs font-bold font-mono px-2.5 py-1 rounded-full bg-slate-200 text-slate-700";
                return;
            }

            const today = new Date();
            today.setHours(0, 0, 0, 0);

            const endDate = new Date(endDateStr);
            endDate.setHours(0, 0, 0, 0);

            const diffTime = endDate - today;
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

            previewEl.innerText = `${diffDays} Hari`;

            if (diffDays < 0) {
                previewEl.className = "text-xs font-bold font-mono px-2.5 py-1 rounded-full bg-rose-100 text-rose-800 border border-rose-300";
            } else if (diffDays <= 14) {
                previewEl.className = "text-xs font-bold font-mono px-2.5 py-1 rounded-full bg-amber-100 text-amber-800 border border-amber-300";
            } else {
                previewEl.className = "text-xs font-bold font-mono px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800 border border-emerald-300";
            }
        }

        // Close on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeModal();
        });
    </script>

</x-app-layout>
