<x-app-layout title="Detail Peminjaman" :subtitle="$loan->code" active="loans">
    <nav class="mb-4 flex items-center gap-2 text-[0.72rem] text-ink-soft">
        <a href="{{ route('loans.history') }}" class="hover:text-forest">Riwayat Peminjaman</a>
        <span>/</span>
        <span class="font-medium text-ink">{{ $loan->code }}</span>
    </nav>

    <div class="grid gap-4 lg:grid-cols-3">
        <section class="card overflow-hidden lg:col-span-2">
            <div class="flex flex-wrap items-start justify-between gap-3 border-b border-line p-4 sm:p-5">
                <div>
                    <p class="font-mono text-[0.72rem] text-ink-soft">{{ $loan->code }}</p>
                    <h2 class="mt-0.5 text-lg font-bold text-ink">{{ $loan->asset?->name }}</h2>
                    <p class="text-[0.78rem] text-ink-soft">{{ $loan->asset?->category?->name }} &middot; {{ $loan->quantity }} unit</p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <x-status-badge :status="$loan->status" />
                    @if ($loan->isOverdue())
                        <span class="badge-danger">Terlambat</span>
                    @endif
                </div>
            </div>

            <dl class="grid grid-cols-2 gap-4 p-4 sm:grid-cols-3 sm:p-5">
                <div>
                    <dt class="text-[0.68rem] tracking-wide text-ink-soft uppercase">Peminjam</dt>
                    <dd class="mt-0.5 text-sm font-semibold text-ink">{{ $loan->borrower?->name }}</dd>
                    <dd class="text-[0.7rem] text-ink-soft">{{ $loan->borrower?->unit ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="text-[0.68rem] tracking-wide text-ink-soft uppercase">Tanggal Pinjam</dt>
                    <dd class="mt-0.5 text-sm font-semibold text-ink">{{ $loan->borrowed_at?->translatedFormat('d M Y') }}</dd>
                </div>
                <div>
                    <dt class="text-[0.68rem] tracking-wide text-ink-soft uppercase">Tenggat</dt>
                    <dd class="mt-0.5 text-sm font-semibold text-ink">{{ $loan->due_at?->translatedFormat('d M Y') ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="text-[0.68rem] tracking-wide text-ink-soft uppercase">Dikembalikan</dt>
                    <dd class="mt-0.5 text-sm font-semibold text-ink">{{ $loan->returned_at?->translatedFormat('d M Y H:i') ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="text-[0.68rem] tracking-wide text-ink-soft uppercase">Kondisi Kembali</dt>
                    <dd class="mt-0.5 text-sm font-semibold text-ink">{{ $loan->condition_on_return?->label() ?? '-' }}</dd>
                </div>
                @if ($loan->initial_photo_path)
                    <div class="col-span-2 sm:col-span-3" x-data="{ showPhoto: false }">
                        <dt class="text-[0.68rem] tracking-wide text-ink-soft uppercase">Foto Kondisi Awal</dt>
                        <dd class="mt-1">
                            <button type="button" @click="showPhoto = !showPhoto" class="inline-flex items-center gap-1.5 rounded-lg border border-line bg-canvas px-3 py-1.5 text-xs font-semibold text-forest hover:bg-forest/5">
                                <x-ui-icon name="eye" class="h-4 w-4" />
                                <span x-text="showPhoto ? 'Sembunyikan Foto' : 'Lihat Foto Kondisi Awal'"></span>
                            </button>
                            <div x-show="showPhoto" x-cloak class="mt-2.5">
                                <img src="{{ asset('storage/'.$loan->initial_photo_path) }}" alt="Foto kondisi awal" class="max-h-64 rounded-xl object-contain ring-1 ring-line shadow-sm">
                            </div>
                        </dd>
                    </div>
                @endif
                <div class="col-span-2 sm:col-span-3">
                    <dt class="text-[0.68rem] tracking-wide text-ink-soft uppercase">Keperluan</dt>
                    <dd class="mt-0.5 text-sm text-ink">{{ $loan->purpose ?? '-' }}</dd>
                </div>
                <div class="col-span-2 sm:col-span-3">
                    <dt class="text-[0.68rem] tracking-wide text-ink-soft uppercase">Ruangan</dt>
                    <dd class="mt-0.5 text-sm text-ink">{{ $loan->room ?? '-' }}</dd>
                </div>
                @if ($loan->note)
                    <div class="col-span-2 sm:col-span-3">
                        <dt class="text-[0.68rem] tracking-wide text-ink-soft uppercase">Catatan</dt>
                        <dd class="mt-0.5 text-sm text-ink">{{ $loan->note }}</dd>
                    </div>
                @endif
            </dl>

            @can('return', $loan)
                <div class="flex flex-wrap gap-2 border-t border-line bg-canvas/60 px-4 py-3 sm:px-5">
                    <a href="{{ route('loans.return.form', $loan) }}" class="btn-primary">
                        <x-ui-icon name="arrow-down-left" class="h-4 w-4" /> Catat Pengembalian
                    </a>
                </div>
            @endcan
        </section>

        <div class="space-y-4">
            <section class="card p-4 sm:p-5">
                <h2 class="mb-3 text-sm font-bold text-ink">Barang</h2>
                <a href="{{ route('assets.show', $loan->asset) }}" class="flex items-center gap-3 rounded-xl border border-line p-2.5 hover:border-leaf/40">
                    <x-asset-thumb :asset="$loan->asset" class="h-14 w-14 rounded-lg object-cover ring-1 ring-line" />
                    <div class="min-w-0">
                        <p class="truncate text-sm font-semibold text-ink">{{ $loan->asset?->name }}</p>
                        <p class="font-mono text-[0.68rem] text-ink-soft">{{ $loan->asset?->code }}</p>
                    </div>
                </a>

                <dl class="mt-3 space-y-2 text-[0.78rem]">
                    <div class="flex items-center justify-between">
                        <dt class="text-ink-soft">Stok tersedia</dt>
                        <dd class="font-semibold text-ink">{{ $loan->asset?->availableQuantity() }} unit</dd>
                    </div>
                </dl>
            </section>

            <section class="card p-4 text-[0.72rem] leading-relaxed text-ink-soft">
                <p class="font-semibold text-ink">Bukti serah terima</p>
                <p class="mt-1">Simpan halaman ini atau cetak sebagai bukti peminjaman barang kantor.</p>
                <button type="button" onclick="window.print()" class="btn-ghost mt-3 w-full">
                    <x-ui-icon name="download" class="h-4 w-4" /> Cetak Halaman
                </button>
            </section>
        </div>
    </div>
</x-app-layout>