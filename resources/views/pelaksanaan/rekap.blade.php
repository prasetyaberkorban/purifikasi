<x-app-layout title="Rekap Pelaksanaan" subtitle="Monitoring & Matriks Tracking Tahapan Proyek 2026 · Manajemen Proyek">

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

        <!-- BAGIAN 1: TABEL RINGKASAN TAHAPAN PELAKSANAAN PEKERJAAN (BAGIAN ATAS) -->
        <div class="rounded-2xl border border-line bg-surface p-5 sm:p-6 shadow-sm">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-4 pb-3 border-b border-line">
                <div>
                    <h3 class="text-base font-bold text-ink flex items-center gap-2">
                        <svg class="w-5 h-5 text-forest" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        Ringkasan Tahapan Pelaksanaan Pekerjaan
                    </h3>
                    <p class="text-xs text-ink-soft mt-0.5">Agregasi jumlah proyek aktif pada masing-masing tahapan standar pelaksanaan.</p>
                </div>
                <div class="text-xs font-semibold px-3 py-1.5 rounded-xl bg-forest/10 text-forest border border-forest/20 self-start sm:self-auto">
                    Standar 7 Tahap Pekerjaan
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs sm:text-sm text-ink">
                    <thead class="bg-surface-alt/70 border-b border-line text-[0.72rem] font-bold uppercase tracking-wider text-ink-soft">
                        <tr>
                            <th class="py-3 px-3 text-center w-12">No.</th>
                            <th class="py-3 px-4 min-w-[220px]">Tahap Pelaksanaan</th>
                            <th class="py-3 px-4 text-center w-36">Jumlah Proyek</th>
                            <th class="py-3 px-4">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line">
                        @foreach ($ringkasanTahap as $tahap)
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="py-3 px-3 text-center font-mono text-ink-soft">
                                    {{ $tahap['no'] }}
                                </td>
                                <td class="py-3 px-4 font-semibold text-ink">
                                    {{ $tahap['tahap'] }}
                                </td>
                                <td class="py-3 px-4 text-center">
                                    @if ($tahap['jumlah_proyek'] > 0)
                                        <span class="inline-flex items-center justify-center px-3 py-0.5 rounded-full text-xs font-bold font-mono bg-forest/10 text-forest border border-forest/20">
                                            {{ $tahap['jumlah_proyek'] }} Proyek
                                        </span>
                                    @else
                                        <span class="text-xs font-mono text-ink-soft">0 Proyek</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-xs text-ink-soft leading-relaxed">
                                    {{ $tahap['keterangan'] }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="border-t-2 border-line bg-surface-alt/50 font-bold text-xs sm:text-sm text-ink">
                        <tr>
                            <td colspan="2" class="py-3.5 px-4 text-right">
                                Total Proyek dalam Pelaksanaan :
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <span class="inline-flex items-center justify-center px-3.5 py-1 rounded-xl text-xs font-extrabold font-mono bg-forest text-white shadow-sm">
                                    {{ $totalProjects }} Proyek
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-ink-soft text-xs font-normal">
                                Terhubung langsung dengan Matriks Pelaksanaan
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- BAGIAN 2: TABEL MATRIKS TRACKING TAHAPAN PROYEK (BAGIAN BAWAH) -->
        <div class="rounded-2xl border border-line bg-surface p-5 sm:p-6 shadow-sm">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-4 pb-3 border-b border-line">
                <div>
                    <h3 class="text-base font-bold text-ink flex items-center gap-2">
                        <svg class="w-5 h-5 text-forest" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                        </svg>
                        Matriks Tracking Tahapan Proyek
                    </h3>
                    <p class="text-xs text-ink-soft mt-0.5">Monitoring visual tahapan yang telah dilalui dan tahap yang sedang berlangsung.</p>
                </div>
                <div class="flex items-center gap-2 text-xs font-semibold text-ink-soft">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-800 border border-emerald-200">
                        <span class="text-emerald-600 font-bold">✓</span> Selesai
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-sky-50 text-sky-800 border border-sky-200">
                        <span class="text-sky-600 font-bold">●</span> Tahap Saat Ini
                    </span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs sm:text-sm text-ink border-collapse">
                    <thead class="bg-surface-alt/80 border-b border-line text-[0.72rem] font-bold uppercase tracking-wider text-ink-soft">
                        <tr>
                            <th class="py-3 px-2 text-center w-10">No.</th>
                            <th class="py-3 px-3 min-w-[200px]">Nama Project</th>
                            <th class="py-3 px-2 whitespace-nowrap">PIC</th>
                            <th class="py-3 px-3 whitespace-nowrap">Tahap Saat Ini</th>
                            <th class="py-3 px-2 text-center whitespace-nowrap">Urutan</th>
                            <th class="py-3 px-2 whitespace-nowrap">Kontrak Awal</th>
                            <th class="py-3 px-2 whitespace-nowrap">Kontrak Akhir</th>
                            <th class="py-3 px-2 text-center whitespace-nowrap">Sisa Hari</th>
                            
                            <!-- 7 Kolom Checklist Tahapan -->
                            <th class="py-3 px-2 text-center whitespace-nowrap bg-emerald-50/40 border-l border-line text-[0.68rem]" title="Kick Off Meeting">Kick Off</th>
                            <th class="py-3 px-2 text-center whitespace-nowrap bg-emerald-50/40 text-[0.68rem]" title="Pengurusan Izin Kerja dan Survey Awal">Izin & Survey</th>
                            <th class="py-3 px-2 text-center whitespace-nowrap bg-emerald-50/40 text-[0.68rem]" title="Pengambilan Data">Ambil Data</th>
                            <th class="py-3 px-2 text-center whitespace-nowrap bg-emerald-50/40 text-[0.68rem]" title="Pengolahan Data">Olah Data</th>
                            <th class="py-3 px-2 text-center whitespace-nowrap bg-emerald-50/40 text-[0.68rem]" title="Draft Laporan">Draft Laporan</th>
                            <th class="py-3 px-2 text-center whitespace-nowrap bg-emerald-50/40 text-[0.68rem]" title="Submit Laporan">Submit Lap.</th>
                            <th class="py-3 px-2 text-center whitespace-nowrap bg-emerald-50/40 text-[0.68rem]" title="Paparan Akhir / Paparan Final">Paparan Akhir</th>
                            
                            <!-- Kolom Aksi Edit -->
                            <th class="py-3 px-3 text-center whitespace-nowrap w-16 bg-slate-100/60 border-l border-line">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line">
                        @forelse ($trackingProjects as $idx => $item)
                            @php
                                $proj = $item['model'];
                                $days = $proj->calculated_remaining_days;
                            @endphp
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="py-3 px-2 text-center font-mono text-ink-soft">
                                    {{ $idx + 1 }}
                                </td>
                                <td class="py-3 px-3 font-semibold text-forest leading-snug">
                                    <div>{{ $proj->nama_project }}</div>
                                    @if ($proj->kode_project)
                                        <span class="text-[10px] font-mono text-ink-soft">[{{ $proj->kode_project }}]</span>
                                    @endif
                                </td>
                                <td class="py-3 px-2 text-xs font-semibold text-slate-700 whitespace-nowrap">
                                    {{ $proj->pic ?: '—' }}
                                </td>
                                <td class="py-3 px-3 text-xs font-medium text-ink whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold bg-slate-100 text-slate-800 border border-slate-200">
                                        {{ $proj->status_pekerjaan }}
                                    </span>
                                </td>
                                <td class="py-3 px-2 text-center font-mono text-xs font-bold text-slate-600 whitespace-nowrap">
                                    {{ $item['current_stage_order'] }}
                                </td>
                                <td class="py-3 px-2 font-mono text-xs text-ink-soft whitespace-nowrap">
                                    {{ $proj->kontrak_awal ? $proj->kontrak_awal->translatedFormat('d/m/y') : '—' }}
                                </td>
                                <td class="py-3 px-2 font-mono text-xs text-ink-soft whitespace-nowrap">
                                    {{ $proj->kontrak_akhir ? $proj->kontrak_akhir->translatedFormat('d/m/y') : '—' }}
                                </td>
                                <td class="py-3 px-2 text-center whitespace-nowrap">
                                    @if ($days !== null)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold font-mono border {{ $proj->remaining_days_badge_class }}">
                                            {{ $days }}
                                        </span>
                                    @else
                                        <span class="text-ink-soft font-mono text-xs">—</span>
                                    @endif
                                </td>

                                <!-- 7 Checklist Columns -->
                                @for ($stageNum = 1; $stageNum <= 7; $stageNum++)
                                    @php
                                        $state = $item['checklist'][$stageNum] ?? 'future';
                                    @endphp
                                    <td class="py-3 px-2 text-center border-l border-slate-100 {{ $state === 'active' ? 'bg-sky-50/70' : ($state === 'completed' ? 'bg-emerald-50/30' : '') }}">
                                        @if ($state === 'completed')
                                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-emerald-100 text-emerald-700 font-extrabold text-xs shadow-xs" title="Tahap Selesai">
                                                ✓
                                            </span>
                                        @elseif ($state === 'active')
                                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-sky-600 text-white font-extrabold text-sm shadow-md animate-pulse ring-2 ring-sky-300" title="Tahap Saat Ini">
                                                ●
                                            </span>
                                        @else
                                            <span class="text-slate-300 font-mono text-xs">—</span>
                                        @endif
                                    </td>
                                @endfor

                                <!-- Kolom Aksi Edit Button -->
                                <td class="py-3 px-2 text-center whitespace-nowrap border-l border-slate-100">
                                    <button type="button" onclick="openEditModal({{ json_encode($proj) }})"
                                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200 transition-colors shadow-xs"
                                            title="Ubah Data & Tahapan Proyek">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                        Edit
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="16" class="py-12 text-center text-ink-soft">
                                    Belum ada data proyek dalam pelaksanaan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Legend Keterangan Simbol -->
            <div class="mt-4 pt-3 border-t border-line flex flex-wrap items-center justify-between text-xs text-ink-soft gap-2">
                <div class="flex items-center gap-4">
                    <span class="font-semibold text-ink">Ket :</span>
                    <span class="inline-flex items-center gap-1.5 font-medium text-emerald-800">
                        <span class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-xs">✓</span>
                        = selesai
                    </span>
                    <span class="inline-flex items-center gap-1.5 font-medium text-sky-800">
                        <span class="w-5 h-5 rounded-full bg-sky-600 text-white flex items-center justify-center font-bold text-xs">●</span>
                        = tahap saat ini
                    </span>
                </div>
                <div class="font-mono text-[11px] text-ink-soft">
                    Klik tombol Edit pada baris proyek untuk mengubah status tahapan & progress.
                </div>
            </div>
        </div>

    </div>

    <!-- MODAL POP-UP EDIT PROYEK PELAKSANAAN (SOLID OPAQUE WHITE BACKGROUND) -->
    <div id="projectModal" class="fixed inset-0 z-50 hidden overflow-y-auto backdrop-blur-sm flex items-center justify-center p-3 sm:p-4" style="background-color: rgba(15, 23, 42, 0.75) !important;">
        <div class="relative w-full max-w-2xl max-h-[92vh] overflow-y-auto rounded-2xl shadow-2xl p-5 sm:p-7 my-auto animate-in fade-in zoom-in-95 duration-150" style="background-color: #ffffff !important; color: #0f172a !important; border: 1px solid #e2e8f0 !important;">
            <div class="flex items-center justify-between pb-3 border-b border-slate-200 mb-5">
                <div class="flex items-center gap-2.5">
                    <span class="grid h-9 w-9 place-items-center rounded-xl bg-forest/10 text-forest shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                    </span>
                    <div>
                        <h3 id="modalTitle" class="text-base font-bold text-slate-900">Ubah Data / Tahapan Proyek</h3>
                        <p class="text-xs text-slate-500">Perbarui status pekerjaan, progress bobot, atau PIC proyek.</p>
                    </div>
                </div>
                <button type="button" onclick="closeModal()" class="rounded-xl p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-700 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <form id="projectForm" method="POST" action="" class="space-y-4">
                @csrf
                <input type="hidden" name="_method" value="PUT">
                <input type="hidden" name="redirect_to" value="{{ url()->full() }}">

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Kode Project (Opsional)</label>
                        <input type="text" name="kode_project" id="input_kode_project" placeholder="Contoh: ENG-2026-001"
                               class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs sm:text-sm text-slate-900 focus:border-forest focus:outline-none focus:ring-1 focus:ring-forest" style="background-color: #ffffff !important; color: #0f172a !important;">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Pengelola <span class="text-rose-500">*</span></label>
                        <select name="pengelola" id="input_pengelola" required
                                class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs sm:text-sm text-slate-900 focus:border-forest focus:outline-none focus:ring-1 focus:ring-forest" style="background-color: #ffffff !important; color: #0f172a !important;">
                            @foreach ($pengelolaOptions as $p)
                                <option value="{{ $p }}">{{ $p }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Project <span class="text-rose-500">*</span></label>
                    <textarea name="nama_project" id="input_nama_project" rows="2" required placeholder="Tuliskan nama lengkap pekerjaan / kajian..."
                              class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs sm:text-sm text-slate-900 focus:border-forest focus:outline-none focus:ring-1 focus:ring-forest" style="background-color: #ffffff !important; color: #0f172a !important;"></textarea>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Status Pekerjaan <span class="text-rose-500">*</span></label>
                        <select name="status_pekerjaan" id="input_status_pekerjaan" required onchange="handleStatusChange(this)"
                                class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs sm:text-sm text-slate-900 focus:border-forest focus:outline-none focus:ring-1 focus:ring-forest" style="background-color: #ffffff !important; color: #0f172a !important;">
                            @foreach ($refTahap as $st)
                                <option value="{{ $st->tahap }}" data-bobot="{{ $st->bobot }}">{{ $st->tahap }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Progress Pekerjaan <span class="text-rose-500">*</span></label>
                        <select name="progress_pekerjaan" id="input_progress_pekerjaan" required
                                class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs sm:text-sm text-slate-900 focus:border-forest focus:outline-none focus:ring-1 focus:ring-forest" style="background-color: #ffffff !important; color: #0f172a !important;">
                            @foreach ($progressOptions as $pr)
                                <option value="{{ $pr['value'] }}">{{ $pr['label'] }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-xs font-semibold text-slate-700">PIC (Penanggung Jawab)</label>
                        <span class="text-[11px] text-slate-500">Ketik nama baru atau pilih dari daftar</span>
                    </div>
                    <input type="text" name="pic" id="input_pic" list="picOptionsList" placeholder="Ketik nama PIC..."
                           class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs sm:text-sm text-slate-900 focus:border-forest focus:outline-none focus:ring-1 focus:ring-forest" style="background-color: #ffffff !important; color: #0f172a !important;">
                    <datalist id="picOptionsList">
                        @foreach ($refPic as $picName)
                            <option value="{{ $picName }}"></option>
                        @endforeach
                    </datalist>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Kontrak Awal</label>
                        <input type="date" name="kontrak_awal" id="input_kontrak_awal"
                               class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs sm:text-sm text-slate-900 focus:border-forest focus:outline-none focus:ring-1 focus:ring-forest" style="background-color: #ffffff !important; color: #0f172a !important;">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Kontrak Akhir</label>
                        <input type="date" name="kontrak_akhir" id="input_kontrak_akhir" onchange="calculateRemainingDaysPreview()"
                               class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs sm:text-sm text-slate-900 focus:border-forest focus:outline-none focus:ring-1 focus:ring-forest" style="background-color: #ffffff !important; color: #0f172a !important;">
                    </div>
                </div>

                <div class="rounded-xl border border-slate-200 p-3 flex items-center justify-between" style="background-color: #f8fafc !important;">
                    <div>
                        <div class="text-xs font-semibold text-slate-700">Estimasi Sisa Hari (Remaining Days):</div>
                        <div class="text-[11px] text-slate-500">Dihitung otomatis dari selisih Kontrak Akhir dengan hari ini.</div>
                    </div>
                    <span id="previewRemainingDays" class="text-xs font-bold font-mono px-2.5 py-1 rounded-full bg-slate-200 text-slate-700">
                        —
                    </span>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200 mt-6">
                    <button type="button" onclick="closeModal()" class="btn-secondary px-4 py-2 text-xs sm:text-sm font-semibold">
                        Batal
                    </button>
                    <button type="submit" class="btn-primary px-5 py-2 text-xs sm:text-sm font-semibold shadow-sm">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const updateRouteBase = "{{ url('manajemen-proyek/pelaksanaan/matriks') }}";

        function openEditModal(project) {
            document.getElementById('modalTitle').innerText = "Ubah Data / Tahapan: " + (project.nama_project || '');
            document.getElementById('projectForm').action = `${updateRouteBase}/${project.id}`;

            document.getElementById('input_kode_project').value = project.kode_project || "";
            document.getElementById('input_nama_project').value = project.nama_project || "";
            document.getElementById('input_pengelola').value = project.pengelola || "Semarang";
            document.getElementById('input_status_pekerjaan').value = project.status_pekerjaan || "";
            
            const prog = parseFloat(project.progress_pekerjaan || 0).toFixed(2);
            document.getElementById('input_progress_pekerjaan').value = prog;

            document.getElementById('input_pic').value = project.pic || "";
            
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

        function handleStatusChange(selectEl) {
            const selectedOpt = selectEl.options[selectEl.selectedIndex];
            const autoBobot = selectedOpt.getAttribute('data-bobot');
            if (autoBobot !== null && autoBobot !== undefined) {
                const formatted = parseFloat(autoBobot).toFixed(2);
                const progSelect = document.getElementById('input_progress_pekerjaan');
                for (let i = 0; i < progSelect.options.length; i++) {
                    if (parseFloat(progSelect.options[i].value).toFixed(2) === formatted) {
                        progSelect.selectedIndex = i;
                        break;
                    }
                }
            }
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

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeModal();
        });
    </script>

</x-app-layout>
