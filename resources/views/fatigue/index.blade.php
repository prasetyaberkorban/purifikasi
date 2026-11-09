<x-app-layout title="Riwayat Penilaian Fatik Individual" subtitle="Daftar evaluasi kebugaran dan kesiapan kerja karyawan" active="fatigue">
    <div class="space-y-4"
         x-data="{
             selected: [],
             allIds: {{ json_encode($assessments->pluck('id')) }},
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

        {{-- Bulk Actions Bar --}}
        <div x-show="selected.length > 0" x-cloak x-transition
             class="flex flex-wrap items-center justify-between gap-3 rounded-xl bg-forest-900/5 border border-forest/20 p-2.5 sm:px-4">
            <div class="flex items-center gap-2 text-xs font-semibold text-forest">
                <span class="inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-forest text-white text-[0.7rem] px-1.5" x-text="selected.length"></span>
                <span>data penilaian fatik dipilih</span>
            </div>

            <form method="POST" action="{{ route('fatigue.bulk-destroy') }}"
                  data-confirm-title="Hapus Data Penilaian Fatik Terpilih?"
                  :data-confirm-delete="'Apakah Anda yakin ingin menghapus ' + selected.length + ' data penilaian fatik terpilih secara permanen?'"
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
                            <tr class="hover:bg-canvas/50 transition" :class="selected.includes({{ $item->id }}) ? '!bg-forest/10' : ''">
                                <td class="px-3 py-3.5 text-center">
                                    <input type="checkbox"
                                           :value="{{ $item->id }}"
                                           x-model="selected"
                                           class="rounded border-line text-forest focus:ring-forest cursor-pointer">
                                </td>
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
                                    <div class="flex items-center justify-end gap-1.5">
                                        <a href="{{ route('fatigue.show', $item) }}" class="btn-ghost py-1 px-2.5 text-xs">
                                            Detail
                                        </a>
                                        @if (auth()->user()->isAdmin() || $item->user_id === auth()->id())
                                            <form method="POST" action="{{ route('fatigue.destroy', $item) }}"
                                                  data-confirm-title="Hapus Penilaian Fatik?"
                                                  data-confirm-delete="Apakah Anda yakin ingin menghapus data penilaian fatik {{ $item->code }} ({{ $item->name }})? Tindakan ini permanen."
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
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-12 text-center text-sm text-ink-soft">
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
