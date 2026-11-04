<x-app-layout title="Daftar Inventaris" subtitle="Daftar aset aktif. Klik item untuk membuka detail aset." active="assets">
    {{-- Ringkasan --}}
    <div class="grid grid-cols-2 gap-2.5 sm:grid-cols-3 lg:grid-cols-5">
        <a href="{{ route('assets.index') }}"
           class="card px-4 py-3 transition hover:shadow-md {{ empty($filters['status']) && empty($filters['condition']) ? 'ring-2 ring-forest/40 bg-forest/5' : '' }}">
            <p class="text-[0.68rem] font-semibold tracking-wide text-ink-soft uppercase">Total Aset</p>
            <p class="mt-1 text-xl font-extrabold text-ink">{{ number_format($stats['total'], 0, ',', '.') }}</p>
        </a>
        <a href="{{ route('assets.index', ['status' => \App\Enums\AssetStatus::Tersedia->value]) }}"
           class="card px-4 py-3 transition hover:shadow-md {{ $filters['status'] === \App\Enums\AssetStatus::Tersedia->value ? 'ring-2 ring-leaf/40 bg-leaf/5' : '' }}">
            <p class="text-[0.68rem] font-semibold tracking-wide text-ink-soft uppercase">Tersedia</p>
            <p class="mt-1 text-xl font-extrabold text-leaf">{{ number_format($stats['available'], 0, ',', '.') }}</p>
        </a>
        <a href="{{ route('assets.index', ['status' => \App\Enums\AssetStatus::Dipinjam->value]) }}"
           class="card px-4 py-3 transition hover:shadow-md {{ $filters['status'] === \App\Enums\AssetStatus::Dipinjam->value ? 'ring-2 ring-[#2f5fbf]/40 bg-sky-50' : '' }}">
            <p class="text-[0.68rem] font-semibold tracking-wide text-ink-soft uppercase">Dipinjam</p>
            <p class="mt-1 text-xl font-extrabold text-[#2f5fbf]">{{ number_format($stats['borrowed'], 0, ',', '.') }}</p>
        </a>
        <a href="{{ route('assets.index', ['status' => \App\Enums\AssetStatus::Habis->value]) }}"
           class="card px-4 py-3 transition hover:shadow-md {{ $filters['status'] === \App\Enums\AssetStatus::Habis->value ? 'ring-2 ring-red-400 bg-red-50' : '' }}">
            <p class="text-[0.68rem] font-semibold tracking-wide text-ink-soft uppercase">Habis</p>
            <p class="mt-1 text-xl font-extrabold text-red-500">{{ number_format($stats['empty'], 0, ',', '.') }}</p>
        </a>
        <a href="{{ route('assets.index', ['condition' => \App\Enums\AssetCondition::Rusak->value]) }}"
           class="card px-4 py-3 transition hover:shadow-md {{ $filters['condition'] === \App\Enums\AssetCondition::Rusak->value ? 'ring-2 ring-amber-500/40 bg-amber-50' : '' }}">
            <p class="text-[0.68rem] font-semibold tracking-wide text-ink-soft uppercase">Barang Rusak</p>
            <p class="mt-1 text-xl font-extrabold text-amber-600">{{ number_format($stats['damaged'], 0, ',', '.') }}</p>
        </a>
    </div>

    {{-- Filter --}}
    <form method="GET" action="{{ route('assets.index') }}" class="card mt-4 p-3.5 sm:p-4" x-data="{ advanced: false }">
        <div class="grid gap-2.5 lg:grid-cols-12">
            <div class="relative lg:col-span-5">
                <span class="pointer-events-none absolute top-1/2 left-3 -translate-y-1/2 text-slate-400">
                    <x-ui-icon name="filter" class="h-4 w-4" />
                </span>
                <input type="search" name="q" value="{{ $filters['q'] }}" class="field pl-9"
                       placeholder="Cari aset, lokasi, kategori, atau kode...">
            </div>
            <div class="lg:col-span-3">
                <select name="category" class="field" aria-label="Filter kategori">
                    <option value="">Semua Kategori</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected((string) $filters['category'] === (string) $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="lg:col-span-2">
                <select name="status" class="field" aria-label="Filter status peminjaman">
                    <option value="">Semua Status</option>
                    @foreach (\App\Enums\AssetStatus::options() as $value => $label)
                        <option value="{{ $value }}" @selected($filters['status'] === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-2 lg:col-span-2">
                <button type="submit" class="btn-forest flex-1">Terapkan</button>
                <a href="{{ route('assets.index') }}" class="btn-ghost" aria-label="Reset filter">Reset</a>
            </div>
        </div>

        <div class="mt-2.5 flex flex-wrap items-center gap-3">
            <button type="button" @click="advanced = !advanced" class="inline-flex items-center gap-1.5 text-xs font-semibold text-leaf">
                <x-ui-icon name="filter" class="h-3.5 w-3.5" />
                <span x-text="advanced ? 'Sembunyikan filter lanjutan' : 'Filter lanjutan'">Filter lanjutan</span>
            </button>
            <span class="text-[0.7rem] text-ink-soft">{{ $assets->total() }} aset ditemukan</span>
        </div>

        <div x-show="advanced" x-cloak class="mt-3 grid gap-2.5 border-t border-line pt-3 lg:grid-cols-3">
            <select name="location" class="field" aria-label="Filter lokasi">
                <option value="">Semua Lokasi</option>
                @foreach ($locations as $location)
                    <option value="{{ $location->id }}" @selected((string) $filters['location'] === (string) $location->id)>{{ $location->label }}</option>
                @endforeach
            </select>
            <select name="condition" class="field" aria-label="Filter kondisi">
                <option value="">Semua Kondisi</option>
                @foreach (\App\Enums\AssetCondition::options() as $value => $label)
                    <option value="{{ $value }}" @selected($filters['condition'] === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <select name="owner" class="field" aria-label="Filter unit pemilik">
                <option value="">Semua Unit Pemilik</option>
                @foreach ($owners as $owner)
                    <option value="{{ $owner }}" @selected($filters['owner'] === $owner)>{{ $owner }}</option>
                @endforeach
            </select>
        </div>
    </form>

    {{-- Aksi --}}
    @can('create', \App\Models\Asset::class)
        <div class="mt-4 flex flex-wrap items-center justify-end gap-2">
            <a href="{{ route('reports.index') }}" class="btn-ghost">
                <x-ui-icon name="download" class="h-4 w-4" /> Ekspor
            </a>
            <a href="{{ route('assets.create') }}" class="btn-olive hidden px-3 py-1.5 text-xs sm:inline-flex">
                <x-ui-icon name="plus" class="h-4 w-4" /> Tambah Barang
            </a>
        </div>
    @endcan

    {{-- Tabel desktop --}}
    <div class="card mt-4 hidden overflow-hidden md:block">
        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th scope="col">Kode Aset</th>
                        <th scope="col">Nama Barang</th>
                        <th scope="col">Kategori</th>
                        <th scope="col">Stok</th>
                        <th scope="col">Status</th>
                        <th scope="col" class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($assets as $asset)
                        <tr>
                            <td class="font-mono text-[0.72rem] text-ink-soft">{{ $asset->code }}</td>
                            <td>
                                <a href="{{ route('assets.show', $asset) }}" class="font-semibold text-ink hover:text-forest">{{ $asset->name }}</a>
                            </td>
                            <td class="text-ink-soft">{{ $asset->category?->name }}</td>
                            <td>
                                <span class="font-semibold text-ink">{{ $asset->availableQuantity() }}</span>
                                <span class="text-ink-soft">/ {{ $asset->quantity }}</span>
                            </td>
                            <td><x-status-badge :status="$asset->status" /></td>
                            <td>
                                <div class="flex items-center justify-end gap-1">
                                    @can('update', $asset)
                                        <a href="{{ route('assets.edit', $asset) }}" class="rounded-lg p-2 text-ink-soft hover:bg-canvas hover:text-forest" aria-label="Ubah {{ $asset->name }}">
                                            <x-ui-icon name="pencil" class="h-4 w-4" />
                                        </a>
                                    @endcan
                                    <a href="{{ route('assets.show', $asset) }}" class="rounded-lg p-2 text-ink-soft hover:bg-canvas hover:text-forest" aria-label="Detail {{ $asset->name }}">
                                        <x-ui-icon name="arrow-up-right" class="h-4 w-4" />
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-10 text-center text-ink-soft">
                                Tidak ada aset yang cocok dengan filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Kartu mobile --}}
    <div class="mt-4 grid grid-cols-2 gap-3 md:hidden">
        @foreach ($assets as $asset)
            <x-asset-card :asset="$asset" :show-image="false" />
        @endforeach
    </div>

    @if ($assets->isEmpty())
        <div class="card mt-4 p-8 text-center md:hidden">
            <p class="text-sm text-ink-soft">Tidak ada aset yang cocok dengan filter.</p>
        </div>
    @endif

    <div class="mt-5">{{ $assets->links() }}</div>
</x-app-layout>