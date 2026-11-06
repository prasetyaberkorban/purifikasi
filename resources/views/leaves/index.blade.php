<x-app-layout title="Riwayat Izin / Cuti / WFA" subtitle="Daftar pengajuan izin kerja, cuti, dan WFA karyawan & magang" active="leaves">
    <div class="space-y-4">
        {{-- Toolbar Atas --}}
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <form method="GET" action="{{ route('leaves.index') }}" class="flex flex-wrap items-center gap-2 flex-1 max-w-xl">
                <div class="relative flex-1 min-w-[200px]">
                    <input type="search" name="q" value="{{ $search }}"
                           placeholder="Cari nama, kode, keterangan..."
                           class="field pr-9">
                    <button type="submit" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-ink-soft hover:text-forest">
                        <x-ui-icon name="filter" class="h-4 w-4" />
                    </button>
                </div>

                <select name="type" class="field text-xs w-auto" onchange="this.form.submit()">
                    <option value="">Semua Jenis Izin</option>
                    @foreach ($leaveTypes as $val => $lbl)
                        <option value="{{ $val }}" @selected($selectedType === $val)>{{ $lbl }}</option>
                    @endforeach
                </select>
            </form>

            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('leaves.export.pdf', request()->query()) }}" class="btn-ghost text-xs">
                    <x-ui-icon name="download" class="h-3.5 w-3.5" /> PDF
                </a>
                <a href="{{ route('leaves.export.excel', request()->query()) }}" class="btn-ghost text-xs">
                    <x-ui-icon name="download" class="h-3.5 w-3.5" /> Excel
                </a>
                <a href="{{ route('leaves.create') }}" class="btn-primary inline-flex items-center gap-2 self-start text-xs">
                    <x-ui-icon name="plus" class="h-4 w-4" />
                    <span>Ajukan Izin / Cuti / WFA</span>
                </a>
            </div>
        </div>

        {{-- Tabel Data --}}
        <div class="card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="border-b border-line bg-canvas/75 text-[0.7rem] uppercase tracking-wider text-ink-soft">
                        <tr>
                            <th class="px-4 py-3">Kode &amp; Tanggal</th>
                            <th class="px-4 py-3">Nama</th>
                            <th class="px-4 py-3">Jenis Izin</th>
                            <th class="px-4 py-3">Durasi</th>
                            <th class="px-4 py-3">Keterangan</th>
                            <th class="px-4 py-3">Lampiran</th>
                            <th class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line">
                        @forelse ($leaves as $item)
                            <tr class="hover:bg-canvas/50 transition">
                                <td class="px-4 py-3.5 whitespace-nowrap">
                                    <span class="font-mono font-bold text-ink">{{ $item->code }}</span>
                                    <p class="text-[0.7rem] text-ink-soft">{{ $item->created_at?->translatedFormat('d M Y') }}</p>
                                </td>
                                <td class="px-4 py-3.5 font-semibold text-ink whitespace-nowrap">
                                    {{ $item->name }}
                                </td>
                                <td class="px-4 py-3.5 whitespace-nowrap">
                                    @php
                                        $badgeColor = match ($item->leave_type) {
                                            'Izin sakit' => 'bg-red-50 text-red-700 border-red-200',
                                            'WFA' => 'bg-blue-50 text-blue-700 border-blue-200',
                                            'Cuti', 'Tahunan' => 'bg-amber-50 text-amber-800 border-amber-200',
                                            'Izin pelatihan / sertifikasi' => 'bg-purple-50 text-purple-700 border-purple-200',
                                            default => 'bg-forest/10 text-forest border-forest/20',
                                        };
                                    @endphp
                                    <span class="rounded-lg border px-2 py-0.5 text-[0.68rem] font-bold {{ $badgeColor }}">
                                        {{ $item->leave_type }}
                                    </span>
                                </td>
                                <td class="px-4 py-3.5 font-bold text-ink whitespace-nowrap">
                                    {{ $item->days_count }} Hari
                                </td>
                                <td class="px-4 py-3.5 max-w-xs truncate text-ink-soft" title="{{ $item->description }}">
                                    {{ $item->description }}
                                </td>
                                <td class="px-4 py-3.5 whitespace-nowrap">
                                    <a href="{{ route('leaves.download', $item) }}"
                                       class="inline-flex items-center gap-1.5 rounded-lg border border-line bg-canvas px-2.5 py-1 text-[0.7rem] font-semibold text-forest hover:bg-forest/10"
                                       title="Unduh {{ $item->attachment_name }}">
                                        <x-ui-icon name="download" class="h-3.5 w-3.5" />
                                        <span class="max-w-[100px] truncate">{{ $item->fileExtension() ?: 'File' }}</span>
                                        <span class="text-[0.65rem] opacity-60">({{ $item->formattedFileSize() }})</span>
                                    </a>
                                </td>
                                <td class="px-4 py-3.5 text-right whitespace-nowrap">
                                    <a href="{{ route('leaves.show', $item) }}" class="btn-ghost py-1 px-2.5 text-xs">
                                        Lihat Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-12 text-center text-sm text-ink-soft">
                                    Belum ada data pengajuan izin/cuti/WFA yang dicatat.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($leaves->hasPages())
                <div class="border-t border-line px-4 py-3">
                    {{ $leaves->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
