<x-app-layout title="Formulir Peminjaman Kendaraan" subtitle="Pencatatan peminjaman kendaraan operasional kantor" active="vehicle_loans">
    <div class="space-y-5 max-w-4xl">
        {{-- Banner Pemberitahuan di Bagian Paling Atas --}}
        <div class="rounded-2xl border border-leaf/30 bg-leaf-50/70 p-4 sm:p-5 flex items-start gap-3.5 text-forest shadow-xs">
            <div class="rounded-xl bg-forest/10 p-2 text-forest shrink-0 mt-0.5">
                <x-ui-icon name="car" class="h-5 w-5" />
            </div>
            <div class="space-y-1">
                <h2 class="text-sm font-bold text-forest">Ketentuan Peminjaman Kendaraan</h2>
                <p class="text-xs sm:text-[0.82rem] leading-relaxed text-forest/90">
                    Formulir ini digunakan pada saat Personil/Tim melakukan peminjaman kendaraan untuk kepentingan perusahaan dan/atau pribadi, dengan catatan menginformasikan kepada penanggung jawab kendaraan.
                </p>
            </div>
        </div>

        {{-- Formulir Peminjaman Kendaraan --}}
        <form method="POST" action="{{ route('vehicle-loans.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <section class="card p-4 sm:p-6 space-y-5">
                <div class="flex items-center justify-between border-b border-line pb-3">
                    <div>
                        <h2 class="text-sm font-bold text-ink">Informasi Peminjaman &amp; Pengemudi</h2>
                        <p class="text-[0.72rem] text-ink-soft">Kolom bertanda bintang merah (*) wajib diisi lengkap.</p>
                    </div>
                    <span class="rounded-lg bg-red-50 px-2 py-1 text-[0.7rem] font-semibold text-red-600 border border-red-200">
                        * Wajib Diisi
                    </span>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    {{-- 1. PIC --}}
                    <div>
                        <label for="pic_name" class="label">
                            1. PIC <span class="text-red-500">*</span>
                        </label>
                        <input id="pic_name" name="pic_name" type="text" required
                               value="{{ old('pic_name', auth()->user()->name) }}"
                               class="field" placeholder="Nama Person In Charge (PIC)">
                        @error('pic_name')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- 2. Nama Driver --}}
                    <div>
                        <label for="driver_name" class="label">
                            2. Nama Driver <span class="text-red-500">*</span>
                        </label>
                        <input id="driver_name" name="driver_name" type="text" required
                               value="{{ old('driver_name') }}"
                               class="field" placeholder="Nama pengemudi yang membawa kendaraan">
                        @error('driver_name')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- 3. Tanggal Pergi --}}
                    <div>
                        <label for="departure_date" class="label">
                            3. Tanggal Pergi <span class="text-red-500">*</span>
                        </label>
                        <input id="departure_date" name="departure_date" type="date" required
                               value="{{ old('departure_date', now()->toDateString()) }}"
                               class="field">
                        @error('departure_date')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- 4. Tanggal Kembali --}}
                    <div>
                        <label for="return_date" class="label">
                            4. Tanggal Kembali <span class="text-red-500">*</span>
                        </label>
                        <input id="return_date" name="return_date" type="date" required
                               value="{{ old('return_date', now()->toDateString()) }}"
                               class="field">
                        @error('return_date')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- 5. Kilometer (Km) Berangkat (ketik) --}}
                    <div class="sm:col-span-2">
                        <label for="departure_km" class="label">
                            5. Kilometer (Km) Berangkat (ketik) <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input id="departure_km" name="departure_km" type="text" required
                                   value="{{ old('departure_km') }}"
                                   class="field pr-14" placeholder="Ketik angka kilometer awal, contoh: 45.230 atau 45230">
                            <span class="absolute right-3.5 top-1/2 -translate-y-1/2 text-xs font-semibold text-ink-soft pointer-events-none">
                                KM
                            </span>
                        </div>
                        @error('departure_km')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- 6. Jenis Kendaraan (Innova Reborn, Ertiga, Ignis, Xenia, Yang lain: --}}
                    <div class="sm:col-span-2" x-data="{ selectedType: '{{ old('vehicle_type_choice', '') }}' }">
                        <label class="label mb-2">
                            6. Jenis Kendaraan <span class="text-red-500">*</span>
                        </label>
                        <div class="grid grid-cols-2 sm:grid-cols-5 gap-2.5">
                            @foreach ($vehicleTypes as $key => $lbl)
                                <label class="flex items-center gap-2 rounded-xl border p-3 cursor-pointer text-xs font-semibold transition"
                                       :class="selectedType === '{{ $key }}' ? 'border-forest bg-forest/5 text-forest' : 'border-line text-ink hover:bg-canvas/50'">
                                    <input type="radio" name="vehicle_type_choice" value="{{ $key }}"
                                           x-model="selectedType" required
                                           @checked(old('vehicle_type_choice') === $key)
                                           class="h-4 w-4 border-line text-forest focus:ring-forest/30">
                                    <span>{{ $lbl }}</span>
                                </label>
                            @endforeach
                        </div>
                        <div x-show="selectedType === 'other'" x-cloak class="mt-3">
                            <input type="text" name="vehicle_type_custom" id="vehicle_type_custom"
                                   value="{{ old('vehicle_type_custom') }}"
                                   class="field" placeholder="Ketik jenis/merk kendaraan lainnya...">
                        </div>
                        @error('vehicle_type_choice')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                        @error('vehicle_type_custom')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- 7. Tujuan peminjaman --}}
                    <div class="sm:col-span-2">
                        <label for="purpose" class="label">
                            7. Tujuan Peminjaman <span class="text-red-500">*</span>
                        </label>
                        <textarea id="purpose" name="purpose" rows="3" required
                                  class="field" placeholder="Jelaskan tujuan peminjaman kendaraan, rute perjalanan, atau kegiatan yang dilakukan...">{{ old('purpose') }}</textarea>
                        @error('purpose')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- 8. Kondisi Kebersihan Kendaraan --}}
                    <div class="sm:col-span-2" x-data="{ selectedClean: '{{ old('cleanliness_choice', '') }}' }">
                        <label class="label mb-2">
                            8. Kondisi Kebersihan Kendaraan <span class="text-red-500">*</span>
                        </label>
                        <div class="space-y-2">
                            @foreach ($cleanlinessOptions as $key => $lbl)
                                <label class="flex items-center gap-2.5 rounded-xl border p-3 cursor-pointer text-xs font-semibold transition"
                                       :class="selectedClean === '{{ $key }}' ? 'border-forest bg-forest/5 text-forest' : 'border-line text-ink hover:bg-canvas/50'">
                                    <input type="radio" name="cleanliness_choice" value="{{ $key }}"
                                           x-model="selectedClean" required
                                           @checked(old('cleanliness_choice') === $key)
                                           class="h-4 w-4 border-line text-forest focus:ring-forest/30">
                                    <span>{{ $lbl }}</span>
                                </label>
                            @endforeach
                        </div>
                        <div x-show="selectedClean === 'other'" x-cloak class="mt-3">
                            <input type="text" name="cleanliness_custom" id="cleanliness_custom"
                                   value="{{ old('cleanliness_custom') }}"
                                   class="field" placeholder="Ketik rincian kondisi kebersihan kendaraan lainnya...">
                        </div>
                        @error('cleanliness_choice')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                        @error('cleanliness_custom')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- 9. Saran dan Masukan (optional) --}}
                    <div class="sm:col-span-2">
                        <div class="flex items-center justify-between">
                            <label for="suggestions" class="label">
                                9. Saran dan Masukan
                            </label>
                            <span class="text-[0.7rem] font-medium text-ink-soft">Opsional</span>
                        </div>
                        <textarea id="suggestions" name="suggestions" rows="3"
                                  class="field" placeholder="Tuliskan saran, masukan, keluhan performa mesin, atau catatan untuk penanggung jawab kendaraan (opsional)...">{{ old('suggestions') }}</textarea>
                        @error('suggestions')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </section>

            {{-- Bagian Dokumentasi Speedometer Berangkat & Kembali --}}
            <section class="card p-4 sm:p-6 space-y-5">
                <div class="border-b border-line pb-3">
                    <h2 class="text-sm font-bold text-ink">Dokumentasi Foto Speedometer</h2>
                    <p class="text-[0.72rem] text-ink-soft">Foto speedometer saat berangkat dan kembali wajib dilampirkan, dilengkapi timestamp (bisa langsung ambil lewat kamera HP atau pilih dari galeri).</p>
                </div>

                <div class="grid gap-6 sm:grid-cols-2">
                    {{-- 10. Lampirkan foto speedometer berangkat, dilengkapi timestamp (wajib) --}}
                    <div class="rounded-xl border border-line p-4 bg-canvas/40">
                        <x-photo-picker name="departure_speedometer_photo" id="departure_speedometer_photo"
                                        label="10. Foto Speedometer Berangkat (Dilengkapi Timestamp)"
                                        :required="true"
                                        hint="Ambil foto panel speedometer saat kendaraan mulai diberangkatkan." />
                    </div>

                    {{-- 11. Lampirkan foto speedometer kembali, dilengkapi timestamp (wajib) --}}
                    <div class="rounded-xl border border-line p-4 bg-canvas/40">
                        <x-photo-picker name="return_speedometer_photo" id="return_speedometer_photo"
                                        label="11. Foto Speedometer Kembali (Dilengkapi Timestamp)"
                                        :required="true"
                                        hint="Ambil foto panel speedometer setelah kendaraan selesai digunakan dan kembali." />
                    </div>
                </div>
            </section>

            {{-- Tombol Aksi --}}
            <div class="flex flex-wrap items-center justify-between gap-3 pt-2">
                <a href="{{ route('vehicle-loans.index') }}" class="btn-ghost">
                    Lihat Riwayat Peminjaman
                </a>

                <div class="flex items-center gap-2">
                    <a href="{{ route('dashboard') }}" class="btn-ghost">Batal</a>
                    <button type="submit" class="btn-primary inline-flex items-center gap-2 px-5 py-2.5">
                        <x-ui-icon name="check" class="h-4 w-4" />
                        <span>Kirim Formulir Peminjaman</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</x-app-layout>
