<x-app-layout title="Ubah Rekapitulasi Kontrak / SPK" subtitle="Perbarui data surat perintah kerja dan kontrak kerja proyek" active="contracts">
    <div class="mx-auto max-w-4xl space-y-4"
         x-data="{
             projectName: '{{ old('project_name', $contract->project_name) }}',
             rawContractValue: '{{ old('contract_value', (int) $contract->contract_value) }}',
             formattedContractValue: '',
             
             init() {
                 if (this.rawContractValue) {
                     this.formatFromRaw(this.rawContractValue);
                 }
             },
             
             saveProject(val) {
                 this.projectName = val;
                 localStorage.setItem('sticky_contract_project', val);
             },
             
             onContractInput(e) {
                 let val = e.target.value.replace(/[^0-9]/g, '');
                 this.rawContractValue = val;
                 this.formattedContractValue = val ? new Intl.NumberFormat('id-ID').format(val) : '';
             },
             
             formatFromRaw(val) {
                 let num = String(val).replace(/[^0-9]/g, '');
                 this.formattedContractValue = num ? new Intl.NumberFormat('id-ID').format(num) : '';
             }
         }">

        <form method="POST" action="{{ route('contracts.update', $contract) }}" enctype="multipart/form-data" class="card p-5 sm:p-6 space-y-6">
            @csrf
            @method('PUT')

            {{-- 1. No. SPK / PO & Tanggal SPK --}}
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="spk_number" class="label">
                        2. No. SPK / PO <span class="text-red-500">*</span>
                    </label>
                    <input id="spk_number" type="text" name="spk_number" value="{{ old('spk_number', $contract->spk_number) }}"
                           required
                           class="field @error('spk_number') border-red-500 ring-1 ring-red-500 @enderror">
                    @error('spk_number')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="spk_date" class="label">
                        3. Tanggal SPK <span class="text-red-500">*</span>
                    </label>
                    <input id="spk_date" type="date" name="spk_date" value="{{ old('spk_date', $contract->spk_date?->format('Y-m-d')) }}"
                           required
                           class="field @error('spk_date') border-red-500 ring-1 ring-red-500 @enderror">
                    @error('spk_date')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- 2. Proyek / Kegiatan & Nama Pihak Kedua --}}
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="project_name" class="label">
                        4. Proyek / Kegiatan <span class="text-red-500">*</span>
                    </label>
                    <input id="project_name" type="text" name="project_name"
                           list="project_list"
                           x-model="projectName"
                           @input="saveProject($event.target.value)"
                           placeholder="Pilih proyek yang ada atau ketik proyek baru..."
                           required
                           class="field @error('project_name') border-red-500 ring-1 ring-red-500 @enderror">
                    <datalist id="project_list">
                        @foreach ($existingProjects as $project)
                            <option value="{{ $project }}"></option>
                        @endforeach
                    </datalist>
                    @error('project_name')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="second_party_name" class="label">
                        5. Nama Pihak Kedua <span class="text-red-500">*</span>
                    </label>
                    <input id="second_party_name" type="text" name="second_party_name" value="{{ old('second_party_name', $contract->second_party_name) }}"
                           required
                           class="field @error('second_party_name') border-red-500 ring-1 ring-red-500 @enderror">
                    @error('second_party_name')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- 3. Jabatan / Uraian Pekerjaan --}}
            <div>
                <label for="job_description" class="label">
                    6. Jabatan / Uraian Pekerjaan <span class="text-red-500">*</span>
                </label>
                <textarea id="job_description" name="job_description" rows="3"
                          required
                          class="field @error('job_description') border-red-500 ring-1 ring-red-500 @enderror">{{ old('job_description', $contract->job_description) }}</textarea>
                @error('job_description')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- 4. Nilai Kontrak & Durasi --}}
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="contract_value_display" class="label">
                        7. Nilai Kontrak (Rp) <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 font-bold text-xs text-ink-soft">Rp</span>
                        <input id="contract_value_display" type="text"
                               :value="formattedContractValue"
                               @input="onContractInput($event)"
                               required
                               class="field pl-10 font-bold text-forest @error('contract_value') border-red-500 ring-1 ring-red-500 @enderror">
                    </div>
                    <input type="hidden" name="contract_value" :value="rawContractValue">
                    @error('contract_value')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="duration_months" class="label">
                        8. Durasi (Bulan) <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input id="duration_months" type="number" name="duration_months" value="{{ old('duration_months', $contract->duration_months) }}"
                               min="1" max="120"
                               required
                               class="field pr-14 @error('duration_months') border-red-500 ring-1 ring-red-500 @enderror">
                        <span class="absolute right-3.5 top-1/2 -translate-y-1/2 text-xs font-semibold text-ink-soft">Bulan</span>
                    </div>
                    @error('duration_months')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- 5. Skema Termin I, II, III (%) --}}
            <div class="rounded-xl border border-line bg-canvas/60 p-4">
                <label class="label mb-2 block font-bold text-ink">Skema Pembayaran Termin (%)</label>
                <div class="grid gap-3 sm:grid-cols-3">
                    <div>
                        <label for="termin_1" class="text-[0.72rem] font-semibold text-ink-soft">9. Termin I (%)</label>
                        <div class="relative mt-1">
                            <input id="termin_1" type="number" step="0.01" min="0" max="100" name="termin_1"
                                   value="{{ old('termin_1', $contract->termin_1) }}"
                                   class="field pr-8 text-xs font-semibold">
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-ink-soft">%</span>
                        </div>
                    </div>
                    <div>
                        <label for="termin_2" class="text-[0.72rem] font-semibold text-ink-soft">10. Termin II (%)</label>
                        <div class="relative mt-1">
                            <input id="termin_2" type="number" step="0.01" min="0" max="100" name="termin_2"
                                   value="{{ old('termin_2', $contract->termin_2) }}"
                                   class="field pr-8 text-xs font-semibold">
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-ink-soft">%</span>
                        </div>
                    </div>
                    <div>
                        <label for="termin_3" class="text-[0.72rem] font-semibold text-ink-soft">11. Termin III (%)</label>
                        <div class="relative mt-1">
                            <input id="termin_3" type="number" step="0.01" min="0" max="100" name="termin_3"
                                   value="{{ old('termin_3', $contract->termin_3) }}"
                                   class="field pr-8 text-xs font-semibold">
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-ink-soft">%</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 6. Rekening Bank (STRICT: TANPA DROPDOWN, HANYA TEXT INPUT) --}}
            <div class="grid gap-4 sm:grid-cols-3">
                <div>
                    <label for="bank_name" class="label">
                        12. Bank <span class="text-red-500">*</span>
                    </label>
                    <input id="bank_name" type="text" name="bank_name" value="{{ old('bank_name', $contract->bank_name) }}"
                           required
                           class="field @error('bank_name') border-red-500 ring-1 ring-red-500 @enderror">
                    @error('bank_name')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="bank_account_number" class="label">
                        13. Nomor Rekening <span class="text-red-500">*</span>
                    </label>
                    <input id="bank_account_number" type="text" name="bank_account_number" value="{{ old('bank_account_number', $contract->bank_account_number) }}"
                           required
                           class="field font-mono @error('bank_account_number') border-red-500 ring-1 ring-red-500 @enderror">
                    @error('bank_account_number')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="bank_account_holder" class="label">
                        14. Atas Nama Rekening <span class="text-red-500">*</span>
                    </label>
                    <input id="bank_account_holder" type="text" name="bank_account_holder" value="{{ old('bank_account_holder', $contract->bank_account_holder) }}"
                           required
                           class="field @error('bank_account_holder') border-red-500 ring-1 ring-red-500 @enderror">
                    @error('bank_account_holder')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- 7. Catatan Kontrak (Opsional) --}}
            <div>
                <label for="contract_notes" class="label">
                    15. Catatan Kontrak <span class="text-ink-soft text-xs font-normal">(Opsional)</span>
                </label>
                <textarea id="contract_notes" name="contract_notes" rows="2"
                          class="field">{{ old('contract_notes', $contract->contract_notes) }}</textarea>
            </div>

            {{-- 8. Dokumen Sumber --}}
            <div class="rounded-xl border border-line bg-canvas/40 p-4 space-y-3">
                <label class="label block font-bold text-ink">
                    16. Dokumen Sumber
                </label>

                {{-- Teks Input --}}
                <div>
                    <label for="source_document_text" class="text-[0.72rem] font-semibold text-ink-soft">
                        Keterangan Teks / Link URL Dokumen
                    </label>
                    <input id="source_document_text" type="text" name="source_document_text"
                           value="{{ old('source_document_text', $contract->source_document_text) }}"
                           placeholder="https://drive.google.com/... atau Surat No. 12/DIR/2026"
                           class="field mt-1 text-xs">
                </div>

                {{-- File Upload --}}
                <div>
                    <label for="source_document_file" class="text-[0.72rem] font-semibold text-ink-soft">
                        Unggah File Lampiran Baru (Opsional, PDF / DOCX / Gambar, Maks. 20MB)
                    </label>
                    @if ($contract->source_document_path)
                        <div class="mb-2 flex items-center gap-2 text-xs">
                            <span class="text-ink-soft">File saat ini:</span>
                            <a href="{{ route('contracts.download', $contract) }}" class="font-semibold text-leaf hover:underline flex items-center gap-1">
                                <x-ui-icon name="download" class="h-3 w-3" /> {{ $contract->source_document_name }}
                            </a>
                        </div>
                    @endif
                    <input id="source_document_file" type="file" name="source_document_file"
                           accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.webp"
                           class="field mt-1 text-xs file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-forest file:text-white hover:file:bg-forest-700">
                    <p class="mt-1 text-[0.68rem] text-ink-soft">Biarkan kosong jika tidak ingin mengubah file lampiran.</p>
                    @error('source_document_file')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Tombol Aksi Simpan --}}
            <div class="flex items-center justify-end gap-3 pt-2 border-t border-line">
                <a href="{{ route('contracts.index') }}" class="btn-ghost">
                    Batal
                </a>
                <button type="submit" class="btn-forest inline-flex items-center gap-2">
                    <x-ui-icon name="check" class="h-4 w-4" />
                    <span>Perbarui Kontrak</span>
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
