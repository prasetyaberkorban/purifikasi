<x-app-layout title="Log Aktivitas Terbaru" subtitle="Rekam jejak aktivitas, transaksi peminjaman, dan perubahan data inventaris" active="dashboard">
    <div class="space-y-4"
         x-data="{
             selected: [],
             allIds: {{ json_encode($activities->pluck('id')) }},
             toggleAll() {
                 if (this.selected.length === this.allIds.length) {
                     this.selected = [];
                 } else {
                     this.selected = [...this.allIds];
                 }
             }
         }">

        {{-- Ringkasan Metrik --}}
        <div class="grid grid-cols-2 gap-3 sm:grid-cols-2 lg:max-w-md">
            <div class="card p-3.5 flex items-center gap-3">
                <div class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-forest/10 text-forest">
                    <x-ui-icon name="clock" class="h-5 w-5" />
                </div>
                <div>
                    <p class="text-[0.68rem] font-semibold uppercase tracking-wider text-ink-soft">Hari Ini</p>
                    <p class="text-lg font-extrabold text-ink">{{ number_format($todayCount, 0, ',', '.') }}</p>
                </div>
            </div>
            <div class="card p-3.5 flex items-center gap-3">
                <div class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-leaf/10 text-leaf">
                    <x-ui-icon name="chart" class="h-5 w-5" />
                </div>
                <div>
                    <p class="text-[0.68rem] font-semibold uppercase tracking-wider text-ink-soft">Total Aktivitas</p>
                    <p class="text-lg font-extrabold text-forest">{{ number_format($totalCount, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>

        {{-- Toolbar Pencarian & Filter --}}
        <form method="GET" action="{{ route('activities.index') }}" class="card p-3 sm:p-4 space-y-3">
            <div class="grid gap-2.5 sm:grid-cols-2 lg:grid-cols-12">
                {{-- Global Search --}}
                <div class="lg:col-span-5">
                    <label for="q" class="label text-xs">Pencarian Aktivitas</label>
                    <div class="relative">
                        <input id="q" type="search" name="q" value="{{ $search }}"
                               placeholder="Cari deskripsi, subjek, nama user, aset..."
                               class="field pr-8 text-xs py-1.5">
                        <span class="absolute right-2.5 top-1/2 -translate-y-1/2 text-ink-soft">
                            <x-ui-icon name="filter" class="h-3.5 w-3.5 opacity-50" />
                        </span>
                    </div>
                </div>

                {{-- Filter Tipe Aktivitas --}}
                <div class="lg:col-span-3">
                    <label for="type" class="label text-xs">Jenis Aktivitas</label>
                    <select id="type" name="type" class="field text-xs py-1.5" onchange="this.form.submit()">
                        <option value="">Semua Jenis Aktivitas</option>
                        @foreach ($types as $key => $label)
                            <option value="{{ $key }}" @selected($selectedType === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Filter Tanggal --}}
                <div class="lg:col-span-3">
                    <label for="date" class="label text-xs">Tanggal</label>
                    <input id="date" type="date" name="date" value="{{ $selectedDate }}" class="field text-xs py-1.5" onchange="this.form.submit()">
                </div>

                {{-- Tombol Terapkan & Reset --}}
                <div class="flex items-end gap-1.5 lg:col-span-1">
                    <button type="submit" class="btn-forest w-full py-1.5 px-2 text-xs font-semibold" title="Terapkan Filter">
                        Filter
                    </button>
                    @if ($search !== '' || $selectedType !== '' || $selectedDate !== '')
                        <a href="{{ route('activities.index') }}" class="btn-ghost py-1.5 px-2 text-xs text-red-600 hover:bg-red-50" title="Reset filter">
                            <x-ui-icon name="x" class="h-3.5 w-3.5" />
                        </a>
                    @endif
                </div>
            </div>
        </form>

        {{-- Bulk Actions Bar (Admin) --}}
        @if (auth()->user()->isAdmin())
            <div x-show="selected.length > 0" x-cloak x-transition
                 class="flex flex-wrap items-center justify-between gap-3 rounded-xl bg-forest-900/5 border border-forest/20 p-2.5 sm:px-4">
                <div class="flex items-center gap-2 text-xs font-semibold text-forest">
                    <span class="inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-forest text-white text-[0.7rem] px-1.5" x-text="selected.length"></span>
                    <span>log aktivitas dipilih</span>
                </div>

                <form method="POST" action="{{ route('activities.bulk-destroy') }}"
                      data-confirm-title="Hapus Log Aktivitas Terpilih?"
                      :data-confirm-delete="'Apakah Anda yakin ingin menghapus ' + selected.length + ' log aktivitas terpilih secara permanen?'"
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
        @endif

        {{-- Daftar Log Aktivitas --}}
        <div class="card overflow-hidden">
            <div class="flex items-center justify-between border-b border-line px-4 py-3 bg-canvas/60">
                <div class="flex items-center gap-2">
                    @if (auth()->user()->isAdmin())
                        <input type="checkbox"
                               @change="toggleAll()"
                               :checked="allIds.length > 0 && selected.length === allIds.length"
                               class="rounded border-line text-forest focus:ring-forest cursor-pointer"
                               title="Pilih Semua di Halaman Ini">
                    @endif
                    <span class="text-xs font-bold text-ink">Semua Catatan Aktivitas</span>
                </div>
                <span class="text-[0.72rem] text-ink-soft">Menampilkan {{ $activities->count() }} dari {{ $activities->total() }} log</span>
            </div>

            <ol class="divide-y divide-line">
                @forelse ($activities as $activity)
                    <li class="flex items-start gap-3.5 px-4 py-3.5 transition hover:bg-canvas/50"
                        :class="selected.includes({{ $activity->id }}) ? '!bg-forest/10' : ''">
                        @if (auth()->user()->isAdmin())
                            <div class="mt-1">
                                <input type="checkbox"
                                       :value="{{ $activity->id }}"
                                       x-model="selected"
                                       class="rounded border-line text-forest focus:ring-forest cursor-pointer">
                            </div>
                        @endif

                        <span class="mt-0.5 grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-canvas text-forest border border-line">
                            <x-ui-icon :name="$activity->icon()" class="h-4 w-4" />
                        </span>

                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <p class="text-xs font-semibold text-ink leading-snug">{{ $activity->description }}</p>
                                @if ($activity->subject)
                                    <span class="inline-flex items-center rounded-md bg-canvas px-1.5 py-0.5 font-mono text-[0.65rem] font-bold text-ink-soft border border-line">
                                        {{ $activity->subject }}
                                    </span>
                                @endif
                            </div>

                            <div class="mt-1 flex flex-wrap items-center gap-x-3 gap-y-1 text-[0.7rem] text-ink-soft">
                                <span class="font-medium text-ink">
                                    <x-ui-icon name="user" class="inline-block h-3 w-3 -mt-0.5 opacity-60" />
                                    {{ $activity->user?->name ?? 'Sistem' }}
                                </span>
                                <span>&middot;</span>
                                <span>{{ $activity->created_at?->translatedFormat('d M Y, H:i') }} WIB</span>
                                <span>({{ $activity->created_at?->diffForHumans() }})</span>

                                @if ($activity->asset)
                                    <span>&middot;</span>
                                    <a href="{{ route('assets.show', $activity->asset) }}" class="font-medium text-forest hover:underline">
                                        Lihat Aset: {{ $activity->asset->name }}
                                    </a>
                                @endif
                            </div>
                        </div>

                        @if (auth()->user()->isAdmin())
                            <form method="POST" action="{{ route('activities.destroy', $activity) }}"
                                  data-confirm-title="Hapus Log Aktivitas?"
                                  data-confirm-delete="Apakah Anda yakin ingin menghapus catatan log ini? Tindakan ini permanen."
                                  class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="rounded-lg p-1.5 text-ink-soft hover:bg-red-50 hover:text-red-600 transition" title="Hapus log">
                                    <x-ui-icon name="trash" class="h-3.5 w-3.5" />
                                </button>
                            </form>
                        @endif
                    </li>
                @empty
                    <li class="px-4 py-12 text-center text-sm text-ink-soft">
                        <div class="mx-auto flex h-10 w-10 items-center justify-center rounded-xl bg-canvas text-ink-soft mb-2">
                            <x-ui-icon name="clock" class="h-5 w-5 opacity-40" />
                        </div>
                        <p class="font-medium text-xs">Belum ada catatan aktivitas yang sesuai filter.</p>
                    </li>
                @endforelse
            </ol>

            @if ($activities->hasPages())
                <div class="border-t border-line px-4 py-3">
                    {{ $activities->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
