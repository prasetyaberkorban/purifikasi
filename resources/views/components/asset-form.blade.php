@props(['asset', 'categories', 'locations', 'action', 'method' => 'POST', 'submitLabel' => 'Simpan Aset', 'presetCategory' => null, 'presetLocation' => null])

<form method="POST" action="{{ $action }}" enctype="multipart/form-data" class="grid gap-4 lg:grid-cols-3">
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    <div class="space-y-4 lg:col-span-2">
        <section class="card p-4 sm:p-5">
            <h2 class="text-sm font-bold text-ink">Identitas Barang</h2>
            <p class="mb-4 text-[0.72rem] text-ink-soft">Kode aset dibuat otomatis dengan format <span class="font-mono">SMG.[KATEGORI]/[TAHUN]/[NOMOR_URUT]</span> (contoh <span class="font-mono">SMG.PL/2026/001</span>).</p>

            <div class="grid gap-3.5 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label for="name" class="label">Nama Barang <span class="text-red-500">*</span></label>
                    <input id="name" name="name" type="text" value="{{ old('name', $asset->name) }}" required class="field" placeholder="Contoh: Linggis">
                </div>

                <div>
                    <label for="serial_number" class="label">Serial Number <span class="text-xs font-normal text-ink-soft">(Opsional)</span></label>
                    <input id="serial_number" name="serial_number" type="text" value="{{ old('serial_number', $asset->serial_number) }}" class="field" placeholder="Opsional (contoh: SN-PL-01007)">
                </div>

                <div>
                    <label for="brand" class="label">Merek / Tipe</label>
                    <input id="brand" name="brand" type="text" value="{{ old('brand', $asset->brand) }}" class="field" placeholder="Contoh: Bosch">
                </div>

                <div>
                    <label for="category_id" class="label">Kategori <span class="text-red-500">*</span></label>
                    <select id="category_id" name="category_id" required class="field">
                        <option value="">Pilih kategori</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}"
                                @selected((string) old('category_id', $asset->category_id ?? $presetCategory) === (string) $category->id)>
                                {{ $category->name }} ({{ $category->code }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="room" class="label">Ruangan / Rak</label>
                    <input id="room" name="room" type="text" value="{{ old('room', $asset->room) }}" class="field" placeholder="Gudang A">
                </div>

                <div>
                    <label for="condition" class="label">Kondisi <span class="text-red-500">*</span></label>
                    <select id="condition" name="condition" required class="field">
                        @foreach (\App\Enums\AssetCondition::options() as $value => $label)
                            <option value="{{ $value }}" @selected(old('condition', $asset->condition?->value ?? 'belum_dicek') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <label for="description" class="label">Keterangan</label>
                    <textarea id="description" name="description" rows="3" class="field" placeholder="Catatan tambahan tentang barang ini.">{{ old('description', $asset->description) }}</textarea>
                </div>
            </div>
        </section>

        <section class="card p-4 sm:p-5">
            <h2 class="text-sm font-bold text-ink">Stok &amp; Perolehan</h2>

            <div class="grid gap-3.5 sm:grid-cols-2">
                <div>
                    <label for="quantity" class="label">Jumlah Stok <span class="text-red-500">*</span></label>
                    <input id="quantity" name="quantity" type="number" min="0" value="{{ old('quantity', $asset->quantity ?? 1) }}" required class="field">
                </div>
                <div>
                    <label for="purchase_date" class="label">Tanggal Beli</label>
                    <input id="purchase_date" name="purchase_date" type="date"
                           value="{{ old('purchase_date', $asset->purchase_date?->format('Y-m-d')) }}" class="field">
                </div>
                <div>
                    <label for="purchase_price" class="label">Harga Beli (Rp)</label>
                    <input id="purchase_price" name="purchase_price" type="number" min="0" step="1000"
                           value="{{ old('purchase_price', $asset->purchase_price) }}" class="field" placeholder="125000">
                </div>
            </div>
        </section>
    </div>

    <div class="space-y-4">
        <section class="card p-4 sm:p-5">
            <h2 class="text-sm font-bold text-ink">Foto Barang</h2>
            <p class="mb-3 text-[0.72rem] text-ink-soft">JPG, PNG, atau WEBP maksimal 4 MB.</p>

            <x-photo-picker name="photo" id="photo"
                            :current-url="$asset->photoUrl()"
                            hint="Pilih apakah ingin langsung mengambil foto dari kamera atau mengunggah dari galeri/file." />

            @if ($asset->photo_path)
                <label class="mt-3 flex items-center gap-2 text-[0.75rem] text-ink-soft">
                    <input type="checkbox" name="remove_photo" value="1" class="h-4 w-4 rounded border-line text-leaf focus:ring-leaf/30">
                    Hapus foto saat ini
                </label>
            @endif
        </section>

        <section class="card p-4 sm:p-5">
            <h2 class="mb-3 text-sm font-bold text-ink">Ringkasan</h2>
            <dl class="space-y-2 text-[0.78rem]">
                <div class="flex items-center justify-between">
                    <dt class="text-ink-soft">Status otomatis</dt>
                    <dd class="font-semibold text-ink">{{ $asset->status?->label() ?? 'Tersedia' }}</dd>
                </div>
                <div class="flex items-center justify-between">
                    <dt class="text-ink-soft">Sedang dipinjam</dt>
                    <dd class="font-semibold text-ink">{{ $asset->borrowed_quantity ?? 0 }} unit</dd>
                </div>
                <div class="flex items-center justify-between">
                    <dt class="text-ink-soft">Tersedia</dt>
                    <dd class="font-semibold text-ink">{{ $asset->availableQuantity() }} unit</dd>
                </div>
            </dl>

            <div class="mt-4 flex flex-col gap-2">
                <button type="submit" class="btn-primary w-full">{{ $submitLabel }}</button>
                <a href="{{ $asset->exists ? route('assets.show', $asset) : route('assets.index') }}" class="btn-ghost w-full">Batal</a>
            </div>
        </section>
    </div>
</form>