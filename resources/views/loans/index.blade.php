<x-app-layout :title="$title" :subtitle="$subtitle" active="loans">
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

    <form method="GET" class="card mt-4 flex flex-col gap-2.5 p-3.5 sm:flex-row sm:items-center sm:p-4">
        <input type="search" name="q" value="{{ $filters['q'] }}" class="field sm:flex-1"
               placeholder="Cari kode pinjam, nama barang, atau peminjam...">
        <button type="submit" class="btn-forest">Cari</button>
        @can('create', \App\Models\AssetLoan::class)
            <a href="{{ route('loans.create') }}" class="btn-olive">
                <x-ui-icon name="plus" class="h-4 w-4" /> Pinjam Barang
            </a>
        @endcan
    </form>

    <div class="card mt-4 hidden overflow-hidden md:block">
        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th scope="col">Kode Pinjam</th>
                        <th scope="col">Barang</th>
                        @if ($mode === 'history')
                            <th scope="col">Peminjam</th>
                        @else
                            <th scope="col">Peminjam</th>
                        @endif
                        <th scope="col">Jumlah</th>
                        <th scope="col">Tgl Pinjam</th>
                        <th scope="col">Tenggat</th>
                        <th scope="col">Status</th>
                        <th scope="col" class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($loans as $loan)
                        <tr>
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
                                <div class="flex items-center justify-end gap-1.5">
                                    @can('return', $loan)
                                        <a href="{{ route('loans.return.form', $loan) }}" class="btn-soft px-3 py-1.5 text-xs">Kembalikan</a>
                                    @endcan
                                    <a href="{{ route('loans.show', $loan) }}" class="rounded-lg p-2 text-ink-soft hover:bg-canvas hover:text-forest" aria-label="Detail {{ $loan->code }}">
                                        <x-ui-icon name="arrow-up-right" class="h-4 w-4" />
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="py-10 text-center text-ink-soft">Tidak ada data peminjaman.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4 space-y-3 md:hidden">
        @forelse ($loans as $loan)
            <article class="card p-4">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <p class="font-mono text-[0.68rem] text-ink-soft">{{ $loan->code }}</p>
                        <a href="{{ route('loans.show', $loan) }}" class="text-sm font-bold text-ink">{{ $loan->asset?->name }}</a>
                        <p class="text-[0.72rem] text-ink-soft">{{ $loan->borrower?->name }} &middot; {{ $loan->quantity }} unit</p>
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
                <div class="mt-3 flex gap-2">
                    @can('return', $loan)
                        <a href="{{ route('loans.return.form', $loan) }}" class="btn-soft flex-1 py-2 text-xs">Kembalikan</a>
                    @endcan
                    <a href="{{ route('loans.show', $loan) }}" class="btn-ghost flex-1 py-2 text-xs">Detail</a>
                </div>
            </article>
        @empty
            <div class="card p-8 text-center text-sm text-ink-soft">Tidak ada data peminjaman.</div>
        @endforelse
    </div>

    <div class="mt-5">{{ $loans->links() }}</div>
</x-app-layout>