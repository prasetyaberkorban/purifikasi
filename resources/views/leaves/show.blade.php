<x-app-layout title="Detail Pengajuan Izin" :subtitle="$leave->code.' · '.$leave->name" active="leaves">
    <nav class="mb-4 flex items-center gap-2 text-[0.72rem] text-ink-soft">
        <a href="{{ route('leaves.index') }}" class="hover:text-forest">Riwayat Pengajuan</a>
        <span>/</span>
        <span class="font-medium text-ink">{{ $leave->code }}</span>
    </nav>

    <div class="grid gap-4 lg:grid-cols-3">
        <div class="space-y-4 lg:col-span-2">
            {{-- Data Detail Pengajuan --}}
            <section class="card p-4 sm:p-6 space-y-4">
                <div class="flex items-center justify-between border-b border-line pb-3">
                    <div>
                        <span class="font-mono text-xs font-bold text-forest">{{ $leave->code }}</span>
                        <h2 class="text-base font-bold text-ink">{{ $leave->name }}</h2>
                    </div>
                    @php
                        $badgeColor = match ($leave->leave_type) {
                            'Izin sakit' => 'bg-red-50 text-red-700 border-red-200',
                            'WFA' => 'bg-blue-50 text-blue-700 border-blue-200',
                            'Cuti', 'Tahunan' => 'bg-amber-50 text-amber-800 border-amber-200',
                            'Izin pelatihan / sertifikasi' => 'bg-purple-50 text-purple-700 border-purple-200',
                            default => 'bg-forest/10 text-forest border-forest/20',
                        };
                    @endphp
                    <span class="rounded-lg border px-2.5 py-1 text-xs font-bold {{ $badgeColor }}">
                        {{ $leave->leave_type }}
                    </span>
                </div>

                <dl class="grid gap-3 sm:grid-cols-2 text-xs">
                    <div>
                        <dt class="text-ink-soft">1. Nama</dt>
                        <dd class="mt-0.5 font-semibold text-ink text-sm">{{ $leave->name }}</dd>
                    </div>

                    <div>
                        <dt class="text-ink-soft">2. Jenis Izin yang Diambil</dt>
                        <dd class="mt-0.5 font-semibold text-ink text-sm">{{ $leave->leave_type }}</dd>
                    </div>

                    <div>
                        <dt class="text-ink-soft">3. Jumlah Hari Izin / Cuti / WFA</dt>
                        <dd class="mt-0.5 font-bold text-forest text-sm">{{ $leave->days_count }} Hari</dd>
                    </div>

                    <div>
                        <dt class="text-ink-soft">Tanggal Pengajuan</dt>
                        <dd class="mt-0.5 font-semibold text-ink">{{ $leave->created_at?->translatedFormat('d F Y') }}</dd>
                    </div>

                    <div class="sm:col-span-2 border-t border-line pt-3">
                        <dt class="text-ink-soft font-medium">4. Keterangan</dt>
                        <dd class="mt-1 rounded-xl bg-canvas p-3 font-normal text-ink leading-relaxed text-[0.82rem] whitespace-pre-line">
                            {{ $leave->description }}
                        </dd>
                    </div>
                </dl>
            </section>

            {{-- 5. Lampiran --}}
            @if ($leave->attachment_path)
                <section class="card p-4 sm:p-6 space-y-4" x-data="{ showFile: false }">
                    <div class="flex items-center justify-between border-b border-line pb-2">
                        <h2 class="text-sm font-bold text-ink">5. Dokumen Lampiran</h2>
                        <span class="text-xs text-ink-soft">{{ $leave->formattedFileSize() }}</span>
                    </div>

                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 rounded-xl border border-line bg-canvas/40 p-4">
                        <div class="flex items-center gap-3.5 min-w-0">
                            <div class="grid h-12 w-12 shrink-0 place-items-center rounded-xl bg-forest/10 text-forest">
                                @if ($leave->isImage())
                                    <x-ui-icon name="photo" class="h-6 w-6" />
                                @elseif ($leave->isPdf())
                                    <span class="font-bold text-sm uppercase text-red-600">PDF</span>
                                @else
                                    <x-ui-icon name="document" class="h-6 w-6" />
                                @endif
                            </div>
                            <div class="min-w-0">
                                <p class="truncate text-sm font-bold text-ink">{{ $leave->attachment_name }}</p>
                                <p class="text-xs text-ink-soft">
                                    Format: <span class="uppercase font-semibold">{{ $leave->fileExtension() }}</span> &middot; {{ $leave->formattedFileSize() }}
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 shrink-0">
                            @if ($leave->attachmentUrl())
                                <button type="button" @click="showFile = !showFile"
                                        class="btn-ghost text-xs inline-flex items-center gap-1.5 transition active:scale-95"
                                        :class="showFile ? 'border-forest bg-forest text-white hover:bg-forest/90' : ''">
                                    <template x-if="!showFile">
                                        <span class="inline-flex items-center gap-1.5">
                                            <x-ui-icon name="eye" class="h-4 w-4" />
                                            <span>Buka File</span>
                                        </span>
                                    </template>
                                    <template x-if="showFile">
                                        <span class="inline-flex items-center gap-1.5">
                                            <x-ui-icon name="eye-off" class="h-4 w-4" />
                                            <span>Tutup File</span>
                                        </span>
                                    </template>
                                </button>
                            @endif
                            <a href="{{ route('leaves.download', $leave) }}" class="btn-primary text-xs inline-flex items-center gap-1.5">
                                <x-ui-icon name="download" class="h-4 w-4" />
                                <span>Unduh Lampiran</span>
                            </a>
                        </div>
                    </div>

                    {{-- Toggle Show/Hide Preview jika gambar atau PDF --}}
                    @if ($leave->isImage() && $leave->attachmentUrl())
                        <div x-show="showFile" x-cloak class="rounded-xl border border-line overflow-hidden p-2 bg-canvas">
                            <img src="{{ $leave->attachmentUrl() }}" alt="Lampiran" class="max-h-96 w-full rounded-lg object-contain">
                        </div>
                    @elseif ($leave->isPdf() && $leave->attachmentUrl())
                        <div x-show="showFile" x-cloak class="rounded-xl border border-line overflow-hidden shadow-sm">
                            <iframe src="{{ $leave->attachmentUrl() }}" class="w-full h-96"></iframe>
                        </div>
                    @elseif ($leave->attachmentUrl())
                        <div x-show="showFile" x-cloak class="rounded-xl border border-line p-4 text-center bg-canvas text-xs text-ink-soft">
                            <p>File ini tidak dapat dipratinjau langsung di peramban.</p>
                            <a href="{{ route('leaves.download', $leave) }}" class="btn-primary mt-2 inline-flex items-center gap-1.5 text-xs">
                                <x-ui-icon name="download" class="h-4 w-4" /> Unduh File
                            </a>
                        </div>
                    @endif
                </section>
            @else
                <section class="card p-4 sm:p-6 space-y-3">
                    <div class="border-b border-line pb-2">
                        <h2 class="text-sm font-bold text-ink">5. Dokumen Lampiran</h2>
                    </div>
                    <p class="text-xs text-ink-soft py-2">Tidak ada dokumen lampiran yang diunggah.</p>
                </section>
            @endif
        </div>

        {{-- Panel Kanan --}}
        <div class="space-y-4">
            <section class="card p-4 sm:p-5">
                <h3 class="mb-3 text-sm font-bold text-ink">Informasi Tambahan</h3>
                <dl class="space-y-2.5 text-xs">
                    <div class="flex items-center justify-between">
                        <dt class="text-ink-soft">Waktu Kirim</dt>
                        <dd class="font-semibold text-ink">{{ $leave->created_at?->translatedFormat('d M Y, H:i') }}</dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt class="text-ink-soft">Akun Pengirim</dt>
                        <dd class="font-semibold text-ink">{{ $leave->user?->name ?? $leave->name }}</dd>
                    </div>
                    <div class="flex items-center justify-between border-t border-line pt-2">
                        <dt class="text-ink-soft">Status Pengajuan</dt>
                        <dd class="font-semibold text-forest uppercase">Diajukan</dd>
                    </div>
                </dl>

                <div class="mt-5 flex flex-col gap-2">
                    <a href="{{ route('leaves.create') }}" class="btn-primary w-full text-center text-xs">
                        + Buat Pengajuan Baru
                    </a>
                    <a href="{{ route('leaves.index') }}" class="btn-ghost w-full text-center text-xs">
                        Kembali ke Riwayat
                    </a>
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
