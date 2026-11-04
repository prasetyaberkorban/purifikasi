<x-app-layout title="Formulir Realisasi Pengadaan dan Aset Kantor" subtitle="Pencatatan realisasi pembelian dan pengadaan aset kantor" active="procurements">
    <div class="space-y-5 max-w-4xl">
        {{-- Form Realisasi Pengadaan --}}
        <form method="POST" action="{{ route('procurements.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <section class="card p-4 sm:p-6 space-y-5">
                <div class="flex items-center justify-between border-b border-line pb-3">
                    <div>
                        <h2 class="text-sm font-bold text-ink">Data Realisasi Pengadaan</h2>
                        <p class="text-[0.72rem] text-ink-soft">Semua kolom wajib diisi lengkap sebelum mengirimkan formulir.</p>
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
                               class="field" placeholder="Nama lengkap PIC / Penanggung jawab">
                        @error('pic_name')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- 2. Tanggal Realisasi --}}
                    <div>
                        <label for="realization_date" class="label">
                            2. Tanggal Realisasi <span class="text-red-500">*</span>
                        </label>
                        <input id="realization_date" name="realization_date" type="date" required
                               value="{{ old('realization_date', now()->toDateString()) }}"
                               class="field">
                        @error('realization_date')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- 3. Nama Barang --}}
                    <div>
                        <label for="item_name" class="label">
                            3. Nama Barang <span class="text-red-500">*</span>
                        </label>
                        <input id="item_name" name="item_name" type="text" required
                               value="{{ old('item_name') }}"
                               class="field" placeholder="Contoh: Meja Kerja Kayu, Monitor LED, Printer">
                        @error('item_name')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- 4. Merk Type --}}
                    <div>
                        <label for="brand_type" class="label">
                            4. Merk Type <span class="text-red-500">*</span>
                        </label>
                        <input id="brand_type" name="brand_type" type="text" required
                               value="{{ old('brand_type') }}"
                               class="field" placeholder="Contoh: LG 24MP400 / Olympic / Brother DCP-T720DW">
                        @error('brand_type')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- 5. Jumlah / Ukuran --}}
                    <div class="sm:col-span-2">
                        <label for="quantity_size" class="label">
                            5. Jumlah / Ukuran <span class="text-red-500">*</span>
                        </label>
                        <input id="quantity_size" name="quantity_size" type="text" required
                               value="{{ old('quantity_size') }}"
                               class="field" placeholder="Contoh: 5 Unit / 24 Inch / 120 x 60 x 75 cm / 10 Box">
                        @error('quantity_size')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- 6. Nama Toko (Isi link jika beli online) --}}
                    <div class="sm:col-span-2">
                        <label for="store_name" class="label">
                            6. Nama Toko (Isi link jika beli online) <span class="text-red-500">*</span>
                        </label>
                        <textarea id="store_name" name="store_name" rows="2" required
                                  class="field" placeholder="Contoh: Toko Berkah ATK Pasar Minggu / https://tokopedia.com/toko-komputer/item-12345">{{ old('store_name') }}</textarea>
                        @error('store_name')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </section>

            {{-- 7. Bukti Realisasi (Upload 1 file: PDF, Document, Spreadsheet) --}}
            <section class="card p-4 sm:p-6 space-y-4"
                     x-data="{
                         fileName: '',
                         fileSize: '',
                         fileExt: '',
                         hasFile: false,
                         handleFileSelect(e) {
                             const file = e.target.files[0];
                             if (file) {
                                 this.fileName = file.name;
                                 this.fileExt = file.name.split('.').pop().toLowerCase();
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
                             this.fileExt = '';
                             this.hasFile = false;
                             this.$refs.fileInput.value = '';
                         }
                     }">
                <div class="border-b border-line pb-3">
                    <label for="proof_file" class="text-sm font-bold text-ink block">
                        7. Bukti Realisasi <span class="text-red-500">*</span>
                    </label>
                    <p class="mt-1 text-xs text-ink-soft">
                        Upload 1 file yang didukung: <strong>PDF</strong>, <strong>document</strong> (DOC, DOCX), atau <strong>spreadsheet</strong> (XLS, XLSX, CSV). Maksimal 15 MB.
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
                                    Pilih dokumen bukti transaksi / nota / invoice / purchase order
                                </p>
                                <p class="mt-1 text-[0.72rem] text-ink-soft">
                                    Format: .pdf, .doc, .docx, .xls, .xlsx, .csv
                                </p>
                            </div>
                            <button type="button" @click="$refs.fileInput.click()" class="btn-primary text-xs inline-flex items-center gap-2 px-4 py-2">
                                <x-ui-icon name="plus" class="h-4 w-4" />
                                <span>Pilih File Bukti</span>
                            </button>
                        </div>
                    </template>

                    {{-- Preview File Terpilih --}}
                    <template x-if="hasFile">
                        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 rounded-xl border border-line bg-white p-4 shadow-sm">
                            <div class="flex items-center gap-3 min-w-0 text-left">
                                <div class="grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-forest/10 text-forest">
                                    <template x-if="fileExt === 'pdf'">
                                        <span class="font-bold text-xs uppercase text-red-600">PDF</span>
                                    </template>
                                    <template x-if="['xls','xlsx','csv','ods'].includes(fileExt)">
                                        <span class="font-bold text-xs uppercase text-emerald-700">XLS</span>
                                    </template>
                                    <template x-if="['doc','docx','odt','rtf','txt'].includes(fileExt)">
                                        <span class="font-bold text-xs uppercase text-blue-700">DOC</span>
                                    </template>
                                    <template x-if="!['pdf','xls','xlsx','csv','ods','doc','docx','odt','rtf','txt'].includes(fileExt)">
                                        <x-ui-icon name="document" class="h-6 w-6" />
                                    </template>
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

                    {{-- Native input file (Required) --}}
                    <input type="file" id="proof_file" name="proof_file" x-ref="fileInput" required
                           accept=".pdf,.doc,.docx,.xls,.xlsx,.csv,.odt,.ods,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,text/csv"
                           class="sr-only" @change="handleFileSelect($event)">
                </div>

                @error('proof_file')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </section>

            {{-- Tombol Aksi --}}
            <div class="flex flex-wrap items-center justify-between gap-3 pt-2">
                <a href="{{ route('procurements.index') }}" class="btn-ghost">
                    Lihat Riwayat Realisasi
                </a>

                <div class="flex items-center gap-2">
                    <a href="{{ route('dashboard') }}" class="btn-ghost">Batal</a>
                    <button type="submit" class="btn-primary inline-flex items-center gap-2 px-5 py-2.5">
                        <x-ui-icon name="check" class="h-4 w-4" />
                        <span>Kirim Formulir Realisasi</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</x-app-layout>
