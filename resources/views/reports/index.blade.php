<x-app-layout title="Laporan Inventaris" subtitle="Rekap aset dan peminjaman, siap diekspor" active="reports">
    <form method="GET" action="{{ route('reports.index') }}" class="card p-3.5 sm:p-4">
        <div class="grid gap-2.5 lg:grid-cols-12">
            <div class="lg:col-span-3">
                <label for="from" class="label">Dari Tanggal</label>
                <input id="from" name="from" type="date" value="{{ $filters['from'] }}" class="field">
            </div>
            <div class="lg:col-span-3">
                <label for="to" class="label">Sampai Tanggal</label>
                <input id="to" name="to" type="date" value="{{ $filters['to'] }}" class="field">
            </div>
            <div class="lg:col-span-2">
                <label for="status" class="label">Status</label>
                <select id="status" name="status" class="field">
                    <option value="">Semua Status</option>
                    @foreach (\App\Enums\AssetStatus::options() as $value => $label)
                        <option value="{{ $value }}" @selected($filters['status'] === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="lg:col-span-2">
                <label for="category" class="label">Kategori</label>
                <select id="category" name="category" class="field">
                    <option value="">Semua</option>
                    @foreach (\App\Http\Controllers\ReportController::filterOptions()['categories'] as $category)
                        <option value="{{ $category->id }}" @selected((string) $filters['category'] === (string) $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="lg:col-span-2">
                <label for="location" class="label">Lokasi</label>
                <select id="location" name="location" class="field">
                    <option value="">Semua</option>
                    @foreach (\App\Http\Controllers\ReportController::filterOptions()['locations'] as $location)
                        <option value="{{ $location->id }}" @selected((string) $filters['location'] === (string) $location->id)>{{ $location->label }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="mt-3 flex flex-wrap items-center gap-2 border-t border-line pt-3">
            <button type="submit" class="btn-forest">Terapkan Filter</button>
            <a href="{{ route('reports.pdf', ['type' => 'assets'] + $filters) }}" class="btn-ghost">
                <x-ui-icon name="download" class="h-4 w-4" /> PDF Aset
            </a>
            <a href="{{ route('reports.excel', ['type' => 'assets'] + $filters) }}" class="btn-ghost">
                <x-ui-icon name="download" class="h-4 w-4" /> Excel Aset
            </a>
            <a href="{{ route('reports.pdf', ['type' => 'loans'] + $filters) }}" class="btn-olive">
                <x-ui-icon name="download" class="h-4 w-4" /> PDF Peminjaman
            </a>
            <a href="{{ route('reports.excel', ['type' => 'loans'] + $filters) }}" class="btn-olive">
                <x-ui-icon name="download" class="h-4 w-4" /> Excel Peminjaman
            </a>
        </div>
    </form>

    <div class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-4">
        <div class="card px-4 py-3">
            <p class="text-[0.68rem] font-semibold tracking-wide text-ink-soft uppercase">Jenis Aset</p>
            <p class="mt-1 text-xl font-extrabold text-ink">{{ number_format($totals['skus'], 0, ',', '.') }}</p>
        </div>
        <div class="card px-4 py-3">
            <p class="text-[0.68rem] font-semibold tracking-wide text-ink-soft uppercase">Total Unit</p>
            <p class="mt-1 text-xl font-extrabold text-ink">{{ number_format($totals['units'], 0, ',', '.') }}</p>
        </div>
        <div class="card px-4 py-3">
            <p class="text-[0.68rem] font-semibold tracking-wide text-ink-soft uppercase">Unit Dipinjam</p>
            <p class="mt-1 text-xl font-extrabold text-[#2f5fbf]">{{ number_format($totals['loans'], 0, ',', '.') }}</p>
        </div>
        <div class="card px-4 py-3">
            <p class="text-[0.68rem] font-semibold tracking-wide text-ink-soft uppercase">Nilai Aset</p>
            <p class="mt-1 text-lg font-extrabold text-forest">Rp {{ number_format($totals['value'] / 1000000, 1, ',', '.') }} Jt</p>
        </div>
    </div>

    <section class="card mt-4 overflow-hidden">
        <div class="flex items-center justify-between border-b border-line px-4 py-4 sm:px-5">
            <h2 class="text-sm font-bold text-ink">Rekap Aset</h2>
            <span class="text-[0.7rem] text-ink-soft">{{ $rows->count() }} baris</span>
        </div>
        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th scope="col">Kode</th>
                        <th scope="col">Nama</th>
                        <th scope="col">Kategori</th>
                        <th scope="col">Stok</th>
                        <th scope="col">Nilai</th>
                        <th scope="col">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rows as $asset)
                        <tr>
                            <td class="font-mono text-[0.7rem] text-ink-soft">{{ $asset->code }}</td>
                            <td class="font-medium text-ink">{{ $asset->name }}</td>
                            <td class="text-ink-soft">{{ $asset->category?->name }}</td>
                            <td>{{ $asset->quantity }}</td>
                            <td class="text-ink font-semibold">Rp {{ number_format($asset->totalValue(), 0, ',', '.') }}</td>
                            <td><x-status-badge :status="$asset->status" /></td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="py-8 text-center text-ink-soft">Tidak ada data pada filter ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <section class="card mt-4 overflow-hidden">
        <div class="flex items-center justify-between border-b border-line px-4 py-4 sm:px-5">
            <h2 class="text-sm font-bold text-ink">Rekap Peminjaman</h2>
            <span class="text-[0.7rem] text-ink-soft">{{ $loanRows->count() }} baris</span>
        </div>
        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th scope="col">Kode</th>
                        <th scope="col">Barang</th>
                        <th scope="col">Peminjam</th>
                        <th scope="col">Jumlah</th>
                        <th scope="col">Pinjam</th>
                        <th scope="col">Kembali</th>
                        <th scope="col">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($loanRows as $loan)
                        <tr>
                            <td class="font-mono text-[0.7rem] text-ink-soft">{{ $loan->code }}</td>
                            <td class="text-ink">{{ $loan->asset?->name }}</td>
                            <td class="text-ink-soft">{{ $loan->borrower?->name }}</td>
                            <td>{{ $loan->quantity }}</td>
                            <td class="text-ink-soft">{{ $loan->borrowed_at?->format('d/m/Y') }}</td>
                            <td class="text-ink-soft">{{ $loan->returned_at?->format('d/m/Y') ?? '-' }}</td>
                            <td><x-status-badge :status="$loan->status" /></td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="py-8 text-center text-ink-soft">Tidak ada peminjaman pada rentang ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</x-app-layout>