<x-app-layout title="Detail Service Aset" :subtitle="$maintenance->code.' · '.$maintenance->asset_name" active="services">
    <nav class="mb-4 flex items-center gap-2 text-[0.72rem] text-ink-soft">
        <a href="{{ route('services.index') }}" class="hover:text-forest">Riwayat Service</a>
        <span>/</span>
        <span class="font-medium text-ink">{{ $maintenance->code }}</span>
    </nav>

    <div class="grid gap-4 lg:grid-cols-3">
        <div class="space-y-4 lg:col-span-2">
            {{-- Data Detail Pengajuan Service --}}
            <section class="card p-4 sm:p-6 space-y-4">
                <div class="flex items-center justify-between border-b border-line pb-3">
                    <div>
                        <span class="font-mono text-xs font-bold text-forest">{{ $maintenance->code }}</span>
                        <h2 class="text-base font-bold text-ink">{{ $maintenance->asset_name }}</h2>
                    </div>
                    <span class="rounded-lg bg-leaf-50 px-2.5 py-1 text-xs font-semibold text-forest border border-leaf/30">
                        {{ $maintenance->previously_serviced === 'sudah' ? 'Pernah Diservice' : 'Service Pertama' }}
                    </span>
                </div>

                <dl class="grid gap-3 sm:grid-cols-2 text-xs">
                    <div>
                        <dt class="text-ink-soft">1. Nama Pengguna PIC</dt>
                        <dd class="mt-0.5 font-semibold text-ink text-sm">{{ $maintenance->pic_name }}</dd>
                    </div>

                    <div>
                        <dt class="text-ink-soft">2. Nama Aset / Barang</dt>
                        <dd class="mt-0.5 font-semibold text-ink text-sm">{{ $maintenance->asset_name }}</dd>
                    </div>

                    <div>
                        <dt class="text-ink-soft">3. Merk / Type</dt>
                        <dd class="mt-0.5 font-semibold text-ink">{{ $maintenance->brand_type }}</dd>
                    </div>

                    <div>
                        <dt class="text-ink-soft">6. Tanggal Service</dt>
                        <dd class="mt-0.5 font-semibold text-ink">{{ $maintenance->service_date?->translatedFormat('d F Y') }}</dd>
                    </div>

                    <div class="sm:col-span-2">
                        <dt class="text-ink-soft">7. Alamat Tempat Service</dt>
                        <dd class="mt-0.5 font-semibold text-ink leading-relaxed">{{ $maintenance->service_location }}</dd>
                    </div>

                    <div class="sm:col-span-2 border-t border-line pt-3">
                        <dt class="text-ink-soft font-medium">4. Deskripsi Kerusakan / Keluhan</dt>
                        <dd class="mt-1 rounded-xl bg-canvas p-3 font-normal text-ink leading-relaxed text-[0.82rem]">
                            {{ $maintenance->damage_description }}
                        </dd>
                    </div>
                </dl>
            </section>

            {{-- Dokumentasi Foto & Nota --}}
            <section class="card p-4 sm:p-6 space-y-4">
                <h2 class="text-sm font-bold text-ink border-b border-line pb-2">Dokumentasi &amp; Nota Service</h2>

                <div class="grid gap-4 sm:grid-cols-2">
                    {{-- 8. Foto Kondisi Fisik --}}
                    <div class="space-y-2 rounded-xl border border-line bg-canvas/40 p-3">
                        <p class="text-xs font-bold text-ink">8. Dokumentasi Kondisi Aset</p>
                        @if ($maintenance->condition_photo_path)
                            <a href="{{ asset('storage/'.$maintenance->condition_photo_path) }}" target="_blank" class="block group">
                                <img src="{{ asset('storage/'.$maintenance->condition_photo_path) }}"
                                     alt="Dokumentasi Kondisi Aset"
                                     class="max-h-72 w-full rounded-lg object-contain bg-black/5 ring-1 ring-line group-hover:opacity-90 transition">
                                <p class="mt-1 text-[0.68rem] text-forest underline text-center">Klik untuk memperbesar foto</p>
                            </a>
                        @else
                            <p class="text-xs text-ink-soft">Tidak ada foto.</p>
                        @endif
                    </div>

                    {{-- 9. Foto Nota Service --}}
                    <div class="space-y-2 rounded-xl border border-line bg-canvas/40 p-3">
                        <p class="text-xs font-bold text-ink">9. Nota Service / Pemeliharaan</p>
                        @if ($maintenance->invoice_photo_path)
                            <a href="{{ asset('storage/'.$maintenance->invoice_photo_path) }}" target="_blank" class="block group">
                                <img src="{{ asset('storage/'.$maintenance->invoice_photo_path) }}"
                                     alt="Nota Service / Pemeliharaan"
                                     class="max-h-72 w-full rounded-lg object-contain bg-black/5 ring-1 ring-line group-hover:opacity-90 transition">
                                <p class="mt-1 text-[0.68rem] text-forest underline text-center">Klik untuk memperbesar foto</p>
                            </a>
                        @else
                            <p class="text-xs text-ink-soft">Tidak ada nota.</p>
                        @endif
                    </div>
                </div>
            </section>
        </div>

        {{-- Panel Kanan --}}
        <div class="space-y-4">
            <section class="card p-4 sm:p-5">
                <h3 class="mb-3 text-sm font-bold text-ink">Informasi Tambahan</h3>
                <dl class="space-y-2.5 text-xs">
                    <div class="flex items-center justify-between">
                        <dt class="text-ink-soft">Waktu Pelaporan</dt>
                        <dd class="font-semibold text-ink">{{ $maintenance->created_at?->translatedFormat('d M Y, H:i') }}</dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt class="text-ink-soft">Pelapor Akun</dt>
                        <dd class="font-semibold text-ink">{{ $maintenance->user?->name ?? $maintenance->pic_name }}</dd>
                    </div>
                    @if ($maintenance->asset)
                        <div class="flex items-center justify-between border-t border-line pt-2">
                            <dt class="text-ink-soft">Kode Inventaris</dt>
                            <dd class="font-semibold text-forest">
                                <a href="{{ route('assets.show', $maintenance->asset) }}" class="underline hover:text-leaf">
                                    {{ $maintenance->asset->code }}
                                </a>
                            </dd>
                        </div>
                    @endif
                </dl>

                <div class="mt-5 flex flex-col gap-2">
                    <a href="{{ route('services.create') }}" class="btn-primary w-full text-center text-xs">
                        + Buat Formulir Baru
                    </a>
                    <a href="{{ route('services.index') }}" class="btn-ghost w-full text-center text-xs">
                        Kembali ke Riwayat
                    </a>
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
