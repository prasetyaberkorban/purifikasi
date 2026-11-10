<x-app-layout title="Detail Kontrak / SPK" subtitle="Informasi rincian surat perintah kerja dan termin pembayaran" active="contracts">
    <div class="mx-auto max-w-4xl space-y-4">
        {{-- Header Bar --}}
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <a href="{{ route('contracts.index', ['category' => $contract->category ?? 'amdal']) }}" class="btn-ghost inline-flex items-center gap-2 self-start text-xs">
                <x-ui-icon name="arrow-down-left" class="h-4 w-4" />
                <span>Kembali ke Rekapitulasi {{ $contract->isEngineering() ? 'Engineering' : 'AMDAL' }}</span>
            </a>

            @if (auth()->user()->isAdmin() || $contract->user_id === auth()->id())
                <div class="flex items-center gap-2">
                    <a href="{{ route('contracts.edit', $contract) }}" class="btn-primary inline-flex items-center gap-1.5 text-xs">
                        <x-ui-icon name="pencil" class="h-4 w-4" />
                        <span>Ubah Data</span>
                    </a>

                    <form method="POST" action="{{ route('contracts.destroy', $contract) }}"
                          data-confirm-title="Hapus Kontrak / SPK?"
                          data-confirm-delete="Apakah Anda yakin ingin menghapus data kontrak No. {{ $contract->spk_number }} ({{ $contract->project_name }})? Tindakan ini permanen.">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-ghost text-red-600 hover:bg-red-50 text-xs">
                            <x-ui-icon name="trash" class="h-4 w-4" /> Hapus
                        </button>
                    </form>
                </div>
            @endif
        </div>

        {{-- Card Detail Kontrak --}}
        <div class="card p-6 space-y-6">
            {{-- Bagian Atas: SPK & Proyek --}}
            <div class="border-b border-line pb-5">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center rounded-lg bg-forest text-white px-2.5 py-0.5 text-xs font-bold uppercase tracking-wider">
                                {{ $contract->categoryLabel() }}
                            </span>
                            <span class="inline-flex items-center rounded-lg bg-leaf-50 px-2.5 py-0.5 text-xs font-bold text-forest border border-leaf/20">
                                {{ $contract->project_name }}
                            </span>
                        </div>
                        <h2 class="mt-2 text-xl font-bold text-ink">{{ $contract->spk_number }}</h2>
                        <p class="text-xs text-ink-soft">Tanggal SPK: <strong>{{ $contract->spk_date?->translatedFormat('d F Y') }}</strong></p>
                    </div>

                    <div class="text-right">
                        <p class="text-xs font-semibold uppercase tracking-wider text-ink-soft">Nilai Kontrak</p>
                        <p class="text-2xl font-black text-forest">Rp {{ number_format($contract->contract_value, 0, ',', '.') }}</p>
                        <p class="text-xs text-ink-soft">Durasi: <strong>{{ $contract->duration_months }} Bulan</strong></p>
                    </div>
                </div>
            </div>

            {{-- Informasi Pihak Kedua & Uraian --}}
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="rounded-xl border border-line bg-canvas/40 p-4">
                    <p class="text-xs font-bold uppercase tracking-wider text-ink-soft">Pihak Kedua (Vendor / Pelaksana)</p>
                    <p class="mt-1.5 text-base font-bold text-ink">{{ $contract->second_party_name }}</p>
                </div>

                <div class="rounded-xl border border-line bg-canvas/40 p-4">
                    <p class="text-xs font-bold uppercase tracking-wider text-ink-soft">Rekening Pembayaran</p>
                    <p class="mt-1.5 font-bold text-ink">{{ $contract->bank_name }} - <span class="font-mono">{{ $contract->bank_account_number }}</span></p>
                    <p class="text-xs text-ink-soft">a.n {{ $contract->bank_account_holder }}</p>
                </div>
            </div>

            {{-- Uraian Pekerjaan --}}
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-ink-soft">Jabatan / Uraian Pekerjaan</p>
                <div class="mt-2 rounded-xl border border-line bg-white p-4 text-xs leading-relaxed text-ink whitespace-pre-line">
                    {{ $contract->job_description }}
                </div>
            </div>

            {{-- Skema Termin Pembayaran --}}
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-ink-soft mb-2">Skema Pembayaran Termin</p>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    <div class="rounded-xl border border-line bg-canvas/40 p-3.5 text-center">
                        <p class="text-[0.7rem] font-semibold text-ink-soft">Termin I</p>
                        <p class="mt-1 text-lg font-extrabold text-forest">{{ $contract->termin_1 }}%</p>
                        <p class="mt-0.5 text-[0.7rem] text-ink-soft font-mono">
                            Rp {{ number_format(($contract->contract_value * $contract->termin_1) / 100, 0, ',', '.') }}
                        </p>
                    </div>
                    <div class="rounded-xl border border-line bg-canvas/40 p-3.5 text-center">
                        <p class="text-[0.7rem] font-semibold text-ink-soft">Termin II</p>
                        <p class="mt-1 text-lg font-extrabold text-forest">{{ $contract->termin_2 }}%</p>
                        <p class="mt-0.5 text-[0.7rem] text-ink-soft font-mono">
                            Rp {{ number_format(($contract->contract_value * $contract->termin_2) / 100, 0, ',', '.') }}
                        </p>
                    </div>
                    <div class="rounded-xl border border-line bg-canvas/40 p-3.5 text-center">
                        <p class="text-[0.7rem] font-semibold text-ink-soft">Termin III</p>
                        <p class="mt-1 text-lg font-extrabold text-forest">{{ $contract->termin_3 }}%</p>
                        <p class="mt-0.5 text-[0.7rem] text-ink-soft font-mono">
                            Rp {{ number_format(($contract->contract_value * $contract->termin_3) / 100, 0, ',', '.') }}
                        </p>
                    </div>
                    <div class="rounded-xl border border-line bg-canvas/40 p-3.5 text-center">
                        <p class="text-[0.7rem] font-semibold text-ink-soft">Termin IV</p>
                        <p class="mt-1 text-lg font-extrabold text-forest">{{ $contract->termin_4 }}%</p>
                        <p class="mt-0.5 text-[0.7rem] text-ink-soft font-mono">
                            Rp {{ number_format(($contract->contract_value * $contract->termin_4) / 100, 0, ',', '.') }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Dokumen Sumber --}}
            <div class="rounded-xl border border-line bg-canvas/40 p-4 space-y-3">
                <p class="text-xs font-bold uppercase tracking-wider text-ink-soft">Dokumen Sumber</p>
                <div class="flex flex-col gap-2">
                    {{-- Teks Link --}}
                    @if ($contract->source_document_text)
                        <div>
                            <span class="text-xs text-ink-soft">Tautan / Keterangan Teks:</span>
                            @if ($contract->isSourceTextUrl())
                                <a href="{{ $contract->source_document_text }}" target="_blank" rel="noopener noreferrer"
                                   class="mt-0.5 flex items-center gap-1.5 font-semibold text-leaf hover:underline text-xs">
                                    <x-ui-icon name="arrow-up-right" class="h-3.5 w-3.5" />
                                    <span>{{ $contract->source_document_text }}</span>
                                </a>
                            @else
                                <p class="mt-0.5 text-xs text-ink font-medium">{{ $contract->source_document_text }}</p>
                            @endif
                        </div>
                    @endif

                    {{-- File Lampiran --}}
                    @if ($contract->source_document_path)
                        <div class="pt-2 border-t border-line/60 flex items-center justify-between">
                            <div>
                                <span class="text-xs text-ink-soft">Lampiran File Fisik:</span>
                                <p class="mt-0.5 text-xs font-bold text-ink">{{ $contract->source_document_name }}</p>
                            </div>
                            <a href="{{ route('contracts.download', $contract) }}" class="btn-forest py-1.5 px-3 text-xs">
                                <x-ui-icon name="download" class="h-3.5 w-3.5" /> Unduh Dokumen
                            </a>
                        </div>
                    @endif

                    @if (!$contract->source_document_text && !$contract->source_document_path)
                        <p class="text-xs text-ink-soft italic">Tidak ada dokumen sumber dilampirkan.</p>
                    @endif
                </div>
            </div>

            {{-- Catatan Kontrak --}}
            @if ($contract->contract_notes)
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-ink-soft">Catatan Kontrak</p>
                    <p class="mt-1 text-xs text-ink-soft leading-relaxed">{{ $contract->contract_notes }}</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
