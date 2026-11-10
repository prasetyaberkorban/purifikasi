@props(['asset', 'categories', 'locations', 'action', 'method' => 'POST', 'submitLabel' => 'Simpan Aset', 'presetCategory' => null, 'presetLocation' => null])

<form method="POST" action="{{ $action }}" enctype="multipart/form-data" class="grid gap-4 lg:grid-cols-3"
      x-data="{
          currentBranch: '{{ old('branch', $asset->branch ?? 'Semarang 1') }}',
          currentBranchCode: '{{ \App\Models\Asset::branchCode(old('branch', $asset->branch ?? 'Semarang 1')) }}'
      }"
      @branch-changed.window="currentBranch = $event.detail.branch; currentBranchCode = $event.detail.code">
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    <div class="space-y-4 lg:col-span-2">
        <section class="card p-4 sm:p-5">
            <h2 class="text-sm font-bold text-ink">Identitas Barang</h2>
            <p class="mb-4 text-[0.72rem] text-ink-soft">
                Kode aset dibuat otomatis dengan format
                <span class="font-mono font-semibold text-forest" x-text="currentBranchCode + '.[KATEGORI]/{{ date('Y') }}/[NOMOR_URUT]'">{{ \App\Models\Asset::branchCode(old('branch', $asset->branch ?? 'Semarang 1')) }}.[KATEGORI]/{{ date('Y') }}/001</span>
                (contoh <span class="font-mono">SMG1.PL/{{ date('Y') }}/001</span>, <span class="font-mono">SMG2.PL/{{ date('Y') }}/001</span>, atau <span class="font-mono">BDG.PL/{{ date('Y') }}/001</span>).
            </p>

            <div class="grid gap-3.5 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label for="name" class="label">Nama Barang <span class="text-red-500">*</span></label>
                    <input id="name" name="name" type="text" value="{{ old('name', $asset->name) }}" required class="field" placeholder="Contoh: Linggis">
                </div>

                {{-- Cabang (Dropdown Modern) --}}
                <div x-data="{
                    open: false,
                    selected: '{{ old('branch', $asset->branch ?? 'Semarang 1') }}',
                    branches: [
                        { id: 'Semarang 1', name: 'Semarang 1', code: 'SMG1', desc: 'Kantor Cabang Semarang 1' },
                        { id: 'Semarang 2', name: 'Semarang 2', code: 'SMG2', desc: 'Kantor Cabang Semarang 2' },
                        { id: 'Bandung', name: 'Bandung', code: 'BDG', desc: 'Kantor Cabang Bandung' }
                    ],
                    get current() {
                        return this.branches.find(b => b.id === this.selected) || this.branches[0];
                    },
                    select(branch) {
                        this.selected = branch.id;
                        this.open = false;
                        $dispatch('branch-changed', { branch: branch.id, code: branch.code });
                    }
                }" @click.outside="open = false" class="relative">
                    <label class="label">Cabang <span class="text-red-500">*</span></label>
                    <input type="hidden" name="branch" :value="selected">

                    {{-- Modern Trigger Button --}}
                    <button type="button"
                            @click="open = !open"
                            class="field flex items-center justify-between text-left transition hover:border-leaf focus:border-leaf focus:ring-2 focus:ring-leaf/20">
                        <div class="flex items-center gap-2.5 truncate">
                            <span class="grid h-6 w-6 shrink-0 place-items-center rounded-lg bg-leaf-50 text-leaf">
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                            </span>
                            <div class="truncate">
                                <span class="block text-sm font-semibold text-ink" x-text="current.name"></span>
                            </div>
                        </div>
                        <div class="flex items-center gap-1.5 shrink-0 ml-2">
                            <span class="rounded-md bg-leaf-50 px-2 py-0.5 font-mono text-[0.68rem] font-bold text-forest" x-text="current.code"></span>
                            <svg class="h-4 w-4 text-slate-400 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </button>

                    {{-- Modern Floating Dropdown Menu --}}
                    <div x-show="open"
                         x-cloak
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                         x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                         class="absolute z-30 mt-1.5 w-full rounded-xl border border-line bg-white p-1.5 shadow-xl">
                        <template x-for="b in branches" :key="b.id">
                            <button type="button"
                                    @click="select(b)"
                                    class="flex w-full items-center justify-between rounded-lg px-3 py-2 text-left transition hover:bg-leaf-50/60"
                                    :class="{ 'bg-leaf-50 text-forest font-semibold': selected === b.id, 'text-ink': selected !== b.id }">
                                <div class="flex items-center gap-2.5">
                                    <span class="grid h-6 w-6 place-items-center rounded-md text-xs font-bold"
                                          :class="selected === b.id ? 'bg-leaf text-white' : 'bg-slate-100 text-ink-soft'"
                                          x-text="b.code.substring(0, 3)"></span>
                                    <div>
                                        <div class="text-sm font-medium" x-text="b.name"></div>
                                        <div class="text-[0.7rem] text-ink-soft" x-text="b.desc"></div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="rounded bg-slate-100 px-1.5 py-0.5 font-mono text-[0.65rem] font-semibold text-ink-soft" x-text="b.code"></span>
                                    <svg x-show="selected === b.id" class="h-4 w-4 text-leaf shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                            </button>
                        </template>
                    </div>
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
                    <label for="serial_number" class="label">Serial Number <span class="text-xs font-normal text-ink-soft">(Opsional)</span></label>
                    <input id="serial_number" name="serial_number" type="text" value="{{ old('serial_number', $asset->serial_number) }}" class="field" placeholder="Opsional (contoh: SN-PL-01007)">
                </div>

                <div>
                    <label for="brand" class="label">Merek / Tipe</label>
                    <input id="brand" name="brand" type="text" value="{{ old('brand', $asset->brand) }}" class="field" placeholder="Contoh: Bosch">
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
                <div class="flex items-center justify-between border-t border-line pt-2">
                    <dt class="text-ink-soft">Cabang</dt>
                    <dd class="font-semibold text-forest" x-text="currentBranch">{{ old('branch', $asset->branch ?? 'Semarang 1') }}</dd>
                </div>
            </dl>

            <div class="mt-4 flex flex-col gap-2">
                <button type="submit" class="btn-primary w-full">{{ $submitLabel }}</button>
                <a href="{{ $asset->exists ? route('assets.show', $asset) : route('assets.index') }}" class="btn-ghost w-full">Batal</a>
            </div>
        </section>
    </div>
</form>