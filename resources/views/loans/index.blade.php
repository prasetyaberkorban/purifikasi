<x-app-layout :title="$title" :subtitle="$subtitle" active="loans">
    <div x-data="{
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
        <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
            <div class="card px-4 py-3">
                <p class="text-[0.68rem] font-semibold tracking-wide text-ink-soft uppercase">Pinjam Aktif</p>
                <p class="mt-1 text-xl font-extrabold text-[#2f5fbf]">{{ $summary['active'] }}</p>
            </div>
            <div class="card px-4 py-3">
                <p class="text-[0.68rem] font-semibold tracking-wide text-ink-soft uppercase">Unit di Luar</p>
                <p class="mt-1 text-xl font-extrabold text-ink">{{ $summary['units'] }}</p>
            </div>
            <div class="card px-4 py-3">
                <p class="text-[0.68rem] font-semibold tracking-wide text-ink-soft uppercase">Terlambat</p>
                <p class="mt-1 text-xl font-extrabold text-red-500">{{ $summary['overdue'] }}</p>
            </div>
            <div class="card px-4 py-3">
                <p class="text-[0.68rem] font-semibold tracking-wide text-ink-soft uppercase">Selesai</p>
                <p class="mt-1 text-xl font-extrabold text-leaf">{{ $summary['returned'] }}</p>
            </div>
        </div>

        {{-- Toolbar Pencarian & Aksi Bulk --}}
        <div class="card mt-4 p-3.5 sm:p-4 space-y-3">
            <form method="GET" class="flex flex-col gap-2.5 sm:flex-row sm:items-center">
                <input type="search" name="q" value="{{ $filters['q'] }}" class="field sm:flex-1"
                       placeholder="Cari kode pinjam, nama barang, atau peminjam...">
                <button type="submit" class="btn-forest">Cari</button>
                @can('create', \App\Models\AssetLoan::class)
                    <a href="{{ route('loans.create') }}" class="btn-olive">
                        <x-ui-icon name="plus" class="h-4 w-4" /> Pinjam Barang
                    </a>
                @endcan
            </form>

            {{-- Bulk Actions Bar (Muncul Otomatis saat ada baris dicentang) --}}
            @if (auth()->user()->isAdmin())
                <div x-show="selected.length > 0" x-cloak x-transition
                     class="flex flex-wrap items-center justify-between gap-3 rounded-xl bg-forest-900/5 border border-forest/20 p-2.5 sm:px-4">
                    <div class="flex items-center gap-2 text-xs font-semibold text-forest">
                        <span class="inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-forest text-white text-[0.7rem] px-1.5" x-text="selected.length"></span>
                        <span>data peminjaman dipilih</span>
                    </div>

                    <form method="POST" action="{{ route('loans.bulk-destroy') }}"
                          data-confirm-title="Hapus Data Peminjaman Terpilih?"
                          :data-confirm-delete="'Apakah Anda yakin ingin menghapus ' + selected.length + ' data peminjaman yang dipilih secara permanen? Stok aset akan dikembalikan otomatis jika status masih aktif.'"
                          class="inline">
                        @csrf
                        @method('DELETE')
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
        </div>

        {{-- Desktop Table --}}
        <div class="card mt-4 hidden overflow-hidden md:block">
            <div class="table-wrap">
                <table class="data-table">
                    <thead>
                        <tr>
                            @if (auth()->user()->isAdmin())
                                <th scope="col" style="width: 40px;" class="text-center">
                                    <input type="checkbox"
                                           @change="toggleAll()"
                                           :checked="allIds.length > 0 && selected.length === allIds.length"
                                           class="rounded border-line text-forest focus:ring-forest cursor-pointer"
                                           title="Pilih Semua">
                                </th>
                            @endif
                            <th scope="col">Kode Pinjam</th>
                            <th scope="col">Barang</th>
                            <th scope="col">Peminjam</th>
                            <th scope="col">Jumlah</th>
                            <th scope="col">Tgl Pinjam</th>
                            <th scope="col">Tenggat</th>
                            <th scope="col">Status</th>
                            <th scope="col" class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($loans as $loan)
                            <tr :class="selected.includes({{ $loan->id }}) ? 'bg-forest/5' : ''">
                                @if (auth()->user()->isAdmin())
                                    <td class="text-center">
                                        <input type="checkbox"
                                               :value="{{ $loan->id }}"
                                               x-model="selected"
                                               class="rounded border-line text-forest focus:ring-forest cursor-pointer">
                                    </td>
                                @endif
                                <td class="font-mono text-[0.72rem] text-ink-soft">{{ $loan->code }}</td>
                                <td>
                                    <a href="{{ route('loans.show', $loan) }}" class="font-semibold text-ink hover:text-forest">{{ $loan->asset?->name }}</a>
                                    <p class="font-mono text-[0.68rem] text-ink-soft">{{ $loan->asset?->code }}</p>
                                </td>
                                <td class="text-ink-soft">{{ $loan->borrower?->name }}</td>
                                <td>{{ $loan->quantity }} unit</td>
                                <td class="text-ink-soft">{{ $loan->borrowed_at?->format('d/m/Y') }}</td>
                                <td class="text-ink-soft">
                                    {{ $loan->due_at?->format('d/m/Y') ?? '-' }}
                                    @if ($loan->isOverdue())
                                        <span class="badge-danger ml-1">Terlambat</span>
                                    @endif
                                </td>
                                <td><x-status-badge :status="$loan->status" /></td>
                                <td>
                                    <div class="flex items-center justify-end gap-1">
                                        @can('return', $loan)
                                            <a href="{{ route('loans.return.form', $loan) }}" class="btn-soft px-2.5 py-1 text-xs">Kembalikan</a>
                                        @endcan
                                        <a href="{{ route('loans.show', $loan) }}" class="rounded-lg p-1.5 text-ink-soft hover:bg-canvas hover:text-forest" title="Detail {{ $loan->code }}">
                                            <x-ui-icon name="arrow-up-right" class="h-4 w-4" />
                                        </a>

                                        {{-- Tombol Hapus Peminjaman (Admin) --}}
                                        @can('delete', $loan)
                                            <form method="POST" action="{{ route('loans.destroy', $loan) }}"
                                                  data-confirm-title="Hapus Peminjaman?"
                                                  data-confirm-delete="Apakah Anda yakin ingin menghapus data peminjaman {{ $loan->code }} ({{ $loan->asset?->name }})? Stok barang akan disesuaikan otomatis jika masih aktif."
                                                  class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="rounded-lg p-1.5 text-ink-soft hover:bg-red-50 hover:text-red-600 transition" title="Hapus peminjaman {{ $loan->code }}">
                                                    <x-ui-icon name="trash" class="h-4 w-4" />
                                                </button>
                                            </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="{{ auth()->user()->isAdmin() ? 9 : 8 }}" class="py-10 text-center text-ink-soft">Tidak ada data peminjaman.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Mobile Cards --}}
        <div class="mt-4 space-y-3 md:hidden">
            @forelse ($loans as $loan)
                <article class="card p-4" :class="selected.includes({{ $loan->id }}) ? 'ring-2 ring-forest bg-forest/5' : ''">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-start gap-2.5 min-w-0">
                            @if (auth()->user()->isAdmin())
                                <input type="checkbox"
                                       :value="{{ $loan->id }}"
                                       x-model="selected"
                                       class="mt-1 rounded border-line text-forest focus:ring-forest cursor-pointer">
                            @endif
                            <div class="min-w-0">
                                <p class="font-mono text-[0.68rem] text-ink-soft">{{ $loan->code }}</p>
                                <a href="{{ route('loans.show', $loan) }}" class="text-sm font-bold text-ink">{{ $loan->asset?->name }}</a>
                                <p class="text-[0.72rem] text-ink-soft">{{ $loan->borrower?->name }} &middot; {{ $loan->quantity }} unit</p>
                            </div>
                        </div>
                        <x-status-badge :status="$loan->status" />
                    </div>
                    <div class="mt-3 flex items-center justify-between gap-2 text-[0.72rem] text-ink-soft">
                        <span>Pinjam {{ $loan->borrowed_at?->format('d/m/Y') }}</span>
                        <span>
                            Tenggat {{ $loan->due_at?->format('d/m/Y') ?? '-' }}
                            @if ($loan->isOverdue())
                                <span class="font-semibold text-red-500">· terlambat</span>
                            @endif
                        </span>
                    </div>
                    <div class="mt-3 flex items-center gap-2">
                        @can('return', $loan)
                            <a href="{{ route('loans.return.form', $loan) }}" class="btn-soft flex-1 py-1.5 text-xs text-center">Kembalikan</a>
                        @endcan
                        <a href="{{ route('loans.show', $loan) }}" class="btn-ghost flex-1 py-1.5 text-xs text-center">Detail</a>
                        @can('delete', $loan)
                            <form method="POST" action="{{ route('loans.destroy', $loan) }}"
                                  data-confirm-title="Hapus Peminjaman?"
                                  data-confirm-delete="Apakah Anda yakin ingin menghapus data peminjaman {{ $loan->code }}? Stok barang akan disesuaikan otomatis."
                                  class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-ghost text-red-600 hover:bg-red-50 p-2" title="Hapus">
                                    <x-ui-icon name="trash" class="h-4 w-4" />
                                </button>
                            </form>
                        @endcan
                    </div>
                </article>
            @empty
                <div class="card p-8 text-center text-sm text-ink-soft">Tidak ada data peminjaman.</div>
            @endforelse
        </div>

        <div class="mt-5">{{ $loans->links() }}</div>
    </div>
</x-app-layout>
