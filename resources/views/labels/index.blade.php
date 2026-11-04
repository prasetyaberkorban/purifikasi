<x-app-layout title="Cetak Label QR" subtitle="Pilih aset untuk dicetak labelnya" active="assets">
    <form method="GET" action="{{ route('labels.index') }}" class="card flex flex-col gap-2.5 p-3.5 sm:flex-row sm:items-center sm:p-4">
        <input type="search" name="q" value="{{ $filters['q'] }}" class="field sm:flex-1" placeholder="Cari nama barang atau kode aset...">
        <select name="category" class="field sm:w-56" aria-label="Filter kategori">
            <option value="">Semua Kategori</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" @selected((string) $filters['category'] === (string) $category->id)>{{ $category->name }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn-forest">Filter</button>
    </form>

    <form method="GET" action="{{ route('labels.print') }}" target="_blank">
        <div class="card mt-4 overflow-hidden">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-line px-4 py-3.5 sm:px-5">
                <div>
                    <h2 class="text-sm font-bold text-ink">Pilih Label</h2>
                    <p class="text-[0.72rem] text-ink-soft">{{ $assets->count() }} aset siap dicetak (maksimal 60 per halaman).</p>
                </div>
                <div class="flex items-center gap-2">
                    <label class="flex items-center gap-2 text-[0.75rem] text-ink-soft">
                        Tipe
                        <select name="mode" class="field w-36 py-2">
                            <option value="qr">QR Code</option>
                            <option value="barcode">Barcode</option>
                            <option value="both">QR + Barcode</option>
                        </select>
                    </label>
                    <button type="submit" class="btn-olive">
                        <x-ui-icon name="qr" class="h-4 w-4" /> Cetak Terpilih
                    </button>
                </div>
            </div>

            <ul class="grid gap-3 p-3 sm:grid-cols-2 sm:p-4 xl:grid-cols-3">
                @forelse ($assets as $asset)
                    <li class="flex items-center gap-3 rounded-xl border border-line p-3">
                        <input type="checkbox" name="ids[]" value="{{ $asset->id }}" checked
                               class="h-4 w-4 shrink-0 rounded border-line text-leaf focus:ring-leaf/30"
                               aria-label="Pilih {{ $asset->name }}">
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-semibold text-ink">{{ $asset->name }}</p>
                            <p class="font-mono text-[0.68rem] text-ink-soft">{{ $asset->code }}</p>
                        </div>
                        <img src="{{ $labels->qrDataUri($asset) }}" alt="" class="h-12 w-12 shrink-0 rounded-lg ring-1 ring-line">
                    </li>
                @empty
                    <li class="col-span-full py-10 text-center text-sm text-ink-soft">Tidak ada aset yang cocok.</li>
                @endforelse
            </ul>
        </div>
    </form>
</x-app-layout>