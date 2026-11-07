<x-app-layout title="Rekapitulasi Kontrak / SPK" subtitle="Arsip dan dokumentasi surat perintah kerja serta rekapitulasi nilai kontrak proyek" active="contracts">
    <div class="space-y-4">
        {{-- Ringkasan Metrik (2 Kartu Bersih) --}}
        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
            <div class="card p-3.5 flex items-center gap-3">
                <div class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-forest/10 text-forest">
                    <x-ui-icon name="document" class="h-5 w-5" />
                </div>
                <div>
                    <p class="text-[0.68rem] font-semibold uppercase tracking-wider text-ink-soft">Total Kontrak / SPK</p>
                    <p class="text-lg font-extrabold text-ink">{{ number_format($totalContracts, 0, ',', '.') }}</p>
                </div>
            </div>
            <div class="card p-3.5 flex items-center gap-3">
                <div class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-leaf/10 text-leaf">
                    <x-ui-icon name="chart" class="h-5 w-5" />
                </div>
                <div>
                    <p class="text-[0.68rem] font-semibold uppercase tracking-wider text-ink-soft">Total Nilai Kontrak</p>
                    <p class="text-lg font-extrabold text-forest">Rp {{ number_format($totalValue, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>

        {{-- Baris Filter Pencarian Cepat --}}
        <form method="GET" action="{{ route('contracts.index') }}" class="card p-3 sm:p-3.5">
            <div class="grid gap-2.5 sm:grid-cols-2 lg:grid-cols-12">
                {{-- Global Search --}}
                <div class="lg:col-span-4">
                    <label for="q" class="label text-xs">Pencarian Cepat</label>
                    <div class="relative">
                        <input id="q" type="text" name="q" value="{{ $search }}"
                               placeholder="Cari No. SPK, Proyek, Pihak Kedua, Uraian..."
                               class="field pr-8 text-xs py-1.5">
                        <span class="absolute right-2.5 top-1/2 -translate-y-1/2 text-ink-soft">
                            <x-ui-icon name="filter" class="h-3.5 w-3.5 opacity-50" />
                        </span>
                    </div>
                </div>

                {{-- Filter Bank --}}
                <div class="lg:col-span-3">
                    <label for="bank" class="label text-xs">Filter Bank</label>
                    <input id="bank" type="text" name="bank" value="{{ $bank }}"
                           placeholder="Ketik nama bank (BCA, Mandiri, BRI...)"
                           class="field text-xs py-1.5">
                </div>

                {{-- Filter Rentang Tanggal SPK --}}
                <div class="lg:col-span-2">
                    <label for="from" class="label text-xs">Dari Tanggal</label>
                    <input id="from" type="date" name="from" value="{{ $from }}" class="field text-xs py-1.5">
                </div>

                <div class="lg:col-span-2">
                    <label for="to" class="label text-xs">Sampai Tanggal</label>
                    <input id="to" type="date" name="to" value="{{ $to }}" class="field text-xs py-1.5">
                </div>

                {{-- Tombol Terapkan & Reset --}}
                <div class="flex items-end gap-1.5 lg:col-span-1">
                    <button type="submit" class="btn-forest w-full py-1.5 px-2.5 text-xs font-semibold" title="Terapkan Filter">
                        Filter
                    </button>
                    @if ($search !== '' || $bank !== '' || $from !== '' || $to !== '')
                        <a href="{{ route('contracts.index') }}" class="btn-ghost py-1.5 px-2 text-xs text-red-600 hover:bg-red-50" title="Bersihkan semua filter">
                            <x-ui-icon name="x" class="h-3.5 w-3.5" />
                        </a>
                    @endif
                </div>
            </div>

            {{-- Toolbar Tombol Export & Tombol Tunggal "Tambah Kontrak" --}}
            <div class="mt-3 flex flex-wrap items-center justify-between gap-2 border-t border-line/70 pt-2.5">
                <div class="flex items-center gap-2 text-xs text-ink-soft">
                    <span>Menampilkan <strong>{{ $contracts->total() }}</strong> data kontrak</span>
                    @if ($search !== '' || $bank !== '' || $from !== '' || $to !== '')
                        <span class="inline-flex items-center gap-1 rounded-md bg-amber-50 px-2 py-0.5 text-[0.68rem] font-medium text-amber-700 border border-amber-200">
                            Filter aktif
                        </span>
                    @endif
                </div>

                <div class="flex items-center gap-2">
                    <a href="{{ route('contracts.export.pdf', request()->query()) }}" class="btn-ghost py-1.5 px-3 text-xs">
                        <x-ui-icon name="download" class="h-3.5 w-3.5" /> Export PDF
                    </a>
                    <a href="{{ route('contracts.export.excel', request()->query()) }}" class="btn-ghost py-1.5 px-3 text-xs">
                        <x-ui-icon name="download" class="h-3.5 w-3.5" /> Export Excel
                    </a>
                    <a href="{{ route('contracts.create') }}" class="btn-primary inline-flex items-center gap-1.5 py-1.5 px-3 text-xs shadow-xs">
                        <x-ui-icon name="plus" class="h-3.5 w-3.5" />
                        <span>Tambah Kontrak</span>
                    </a>
                </div>
            </div>
        </form>

        {{-- Tabel Data Rekapitulasi Kontrak (Lebih Kecil, Rapi, Selang-Seling Putih-Hijau) --}}
        <div class="card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-[0.75rem] border-collapse">
                    <thead class="border-b border-line bg-[#0d4c1e]/90 text-white text-[0.68rem] uppercase font-semibold tracking-wider">
                        <tr>
                            <th class="px-2.5 py-2.5 text-center" style="width: 44px;">No</th>
                            <th class="px-3 py-2.5">No. SPK &amp; Tgl</th>
                            <th class="px-3 py-2.5">Proyek / Kegiatan</th>
                            <th class="px-3 py-2.5">Pihak Kedua &amp; Uraian</th>
                            <th class="px-3 py-2.5 text-right">Nilai Kontrak</th>
                            <th class="px-2.5 py-2.5 text-center">Durasi</th>
                            <th class="px-2.5 py-2.5 text-center">Termin</th>
                            <th class="px-3 py-2.5">Bank &amp; Rekening</th>
                            <th class="px-3 py-2.5">Dokumen Sumber</th>
                            <th class="px-3 py-2.5 text-right" style="width: 85px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line/60">
                        @forelse ($contracts as $item)
                            <tr class="{{ $loop->even ? 'bg-[#f0f7f2]' : 'bg-white' }} hover:bg-[#dff0e3] transition-colors">
                                {{-- Nomor Urut Auto --}}
                                <td class="px-2.5 py-2 text-center font-bold text-ink-soft">
                                    {{ ($contracts->firstItem() ?? 1) + $loop->index }}
                                </td>

                                {{-- No SPK & Tanggal --}}
                                <td class="px-3 py-2 whitespace-nowrap leading-tight">
                                    <span class="font-mono font-bold text-ink text-[0.78rem]">{{ $item->spk_number }}</span>
                                    <p class="text-[0.66rem] text-ink-soft mt-0.5">{{ $item->spk_date?->translatedFormat('d M Y') }}</p>
                                </td>

                                {{-- Proyek / Kegiatan --}}
                                <td class="px-3 py-2 leading-tight">
                                    <span class="inline-flex items-center rounded-md bg-white/90 px-2 py-0.5 text-[0.7rem] font-bold text-forest border border-leaf/30 shadow-2xs">
                                        {{ $item->project_name }}
                                    </span>
                                </td>

                                {{-- Nama Pihak Kedua & Uraian Pekerjaan --}}
                                <td class="px-3 py-2 max-w-xs leading-tight">
                                    <p class="font-bold text-ink text-[0.76rem]">{{ $item->second_party_name }}</p>
                                    <p class="text-[0.68rem] text-ink-soft truncate mt-0.5" title="{{ $item->job_description }}">
                                        {{ $item->job_description }}
                                    </p>
                                </td>

                                {{-- Nilai Kontrak Format Rupiah --}}
                                <td class="px-3 py-2 text-right whitespace-nowrap font-extrabold text-forest text-[0.78rem]">
                                    Rp {{ number_format($item->contract_value, 0, ',', '.') }}
                                </td>

                                {{-- Durasi (Bulan) --}}
                                <td class="px-2.5 py-2 text-center whitespace-nowrap leading-tight">
                                    <span class="font-bold text-ink">{{ $item->duration_months }}</span>
                                    <span class="text-[0.66rem] text-ink-soft">Bln</span>
                                </td>

                                {{-- Termin I, II, III (%) --}}
                                <td class="px-2.5 py-2 text-center whitespace-nowrap text-[0.66rem] leading-tight">
                                    <div class="inline-flex items-center gap-1 font-mono">
                                        <span class="rounded bg-black/5 px-1 py-0.5">I:{{ $item->termin_1 }}%</span>
                                        <span class="rounded bg-black/5 px-1 py-0.5">II:{{ $item->termin_2 }}%</span>
                                        <span class="rounded bg-black/5 px-1 py-0.5">III:{{ $item->termin_3 }}%</span>
                                    </div>
                                </td>

                                {{-- Bank, No Rekening, Atas Nama --}}
                                <td class="px-3 py-2 text-[0.7rem] leading-tight whitespace-nowrap">
                                    <p class="font-bold text-ink">{{ $item->bank_name }}</p>
                                    <p class="font-mono text-ink-soft text-[0.66rem] mt-0.5">{{ $item->bank_account_number }}</p>
                                    <p class="text-[0.65rem] text-ink-soft">a.n {{ $item->bank_account_holder }}</p>
                                </td>

                                {{-- Dokumen Sumber (Teks Link & File Lampiran) --}}
                                <td class="px-3 py-2 leading-tight">
                                    <div class="flex flex-col gap-1 max-w-[11rem]">
                                        {{-- Teks/Link Input --}}
                                        @if ($item->source_document_text)
                                            @if ($item->isSourceTextUrl())
                                                <a href="{{ $item->source_document_text }}" target="_blank" rel="noopener noreferrer"
                                                   class="inline-flex items-center gap-1 text-[0.68rem] font-semibold text-leaf hover:underline truncate"
                                                   title="{{ $item->source_document_text }}">
                                                    <x-ui-icon name="arrow-up-right" class="h-3 w-3 shrink-0" />
                                                    <span class="truncate">Buka Link</span>
                                                </a>
                                            @else
                                                <span class="text-[0.68rem] text-ink-soft truncate" title="{{ $item->source_document_text }}">
                                                    {{ $item->source_document_text }}
                                                </span>
                                            @endif
                                        @endif

                                        {{-- File Upload Lampiran --}}
                                        @if ($item->source_document_path)
                                            <a href="{{ route('contracts.download', $item) }}"
                                               class="inline-flex items-center gap-1 rounded bg-white/80 border border-line px-1.5 py-0.5 text-[0.66rem] font-semibold text-forest hover:bg-forest/10 transition shadow-2xs"
                                               title="Unduh {{ $item->source_document_name }}">
                                                <x-ui-icon name="download" class="h-3 w-3 text-forest shrink-0" />
                                                <span class="truncate max-w-[7.5rem]">{{ $item->source_document_name }}</span>
                                            </a>
                                        @endif

                                        @if (!$item->source_document_text && !$item->source_document_path)
                                            <span class="text-[0.66rem] text-ink-soft italic opacity-60">-</span>
                                        @endif
                                    </div>
                                </td>

                                {{-- Tombol Aksi --}}
                                <td class="px-3 py-2 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-0.5">
                                        <a href="{{ route('contracts.show', $item) }}"
                                           class="rounded-md p-1 text-ink-soft hover:bg-white hover:text-forest transition"
                                           title="Lihat Detail Kontrak">
                                            <x-ui-icon name="eye" class="h-3.5 w-3.5" />
                                        </a>

                                        <a href="{{ route('contracts.edit', $item) }}"
                                           class="rounded-md p-1 text-ink-soft hover:bg-white hover:text-forest transition"
                                           title="Ubah Data Kontrak">
                                            <x-ui-icon name="pencil" class="h-3.5 w-3.5" />
                                        </a>

                                        {{-- Custom Delete Modal Trigger --}}
                                        <form method="POST" action="{{ route('contracts.destroy', $item) }}"
                                              data-confirm-title="Hapus Kontrak / SPK?"
                                              data-confirm-delete="Apakah Anda yakin ingin menghapus data kontrak No. {{ $item->spk_number }} ({{ $item->project_name }})? Tindakan ini permanen."
                                              class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="rounded-md p-1 text-ink-soft hover:bg-red-50 hover:text-red-600 transition"
                                                    title="Hapus Kontrak ini">
                                                <x-ui-icon name="trash" class="h-3.5 w-3.5" />
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="py-10 text-center text-ink-soft bg-white">
                                    <div class="mx-auto flex h-10 w-10 items-center justify-center rounded-xl bg-canvas text-ink-soft mb-2">
                                        <x-ui-icon name="document" class="h-5 w-5 opacity-40" />
                                    </div>
                                    <p class="font-medium text-xs">Belum ada data Rekapitulasi Kontrak / SPK.</p>
                                    <p class="mt-0.5 text-[0.7rem]">Klik tombol "Tambah Kontrak" untuk menambahkan data.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($contracts->hasPages())
                <div class="border-t border-line p-2.5 bg-white">
                    {{ $contracts->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
