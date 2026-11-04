<x-app-layout title="Riwayat Realisasi Pengadaan" subtitle="Daftar laporan realisasi pengadaan dan aset kantor" active="procurements">
    <div class="space-y-4">
        {{-- Toolbar Atas --}}
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <form method="GET" action="{{ route('procurements.index') }}" class="flex-1 max-w-md">
                <div class="relative">
                    <input type="search" name="q" value="{{ $search }}"
                           placeholder="Cari kode, barang, PIC, toko..."
                           class="field pr-9">
                    <button type="submit" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-ink-soft hover:text-forest">
                        <x-ui-icon name="filter" class="h-4 w-4" />
                    </button>
                </div>
            </form>

            <a href="{{ route('procurements.create') }}" class="btn-primary inline-flex items-center gap-2 self-start">
                <x-ui-icon name="plus" class="h-4 w-4" />
                <span>Isi Formulir Realisasi</span>
            </a>
        </div>

        {{-- Tabel Data --}}
        <div class="card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="border-b border-line bg-canvas/75 text-[0.7rem] uppercase tracking-wider text-ink-soft">
                        <tr>
                            <th class="px-4 py-3">Kode &amp; Tanggal</th>
                            <th class="px-4 py-3">PIC</th>
                            <th class="px-4 py-3">Nama Barang &amp; Merk</th>
                            <th class="px-4 py-3">Jumlah / Ukuran</th>
                            <th class="px-4 py-3">Nama Toko / Link</th>
                            <th class="px-4 py-3">Bukti File</th>
                            <th class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line">
                        @forelse ($procurements as $item)
                            <tr class="hover:bg-canvas/50 transition">
                                <td class="px-4 py-3.5 whitespace-nowrap">
                                    <span class="font-mono font-bold text-ink">{{ $item->code }}</span>
                                    <p class="text-[0.7rem] text-ink-soft">{{ $item->realization_date?->translatedFormat('d M Y') }}</p>
                                </td>
                                <td class="px-4 py-3.5 font-semibold text-ink">
                                    {{ $item->pic_name }}
                                </td>
                                <td class="px-4 py-3.5">
                                    <p class="font-bold text-ink">{{ $item->item_name }}</p>
                                    <p class="text-[0.7rem] text-ink-soft">{{ $item->brand_type }}</p>
                                </td>
                                <td class="px-4 py-3.5 font-medium text-ink">
                                    {{ $item->quantity_size }}
                                </td>
                                <td class="px-4 py-3.5 max-w-xs truncate text-ink-soft">
                                    @if (str_starts_with($item->store_name, 'http://') || str_starts_with($item->store_name, 'https://'))
                                        <a href="{{ $item->store_name }}" target="_blank" class="text-forest underline hover:text-leaf inline-flex items-center gap-1">
                                            <span>Link Pembelian</span>
                                            <x-ui-icon name="arrow-up-right" class="h-3 w-3" />
                                        </a>
                                    @else
                                        <span title="{{ $item->store_name }}">{{ $item->store_name }}</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5 whitespace-nowrap">
                                    <a href="{{ route('procurements.download', $item) }}"
                                       class="inline-flex items-center gap-1.5 rounded-lg border border-line bg-canvas px-2.5 py-1 text-[0.7rem] font-semibold text-forest hover:bg-forest/10"
                                       title="Unduh {{ $item->proof_file_name }}">
                                        <x-ui-icon name="download" class="h-3.5 w-3.5" />
                                        <span class="max-w-[100px] truncate">{{ $item->fileExtension() ?: 'File' }}</span>
                                        <span class="text-[0.65rem] opacity-60">({{ $item->formattedFileSize() }})</span>
                                    </a>
                                </td>
                                <td class="px-4 py-3.5 text-right whitespace-nowrap">
                                    <a href="{{ route('procurements.show', $item) }}" class="btn-ghost py-1 px-2.5 text-xs">
                                        Lihat Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-12 text-center text-sm text-ink-soft">
                                    Belum ada data formulir realisasi pengadaan yang dicatat.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($procurements->hasPages())
                <div class="border-t border-line px-4 py-3">
                    {{ $procurements->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
