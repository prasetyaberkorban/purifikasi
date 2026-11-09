<x-app-layout title="Detail Realisasi Pengadaan" :subtitle="$procurement->code.' · '.$procurement->item_name" active="procurements">
    <nav class="mb-4 flex items-center gap-2 text-[0.72rem] text-ink-soft">
        <a href="{{ route('procurements.index') }}" class="hover:text-forest">Riwayat Realisasi</a>
        <span>/</span>
        <span class="font-medium text-ink">{{ $procurement->code }}</span>
    </nav>

    <div class="grid gap-4 lg:grid-cols-3">
        <div class="space-y-4 lg:col-span-2">
            {{-- Data Detail Realisasi --}}
            <section class="card p-4 sm:p-6 space-y-4">
                <div class="flex items-center justify-between border-b border-line pb-3">
                    <div>
                        <span class="font-mono text-xs font-bold text-forest">{{ $procurement->code }}</span>
                        <h2 class="text-base font-bold text-ink">{{ $procurement->item_name }}</h2>
                    </div>
                    <span class="rounded-lg bg-leaf-50 px-2.5 py-1 text-xs font-semibold text-forest border border-leaf/30">
                        Telah Direalisasikan
                    </span>
                </div>

                <dl class="grid gap-3 sm:grid-cols-2 text-xs">
                    <div>
                        <dt class="text-ink-soft">1. PIC</dt>
                        <dd class="mt-0.5 font-semibold text-ink text-sm">{{ $procurement->pic_name }}</dd>
                    </div>

                    <div>
                        <dt class="text-ink-soft">2. Tanggal Realisasi</dt>
                        <dd class="mt-0.5 font-semibold text-ink text-sm">{{ $procurement->realization_date?->translatedFormat('d F Y') }}</dd>
                    </div>

                    <div>
                        <dt class="text-ink-soft">3. Nama Barang</dt>
                        <dd class="mt-0.5 font-semibold text-ink">{{ $procurement->item_name }}</dd>
                    </div>

                    <div>
                        <dt class="text-ink-soft">4. Merk Type</dt>
                        <dd class="mt-0.5 font-semibold text-ink">{{ $procurement->brand_type }}</dd>
                    </div>

                    <div class="sm:col-span-2">
                        <dt class="text-ink-soft">5. Jumlah / Ukuran</dt>
                        <dd class="mt-0.5 font-semibold text-ink leading-relaxed">{{ $procurement->quantity_size }}</dd>
                    </div>

                    <div class="sm:col-span-2 border-t border-line pt-3">
                        <dt class="text-ink-soft font-medium">6. Nama Toko (Isi link jika beli online)</dt>
                        <dd class="mt-1 rounded-xl bg-canvas p-3 font-normal text-ink leading-relaxed text-[0.82rem] break-all">
                            @if (str_starts_with($procurement->store_name, 'http://') || str_starts_with($procurement->store_name, 'https://'))
                                <a href="{{ $procurement->store_name }}" target="_blank" class="text-forest underline hover:text-leaf inline-flex items-center gap-1.5 font-medium">
                                    <span>{{ $procurement->store_name }}</span>
                                    <x-ui-icon name="arrow-up-right" class="h-3.5 w-3.5" />
                                </a>
                            @else
                                {{ $procurement->store_name }}
                            @endif
                        </dd>
                    </div>
                </dl>
            </section>

            {{-- 7. Bukti Realisasi Dokumen --}}
            <section class="card p-4 sm:p-6 space-y-4">
                <div class="flex items-center justify-between border-b border-line pb-2">
                    <h2 class="text-sm font-bold text-ink">7. Bukti Realisasi Pengadaan</h2>
                    <span class="text-xs text-ink-soft">{{ $procurement->formattedFileSize() }}</span>
                </div>

                <div class="flex flex-col sm:flex-row items-center justify-between gap-4 rounded-xl border border-line bg-canvas/40 p-4">
                    <div class="flex items-center gap-3.5 min-w-0">
                        <div class="grid h-12 w-12 shrink-0 place-items-center rounded-xl bg-forest/10 text-forest">
                            @if ($procurement->isPdf())
                                <span class="font-bold text-sm uppercase text-red-600">PDF</span>
                            @elseif ($procurement->isSpreadsheet())
                                <span class="font-bold text-sm uppercase text-emerald-700">XLS</span>
                            @elseif ($procurement->isDocument())
                                <span class="font-bold text-sm uppercase text-blue-700">DOC</span>
                            @else
                                <x-ui-icon name="document" class="h-6 w-6" />
                            @endif
                        </div>
                        <div class="min-w-0">
                            <p class="truncate text-sm font-bold text-ink">{{ $procurement->proof_file_name }}</p>
                            <p class="text-xs text-ink-soft">
                                Format: <span class="uppercase font-semibold">{{ $procurement->fileExtension() }}</span> &middot; {{ $procurement->formattedFileSize() }}
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 shrink-0">
                        @if ($procurement->isPdf() && $procurement->proofFileUrl())
                            <a href="{{ $procurement->proofFileUrl() }}" target="_blank" class="btn-ghost text-xs inline-flex items-center gap-1.5">
                                <x-ui-icon name="eye" class="h-4 w-4" />
                                <span>Buka File</span>
                            </a>
                        @endif
                        <a href="{{ route('procurements.download', $procurement) }}" class="btn-primary text-xs inline-flex items-center gap-1.5">
                            <x-ui-icon name="download" class="h-4 w-4" />
                            <span>Unduh File Bukti</span>
                        </a>
                    </div>
                </div>

                @if ($procurement->isPdf() && $procurement->proofFileUrl())
                    <div class="rounded-xl border border-line overflow-hidden shadow-sm">
                        <iframe src="{{ $procurement->proofFileUrl() }}" class="w-full h-96"></iframe>
                    </div>
                @endif
            </section>
        </div>

        {{-- Panel Kanan --}}
        <div class="space-y-4">
            <section class="card p-4 sm:p-5">
                <h3 class="mb-3 text-sm font-bold text-ink">Informasi Tambahan</h3>
                <dl class="space-y-2.5 text-xs">
                    <div class="flex items-center justify-between">
                        <dt class="text-ink-soft">Waktu Pelaporan</dt>
                        <dd class="font-semibold text-ink">{{ $procurement->created_at?->translatedFormat('d M Y, H:i') }}</dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt class="text-ink-soft">Akun Pengunggah</dt>
                        <dd class="font-semibold text-ink">{{ $procurement->user?->name ?? $procurement->pic_name }}</dd>
                    </div>
                    <div class="flex items-center justify-between border-t border-line pt-2">
                        <dt class="text-ink-soft">Status</dt>
                        <dd class="font-semibold text-forest">Selesai</dd>
                    </div>
                </dl>

                <div class="mt-5 flex flex-col gap-2">
                    <a href="{{ route('procurements.create') }}" class="btn-primary w-full text-center text-xs">
                        + Buat Formulir Baru
                    </a>
                    <a href="{{ route('procurements.index') }}" class="btn-ghost w-full text-center text-xs">
                        Kembali ke Riwayat
                    </a>
                    @if (auth()->user()->isAdmin() || $procurement->user_id === auth()->id())
                        <form method="POST" action="{{ route('procurements.destroy', $procurement) }}"
                              data-confirm-title="Hapus Riwayat Realisasi?"
                              data-confirm-delete="Apakah Anda yakin ingin menghapus data realisasi {{ $procurement->code }} ({{ $procurement->item_name }})? File bukti realisasi juga akan dihapus permanen.">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-ghost w-full text-center text-xs text-red-600 hover:bg-red-50 flex items-center justify-center gap-1.5">
                                <x-ui-icon name="trash" class="h-3.5 w-3.5" /> Hapus Realisasi Pengadaan
                            </button>
                        </form>
                    @endif
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
