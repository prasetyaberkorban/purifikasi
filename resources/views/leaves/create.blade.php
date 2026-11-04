<x-app-layout title="Formulir Izin/Cuti/WFA Karyawan & Magang" subtitle="Pengajuan permohonan izin kerja, cuti, atau work from anywhere" active="leaves">
    <div class="space-y-5 max-w-4xl">
        {{-- Form Izin/Cuti/WFA --}}
        <form method="POST" action="{{ route('leaves.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <section class="card p-4 sm:p-6 space-y-5">
                <div class="flex items-center justify-between border-b border-line pb-3">
                    <div>
                        <h2 class="text-sm font-bold text-ink">Data Pengajuan Izin / Cuti / WFA</h2>
                        <p class="text-[0.72rem] text-ink-soft">Semua kolom wajib diisi lengkap sebelum mengirimkan permohonan.</p>
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
                               class="field" placeholder="Nama lengkap karyawan / pemagang">
                        @error('name')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- 2. Jenis izin yang diambil --}}
                    <div>
                        <label for="leave_type" class="label">
                            2. Jenis Izin yang Diambil <span class="text-red-500">*</span>
                        </label>
                        <select id="leave_type" name="leave_type" required class="field">
                            <option value="">-- Pilih Jenis Izin / Cuti / WFA --</option>
                            @foreach ($leaveTypes as $val => $lbl)
                                <option value="{{ $val }}" @selected(old('leave_type') === $val)>{{ $lbl }}</option>
                            @endforeach
                        </select>
                        @error('leave_type')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- 3. Jumlah hari izin / cuti / WFA --}}
                    <div class="sm:col-span-2 sm:max-w-xs">
                        <label for="days_count" class="label">
                            3. Jumlah Hari Izin / Cuti / WFA <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input id="days_count" name="days_count" type="number" min="1" required
                                   value="{{ old('days_count', 1) }}"
                                   class="field pr-14" placeholder="1">
                            <span class="absolute right-3.5 top-1/2 -translate-y-1/2 text-xs font-semibold text-ink-soft pointer-events-none">
                                Hari
                            </span>
                        </div>
                        @error('days_count')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- 4. Keterangan --}}
                    <div class="sm:col-span-2">
                        <label for="description" class="label">
                            4. Keterangan <span class="text-red-500">*</span>
                        </label>
                        <textarea id="description" name="description" rows="3" required
                                  class="field" placeholder="Jelaskan alasan, rincian keperluan, atau rencana koordinasi selama izin/cuti/WFA...">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </section>

            {{-- 5. Lampiran --}}
            <section class="card p-4 sm:p-6 space-y-4"
                     x-data="{
                         fileName: '',
                         fileSize: '',
                         hasFile: false,
                         handleFileSelect(e) {
                             const file = e.target.files[0];
                             if (file) {
                                 this.fileName = file.name;
                                 const sz = file.size;
                                 if (sz >= 1048576) {
                                     this.fileSize = (sz / 1048576).toFixed(2) + ' MB';
                                 } else if (sz >= 1024) {
                                     this.fileSize = (sz / 1024).toFixed(1) + ' KB';
                                 } else {
                                     this.fileSize = sz + ' B';
                                 }
                                 this.hasFile = true;
                             }
                         },
                         clearFile() {
                             this.fileName = '';
                             this.fileSize = '';
                             this.hasFile = false;
                             this.$refs.fileInput.value = '';
                         }
                     }">
                <div class="border-b border-line pb-3">
                    <label for="attachment" class="text-sm font-bold text-ink block">
                        5. Lampiran
                    </label>
                    <p class="mt-1 text-xs text-ink-soft">
                        Unggah bukti pendukung seperti surat dokter, bukti tiket, undangan, dokumen permohonan, atau foto surat (PDF, Word, Excel, Gambar, dll. Maksimal 15 MB).
                    </p>
                </div>

                {{-- Area Upload Box --}}
                <div class="rounded-2xl border-2 border-dashed border-line bg-canvas/40 p-6 text-center hover:border-leaf/50 transition">
                    <template x-if="!hasFile">
                        <div class="space-y-3">
                            <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-forest/10 text-forest">
                                <x-ui-icon name="document" class="h-6 w-6" />
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-ink">
                                    Pilih dokumen lampiran pendukung izin / cuti / WFA
                                </p>
                                <p class="mt-1 text-[0.72rem] text-ink-soft">
                                    Format: PDF, DOC, DOCX, JPG, PNG, WEBP, XLS, XLSX
                                </p>
                            </div>
                            <button type="button" @click="$refs.fileInput.click()" class="btn-primary text-xs inline-flex items-center gap-2 px-4 py-2">
                                <x-ui-icon name="plus" class="h-4 w-4" />
                                <span>Pilih File Lampiran</span>
                            </button>
                        </div>
                    </template>

                    {{-- Preview File Terpilih --}}
                    <template x-if="hasFile">
                        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 rounded-xl border border-line bg-white p-4 shadow-sm">
                            <div class="flex items-center gap-3 min-w-0 text-left">
                                <div class="grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-forest/10 text-forest">
                                    <x-ui-icon name="document" class="h-6 w-6" />
                                </div>
                                <div class="min-w-0">
                                    <p class="truncate text-xs font-bold text-ink" x-text="fileName"></p>
                                    <p class="text-[0.7rem] text-ink-soft" x-text="'Ukuran: ' + fileSize"></p>
                                </div>
                            </div>

                            <div class="flex items-center gap-2 shrink-0">
                                <button type="button" @click="$refs.fileInput.click()" class="btn-ghost py-1 px-3 text-xs">
                                    Ganti File
                                </button>
                                <button type="button" @click="clearFile()" class="btn-ghost py-1 px-2.5 text-xs text-red-600 hover:border-red-200">
                                    <x-ui-icon name="trash" class="h-3.5 w-3.5" />
                                </button>
                            </div>
                        </div>
                    </template>

                    {{-- Native input file --}}
                    <input type="file" id="attachment" name="attachment" x-ref="fileInput"
                           class="sr-only" @change="handleFileSelect($event)">
                </div>

                @error('attachment')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </section>

            {{-- Tombol Aksi --}}
            <div class="flex flex-wrap items-center justify-between gap-3 pt-2">
                <a href="{{ route('leaves.index') }}" class="btn-ghost">
                    Lihat Riwayat Pengajuan
                </a>

                <div class="flex items-center gap-2">
                    <a href="{{ route('dashboard') }}" class="btn-ghost">Batal</a>
                    <button type="submit" class="btn-primary inline-flex items-center gap-2 px-5 py-2.5">
                        <x-ui-icon name="check" class="h-4 w-4" />
                        <span>Kirim Permohonan Izin</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</x-app-layout>
