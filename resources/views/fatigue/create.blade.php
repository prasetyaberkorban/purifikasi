<x-app-layout title="Penilaian Fatik Individual" subtitle="Evaluasi kebugaran dan kesiapan kerja karyawan" active="fatigue">
    <div class="space-y-5 max-w-4xl">
        {{-- Banner Informasi Paling Atas --}}
        <section class="card border-l-4 border-l-forest bg-gradient-to-r from-leaf-50/60 via-canvas to-canvas p-4 sm:p-5">
            <div class="flex items-start gap-3.5">
                <div class="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-forest/10 text-forest">
                    <x-ui-icon name="shield-check" class="h-5 w-5" />
                </div>
                <p class="text-xs leading-relaxed text-ink pt-1">
                    Penilaian Fatik Individual adalah proses evaluasi tingkat kelelahan fisik maupun mental pada setiap karyawan secara mandiri dan/atau melalui pengamatan atasan. Penilaian ini dilakukan untuk memastikan bahwa kondisi karyawan tetap optimal selama bekerja, sehingga risiko kesalahan kerja, kecelakaan, dan penurunan produktivitas dapat diminimalkan.
                </p>
            </div>
        </section>

        {{-- Form Penilaian Fatik --}}
        <form method="POST" action="{{ route('fatigue.store') }}" class="space-y-6">
            @csrf

            {{-- Bagian 1: Data Karyawan & Tanggal --}}
            <section class="card p-4 sm:p-6 space-y-5">
                <div class="flex items-center justify-between border-b border-line pb-3">
                    <div>
                        <h2 class="text-sm font-bold text-ink">Identitas &amp; Waktu Penilaian</h2>
                        <p class="text-[0.72rem] text-ink-soft">Semua kolom wajib diisi lengkap sebelum mengirimkan formulir.</p>
                    </div>
                    <span class="rounded-lg bg-red-50 px-2 py-1 text-[0.7rem] font-semibold text-red-600 border border-red-200">
                        * Wajib Diisi
                    </span>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    {{-- 1. Nama --}}
                    <div>
                        <label for="name" class="label">
                            1. Nama <span class="text-red-500">*</span>
                        </label>
                        <input id="name" name="name" type="text" required
                               value="{{ old('name', auth()->user()->name) }}"
                               class="field" placeholder="Nama lengkap karyawan">
                        @error('name')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- 2. Tanggal Penilaian Fatik Individual --}}
                    <div>
                        <label for="assessment_date" class="label">
                            2. Tanggal Penilaian Fatik Individual <span class="text-red-500">*</span>
                        </label>
                        <input id="assessment_date" name="assessment_date" type="date" required
                               value="{{ old('assessment_date', now()->toDateString()) }}"
                               class="field">
                        @error('assessment_date')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </section>

            {{-- Bagian 2: Kuesioner Evaluasi Fatik --}}
            <section class="card p-4 sm:p-6 space-y-6">
                <div class="border-b border-line pb-3">
                    <h2 class="text-sm font-bold text-ink">Kuesioner Kondisi Kesehatan &amp; Kesiapan Kerja</h2>
                    <p class="text-[0.72rem] text-ink-soft">Jawab seluruh pertanyaan di bawah ini dengan jujur sesuai kondisi aktual saat ini.</p>
                </div>

                <div class="space-y-5 divide-y divide-line/60">
                    {{-- 3. Sakit Ringan --}}
                    <div class="pt-2 first:pt-0">
                        <label class="label text-[0.82rem] font-semibold text-ink leading-relaxed">
                            3. Dalam 1 minggu terakhir apakah Anda mengalami sakit ringan seperti flu, demam, batuk, sesak nafas dll? <span class="text-red-500">*</span>
                        </label>
                        <div class="mt-2.5 flex flex-wrap gap-3">
                            <label class="flex items-center gap-2 rounded-xl border border-line bg-canvas px-4 py-2.5 text-xs font-semibold text-ink cursor-pointer hover:border-forest/50 transition has-[:checked]:border-forest has-[:checked]:bg-forest/10 has-[:checked]:text-forest">
                                <input type="radio" name="recent_illness" value="Ya" required
                                       @checked(old('recent_illness') === 'Ya')
                                       class="h-4 w-4 border-line text-forest focus:ring-forest/30">
                                <span>Ya</span>
                            </label>
                            <label class="flex items-center gap-2 rounded-xl border border-line bg-canvas px-4 py-2.5 text-xs font-semibold text-ink cursor-pointer hover:border-forest/50 transition has-[:checked]:border-forest has-[:checked]:bg-forest/10 has-[:checked]:text-forest">
                                <input type="radio" name="recent_illness" value="Tidak" required
                                       @checked(old('recent_illness') === 'Tidak')
                                       class="h-4 w-4 border-line text-forest focus:ring-forest/30">
                                <span>Tidak</span>
                            </label>
                        </div>
                        @error('recent_illness')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- 4. Konsumsi Obat / Zat Penyebab Kantuk --}}
                    <div class="pt-4">
                        <label class="label text-[0.82rem] font-semibold text-ink leading-relaxed">
                            4. Apakah Anda mengkonsumsi obat atau zat yang dapat menyebabkan kantuk atau menyebabkan Anda tidak layak berkendara? <span class="text-red-500">*</span>
                        </label>
                        <div class="mt-2.5 flex flex-wrap gap-3">
                            <label class="flex items-center gap-2 rounded-xl border border-line bg-canvas px-4 py-2.5 text-xs font-semibold text-ink cursor-pointer hover:border-forest/50 transition has-[:checked]:border-forest has-[:checked]:bg-forest/10 has-[:checked]:text-forest">
                                <input type="radio" name="medication_drowsy" value="Ya" required
                                       @checked(old('medication_drowsy') === 'Ya')
                                       class="h-4 w-4 border-line text-forest focus:ring-forest/30">
                                <span>Ya</span>
                            </label>
                            <label class="flex items-center gap-2 rounded-xl border border-line bg-canvas px-4 py-2.5 text-xs font-semibold text-ink cursor-pointer hover:border-forest/50 transition has-[:checked]:border-forest has-[:checked]:bg-forest/10 has-[:checked]:text-forest">
                                <input type="radio" name="medication_drowsy" value="Tidak" required
                                       @checked(old('medication_drowsy') === 'Tidak')
                                       class="h-4 w-4 border-line text-forest focus:ring-forest/30">
                                <span>Tidak</span>
                            </label>
                        </div>
                        @error('medication_drowsy')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- 5. Hal Lain yang Mengganggu --}}
                    <div class="pt-4">
                        <label class="label text-[0.82rem] font-semibold text-ink leading-relaxed">
                            5. Apakah ada hal lain yang menggangu Anda bekerja saat ini? misal : keluarga meninggal / sakit dll <span class="text-red-500">*</span>
                        </label>
                        <div class="mt-2.5 flex flex-wrap gap-3">
                            <label class="flex items-center gap-2 rounded-xl border border-line bg-canvas px-4 py-2.5 text-xs font-semibold text-ink cursor-pointer hover:border-forest/50 transition has-[:checked]:border-forest has-[:checked]:bg-forest/10 has-[:checked]:text-forest">
                                <input type="radio" name="personal_distraction" value="Ya" required
                                       @checked(old('personal_distraction') === 'Ya')
                                       class="h-4 w-4 border-line text-forest focus:ring-forest/30">
                                <span>Ya</span>
                            </label>
                            <label class="flex items-center gap-2 rounded-xl border border-line bg-canvas px-4 py-2.5 text-xs font-semibold text-ink cursor-pointer hover:border-forest/50 transition has-[:checked]:border-forest has-[:checked]:bg-forest/10 has-[:checked]:text-forest">
                                <input type="radio" name="personal_distraction" value="Tidak" required
                                       @checked(old('personal_distraction') === 'Tidak')
                                       class="h-4 w-4 border-line text-forest focus:ring-forest/30">
                                <span>Tidak</span>
                            </label>
                        </div>
                        @error('personal_distraction')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- 6. Durasi Tidur Tadi Malam --}}
                    <div class="pt-4">
                        <label class="label text-[0.82rem] font-semibold text-ink leading-relaxed">
                            6. Tadi malam Anda tidur berapa jam ? <span class="text-red-500">*</span>
                        </label>
                        <div class="mt-2.5 flex flex-wrap gap-3">
                            <label class="flex items-center gap-2 rounded-xl border border-line bg-canvas px-4 py-2.5 text-xs font-semibold text-ink cursor-pointer hover:border-forest/50 transition has-[:checked]:border-forest has-[:checked]:bg-forest/10 has-[:checked]:text-forest">
                                <input type="radio" name="sleep_hours" value="kurang dari 6 jam" required
                                       @checked(old('sleep_hours') === 'kurang dari 6 jam')
                                       class="h-4 w-4 border-line text-forest focus:ring-forest/30">
                                <span>Kurang dari 6 jam</span>
                            </label>
                            <label class="flex items-center gap-2 rounded-xl border border-line bg-canvas px-4 py-2.5 text-xs font-semibold text-ink cursor-pointer hover:border-forest/50 transition has-[:checked]:border-forest has-[:checked]:bg-forest/10 has-[:checked]:text-forest">
                                <input type="radio" name="sleep_hours" value="lebih dari 6 jam" required
                                       @checked(old('sleep_hours') === 'lebih dari 6 jam')
                                       class="h-4 w-4 border-line text-forest focus:ring-forest/30">
                                <span>Lebih dari 6 jam</span>
                            </label>
                        </div>
                        @error('sleep_hours')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- 7. Yakin/Mampu Mengendarai Kendaraan --}}
                    <div class="pt-4">
                        <label class="label text-[0.82rem] font-semibold text-ink leading-relaxed">
                            7. Apakah Anda yakin/ mampu mengendarai kendaraan dalam waktu perjalanan dinas ini? <span class="text-red-500">*</span>
                        </label>
                        <div class="mt-2.5 flex flex-wrap gap-3">
                            <label class="flex items-center gap-2 rounded-xl border border-line bg-canvas px-4 py-2.5 text-xs font-semibold text-ink cursor-pointer hover:border-forest/50 transition has-[:checked]:border-forest has-[:checked]:bg-forest/10 has-[:checked]:text-forest">
                                <input type="radio" name="fit_to_drive" value="Ya" required
                                       @checked(old('fit_to_drive') === 'Ya')
                                       class="h-4 w-4 border-line text-forest focus:ring-forest/30">
                                <span>Ya</span>
                            </label>
                            <label class="flex items-center gap-2 rounded-xl border border-line bg-canvas px-4 py-2.5 text-xs font-semibold text-ink cursor-pointer hover:border-forest/50 transition has-[:checked]:border-forest has-[:checked]:bg-forest/10 has-[:checked]:text-forest">
                                <input type="radio" name="fit_to_drive" value="Tidak" required
                                       @checked(old('fit_to_drive') === 'Tidak')
                                       class="h-4 w-4 border-line text-forest focus:ring-forest/30">
                                <span>Tidak</span>
                            </label>
                        </div>
                        @error('fit_to_drive')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </section>

            {{-- Tombol Aksi --}}
            <div class="flex flex-wrap items-center justify-between gap-3 pt-2">
                <a href="{{ route('fatigue.index') }}" class="btn-ghost">
                    Lihat Riwayat Penilaian
                </a>

                <div class="flex items-center gap-2">
                    <a href="{{ route('dashboard') }}" class="btn-ghost">Batal</a>
                    <button type="submit" class="btn-primary inline-flex items-center gap-2 px-5 py-2.5">
                        <x-ui-icon name="check" class="h-4 w-4" />
                        <span>Kirim Penilaian Fatik</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</x-app-layout>
