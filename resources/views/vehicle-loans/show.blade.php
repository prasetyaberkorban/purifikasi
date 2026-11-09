<x-app-layout title="Detail Peminjaman Kendaraan" :subtitle="$loan->code . ' · ' . $loan->vehicle_type" active="vehicle_loans">
    <div class="space-y-6 max-w-4xl">
        {{-- Header Status --}}
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <a href="{{ route('vehicle-loans.index') }}" class="rounded-xl border border-line p-2 text-ink-soft hover:bg-canvas">
                    <x-ui-icon name="swap" class="h-4 w-4 rotate-180" />
                </a>
                <div>
                    <h2 class="text-lg font-bold text-ink">{{ $loan->code }}</h2>
                    <p class="text-xs text-ink-soft">Dicatat pada {{ $loan->created_at?->translatedFormat('d F Y, H:i') }} WIB</p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-bold text-emerald-800 border border-emerald-200">
                    Tersimpan
                </span>
                <a href="{{ route('vehicle-loans.create') }}" class="btn-primary inline-flex items-center gap-2 text-xs">
                    <x-ui-icon name="plus" class="h-3.5 w-3.5" />
                    <span>Formulir Baru</span>
                </a>
                @if (auth()->user()->isAdmin() || $loan->user_id === auth()->id())
                    <form method="POST" action="{{ route('vehicle-loans.destroy', $loan) }}"
                          data-confirm-title="Hapus Riwayat Peminjaman?"
                          data-confirm-delete="Apakah Anda yakin ingin menghapus data peminjaman {{ $loan->code }} ({{ $loan->vehicle_type }})? Foto speedometer juga akan dihapus permanen.">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-ghost text-red-600 hover:bg-red-50 text-xs inline-flex items-center gap-1.5">
                            <x-ui-icon name="trash" class="h-3.5 w-3.5" />
                            <span>Hapus</span>
                        </button>
                    </form>
                @endif
            </div>
        </div>

        {{-- Kartu Rincian Peminjaman --}}
        <div class="card p-5 sm:p-6 space-y-6">
            <div class="border-b border-line pb-4 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="rounded-xl bg-forest/10 p-2.5 text-forest">
                        <x-ui-icon name="car" class="h-6 w-6" />
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-ink">{{ $loan->vehicle_type }}</h3>
                        <p class="text-xs text-ink-soft">Peminjaman oleh {{ $loan->pic_name }}</p>
                    </div>
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div class="rounded-xl bg-canvas/60 p-4 border border-line/60">
                    <p class="text-[0.7rem] uppercase tracking-wider text-ink-soft font-semibold">1. PIC (Penanggung Jawab)</p>
                    <p class="mt-1 text-sm font-bold text-ink">{{ $loan->pic_name }}</p>
                    @if ($loan->user)
                        <p class="text-[0.72rem] text-ink-soft">Akun pembuat: {{ $loan->user->name }} ({{ $loan->user->email }})</p>
                    @endif
                </div>

                <div class="rounded-xl bg-canvas/60 p-4 border border-line/60">
                    <p class="text-[0.7rem] uppercase tracking-wider text-ink-soft font-semibold">2. Nama Driver</p>
                    <p class="mt-1 text-sm font-bold text-ink">{{ $loan->driver_name }}</p>
                </div>

                <div class="rounded-xl bg-canvas/60 p-4 border border-line/60">
                    <p class="text-[0.7rem] uppercase tracking-wider text-ink-soft font-semibold">3. Tanggal Pergi</p>
                    <p class="mt-1 text-sm font-bold text-ink">{{ $loan->departure_date?->translatedFormat('l, d F Y') }}</p>
                </div>

                <div class="rounded-xl bg-canvas/60 p-4 border border-line/60">
                    <p class="text-[0.7rem] uppercase tracking-wider text-ink-soft font-semibold">4. Tanggal Kembali</p>
                    <p class="mt-1 text-sm font-bold text-ink">{{ $loan->return_date?->translatedFormat('l, d F Y') }}</p>
                </div>

                <div class="rounded-xl bg-canvas/60 p-4 border border-line/60">
                    <p class="text-[0.7rem] uppercase tracking-wider text-ink-soft font-semibold">5. Kilometer (Km) Berangkat</p>
                    <p class="mt-1 text-base font-mono font-bold text-forest">{{ $loan->departure_km }} KM</p>
                </div>

                <div class="rounded-xl bg-canvas/60 p-4 border border-line/60">
                    <p class="text-[0.7rem] uppercase tracking-wider text-ink-soft font-semibold">6. Jenis Kendaraan</p>
                    <p class="mt-1 text-sm font-bold text-ink">{{ $loan->vehicle_type }}</p>
                </div>

                <div class="sm:col-span-2 rounded-xl bg-canvas/60 p-4 border border-line/60">
                    <p class="text-[0.7rem] uppercase tracking-wider text-ink-soft font-semibold">7. Tujuan Peminjaman</p>
                    <p class="mt-1 text-sm text-ink leading-relaxed whitespace-pre-line">{{ $loan->purpose }}</p>
                </div>

                <div class="sm:col-span-2 rounded-xl bg-canvas/60 p-4 border border-line/60">
                    <p class="text-[0.7rem] uppercase tracking-wider text-ink-soft font-semibold">8. Kondisi Kebersihan Kendaraan</p>
                    <span class="mt-1 inline-block rounded-lg px-2.5 py-1 text-xs font-bold {{ str_contains(strtolower($loan->cleanliness_condition), 'dibersihkan') ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                        {{ $loan->cleanliness_condition }}
                    </span>
                </div>

                @if ($loan->suggestions)
                    <div class="sm:col-span-2 rounded-xl bg-canvas/60 p-4 border border-line/60">
                        <p class="text-[0.7rem] uppercase tracking-wider text-ink-soft font-semibold">9. Saran dan Masukan</p>
                        <p class="mt-1 text-sm text-ink leading-relaxed whitespace-pre-line">{{ $loan->suggestions }}</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Kartu Foto Speedometer --}}
        <div class="card p-5 sm:p-6 space-y-5">
            <div class="border-b border-line pb-3">
                <h3 class="text-sm font-bold text-ink">Dokumentasi Speedometer</h3>
                <p class="text-[0.72rem] text-ink-soft">Foto panel speedometer saat keberangkatan dan kepulangan.</p>
            </div>

            <div class="grid gap-6 sm:grid-cols-2">
                {{-- Foto Berangkat --}}
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <p class="text-xs font-bold text-forest">10. Speedometer Berangkat</p>
                        @if ($loan->departure_speedometer_photo_path)
                            <a href="{{ $loan->departureSpeedometerPhotoUrl() }}" target="_blank"
                               class="text-[0.72rem] text-leaf hover:underline inline-flex items-center gap-1 font-semibold">
                                <span>Buka Penuh</span>
                                <x-ui-icon name="arrow-up-right" class="h-3 w-3" />
                            </a>
                        @endif
                    </div>
                    <div class="overflow-hidden rounded-xl border border-line bg-canvas">
                        @if ($loan->departure_speedometer_photo_path)
                            <img src="{{ $loan->departureSpeedometerPhotoUrl() }}"
                                 alt="Speedometer Berangkat"
                                 class="h-64 w-full object-contain p-2">
                        @else
                            <div class="flex h-64 items-center justify-center text-xs text-ink-soft">
                                Tidak ada foto
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Foto Kembali --}}
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <p class="text-xs font-bold text-forest">11. Speedometer Kembali</p>
                        @if ($loan->return_speedometer_photo_path)
                            <a href="{{ $loan->returnSpeedometerPhotoUrl() }}" target="_blank"
                               class="text-[0.72rem] text-leaf hover:underline inline-flex items-center gap-1 font-semibold">
                                <span>Buka Penuh</span>
                                <x-ui-icon name="arrow-up-right" class="h-3 w-3" />
                            </a>
                        @endif
                    </div>
                    <div class="overflow-hidden rounded-xl border border-line bg-canvas">
                        @if ($loan->return_speedometer_photo_path)
                            <img src="{{ $loan->returnSpeedometerPhotoUrl() }}"
                                 alt="Speedometer Kembali"
                                 class="h-64 w-full object-contain p-2">
                        @else
                            <div class="flex h-64 items-center justify-center text-xs text-ink-soft">
                                Tidak ada foto
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
