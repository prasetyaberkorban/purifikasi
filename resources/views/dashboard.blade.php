<x-app-layout title="Dashboard" subtitle="Ringkasan inventaris kantor PURIFIKASI" active="dashboard">
    {{-- Kartu statistik --}}
    <section class="grid grid-cols-1 gap-3 sm:grid-cols-3 sm:gap-4">
        <x-stat-card label="Total Aset" :value="number_format($summary['asset_count'], 0, ',', '.')"
                     :hint="$summary['total_units'].' unit tercatat'" icon="box" tone="forest"
                     :href="route('assets.index')" />
        <x-stat-card label="Barang Dipinjam" :value="(int) $summary['borrowed_units']"
                     hint="Unit sedang di luar kantor" icon="swap" tone="sky"
                     :href="route('loans.active')" />
        <x-stat-card label="Aktivitas Terbaru" :value="$summary['activity_today']"
                     hint="Pencatatan hari ini" icon="clock" tone="leaf" />
    </section>

    {{-- Grafik 1: Unit Aset per Kategori (1 Baris Memanjang Penuh) --}}
    <section class="mt-4">
        <div class="card min-w-0 p-4 sm:p-5">
            <div class="mb-4 flex items-center justify-between gap-3">
                <div>
                    <h2 class="text-sm font-bold text-ink">Unit Aset per Kategori</h2>
                    <p class="text-[0.72rem] text-ink-soft">Distribusi jumlah unit berdasarkan nama kategori.</p>
                </div>
            </div>
            <x-bar-chart :items="$categories" label="Unit per kategori" height="180" />
        </div>
    </section>

    {{-- Grafik 2: Nilai Perolehan per Tahun & Kondisi Aset (Setengah Layar Masing-masing) --}}
    <section class="mt-4 grid items-start gap-4 lg:grid-cols-2">
        <div class="card min-w-0 p-4 sm:p-5">
            <div class="mb-4">
                <h2 class="text-sm font-bold text-ink">Nilai Perolehan per Tahun</h2>
                <p class="text-[0.72rem] text-ink-soft">Total nilai beli aset (rupiah).</p>
            </div>
            @if ($purchaseYears->isEmpty())
                <p class="py-10 text-center text-sm text-ink-soft">Belum ada data tanggal beli.</p>
            @else
                <x-bar-chart :items="$purchaseYears" label="Nilai perolehan" height="160"
                             :formatter="fn ($v) => 'Rp '.number_format($v / 1000000, 0, ',', '.').' Jt'" />
            @endif
        </div>

        <div class="card min-w-0 p-4 sm:p-5">
            <div class="mb-4">
                <h2 class="text-sm font-bold text-ink">Kondisi Aset</h2>
                <p class="text-[0.72rem] text-ink-soft">Rekap kondisi seluruh barang terdaftar.</p>
            </div>
            <x-donut-chart :items="$conditions" />
        </div>
    </section>

    {{-- Tabel & panel samping --}}
    <section class="mt-4 grid gap-4 xl:grid-cols-3">
        <div class="card overflow-hidden xl:col-span-2">
            <div class="flex items-center justify-between gap-3 border-b border-line px-4 py-4 sm:px-5">
                <div>
                    <h2 class="text-sm font-bold text-ink">Data Inventaris Terbaru</h2>
                    <p class="text-[0.72rem] text-ink-soft">Daftar aset aktif. Klik nama untuk membuka detail.</p>
                </div>
                <a href="{{ route('assets.index') }}" class="text-xs font-semibold text-leaf hover:underline">Lihat Semua</a>
            </div>

            {{-- Tabel (desktop) --}}
            <div class="table-wrap hidden md:block">
                <table class="data-table min-w-[40rem]">
                    <thead>
                        <tr>
                            <th scope="col">Kode Aset</th>
                            <th scope="col">Nama Barang</th>
                            <th scope="col">Kategori</th>
                            <th scope="col">Stok</th>
                            <th scope="col">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($latestAssets as $asset)
                            <tr>
                                <td class="font-mono text-[0.72rem] text-ink-soft">{{ $asset->code }}</td>
                                <td>
                                    <a href="{{ route('assets.show', $asset) }}" class="font-semibold text-ink hover:text-forest">{{ $asset->name }}</a>
                                </td>
                                <td class="text-ink-soft">{{ $asset->category?->name }}</td>
                                <td>{{ $asset->availableQuantity() }} / {{ $asset->quantity }}</td>
                                <td><x-status-badge :status="$asset->status" /></td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="py-8 text-center text-ink-soft">Belum ada aset terdaftar.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Kartu (mobile) --}}
            <div class="grid grid-cols-2 gap-3 p-3 sm:gap-4 md:hidden">
                @foreach ($latestAssets as $asset)
                    <x-asset-card :asset="$asset" />
                @endforeach
            </div>
        </div>

        <div class="space-y-4">
            {{-- Peminjaman aktif --}}
            <div class="card overflow-hidden">
                <div class="flex items-center justify-between border-b border-line px-4 py-4">
                    <h2 class="text-sm font-bold text-ink">Peminjaman Terbaru</h2>
                    <a href="{{ route('loans.active') }}" class="text-xs font-semibold text-leaf hover:underline">Semua</a>
                </div>
                <ul class="divide-y divide-line">
                    @forelse ($recentLoans as $loan)
                        <li class="flex items-start gap-3 px-4 py-3">
                            <span class="mt-0.5 grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-sky-50 text-[#2f5fbf]">
                                <x-ui-icon name="arrow-up-right" class="h-4 w-4" />
                            </span>
                            <div class="min-w-0 flex-1">
                                <a href="{{ route('loans.show', $loan) }}" class="line-clamp-1 text-sm font-semibold text-ink hover:text-forest">{{ $loan->asset?->name }}</a>
                                <p class="text-[0.72rem] text-ink-soft">
                                    {{ $loan->borrower?->name }} &middot; {{ $loan->quantity }} unit
                                </p>
                                <p class="text-[0.68rem] text-ink-soft/80">
                                    Tenggat {{ $loan->due_at?->format('d/m/Y') ?? '-' }}
                                    @if ($loan->isOverdue())
                                        <span class="font-semibold text-red-500">&middot; terlambat</span>
                                    @endif
                                </p>
                            </div>
                            <x-status-badge :status="$loan->status" />
                        </li>
                    @empty
                        <li class="px-4 py-6 text-center text-sm text-ink-soft">Tidak ada peminjaman aktif.</li>
                    @endforelse
                </ul>
            </div>

            {{-- Aktivitas --}}
            <div class="card overflow-hidden">
                <div class="border-b border-line px-4 py-4">
                    <h2 class="text-sm font-bold text-ink">Aktivitas Terbaru</h2>
                </div>
                <ol class="divide-y divide-line">
                    @forelse ($activities as $activity)
                        <li class="flex items-start gap-3 px-4 py-3">
                            <span class="mt-0.5 grid h-8 w-8 shrink-0 place-items-center rounded-lg bg-canvas text-ink-soft">
                                <x-ui-icon :name="$activity->icon()" class="h-4 w-4" />
                            </span>
                            <div class="min-w-0 flex-1">
                                <p class="text-[0.78rem] leading-snug text-ink">{{ $activity->description }}</p>
                                <p class="mt-0.5 text-[0.68rem] text-ink-soft">
                                    {{ $activity->user?->name ?? 'Sistem' }} &middot; {{ $activity->created_at->diffForHumans() }}
                                </p>
                            </div>
                        </li>
                    @empty
                        <li class="px-4 py-6 text-center text-sm text-ink-soft">Belum ada aktivitas.</li>
                    @endforelse
                </ol>
            </div>
        </div>
    </section>
</x-app-layout>