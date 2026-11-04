<x-app-layout title="Riwayat Daftar Hadir" subtitle="Daftar dan arsip kehadiran rapat serta diskusi internal" active="attendances">
    <div class="space-y-4">
        {{-- Toolbar Atas --}}
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <form method="GET" action="{{ route('attendances.index') }}" class="flex-1 max-w-md">
                <div class="relative">
                    <input type="search" name="q" value="{{ $search }}"
                           placeholder="Cari kode, nama, cabang, kegiatan, pimpinan..."
                           class="field pr-9">
                    <button type="submit" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-ink-soft hover:text-forest">
                        <x-ui-icon name="filter" class="h-4 w-4" />
                    </button>
                </div>
            </form>

            <a href="{{ route('attendances.create') }}" class="btn-primary inline-flex items-center gap-2 self-start">
                <x-ui-icon name="plus" class="h-4 w-4" />
                <span>Isi Daftar Hadir Baru</span>
            </a>
        </div>

        {{-- Tabel Data --}}
        <div class="card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="border-b border-line bg-canvas/75 text-[0.7rem] uppercase tracking-wider text-ink-soft">
                        <tr>
                            <th class="px-4 py-3">Kode &amp; Tanggal</th>
                            <th class="px-4 py-3">Peserta &amp; Jabatan</th>
                            <th class="px-4 py-3">Cabang</th>
                            <th class="px-4 py-3">Jenis Kegiatan</th>
                            <th class="px-4 py-3">Pimpinan / Pemateri</th>
                            <th class="px-4 py-3">Materi / Bahasan</th>
                            <th class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line">
                        @forelse ($attendances as $item)
                            <tr class="hover:bg-canvas/50 transition">
                                <td class="px-4 py-3.5 whitespace-nowrap">
                                    <span class="font-mono font-bold text-ink">{{ $item->code }}</span>
                                    <p class="text-[0.7rem] text-ink-soft">{{ $item->event_date?->translatedFormat('d M Y') }}</p>
                                </td>
                                <td class="px-4 py-3.5">
                                    <p class="font-bold text-ink">{{ $item->full_name }}</p>
                                    <p class="text-[0.7rem] text-ink-soft">{{ $item->position }}</p>
                                </td>
                                <td class="px-4 py-3.5 whitespace-nowrap">
                                    <span class="inline-flex items-center rounded-lg bg-canvas px-2.5 py-1 text-[0.72rem] font-bold text-forest border border-line">
                                        {{ $item->branch }}
                                    </span>
                                </td>
                                <td class="px-4 py-3.5">
                                    <span class="inline-block rounded-lg bg-leaf-50 px-2.5 py-1 text-[0.72rem] font-bold text-forest border border-leaf/30">
                                        {{ $item->activity_type }}
                                    </span>
                                </td>
                                <td class="px-4 py-3.5 font-semibold text-ink">
                                    {{ $item->meeting_leader }}
                                </td>
                                <td class="px-4 py-3.5 max-w-xs truncate text-ink-soft" title="{{ $item->material_summary }}">
                                    {{ $item->material_summary }}
                                </td>
                                <td class="px-4 py-3.5 text-right whitespace-nowrap">
                                    <a href="{{ route('attendances.show', $item) }}"
                                       class="inline-flex items-center gap-1 rounded-lg border border-line px-2.5 py-1 text-[0.72rem] font-semibold text-forest hover:border-leaf/40 hover:bg-leaf-50">
                                        <span>Detail</span>
                                        <x-ui-icon name="arrow-up-right" class="h-3 w-3" />
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-12 text-center text-ink-soft">
                                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-canvas text-forest/70 mb-3">
                                        <x-ui-icon name="clipboard-check" class="h-6 w-6" />
                                    </div>
                                    <p class="text-sm font-semibold text-ink">Belum ada data presensi daftar hadir</p>
                                    <p class="text-xs text-ink-soft mt-1">Daftar hadir rapat yang telah diisi akan tercatat di sini.</p>
                                    <a href="{{ route('attendances.create') }}" class="btn-primary inline-flex items-center gap-2 mt-4 text-xs">
                                        <x-ui-icon name="plus" class="h-4 w-4" />
                                        <span>Isi Daftar Hadir Sekarang</span>
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($attendances->hasPages())
                <div class="border-t border-line px-4 py-3">
                    {{ $attendances->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
