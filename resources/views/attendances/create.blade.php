<x-app-layout title="Formulir Daftar Hadir" subtitle="Presensi kehadiran rapat dan diskusi internal perusahaan" active="attendances">
    <div class="space-y-5 max-w-4xl">
        {{-- Banner Pemberitahuan di Bagian Paling Atas --}}
        <div class="rounded-2xl border border-leaf/30 bg-leaf-50/70 p-4 sm:p-5 flex items-start gap-3.5 text-forest shadow-xs">
            <div class="rounded-xl bg-forest/10 p-2 text-forest shrink-0 mt-0.5">
                <x-ui-icon name="clipboard-check" class="h-5 w-5" />
            </div>
            <div class="space-y-1">
                <h2 class="text-sm font-bold text-forest">Pemberitahuan</h2>
                <p class="text-xs sm:text-[0.82rem] leading-relaxed text-forest/90">
                    Daftar Hadir peserta terkait rapat dan/atau diskusi di PT Purifikasi Andalan Diponegoro
                </p>
            </div>
        </div>

        {{-- Formulir Daftar Hadir --}}
        <form method="POST" action="{{ route('attendances.store') }}" class="space-y-6">
            @csrf

            <section class="card p-4 sm:p-6 space-y-5">
                <div class="flex items-center justify-between border-b border-line pb-3">
                    <div>
                        <h2 class="text-sm font-bold text-ink">Data Kehadiran Peserta</h2>
                        <p class="text-[0.72rem] text-ink-soft">Semua kolom wajib diisi lengkap tidak dapat dikosongkan.</p>
                    </div>
                    <span class="rounded-lg bg-red-50 px-2 py-1 text-[0.7rem] font-semibold text-red-600 border border-red-200">
                        * Wajib Diisi
                    </span>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    {{-- 1. Nama lengkap --}}
                    <div>
                        <label for="full_name" class="label">
                            1. Nama Lengkap <span class="text-red-500">*</span>
                        </label>
                        <input id="full_name" name="full_name" type="text" required
                               value="{{ old('full_name', auth()->user()->name) }}"
                               class="field" placeholder="Ketik nama lengkap peserta">
                        @error('full_name')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- 2. Jabatan --}}
                    <div>
                        <label for="position" class="label">
                            2. Jabatan <span class="text-red-500">*</span>
                        </label>
                        <input id="position" name="position" type="text" required
                               value="{{ old('position') }}"
                               class="field" placeholder="Contoh: Manager Operasional, Staf IT, dll.">
                        @error('position')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- 3. Cabang --}}
                    <div class="sm:col-span-2" x-data="{ selectedBranch: '{{ old('branch_choice', '') }}' }">
                        <label class="label mb-2">
                            3. Cabang <span class="text-red-500">*</span>
                        </label>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5">
                            @foreach ($branchOptions as $key => $lbl)
                                <label class="flex items-center gap-2 rounded-xl border p-3 cursor-pointer text-xs font-semibold transition"
                                       :class="selectedBranch === '{{ $key }}' ? 'border-forest bg-forest/5 text-forest' : 'border-line text-ink hover:bg-canvas/50'">
                                    <input type="radio" name="branch_choice" value="{{ $key }}"
                                           x-model="selectedBranch" required
                                           @checked(old('branch_choice') === $key)
                                           class="h-4 w-4 border-line text-forest focus:ring-forest/30">
                                    <span>{{ $lbl }}</span>
                                </label>
                            @endforeach
                        </div>
                        <div x-show="selectedBranch === 'other'" x-cloak class="mt-3">
                            <input type="text" name="branch_custom" id="branch_custom"
                                   value="{{ old('branch_custom') }}"
                                   class="field" placeholder="Ketik nama cabang lainnya...">
                        </div>
                        @error('branch_choice')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                        @error('branch_custom')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- 4. Jenis kegiatan (drop down) --}}
                    <div class="sm:col-span-2" x-data="{ selectedActivity: '{{ old('activity_type_choice', '') }}' }">
                        <label for="activity_type_choice" class="label">
                            4. Jenis Kegiatan <span class="text-red-500">*</span>
                        </label>
                        <select id="activity_type_choice" name="activity_type_choice" required
                                x-model="selectedActivity"
                                class="field">
                            <option value="">-- Pilih Jenis Kegiatan / Rapat --</option>
                            @foreach ($activityOptions as $key => $lbl)
                                <option value="{{ $key }}" @selected(old('activity_type_choice') === $key)>{{ $lbl }}</option>
                            @endforeach
                        </select>
                        <div x-show="selectedActivity === 'other'" x-cloak class="mt-3">
                            <input type="text" name="activity_type_custom" id="activity_type_custom"
                                   value="{{ old('activity_type_custom') }}"
                                   class="field" placeholder="Ketik jenis kegiatan rapat/diskusi lainnya...">
                        </div>
                        @error('activity_type_choice')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                        @error('activity_type_custom')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- 5. Tanggal Pelaksanaan --}}
                    <div>
                        <label for="event_date" class="label">
                            5. Tanggal Pelaksanaan <span class="text-red-500">*</span>
                        </label>
                        <input id="event_date" name="event_date" type="date" required
                               value="{{ old('event_date', now()->toDateString()) }}"
                               class="field">
                        @error('event_date')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- 6. Pimpinan Rapat / Pemateri --}}
                    <div>
                        <label for="meeting_leader" class="label">
                            6. Pimpinan Rapat / Pemateri <span class="text-red-500">*</span>
                        </label>
                        <input id="meeting_leader" name="meeting_leader" type="text" required
                               value="{{ old('meeting_leader') }}"
                               class="field" placeholder="Nama pimpinan rapat atau pemateri">
                        @error('meeting_leader')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- 7. Kegiatan / materi yang disampaikan --}}
                    <div class="sm:col-span-2">
                        <label for="material_summary" class="label">
                            7. Kegiatan / Materi yang Disampaikan <span class="text-red-500">*</span>
                        </label>
                        <textarea id="material_summary" name="material_summary" rows="4" required
                                  class="field" placeholder="Tuliskan ringkasan materi, agenda bahasan, poin-poin keputusan, atau kesimpulan rapat...">{{ old('material_summary') }}</textarea>
                        @error('material_summary')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </section>

            {{-- Tombol Aksi --}}
            <div class="flex flex-wrap items-center justify-between gap-3 pt-2">
                <a href="{{ route('attendances.index') }}" class="btn-ghost">
                    Lihat Riwayat Daftar Hadir
                </a>

                <div class="flex items-center gap-2">
                    <a href="{{ route('dashboard') }}" class="btn-ghost">Batal</a>
                    <button type="submit" class="btn-primary inline-flex items-center gap-2 px-5 py-2.5">
                        <x-ui-icon name="check" class="h-4 w-4" />
                        <span>Kirim Daftar Hadir</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</x-app-layout>
