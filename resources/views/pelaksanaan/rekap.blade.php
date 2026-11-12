<x-app-layout title="Rekap Pelaksanaan" subtitle="Monitoring & Matriks Tracking Tahapan Proyek 2026 · Manajemen Proyek">

    @include('pelaksanaan._tabs')

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
                            </tr>
                        @empty
                            <tr>
                                <td colspan="15" class="py-12 text-center text-ink-soft">
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
                    Diperbarui otomatis dari Matriks Pelaksanaan
                </div>
            </div>
        </div>

    </div>

</x-app-layout>
