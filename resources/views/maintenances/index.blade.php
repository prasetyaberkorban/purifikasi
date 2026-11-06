<x-app-layout title="Riwayat Service & Pemeliharaan" subtitle="Dokumentasi pelaporan service dan perbaikan aset kantor" active="services">
    <div class="space-y-4">
        {{-- Toolbar Atas --}}
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <form method="GET" action="{{ route('services.index') }}" class="flex-1 max-w-md">
                <div class="relative">
                    <input type="search" name="q" value="{{ $search }}"
                           placeholder="Cari kode, barang, PIC, tempat service..."
                           class="field pr-9">
                    <button type="submit" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-ink-soft hover:text-forest">
                        <x-ui-icon name="filter" class="h-4 w-4" />
                    </button>
                </div>
            </form>

            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('services.export.pdf', request()->query()) }}" class="btn-ghost text-xs">
                    <x-ui-icon name="download" class="h-3.5 w-3.5" /> PDF
                </a>
                <a href="{{ route('services.export.excel', request()->query()) }}" class="btn-ghost text-xs">
                    <x-ui-icon name="download" class="h-3.5 w-3.5" /> Excel
                </a>
                <a href="{{ route('services.create') }}" class="btn-primary inline-flex items-center gap-2 self-start text-xs">
                    <x-ui-icon name="plus" class="h-4 w-4" />
                    <span>Isi Formulir Baru</span>
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
                            <th class="px-4 py-3">PIC</th>
                            <th class="px-4 py-3">Barang &amp; Merk</th>
                            <th class="px-4 py-3">Tempat Service</th>
                            <th class="px-4 py-3">Riwayat</th>
                            <th class="px-4 py-3">Foto &amp; Nota</th>
                            <th class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    @forelse ($maintenances as $item)
                        <tbody class="divide-y divide-line" x-data="{ showFisik: false, showNota: false }">
                            <tr class="hover:bg-canvas/50 transition">
                                <td class="px-4 py-3.5 whitespace-nowrap">
                                    <span class="font-mono font-bold text-ink">{{ $item->code }}</span>
                                    <p class="text-[0.7rem] text-ink-soft">{{ $item->service_date?->translatedFormat('d M Y') }}</p>
                                </td>
                                <td class="px-4 py-3.5 font-semibold text-ink">
                                    {{ $item->pic_name }}
                                </td>
                                <td class="px-4 py-3.5">
                                    <p class="font-bold text-ink">{{ $item->asset_name }}</p>
                                    <p class="text-[0.7rem] text-ink-soft">{{ $item->brand_type }}</p>
                                </td>
                                <td class="px-4 py-3.5 max-w-xs truncate text-ink-soft" title="{{ $item->service_location }}">
                                    {{ $item->service_location }}
                                </td>
                                <td class="px-4 py-3.5 whitespace-nowrap">
                                    @if ($item->previously_serviced === 'sudah')
                                        <span class="rounded bg-amber-100 px-2 py-0.5 text-[0.68rem] font-bold text-amber-800">Sudah Pernah</span>
                                    @else
                                        <span class="rounded bg-emerald-100 px-2 py-0.5 text-[0.68rem] font-bold text-emerald-800">Baru Pertama</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5 whitespace-nowrap">
                                    <div class="flex items-center gap-1.5">
                                        @if ($item->condition_photo_path)
                                            <button type="button" @click="showFisik = !showFisik"
                                                    class="inline-flex items-center gap-1 rounded-lg border px-2 py-1 text-[0.7rem] font-semibold transition active:scale-95"
                                                    :class="showFisik ? 'border-forest bg-forest text-white' : 'border-line bg-canvas text-forest hover:bg-forest/10'">
                                                <template x-if="!showFisik">
                                                    <span class="inline-flex items-center gap-1">
                                                        <x-ui-icon name="eye" class="h-3.5 w-3.5" /> Fisik
                                                    </span>
                                                </template>
                                                <template x-if="showFisik">
                                                    <span class="inline-flex items-center gap-1">
                                                        <x-ui-icon name="eye-off" class="h-3.5 w-3.5" /> Tutup
                                                    </span>
                                                </template>
                                            </button>
                                        @endif
                                        @if ($item->invoice_photo_path)
                                            <button type="button" @click="showNota = !showNota"
                                                    class="inline-flex items-center gap-1 rounded-lg border px-2 py-1 text-[0.7rem] font-semibold transition active:scale-95"
                                                    :class="showNota ? 'border-forest bg-forest text-white' : 'border-line bg-canvas text-forest hover:bg-forest/10'">
                                                <template x-if="!showNota">
                                                    <span class="inline-flex items-center gap-1">
                                                        <x-ui-icon name="eye" class="h-3.5 w-3.5" /> Nota
                                                    </span>
                                                </template>
                                                <template x-if="showNota">
                                                    <span class="inline-flex items-center gap-1">
                                                        <x-ui-icon name="eye-off" class="h-3.5 w-3.5" /> Tutup
                                                    </span>
                                                </template>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-3.5 text-right whitespace-nowrap">
                                    <a href="{{ route('services.show', $item) }}" class="btn-ghost py-1 px-2.5 text-xs">
                                        Lihat Detail
                                    </a>
                                </td>
                            </tr>

                            {{-- Area Toggle Foto --}}
                            <tr x-show="showFisik || showNota" x-cloak class="bg-canvas/70">
                                <td colspan="7" class="p-4">
                                    <div class="flex flex-wrap items-start gap-4">
                                        <div x-show="showFisik" class="space-y-1.5 rounded-xl border border-line bg-white p-3 shadow-sm">
                                            <div class="flex items-center justify-between gap-3 border-b border-line pb-1.5">
                                                <span class="text-xs font-bold text-ink">Foto Kondisi Fisik: {{ $item->asset_name }}</span>
                                                <button type="button" @click="showFisik = false" class="text-ink-soft hover:text-ink">
                                                    <x-ui-icon name="x" class="h-3.5 w-3.5" />
                                                </button>
                                            </div>
                                            <img src="{{ asset('storage/'.$item->condition_photo_path) }}" alt="Foto Fisik"
                                                 class="max-h-64 rounded-lg object-contain ring-1 ring-line">
                                        </div>

                                        <div x-show="showNota" class="space-y-1.5 rounded-xl border border-line bg-white p-3 shadow-sm">
                                            <div class="flex items-center justify-between gap-3 border-b border-line pb-1.5">
                                                <span class="text-xs font-bold text-ink">Foto Nota Service ({{ $item->code }})</span>
                                                <button type="button" @click="showNota = false" class="text-ink-soft hover:text-ink">
                                                    <x-ui-icon name="x" class="h-3.5 w-3.5" />
                                                </button>
                                            </div>
                                            <img src="{{ asset('storage/'.$item->invoice_photo_path) }}" alt="Foto Nota"
                                                 class="max-h-64 rounded-lg object-contain ring-1 ring-line">
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    @empty
                        <tbody>
                            <tr>
                                <td colspan="7" class="px-4 py-12 text-center text-sm text-ink-soft">
                                    Belum ada data formulir service yang dicatat.
                                </td>
                            </tr>
                        </tbody>
                    @endforelse
                </table>
            </div>

            @if ($maintenances->hasPages())
                <div class="border-t border-line px-4 py-3">
                    {{ $maintenances->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
