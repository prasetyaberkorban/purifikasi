<x-app-layout title="Pinjam Barang" subtitle="Catat peminjaman aset kantor" active="loans">
    <form method="POST" action="{{ route('loans.store') }}" enctype="multipart/form-data" class="grid gap-4 lg:grid-cols-3"
          x-data="{
              assets: {{ Js::from($assets->map(fn ($a) => ['id' => $a->id, 'name' => $a->name, 'code' => $a->code, 'available' => $a->availableQuantity(), 'room' => $a->room])->values()) }},
              assetId: '{{ old('asset_id', $selectedAsset?->id) }}',
              qty: {{ (int) old('quantity', 1) }},
              get selected() { return this.assets.find(a => String(a.id) === String(this.assetId)) || null },
              get max() { return this.selected ? this.selected.available : 1 }
          }">
        @csrf

        <div class="space-y-4 lg:col-span-2">
            <section class="card p-4 sm:p-5">
                <h2 class="text-sm font-bold text-ink">Data Peminjaman</h2>
                <p class="mb-4 text-[0.72rem] text-ink-soft">Stok otomatis berkurang dan status aset menjadi "Dipinjam".</p>

                <div class="grid gap-3.5 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label for="asset_id" class="label">Barang <span class="text-red-500">*</span></label>
                        <select id="asset_id" name="asset_id" required class="field" x-model="assetId">
                            <option value="">Pilih barang yang tersedia</option>
                            @foreach ($assets as $asset)
                                <option value="{{ $asset->id }}">
                                    {{ $asset->name }} (tersedia {{ $asset->availableQuantity() }} unit)
                                </option>
                            @endforeach
                        </select>
                        <p class="mt-1.5 text-[0.7rem] text-ink-soft" x-show="selected" x-cloak>
                            Stok tersedia: <span class="font-semibold" x-text="max"></span> unit
                        </p>
                    </div>

                    <div>
                        <label class="label">Peminjam <span class="text-red-500">*</span></label>
                        <input type="hidden" name="borrower_id" value="{{ auth()->id() }}">
                        <input type="text" value="{{ auth()->user()->name }}" readonly
                               class="field bg-canvas/70 font-medium text-ink cursor-not-allowed select-none"
                               title="Peminjam otomatis sesuai akun Anda">
                    </div>

                    <div>
                        <label for="quantity" class="label">Jumlah Unit <span class="text-red-500">*</span></label>
                        <input id="quantity" name="quantity" type="number" min="1" :max="max" x-model.number="qty" required class="field">
                        <p class="mt-1.5 text-[0.7rem] text-red-500" x-show="selected && qty > max" x-cloak>
                            Jumlah melebihi stok tersedia.
                        </p>
                    </div>

                    <div class="sm:col-span-2">
                        <label for="purpose" class="label">Keperluan <span class="text-red-500">*</span></label>
                        <input id="purpose" name="purpose" type="text" value="{{ old('purpose') }}" required class="field"
                               placeholder="Contoh: Pemeriksaan jaringan distribusi">
                    </div>

                    <div>
                        <label for="borrowed_at" class="label">Tanggal Pinjam</label>
                        <input id="borrowed_at" name="borrowed_at" type="date" value="{{ old('borrowed_at', now()->toDateString()) }}" class="field">
                    </div>

                    <div>
                        <label for="due_at" class="label">Tenggat Pengembalian</label>
                        <input id="due_at" name="due_at" type="date" value="{{ old('due_at', $defaultDue) }}" class="field">
                    </div>

                    <div class="sm:col-span-2">
                        <label for="room" class="label">Ruangan / Penempatan</label>
                        <input id="room" name="room" type="text" x-bind:value="selected?.room ?? ''" class="field" placeholder="Contoh: Gudang A">
                    </div>

                    <div class="sm:col-span-2">
                        <x-photo-picker name="initial_photo" id="initial_photo" label="Foto Kondisi Awal"
                                        hint="Pilih apakah ingin langsung mengambil foto dari kamera atau mengunggah dari galeri/file." />
                    </div>
                </div>
            </section>
        </div>

        <div class="space-y-4">
            <section class="card p-4 sm:p-5">
                <h2 class="mb-3 text-sm font-bold text-ink">Ringkasan</h2>
                <dl class="space-y-2 text-[0.78rem]">
                    <div class="flex items-center justify-between">
                        <dt class="text-ink-soft">Barang</dt>
                        <dd class="max-w-[60%] truncate text-right font-semibold text-ink" x-text="selected?.name ?? '-'">-</dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt class="text-ink-soft">Jumlah</dt>
                        <dd class="font-semibold text-ink" x-text="qty + ' unit'">1 unit</dd>
                    </div>
                </dl>

                <div class="mt-4 flex flex-col gap-2">
                    <button type="submit" class="btn-primary w-full" :disabled="!selected || qty > max">Simpan Peminjaman</button>
                    <a href="{{ route('loans.active') }}" class="btn-ghost w-full">Batal</a>
                </div>
            </section>

            <section class="card p-4 text-[0.72rem] leading-relaxed text-ink-soft">
                <p class="font-semibold text-ink">Catatan</p>
                <p class="mt-1">Saat pengembalian dicatat, stok dan status aset otomatis diperbarui beserta kondisi barang.</p>
            </section>
        </div>
    </form>
</x-app-layout>
