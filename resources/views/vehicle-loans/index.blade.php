<x-app-layout title="Riwayat Peminjaman Kendaraan" subtitle="Daftar dan arsip peminjaman kendaraan operasional kantor" active="vehicle_loans">
    <div class="space-y-4"
         x-data="{
             selected: [],
             allIds: {{ json_encode($loans->pluck('id')) }},
             toggleAll() {
                 if (this.selected.length === this.allIds.length) {
                     this.selected = [];
                 } else {
                     this.selected = [...this.allIds];
                 }
             }
         }">
        {{-- Toolbar Atas --}}
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <form method="GET" action="{{ route('vehicle-loans.index') }}" class="flex-1 max-w-md">
                <div class="relative">
                    <input type="search" name="q" value="{{ $search }}"
                           placeholder="Cari kode, PIC, driver, kendaraan, tujuan..."
                           class="field pr-9">
                    <button type="submit" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-ink-soft hover:text-forest">
                        <x-ui-icon name="filter" class="h-4 w-4" />
                    </button>
                </div>
            </form>

            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('vehicle-loans.export.pdf', request()->query()) }}" class="btn-ghost text-xs">
                    <x-ui-icon name="download" class="h-3.5 w-3.5" /> PDF
                </a>
                <a href="{{ route('vehicle-loans.export.excel', request()->query()) }}" class="btn-ghost text-xs">
                    <x-ui-icon name="download" class="h-3.5 w-3.5" /> Excel
                </a>
                <a href="{{ route('vehicle-loans.create') }}" class="btn-primary inline-flex items-center gap-2 self-start text-xs">
                    <x-ui-icon name="plus" class="h-4 w-4" />
                    <span>Isi Formulir Peminjaman</span>
                </a>
            </div>
        </div>

        {{-- Bulk Actions Bar --}}
        <div x-show="selected.length > 0" x-cloak x-transition
             class="flex flex-wrap items-center justify-between gap-3 rounded-xl bg-forest-900/5 border border-forest/20 p-2.5 sm:px-4">
            <div class="flex items-center gap-2 text-xs font-semibold text-forest">
                <span class="inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-forest text-white text-[0.7rem] px-1.5" x-text="selected.length"></span>
                <span>data peminjaman kendaraan dipilih</span>
            </div>

            <form method="POST" action="{{ route('vehicle-loans.bulk-destroy') }}"
                  data-confirm-title="Hapus Data Peminjaman Terpilih?"
                  :data-confirm-delete="'Apakah Anda yakin ingin menghapus ' + selected.length + ' data peminjaman kendaraan terpilih secara permanen?'"
                  class="inline">
                @csrf
                @method('DELETE')
                <input type="hidden" name="ids_csv" :value="selected.join(',')">
                <template x-for="id in selected" :key="id">
                    <input type="hidden" name="ids[]" :value="id">
                </template>
                <button type="submit" class="btn-ghost text-red-600 hover:bg-red-50 text-xs py-1.5 px-3">
                    <x-ui-icon name="trash" class="h-4 w-4" />
                    <span>Hapus Terpilih (<span x-text="selected.length"></span>)</span>
                </button>
            </form>
        </div>

        {{-- Tabel Data --}}
        <div class="card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="border-b border-line bg-canvas/75 text-[0.7rem] uppercase tracking-wider text-ink-soft">
                        <tr>
                            <th class="px-3 py-3 text-center" style="width: 38px;">
                                <input type="checkbox"
                                       @change="toggleAll()"
                                       :checked="allIds.length > 0 && selected.length === allIds.length"
                                       class="rounded border-line text-forest focus:ring-forest cursor-pointer"
                                       title="Pilih Semua">
                            </th>
                            <th class="px-4 py-3">Kode &amp; Tanggal</th>
                            <th class="px-4 py-3">PIC &amp; Driver</th>
                            <th class="px-4 py-3">Kendaraan &amp; KM</th>
                            <th class="px-4 py-3">Tujuan Peminjaman</th>
                            <th class="px-4 py-3">Kebersihan</th>
                            <th class="px-4 py-3">Speedometer</th>
                            <th class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    @forelse ($loans as $item)
                        <tbody class="divide-y divide-line" x-data="{ showBerangkat: false, showKembali: false }">
                            <tr class="hover:bg-canvas/50 transition" :class="selected.includes({{ $item->id }}) ? '!bg-forest/10' : ''">
                                <td class="px-3 py-3.5 text-center">
                                    <input type="checkbox"
                                           :value="{{ $item->id }}"
                                           x-model="selected"
                                           class="rounded border-line text-forest focus:ring-forest cursor-pointer">
                                </td>
                                <td class="px-4 py-3.5 whitespace-nowrap">
                                    <span class="font-mono font-bold text-ink">{{ $item->code }}</span>
                                    <p class="text-[0.7rem] text-ink-soft">
                                        {{ $item->departure_date?->translatedFormat('d M') }} - {{ $item->return_date?->translatedFormat('d M Y') }}
                                    </p>
                                </td>
                                <td class="px-4 py-3.5">
                                    <p class="font-bold text-ink">{{ $item->pic_name }}</p>
                                    <p class="text-[0.7rem] text-ink-soft">Driver: {{ $item->driver_name }}</p>
                                </td>
                                <td class="px-4 py-3.5 whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1.5 rounded-lg bg-leaf-50 px-2 py-0.5 text-[0.72rem] font-bold text-forest border border-leaf/30">
                                        <x-ui-icon name="car" class="h-3 w-3" />
                                        {{ $item->vehicle_type }}
                                    </span>
                                    <p class="mt-0.5 font-mono text-[0.7rem] text-ink-soft">{{ $item->departure_km }} KM</p>
                                </td>
                                <td class="px-4 py-3.5 max-w-xs truncate text-ink-soft" title="{{ $item->purpose }}">
                                    {{ $item->purpose }}
                                </td>
                                <td class="px-4 py-3.5">
                                    <span class="rounded px-2 py-0.5 text-[0.68rem] font-bold {{ str_contains(strtolower($item->cleanliness_condition), 'dibersihkan') ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                        {{ $item->cleanliness_condition }}
                                    </span>
                                </td>
                                <td class="px-4 py-3.5 whitespace-nowrap">
                                    <div class="flex items-center gap-1.5">
                                        @if ($item->departure_speedometer_photo_path)
                                            <button type="button" @click="showBerangkat = !showBerangkat"
                                                    class="inline-flex items-center gap-1 rounded-lg border px-2 py-1 text-[0.7rem] font-semibold transition active:scale-95"
                                                    :class="showBerangkat ? 'border-forest bg-forest text-white' : 'border-line bg-canvas text-forest hover:bg-forest/10'">
                                                <template x-if="!showBerangkat">
                                                    <span class="inline-flex items-center gap-1">
                                                        <x-ui-icon name="eye" class="h-3.5 w-3.5" /> Berangkat
                                                    </span>
                                                </template>
                                                <template x-if="showBerangkat">
                                                    <span class="inline-flex items-center gap-1">
                                                        <x-ui-icon name="eye-off" class="h-3.5 w-3.5" /> Tutup
                                                    </span>
                                                </template>
                                            </button>
                                        @endif
                                        @if ($item->return_speedometer_photo_path)
                                            <button type="button" @click="showKembali = !showKembali"
                                                    class="inline-flex items-center gap-1 rounded-lg border px-2 py-1 text-[0.7rem] font-semibold transition active:scale-95"
                                                    :class="showKembali ? 'border-forest bg-forest text-white' : 'border-line bg-canvas text-forest hover:bg-forest/10'">
                                                <template x-if="!showKembali">
                                                    <span class="inline-flex items-center gap-1">
                                                        <x-ui-icon name="eye" class="h-3.5 w-3.5" /> Kembali
                                                    </span>
                                                </template>
                                                <template x-if="showKembali">
                                                    <span class="inline-flex items-center gap-1">
                                                        <x-ui-icon name="eye-off" class="h-3.5 w-3.5" /> Tutup
                                                    </span>
                                                </template>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-3.5 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <a href="{{ route('vehicle-loans.show', $item) }}"
                                           class="inline-flex items-center gap-1 rounded-lg border border-line px-2.5 py-1 text-[0.72rem] font-semibold text-forest hover:border-leaf/40 hover:bg-leaf-50">
                                            <span>Detail</span>
                                            <x-ui-icon name="arrow-up-right" class="h-3 w-3" />
                                        </a>
                                        @if (auth()->user()->isAdmin() || $item->user_id === auth()->id())
                                            <form method="POST" action="{{ route('vehicle-loans.destroy', $item) }}"
                                                  data-confirm-title="Hapus Riwayat Peminjaman?"
                                                  data-confirm-delete="Apakah Anda yakin ingin menghapus data peminjaman {{ $item->code }} ({{ $item->vehicle_type }})? Foto speedometer juga akan dihapus permanen."
                                                  class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="rounded-lg p-1.5 text-ink-soft hover:bg-red-50 hover:text-red-600 transition" title="Hapus {{ $item->code }}">
                                                    <x-ui-icon name="trash" class="h-4 w-4" />
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>

                            {{-- Dropdown Preview Foto Speedometer Berangkat --}}
                            @if ($item->departure_speedometer_photo_path)
                                <tr x-show="showBerangkat" x-cloak class="bg-canvas/30">
                                    <td colspan="8" class="p-4">
                                        <div class="rounded-xl border border-line bg-white p-3 shadow-xs max-w-sm">
                                            <p class="text-[0.7rem] font-bold text-forest mb-2 flex items-center justify-between">
                                                <span>Foto Speedometer Berangkat:</span>
                                                <a href="{{ $item->departureSpeedometerPhotoUrl() }}" target="_blank" class="text-xs text-leaf underline">Buka Ukuran Penuh</a>
                                            </p>
                                            <img src="{{ $item->departureSpeedometerPhotoUrl() }}"
                                                 alt="Speedometer Berangkat {{ $item->code }}"
                                                 class="rounded-lg object-contain w-full max-h-64 border border-line">
                                        </div>
                                    </td>
                                </tr>
                            @endif

                            {{-- Dropdown Preview Foto Speedometer Kembali --}}
                            @if ($item->return_speedometer_photo_path)
                                <tr x-show="showKembali" x-cloak class="bg-canvas/30">
                                    <td colspan="8" class="p-4">
                                        <div class="rounded-xl border border-line bg-white p-3 shadow-xs max-w-sm">
                                            <p class="text-[0.7rem] font-bold text-forest mb-2 flex items-center justify-between">
                                                <span>Foto Speedometer Kembali:</span>
                                                <a href="{{ $item->returnSpeedometerPhotoUrl() }}" target="_blank" class="text-xs text-leaf underline">Buka Ukuran Penuh</a>
                                            </p>
                                            <img src="{{ $item->returnSpeedometerPhotoUrl() }}"
                                                 alt="Speedometer Kembali {{ $item->code }}"
                                                 class="rounded-lg object-contain w-full max-h-64 border border-line">
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    @empty
                        <tbody>
                            <tr>
                                <td colspan="8" class="px-4 py-12 text-center text-ink-soft">
                                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-canvas text-forest/70 mb-3">
                                        <x-ui-icon name="car" class="h-6 w-6" />
                                    </div>
                                    <p class="text-sm font-semibold text-ink">Belum ada data peminjaman kendaraan</p>
                                    <p class="text-xs text-ink-soft mt-1">Formulir peminjaman yang telah diisi akan tercatat rapi di sini.</p>
                                    <a href="{{ route('vehicle-loans.create') }}" class="btn-primary inline-flex items-center gap-2 mt-4 text-xs">
                                        <x-ui-icon name="plus" class="h-4 w-4" />
                                        <span>Isi Formulir Sekarang</span>
                                    </a>
                                </td>
                            </tr>
                        </tbody>
                    @endforelse
                </table>
            </div>

            @if ($loans->hasPages())
                <div class="border-t border-line px-4 py-3">
                    {{ $loans->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
