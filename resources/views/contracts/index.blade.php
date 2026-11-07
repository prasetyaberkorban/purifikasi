<x-app-layout title="Rekapitulasi Kontrak / SPK" subtitle="Arsip dan dokumentasi surat perintah kerja serta rekapitulasi nilai kontrak proyek" active="contracts">
    <div class="space-y-4">
        {{-- Ringkasan Metrik --}}
        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">
            <div class="card p-4 flex items-center gap-3.5">
                <div class="grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-forest/10 text-forest">
                    <x-ui-icon name="document" class="h-5 w-5" />
                </div>
                <div>
                    <p class="text-[0.7rem] font-semibold uppercase tracking-wider text-ink-soft">Total Kontrak / SPK</p>
                    <p class="text-xl font-extrabold text-ink">{{ number_format($totalContracts, 0, ',', '.') }}</p>
                </div>
            </div>
            <div class="card p-4 flex items-center gap-3.5">
                <div class="grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-leaf/10 text-leaf">
                    <x-ui-icon name="chart" class="h-5 w-5" />
                </div>
                <div>
                    <p class="text-[0.7rem] font-semibold uppercase tracking-wider text-ink-soft">Total Nilai Kontrak</p>
                    <p class="text-xl font-extrabold text-forest">Rp {{ number_format($totalValue, 0, ',', '.') }}</p>
                </div>
            </div>
            <div class="card p-4 flex items-center justify-between sm:col-span-2 lg:col-span-1">
                <div>
                    <p class="text-[0.7rem] font-semibold uppercase tracking-wider text-ink-soft">Aksi Cepat</p>
                    <p class="text-xs text-ink">Tambah kontrak baru dengan proyek tersimpan otomatis.</p>
                </div>
                <a href="{{ route('contracts.create') }}" class="btn-primary inline-flex items-center gap-2 text-xs">
                    <x-ui-icon name="plus" class="h-4 w-4" />
                    <span>Tambah Kontrak</span>
                </a>
            </div>
        </div>

        {{-- Baris Filter Pencarian Cepat (STRICT: TANPA DROPDOWN, HANYA TEXT & DATE INPUT) --}}
        <form method="GET" action="{{ route('contracts.index') }}" class="card p-3.5 sm:p-4">
            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-12">
                {{-- Global Search --}}
                <div class="lg:col-span-4">
                    <label for="q" class="label">Pencarian Cepat</label>
                    <div class="relative">
                        <input id="q" type="text" name="q" value="{{ $search }}"
                               placeholder="Cari No. SPK, Proyek, Pihak Kedua, Uraian..."
                               class="field pr-8 text-xs">
                        <span class="absolute right-2.5 top-1/2 -translate-y-1/2 text-ink-soft">
                            <x-ui-icon name="filter" class="h-3.5 w-3.5 opacity-50" />
                        </span>
                    </div>
                </div>

                {{-- Filter Bank (Text Input Murni) --}}
                <div class="lg:col-span-3">
                    <label for="bank" class="label">Filter Bank</label>
                    <input id="bank" type="text" name="bank" value="{{ $bank }}"
                           placeholder="Ketik nama bank (BCA, Mandiri, BRI...)"
                           class="field text-xs">
                </div>

                {{-- Filter Rentang Tanggal SPK --}}
                <div class="lg:col-span-2">
                    <label for="from" class="label">Dari Tanggal</label>
                    <input id="from" type="date" name="from" value="{{ $from }}" class="field text-xs">
                </div>

                <div class="lg:col-span-2">
                    <label for="to" class="label">Sampai Tanggal</label>
                    <input id="to" type="date" name="to" value="{{ $to }}" class="field text-xs">
                </div>

                {{-- Tombol Terapkan & Reset --}}
                <div class="flex items-end gap-2 lg:col-span-1">
                    <button type="submit" class="btn-forest w-full py-2 px-3 text-xs" title="Terapkan Filter">
                        Filter
                    </button>
                    @if ($search !== '' || $bank !== '' || $from !== '' || $to !== '')
                        <a href="{{ route('contracts.index') }}" class="btn-ghost py-2 px-2.5 text-xs text-red-600 hover:bg-red-50" title="Bersihkan semua filter">
                            <x-ui-icon name="x" class="h-4 w-4" />
                        </a>
                    @endif
                </div>
            </div>

            {{-- Toolbar Tombol Export & Info Filter --}}
            <div class="mt-3.5 flex flex-wrap items-center justify-between gap-2 border-t border-line pt-3">
                <div class="flex items-center gap-2 text-xs text-ink-soft">
                    <span>Menampilkan <strong>{{ $contracts->total() }}</strong> data kontrak</span>
                    @if ($search !== '' || $bank !== '' || $from !== '' || $to !== '')
                        <span class="inline-flex items-center gap-1 rounded-md bg-amber-50 px-2 py-0.5 text-[0.7rem] font-medium text-amber-700 border border-amber-200">
                            Filter aktif
                        </span>
                    @endif
                </div>

                <div class="flex items-center gap-2">
                    <a href="{{ route('contracts.export.pdf', request()->query()) }}" class="btn-ghost text-xs">
                        <x-ui-icon name="download" class="h-3.5 w-3.5" /> Export PDF
                    </a>
                    <a href="{{ route('contracts.export.excel', request()->query()) }}" class="btn-ghost text-xs">
                        <x-ui-icon name="download" class="h-3.5 w-3.5" /> Export Excel
                    </a>
                    <a href="{{ route('contracts.create') }}" class="btn-primary inline-flex items-center gap-1.5 text-xs">
                        <x-ui-icon name="plus" class="h-3.5 w-3.5" />
                        <span>Isi Kontrak Baru</span>
                    </a>
                </div>
            </div>
        </form>

        {{-- Tabel Data Rekapitulasi Kontrak --}}
        <div class="card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="border-b border-line bg-canvas/75 text-[0.7rem] uppercase tracking-wider text-ink-soft">
                        <tr>
                            <th class="px-3.5 py-3 text-center" style="width: 48px;">No</th>
                            <th class="px-4 py-3">No. SPK / PO &amp; Tgl</th>
                            <th class="px-4 py-3">Proyek / Kegiatan</th>
                            <th class="px-4 py-3">Pihak Kedua &amp; Uraian</th>
                            <th class="px-4 py-3 text-right">Nilai Kontrak</th>
                            <th class="px-3.5 py-3 text-center">Durasi</th>
                            <th class="px-3.5 py-3 text-center">Termin</th>
                            <th class="px-4 py-3">Bank &amp; Rekening</th>
                            <th class="px-4 py-3">Dokumen Sumber</th>
                            <th class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line">
                        @forelse ($contracts as $item)
                            <tr class="hover:bg-canvas/50 transition">
                                {{-- Nomor Urut Auto --}}
                                <td class="px-3.5 py-3.5 text-center font-semibold text-ink-soft">
                                    {{ ($contracts->firstItem() ?? 1) + $loop->index }}
                                </td>

                                {{-- No SPK & Tanggal --}}
                                <td class="px-4 py-3.5 whitespace-nowrap">
                                    <span class="font-mono font-bold text-ink">{{ $item->spk_number }}</span>
                                    <p class="text-[0.7rem] text-ink-soft">{{ $item->spk_date?->translatedFormat('d M Y') }}</p>
                                </td>

                                {{-- Proyek / Kegiatan --}}
                                <td class="px-4 py-3.5">
                                    <span class="inline-flex items-center rounded-lg bg-leaf-50 px-2.5 py-1 text-[0.72rem] font-bold text-forest border border-leaf/20">
                                        {{ $item->project_name }}
                                    </span>
                                </td>

                                {{-- Nama Pihak Kedua & Uraian Pekerjaan --}}
                                <td class="px-4 py-3.5 max-w-xs">
                                    <p class="font-bold text-ink">{{ $item->second_party_name }}</p>
                                    <p class="text-[0.72rem] text-ink-soft line-clamp-2" title="{{ $item->job_description }}">
                                        {{ $item->job_description }}
                                    </p>
                                </td>

                                {{-- Nilai Kontrak Format Rupiah --}}
                                <td class="px-4 py-3.5 text-right whitespace-nowrap font-extrabold text-forest">
                                    Rp {{ number_format($item->contract_value, 0, ',', '.') }}
                                </td>

                                {{-- Durasi (Bulan) --}}
                                <td class="px-3.5 py-3.5 text-center whitespace-nowrap">
                                    <span class="font-semibold text-ink">{{ $item->duration_months }}</span> <span class="text-[0.7rem] text-ink-soft">Bln</span>
                                </td>

                                {{-- Termin I, II, III (%) --}}
                                <td class="px-3.5 py-3.5 text-center whitespace-nowrap text-[0.7rem]">
                                    <div class="inline-flex flex-col gap-0.5">
                                        <span>I: <strong class="text-ink">{{ $item->termin_1 }}%</strong></span>
                                        <span>II: <strong class="text-ink">{{ $item->termin_2 }}%</strong></span>
                                        <span>III: <strong class="text-ink">{{ $item->termin_3 }}%</strong></span>
                                    </div>
                                </td>

                                {{-- Bank, No Rekening, Atas Nama --}}
                                <td class="px-4 py-3.5 text-[0.72rem] whitespace-nowrap">
                                    <p class="font-bold text-ink">{{ $item->bank_name }}</p>
                                    <p class="font-mono text-ink-soft">{{ $item->bank_account_number }}</p>
                                    <p class="text-[0.68rem] text-ink-soft">a.n {{ $item->bank_account_holder }}</p>
                                </td>

                                {{-- Dokumen Sumber (Teks Link & File Lampiran) --}}
                                <td class="px-4 py-3.5">
                                    <div class="flex flex-col gap-1.5 max-w-[12rem]">
                                        {{-- Teks/Link Input --}}
                                        @if ($item->source_document_text)
                                            @if ($item->isSourceTextUrl())
                                                <a href="{{ $item->source_document_text }}" target="_blank" rel="noopener noreferrer"
                                                   class="inline-flex items-center gap-1 text-[0.72rem] font-medium text-leaf hover:underline truncate"
                                                   title="{{ $item->source_document_text }}">
                                                    <x-ui-icon name="arrow-up-right" class="h-3 w-3 shrink-0" />
                                                    <span class="truncate">Buka Link Dokumen</span>
                                                </a>
                                            @else
                                                <span class="text-[0.72rem] text-ink-soft truncate" title="{{ $item->source_document_text }}">
                                                    {{ $item->source_document_text }}
                                                </span>
                                            @endif
                                        @endif

                                        {{-- File Upload Lampiran --}}
                                        @if ($item->source_document_path)
                                            <a href="{{ route('contracts.download', $item) }}"
                                               class="inline-flex items-center gap-1.5 rounded-lg border border-line bg-canvas px-2.5 py-1 text-[0.7rem] font-semibold text-forest hover:bg-forest/5 transition"
                                               title="Unduh {{ $item->source_document_name }}">
                                                <x-ui-icon name="download" class="h-3.5 w-3.5 text-forest" />
                                                <span class="truncate max-w-[8rem]">{{ $item->source_document_name }}</span>
                                            </a>
                                        @endif

                                        @if (!$item->source_document_text && !$item->source_document_path)
                                            <span class="text-[0.7rem] text-ink-soft italic">Tidak ada dokumen</span>
                                        @endif
                                    </div>
                                </td>

                                {{-- Tombol Aksi --}}
                                <td class="px-4 py-3.5 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ route('contracts.show', $item) }}"
                                           class="rounded-lg p-1.5 text-ink-soft hover:bg-canvas hover:text-forest"
                                           title="Lihat Detail Kontrak">
                                            <x-ui-icon name="eye" class="h-4 w-4" />
                                        </a>

                                        <a href="{{ route('contracts.edit', $item) }}"
                                           class="rounded-lg p-1.5 text-ink-soft hover:bg-canvas hover:text-forest"
                                           title="Ubah Data Kontrak">
                                            <x-ui-icon name="pencil" class="h-4 w-4" />
                                        </a>

                                        {{-- Custom Delete Modal Trigger --}}
                                        <form method="POST" action="{{ route('contracts.destroy', $item) }}"
                                              data-confirm-title="Hapus Kontrak / SPK?"
                                              data-confirm-delete="Apakah Anda yakin ingin menghapus data kontrak No. {{ $item->spk_number }} ({{ $item->project_name }})? Tindakan ini permanen."
                                              class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="rounded-lg p-1.5 text-ink-soft hover:bg-red-50 hover:text-red-600 transition"
                                                    title="Hapus Kontrak ini">
                                                <x-ui-icon name="trash" class="h-4 w-4" />
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="py-12 text-center text-ink-soft">
                                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl bg-canvas text-ink-soft mb-2">
                                        <x-ui-icon name="document" class="h-6 w-6 opacity-40" />
                                    </div>
                                    <p class="font-medium">Belum ada data Rekapitulasi Kontrak / SPK.</p>
                                    <p class="mt-0.5 text-xs">Klik tombol "Isi Kontrak Baru" untuk menambahkan data.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($contracts->hasPages())
                <div class="border-t border-line p-3">
                    {{ $contracts->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
