<x-app-layout title="Catat Pengembalian" :subtitle="$loan->code.' · '.$loan->asset?->name" active="loans">
    <form method="POST" action="{{ route('loans.return', $loan) }}" enctype="multipart/form-data" class="grid max-w-3xl gap-4">
        @csrf

        <section class="card p-4 sm:p-5">
            <h2 class="text-sm font-bold text-ink">Kondisi Saat Dikembalikan</h2>
            <p class="mb-4 text-[0.72rem] text-ink-soft">Kondisi yang dipilih akan memperbarui data kondisi aset dan menambah stok tersedia.</p>

            <div class="grid gap-3.5 sm:grid-cols-2">
                <div>
                    <label for="condition_on_return" class="label">Kondisi <span class="text-red-500">*</span></label>
                    <select id="condition_on_return" name="condition_on_return" required class="field">
                        @foreach (\App\Enums\AssetCondition::options() as $value => $label)
                            <option value="{{ $value }}" @selected(old('condition_on_return', 'baik') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="quantity" class="label">Jumlah Dikembalikan</label>
                    <input id="quantity" type="text" value="{{ $loan->quantity }} unit" class="field bg-canvas" disabled>
                </div>
                <div class="sm:col-span-2">
                    <x-photo-picker name="return_photo" id="return_photo" label="Foto Kondisi Barang" :required="true"
                                    hint="Pilih apakah ingin langsung mengambil foto dari kamera atau mengunggah dari galeri/file." />
                </div>
                <div class="sm:col-span-2">
                    <label for="note" class="label">Catatan</label>
                    <textarea id="note" name="note" rows="3" class="field" placeholder="Contoh: dikembalikan lengkap dengan kotak penyimpanan.">{{ old('note', $loan->note) }}</textarea>
                </div>
            </div>
        </section>

        <div class="flex flex-wrap gap-2">
            <button type="submit" class="btn-primary">Simpan Pengembalian</button>
            <a href="{{ route('loans.show', $loan) }}" class="btn-ghost">Batal</a>
        </div>
    </form>
</x-app-layout>
