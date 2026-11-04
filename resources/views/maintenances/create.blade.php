<x-app-layout title="Formulir Service dan Pemeliharaan Aset Kantor" subtitle="Pelaporan kerusakan dan dokumentasi pemeliharaan aset kantor" active="services">
    <div class="space-y-5 max-w-4xl">
        {{-- Banner Informasi Paling Atas --}}
        <section class="card border-l-4 border-l-forest bg-gradient-to-r from-leaf-50/60 via-canvas to-canvas p-4 sm:p-5">
            <div class="flex items-start gap-3.5">
                <div class="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-forest/10 text-forest">
                    <x-ui-icon name="shield-check" class="h-5 w-5" />
                </div>
                <p class="text-xs leading-relaxed text-ink pt-1">
                    Formulir ini digunakan untuk melaporkan kerusakan, mengajukan perbaikan, serta mendokumentasikan kegiatan service dan pemeliharaan aset kantor guna memastikan seluruh aset perusahaan tetap dalam kondisi baik dan dapat digunakan secara optimal.
                </p>
            </div>
        </section>

        {{-- Form Service & Pemeliharaan --}}
        <form method="POST" action="{{ route('services.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <section class="card p-4 sm:p-6 space-y-5">
                <div class="flex items-center justify-between border-b border-line pb-3">
                    <div>
                        <h2 class="text-sm font-bold text-ink">Data Pengajuan &amp; Barang</h2>
                        <p class="text-[0.72rem] text-ink-soft">Semua kolom wajib diisi lengkap sebelum mengirimkan formulir.</p>
                    </div>
                    <span class="rounded-lg bg-red-50 px-2 py-1 text-[0.7rem] font-semibold text-red-600 border border-red-200">
                        * Wajib Diisi
                    </span>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    {{-- 1. Nama Pengguna PIC --}}
                    <div>
                        <label for="pic_name" class="label">
                            1. Nama Pengguna PIC <span class="text-red-500">*</span>
                        </label>
                        <input id="pic_name" name="pic_name" type="text" required
                               value="{{ old('pic_name', auth()->user()->name) }}"
                               class="field" placeholder="Nama lengkap penanggung jawab">
                        @error('pic_name')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- 2. Nama Aset / Barang --}}
                    <div>
                        <label for="asset_name" class="label">
                            2. Nama Aset / Barang <span class="text-red-500">*</span>
                        </label>
                        <input id="asset_name" name="asset_name" type="text" required
                               value="{{ old('asset_name') }}"
                               class="field" placeholder="Contoh: AC Daikin 1 PK, Printer Epson">
                        @error('asset_name')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- 3. Merk / Type --}}
                    <div>
                        <label for="brand_type" class="label">
                            3. Merk / Type <span class="text-red-500">*</span>
                        </label>
                        <input id="brand_type" name="brand_type" type="text" required
                               value="{{ old('brand_type') }}"
                               class="field" placeholder="Contoh: Daikin Inverter FTKQ25UVM4 / Epson L3210">
                        @error('brand_type')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- 5. Apakah sebelumnya sudah pernah di service ? --}}
                    <div>
                        <label class="label">
                            5. Apakah sebelumnya sudah pernah di service? <span class="text-red-500">*</span>
                        </label>
                        <div class="flex items-center gap-4 pt-1">
                            <label class="inline-flex items-center gap-2 text-xs font-semibold text-ink cursor-pointer">
                                <input type="radio" name="previously_serviced" value="belum" required
                                       @checked(old('previously_serviced', 'belum') === 'belum')
                                       class="h-4 w-4 border-line text-forest focus:ring-forest/30">
                                <span>Belum Pernah</span>
                            </label>
                            <label class="inline-flex items-center gap-2 text-xs font-semibold text-ink cursor-pointer">
                                <input type="radio" name="previously_serviced" value="sudah" required
                                       @checked(old('previously_serviced') === 'sudah')
                                       class="h-4 w-4 border-line text-forest focus:ring-forest/30">
                                <span>Sudah Pernah</span>
                            </label>
                        </div>
                        @error('previously_serviced')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- 6. Tanggal service --}}
                    <div>
                        <label for="service_date" class="label">
                            6. Tanggal Service <span class="text-red-500">*</span>
                        </label>
                        <input id="service_date" name="service_date" type="date" required
                               value="{{ old('service_date', now()->toDateString()) }}"
                               class="field">
                        @error('service_date')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- 7. Alamat Tempat Service --}}
                    <div class="sm:col-span-2">
                        <label for="service_location" class="label">
                            7. Alamat Tempat Service <span class="text-red-500">*</span>
                        </label>
                        <textarea id="service_location" name="service_location" rows="2" required
                                  class="field" placeholder="Contoh: Service Center Resmi Daikin, Jl. Raya Margonda No. 123, Depok / Teknisi Panggilan Datang ke Kantor">{{ old('service_location') }}</textarea>
                        @error('service_location')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- 4. Deskripsi Kerusakan / Keluhan --}}
                    <div class="sm:col-span-2">
                        <label for="damage_description" class="label">
                            4. Deskripsi Kerusakan / Keluhan <span class="text-red-500">*</span>
                        </label>
                        <textarea id="damage_description" name="damage_description" rows="3" required
                                  class="field" placeholder="Jelaskan secara detail kendala, gejala kerusakan, atau perbaikan yang dilakukan...">{{ old('damage_description') }}</textarea>
                        @error('damage_description')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </section>

            {{-- Bagian Upload Foto (Dokumentasi & Nota) --}}
            <section class="card p-4 sm:p-6 space-y-5">
                <div class="border-b border-line pb-3">
                    <h2 class="text-sm font-bold text-ink">Dokumentasi &amp; Bukti Nota</h2>
                    <p class="text-[0.72rem] text-ink-soft">Foto kondisi aset dan nota perbaikan wajib dilampirkan (bisa langsung foto dengan kamera HP / laptop atau ambil dari galeri).</p>
                </div>

                <div class="grid gap-6 sm:grid-cols-2">
                    {{-- 8. Dokumentasi Kondisi Aset (upload foto) --}}
                    <div class="rounded-xl border border-line p-4 bg-canvas/40">
                        <x-photo-picker name="condition_photo" id="condition_photo"
                                        label="8. Dokumentasi Kondisi Aset"
                                        :required="true"
                                        hint="Ambil foto fisik aset yang mengalami kerusakan atau sedang diservice." />
                    </div>

                    {{-- 9. Nota Service / Pemeliharaan (upload foto) --}}
                    <div class="rounded-xl border border-line p-4 bg-canvas/40">
                        <x-photo-picker name="invoice_photo" id="invoice_photo"
                                        label="9. Nota Service / Pemeliharaan"
                                        :required="true"
                                        hint="Ambil foto nota, kuitansi, tanda terima, atau bukti faktur service." />
                    </div>
                </div>
            </section>

            {{-- Tombol Aksi --}}
            <div class="flex flex-wrap items-center justify-between gap-3 pt-2">
                <a href="{{ route('services.index') }}" class="btn-ghost">
                    Lihat Riwayat Service
                </a>

                <div class="flex items-center gap-2">
                    <a href="{{ route('dashboard') }}" class="btn-ghost">Batal</a>
                    <button type="submit" class="btn-primary inline-flex items-center gap-2 px-5 py-2.5">
                        <x-ui-icon name="check" class="h-4 w-4" />
                        <span>Kirim Formulir Service</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</x-app-layout>
