<x-app-layout :title="$asset->name" :subtitle="$asset->code.' · '.$asset->category?->name" active="assets">
    <nav class="mb-4 flex items-center gap-2 text-[0.72rem] text-ink-soft">
        <a href="{{ route('assets.index') }}" class="hover:text-forest">Daftar Inventaris</a>
        <span>/</span>
        <span class="font-medium text-ink">{{ $asset->name }}</span>
    </nav>

    <div class="grid gap-4 lg:grid-cols-3">
        <div class="space-y-4 lg:col-span-2">
            {{-- Header aset --}}
            <section class="card overflow-hidden">
                <div class="flex flex-col gap-4 p-4 sm:flex-row sm:p-5">
                    <x-asset-thumb :asset="$asset" class="h-40 w-full rounded-xl object-cover ring-1 ring-line sm:h-32 sm:w-32 sm:shrink-0" />
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <x-status-badge :status="$asset->status" />
                        </div>
                        <h2 class="mt-2 text-xl font-bold text-ink">{{ $asset->name }}</h2>
                        <p class="font-mono text-[0.75rem] text-ink-soft">{{ $asset->code }}</p>

                        <dl class="mt-3 grid grid-cols-2 gap-x-4 gap-y-2 text-[0.78rem] sm:grid-cols-2">
                            <div>
                                <dt class="text-ink-soft">Kategori</dt>
                                <dd class="font-semibold text-ink">{{ $asset->category?->name ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-ink-soft">Pemeriksaan terakhir</dt>
                                <dd class="font-semibold text-ink">{{ $asset->last_checked_at?->format('d/m/Y') ?? 'Belum dicek' }}</dd>
                            </div>
                        </dl>

                        <div class="mt-4 flex flex-wrap gap-2">
                            @can('update', $asset)
                                <a href="{{ route('assets.edit', $asset) }}" class="btn-primary">
                                    <x-ui-icon name="pencil" class="h-4 w-4" /> Ubah Aset
                                </a>
                                <form method="POST" action="{{ route('assets.check', $asset) }}">
                                    @csrf
                                    <button type="submit" class="btn-soft">
                                        <x-ui-icon name="shield-check" class="h-4 w-4" /> Catat Pemeriksaan
                                    </button>
                                </form>
                            @endcan
                            @can('create', \App\Models\AssetLoan::class)
                                <a href="{{ route('loans.create', ['asset' => $asset->id]) }}"
                                   class="btn-olive {{ $asset->availableQuantity() < 1 ? 'pointer-events-none opacity-50' : '' }}">
                                    <x-ui-icon name="swap" class="h-4 w-4" /> Pinjam Barang
                                </a>
                            @endcan
                            @can('delete', $asset)
                                <form method="POST" action="{{ route('assets.destroy', $asset) }}"
                                      onsubmit="return confirm('Hapus aset {{ $asset->name }} dari inventaris?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-ghost text-red-600 hover:border-red-200">
                                        <x-ui-icon name="x" class="h-4 w-4" /> Hapus
                                    </button>
                                </form>
                            @endcan
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-3 divide-x divide-line border-t border-line bg-canvas/60">
                    <div class="px-4 py-3">
                        <p class="text-[0.68rem] tracking-wide text-ink-soft uppercase">Total Stok</p>
                        <p class="text-lg font-bold text-ink">{{ $asset->quantity }}</p>
                    </div>
                    <div class="px-4 py-3">
                        <p class="text-[0.68rem] tracking-wide text-ink-soft uppercase">Dipinjam</p>
                        <p class="text-lg font-bold text-[#2f5fbf]">{{ $asset->borrowed_quantity }}</p>
                    </div>
                    <div class="px-4 py-3">
                        <p class="text-[0.68rem] tracking-wide text-ink-soft uppercase">Tersedia</p>
                        <p class="text-lg font-bold text-leaf">{{ $asset->availableQuantity() }}</p>
                    </div>
                </div>
            </section>

            {{-- Daftar Unit Fisik Barang --}}
            <section class="card overflow-hidden" x-data="{ addUnitOpen: false }">
                <div class="flex items-center justify-between border-b border-line px-4 py-4 sm:px-5">
                    <div>
                        <h2 class="text-sm font-bold text-ink">Daftar Unit Barang</h2>
                        <p class="text-[0.72rem] text-ink-soft">Kondisi, serial number, foto fisik, dan penempatan ruangan masing-masing unit (total {{ $asset->items->count() }} unit).</p>
                    </div>
                    @can('update', $asset)
                        <button type="button" @click="addUnitOpen = !addUnitOpen"
                                class="btn-primary py-1.5 px-3 text-xs inline-flex items-center gap-1.5 shadow-xs">
                            <x-ui-icon name="plus" class="h-3.5 w-3.5" />
                            <span x-text="addUnitOpen ? 'Tutup Form' : 'Tambah Barang'">Tambah Barang</span>
                        </button>
                    @endcan
                </div>

                {{-- Form Tambah Unit Barang Baru --}}
                @can('update', $asset)
                    <div x-show="addUnitOpen" x-cloak class="border-b border-line bg-leaf-50/60 p-4 sm:p-5">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="text-xs font-bold text-forest">Form Tambah Unit Barang Baru (#{{ ((int) $asset->items->max('unit_number')) + 1 }})</h3>
                            <button type="button" @click="addUnitOpen = false" class="text-ink-soft hover:text-ink">
                                <x-ui-icon name="x" class="h-4 w-4" />
                            </button>
                        </div>
                        <form method="POST" action="{{ route('assets.items.store', $asset) }}" enctype="multipart/form-data" class="space-y-3">
                            @csrf
                            <div class="grid gap-3 sm:grid-cols-3">
                                <div>
                                    <label class="label">Serial Number</label>
                                    <input type="text" name="serial_number" class="field" placeholder="Contoh: SN-12345678">
                                </div>
                                <div>
                                    <label class="label">Kondisi Unit <span class="text-red-500">*</span></label>
                                    <select name="condition" class="field" required>
                                        @foreach (\App\Enums\AssetCondition::options() as $val => $lbl)
                                            <option value="{{ $val }}">{{ $lbl }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="label">Ruangan</label>
                                    <input type="text" name="room" class="field" placeholder="Contoh: Rak Perkakas, Gudang B">
                                </div>
                            </div>
                            <div class="grid gap-3 sm:grid-cols-2">
                                <div>
                                    <label class="label">Harga Satuan Barang (Rp)</label>
                                    <input type="number" name="purchase_price" class="field" placeholder="0" min="0" step="1000">
                                </div>
                                <div>
                                    <label class="label">Catatan Unit</label>
                                    <input type="text" name="notes" class="field" placeholder="Kondisi atau keterangan unit">
                                </div>
                            </div>
                            <div>
                                <x-photo-picker name="photo" id="photo_item_new" label="Foto Kondisi Unit (Opsional)" />
                            </div>
                            <div class="flex justify-end gap-2 pt-2">
                                <button type="button" @click="addUnitOpen = false" class="btn-ghost py-1 px-3 text-xs">Batal</button>
                                <button type="submit" class="btn-forest py-1 px-4 text-xs font-semibold">Simpan & Tambah Unit</button>
                            </div>
                        </form>
                    </div>
                @endcan

                <div class="divide-y divide-line">
                    @forelse ($asset->items as $item)
                        <div class="p-4 sm:p-5" x-data="{ showPhoto: false, editMode: false }">
                            <div class="flex flex-wrap items-center justify-between gap-3">
                                <div class="flex items-center gap-3">
                                    <div class="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-forest/10 font-bold text-forest text-sm">
                                        #{{ $item->unit_number }}
                                    </div>
                                    <div>
                                        <div class="flex flex-wrap items-center gap-2">
                                            <span class="font-mono text-xs font-semibold text-ink">{{ $item->item_code }}</span>
                                            <x-status-badge :status="$item->condition" />
                                            <x-status-badge :status="$item->status" />
                                            @if ($item->serial_number)
                                                <span class="inline-flex items-center gap-1 rounded bg-canvas px-1.5 py-0.5 text-[0.65rem] font-mono font-medium text-ink border border-line" title="Serial Number">
                                                    <span>SN: {{ $item->serial_number }}</span>
                                                </span>
                                            @endif
                                            @if ($item->room)
                                                <span class="inline-flex items-center gap-1 rounded bg-canvas px-1.5 py-0.5 text-[0.65rem] font-medium text-ink border border-line">
                                                    <x-ui-icon name="pin" class="h-2.5 w-2.5 text-forest" />
                                                    <span>{{ $item->room }}</span>
                                                </span>
                                            @endif
                                        </div>
                                        <p class="mt-0.5 text-[0.72rem] text-ink-soft">
                                            Unit {{ $item->unit_number }} dari {{ $asset->quantity }}
                                            @if ($item->serial_number)
                                                &middot; SN: <span class="font-mono font-semibold text-ink">{{ $item->serial_number }}</span>
                                            @endif
                                            &middot; {{ $item->notes ?? 'Kondisi fisik unit tercatat' }}
                                            @if ($item->room)
                                                &middot; Ruangan: <span class="font-semibold text-ink">{{ $item->room }}</span>
                                            @endif
                                            @if ($item->purchase_price)
                                                &middot; <span class="text-forest font-semibold">Rp {{ number_format($item->purchase_price, 0, ',', '.') }}</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2">
                                    {{-- Toggle Icon Mata untuk Show/Hide Foto --}}
                                    @if ($item->photo_path)
                                        <button type="button" @click="showPhoto = !showPhoto"
                                                class="inline-flex items-center gap-1.5 rounded-lg border border-line bg-canvas px-3 py-1.5 text-xs font-semibold text-forest hover:bg-forest/5 active:scale-95 transition"
                                                title="Lihat foto kondisi fisik">
                                            <template x-if="!showPhoto">
                                                <div class="inline-flex items-center gap-1.5">
                                                    <x-ui-icon name="eye" class="h-4 w-4 text-forest" />
                                                    <span>Lihat Foto</span>
                                                </div>
                                            </template>
                                            <template x-if="showPhoto">
                                                <div class="inline-flex items-center gap-1.5">
                                                    <x-ui-icon name="eye-off" class="h-4 w-4 text-forest" />
                                                    <span>Tutup Foto</span>
                                                </div>
                                            </template>
                                        </button>
                                    @else
                                        <span class="inline-flex items-center gap-1 rounded-lg border border-line/60 bg-canvas/40 px-2.5 py-1 text-[0.7rem] text-ink-soft">
                                            <x-ui-icon name="photo" class="h-3.5 w-3.5 opacity-40" /> Belum ada foto
                                        </span>
                                    @endif

                                    @can('update', $asset)
                                        <button type="button" @click="editMode = !editMode"
                                                class="rounded-lg p-1.5 text-ink-soft hover:bg-canvas hover:text-forest"
                                                title="Perbarui kondisi / foto unit">
                                            <x-ui-icon name="pencil" class="h-4 w-4" />
                                        </button>
                                    @endcan

                                    @can('delete', $asset)
                                        <form method="POST" action="{{ route('assets.items.destroy', [$asset, $item]) }}"
                                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus Unit #{{ $item->unit_number }} ({{ $item->item_code }})? Total stok barang akan berkurang 1.');"
                                              class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="rounded-lg p-1.5 text-ink-soft hover:bg-red-50 hover:text-red-600 transition"
                                                    title="Hapus Unit Barang ini">
                                                <x-ui-icon name="trash" class="h-4 w-4" />
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            </div>

                            {{-- Area Foto yang Ditampilkan saat Toggle Mata Aktif --}}
                            <div x-show="showPhoto" x-cloak class="mt-3.5 rounded-xl border border-line bg-canvas/40 p-3">
                                <div class="flex items-start justify-between gap-2 mb-2">
                                    <p class="text-[0.7rem] font-semibold text-ink">Foto Kondisi Fisik Terakhir (Unit #{{ $item->unit_number }})</p>
                                    <button type="button" @click="showPhoto = false" class="text-ink-soft hover:text-ink">
                                        <x-ui-icon name="x" class="h-4 w-4" />
                                    </button>
                                </div>
                                <img src="{{ asset('storage/'.$item->photo_path) }}" alt="Foto {{ $item->item_code }}"
                                     class="max-h-72 rounded-lg object-contain ring-1 ring-line shadow-sm">
                            </div>

                            {{-- Form Update Kondisi & Upload Foto Baru untuk Unit ini --}}
                            @can('update', $asset)
                                <div x-show="editMode" x-cloak class="mt-3.5 rounded-xl border border-leaf/30 bg-leaf-50/50 p-4">
                                    <form method="POST" action="{{ route('assets.items.update', [$asset, $item]) }}" enctype="multipart/form-data" class="space-y-3">
                                        @csrf
                                        <div class="grid gap-3 sm:grid-cols-3">
                                            <div>
                                                <label class="label">Serial Number</label>
                                                <input type="text" name="serial_number" value="{{ old('serial_number', $item->serial_number) }}" class="field" placeholder="Contoh: SN-12345678">
                                            </div>
                                            <div>
                                                <label class="label">Kondisi Unit #{{ $item->unit_number }}</label>
                                                <select name="condition" class="field" required>
                                                    @foreach (\App\Enums\AssetCondition::options() as $val => $lbl)
                                                        <option value="{{ $val }}" @selected($item->condition?->value === $val)>{{ $lbl }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div>
                                                <label class="label">Ruangan</label>
                                                <input type="text" name="room" value="{{ old('room', $item->room) }}" class="field" placeholder="Contoh: Rak Perkakas, Gudang B">
                                            </div>
                                        </div>
                                        <div class="grid gap-3 sm:grid-cols-2">
                                            <div>
                                                <label class="label">Harga Barang (Rp)</label>
                                                <input type="number" name="purchase_price" value="{{ old('purchase_price', $item->purchase_price ? (int)$item->purchase_price : '') }}" class="field" placeholder="0" min="0" step="1000">
                                            </div>
                                            <div>
                                                <label class="label">Catatan Unit</label>
                                                <input type="text" name="notes" value="{{ old('notes', $item->notes) }}" class="field" placeholder="Contoh: lecet di pojok kanan, fungsi normal">
                                            </div>
                                        </div>
                                        <div>
                                            <x-photo-picker name="photo" id="photo_item_{{ $item->id }}"
                                                            label="Foto Kondisi Terakhir"
                                                            :current-url="$item->photo_path ? asset('storage/'.$item->photo_path) : null" />
                                        </div>
                                        <div class="flex justify-end gap-2 pt-1">
                                            <button type="button" @click="editMode = false" class="btn-ghost py-1 text-xs">Batal</button>
                                            <button type="submit" class="btn-forest py-1 text-xs">Simpan Perubahan Unit</button>
                                        </div>
                                    </form>
                                </div>
                            @endcan
                        </div>
                    @empty
                        <div class="p-6 text-center text-sm text-ink-soft">
                            Tidak ada unit terdaftar.
                        </div>
                    @endforelse
                </div>
            </section>

            {{-- Riwayat peminjaman --}}
            <section class="card overflow-hidden">
                <div class="flex items-center justify-between border-b border-line px-4 py-4 sm:px-5">
                    <h2 class="text-sm font-bold text-ink">Riwayat Peminjaman</h2>
                    <a href="{{ route('loans.history') }}" class="text-xs font-semibold text-leaf hover:underline">Semua riwayat</a>
                </div>

                <div class="table-wrap hidden sm:block">
                    <table class="data-table min-w-[42rem]">
                        <thead>
                            <tr>
                                <th scope="col">Kode</th>
                                <th scope="col">Peminjam</th>
                                <th scope="col">Jumlah</th>
                                <th scope="col">Pinjam</th>
                                <th scope="col">Kembali</th>
                                <th scope="col">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($loans as $loan)
                                <tr>
                                    <td class="font-mono text-[0.72rem]">
                                        <a href="{{ route('loans.show', $loan) }}" class="text-ink-soft hover:text-forest">{{ $loan->code }}</a>
                                    </td>
                                    <td class="font-medium text-ink">{{ $loan->borrower?->name }}</td>
                                    <td>{{ $loan->quantity }} unit</td>
                                    <td class="text-ink-soft">{{ $loan->borrowed_at?->format('d/m/Y') }}</td>
                                    <td class="text-ink-soft">{{ $loan->returned_at?->format('d/m/Y') ?? '-' }}</td>
                                    <td><x-status-badge :status="$loan->status" /></td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="py-8 text-center text-ink-soft">Belum pernah dipinjam.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <ul class="divide-y divide-line sm:hidden">
                    @forelse ($loans as $loan)
                        <li class="flex items-start justify-between gap-3 px-4 py-3">
                            <div class="min-w-0">
                                <p class="font-mono text-[0.7rem] text-ink-soft">{{ $loan->code }}</p>
                                <p class="text-sm font-semibold text-ink">{{ $loan->borrower?->name }}</p>
                                <p class="text-[0.72rem] text-ink-soft">{{ $loan->quantity }} unit &middot; {{ $loan->borrowed_at?->format('d/m/Y') }}</p>
                            </div>
                            <x-status-badge :status="$loan->status" />
                        </li>
                    @empty
                        <li class="px-4 py-6 text-center text-sm text-ink-soft">Belum pernah dipinjam.</li>
                    @endforelse
                </ul>
            </section>

            {{-- Aktivitas --}}
            <section class="card overflow-hidden">
                <div class="border-b border-line px-4 py-4 sm:px-5">
                    <h2 class="text-sm font-bold text-ink">Jejak Aktivitas Aset</h2>
                </div>
                <ol class="divide-y divide-line">
                    @forelse ($activities as $activity)
                        <li class="flex items-start gap-3 px-4 py-3">
                            <span class="mt-0.5 grid h-8 w-8 shrink-0 place-items-center rounded-lg bg-canvas text-ink-soft">
                                <x-ui-icon :name="$activity->icon()" class="h-4 w-4" />
                            </span>
                            <div class="min-w-0 flex-1">
                                <p class="text-[0.8rem] leading-snug text-ink">{{ $activity->description }}</p>
                                <p class="text-[0.68rem] text-ink-soft">
                                    {{ $activity->user?->name ?? 'Sistem' }} &middot; {{ $activity->created_at->translatedFormat('d M Y H:i') }}
                                </p>
                            </div>
                        </li>
                    @empty
                        <li class="px-4 py-6 text-center text-sm text-ink-soft">Belum ada aktivitas tercatat.</li>
                    @endforelse
                </ol>
            </section>
        </div>

        {{-- Panel kanan --}}
        <div class="space-y-4">
            <section class="card p-4 sm:p-5">
                <h2 class="mb-3 text-sm font-bold text-ink">Nilai &amp; Perolehan</h2>
                <dl class="space-y-2 text-[0.78rem]">
                    <div class="flex items-center justify-between">
                        <dt class="text-ink-soft">Tanggal beli</dt>
                        <dd class="font-semibold text-ink">{{ $asset->purchase_date?->translatedFormat('d M Y') ?? '-' }}</dd>
                    </div>
                    <div class="flex items-center justify-between border-t border-line pt-2">
                        <dt class="text-ink-soft">Nilai total</dt>
                        <dd class="font-bold text-forest">Rp {{ number_format($asset->totalValue(), 0, ',', '.') }}</dd>
                    </div>
                </dl>

                @if ($asset->description)
                    <div class="mt-4 border-t border-line pt-3">
                        <p class="text-[0.72rem] font-semibold text-ink-soft">Keterangan</p>
                        <p class="mt-1 text-[0.78rem] leading-relaxed text-ink">{{ $asset->description }}</p>
                    </div>
                @endif
            </section>
        </div>
    </div>
</x-app-layout>