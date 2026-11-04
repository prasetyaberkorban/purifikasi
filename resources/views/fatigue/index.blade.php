<x-app-layout title="Riwayat Penilaian Fatik Individual" subtitle="Daftar evaluasi kebugaran dan kesiapan kerja karyawan" active="fatigue">
    <div class="space-y-4">
        {{-- Toolbar Atas --}}
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <form method="GET" action="{{ route('fatigue.index') }}" class="flex-1 max-w-md">
                <div class="relative">
                    <input type="search" name="q" value="{{ $search }}"
                           placeholder="Cari nama karyawan, kode..."
                           class="field pr-9">
                    <button type="submit" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-ink-soft hover:text-forest">
                        <x-ui-icon name="filter" class="h-4 w-4" />
                    </button>
                </div>
            </form>

            <a href="{{ route('fatigue.create') }}" class="btn-primary inline-flex items-center gap-2 self-start">
                <x-ui-icon name="plus" class="h-4 w-4" />
                <span>Isi Penilaian Baru</span>
            </a>
        </div>

        {{-- Tabel Data --}}
        <div class="card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="border-b border-line bg-canvas/75 text-[0.7rem] uppercase tracking-wider text-ink-soft">
                        <tr>
                            <th class="px-4 py-3">Kode &amp; Tanggal</th>
                            <th class="px-4 py-3">Nama Karyawan</th>
                            <th class="px-4 py-3">Jam Tidur</th>
                            <th class="px-4 py-3">Sakit / Obat</th>
                            <th class="px-4 py-3">Mampu Berkendara</th>
                            <th class="px-4 py-3">Kondisi</th>
                            <th class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line">
                        @forelse ($assessments as $item)
                            <tr class="hover:bg-canvas/50 transition">
                                <td class="px-4 py-3.5 whitespace-nowrap">
                                    <span class="font-mono font-bold text-ink">{{ $item->code }}</span>
                                    <p class="text-[0.7rem] text-ink-soft">{{ $item->assessment_date?->translatedFormat('d M Y') }}</p>
                                </td>
                                <td class="px-4 py-3.5 font-semibold text-ink whitespace-nowrap">
                                    {{ $item->name }}
                                </td>
                                <td class="px-4 py-3.5 whitespace-nowrap">
                                    @if ($item->sleep_hours === 'kurang dari 6 jam')
                                        <span class="rounded bg-red-50 text-red-700 px-2 py-0.5 font-medium border border-red-200 text-[0.7rem]">&lt; 6 Jam</span>
                                    @else
                                        <span class="rounded bg-emerald-50 text-emerald-800 px-2 py-0.5 font-medium border border-emerald-200 text-[0.7rem]">&gt; 6 Jam</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5 text-ink-soft whitespace-nowrap">
                                    <span>Sakit: <strong>{{ $item->recent_illness }}</strong></span> &middot;
                                    <span>Obat: <strong>{{ $item->medication_drowsy }}</strong></span>
                                </td>
                                <td class="px-4 py-3.5 whitespace-nowrap">
                                    @if ($item->fit_to_drive === 'Ya')
                                        <span class="text-emerald-700 font-bold">Mampu (Ya)</span>
                                    @else
                                        <span class="text-red-600 font-bold">Tidak Mampu</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5 whitespace-nowrap">
                                    @if ($item->isFullyFit())
                                        <span class="rounded-lg bg-leaf-50 px-2.5 py-1 text-[0.68rem] font-bold text-forest border border-leaf/30">
                                            Fit / Prima
                                        </span>
                                    @else
                                        <span class="rounded-lg bg-amber-50 px-2.5 py-1 text-[0.68rem] font-bold text-amber-800 border border-amber-200">
                                            Perlu Perhatian
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5 text-right whitespace-nowrap">
                                    <a href="{{ route('fatigue.show', $item) }}" class="btn-ghost py-1 px-2.5 text-xs">
                                        Lihat Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-12 text-center text-sm text-ink-soft">
                                    Belum ada data penilaian fatik yang dicatat.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($assessments->hasPages())
                <div class="border-t border-line px-4 py-3">
                    {{ $assessments->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
