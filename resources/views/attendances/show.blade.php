<x-app-layout title="Detail Daftar Hadir" :subtitle="$attendance->code . ' · ' . $attendance->full_name" active="attendances">
    <div class="space-y-6 max-w-4xl">
        {{-- Header Aksi & Status --}}
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <a href="{{ route('attendances.index') }}" class="rounded-xl border border-line p-2 text-ink-soft hover:bg-canvas">
                    <x-ui-icon name="swap" class="h-4 w-4 rotate-180" />
                </a>
                <div>
                    <h2 class="text-lg font-bold text-ink">{{ $attendance->code }}</h2>
                    <p class="text-xs text-ink-soft">Dicatat pada {{ $attendance->created_at?->translatedFormat('d F Y, H:i') }} WIB</p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-bold text-emerald-800 border border-emerald-200">
                    Hadir Terverifikasi
                </span>
                <a href="{{ route('attendances.create') }}" class="btn-primary inline-flex items-center gap-2 text-xs">
                    <x-ui-icon name="plus" class="h-3.5 w-3.5" />
                    <span>Daftar Hadir Baru</span>
                </a>
                @if (auth()->user()->isAdmin() || $attendance->user_id === auth()->id())
                    <form method="POST" action="{{ route('attendances.destroy', $attendance) }}"
                          data-confirm-title="Hapus Data Presensi?"
                          data-confirm-delete="Apakah Anda yakin ingin menghapus data presensi {{ $attendance->code }} ({{ $attendance->full_name }})? Tindakan ini permanen.">
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

        {{-- Kartu Detail Kehadiran --}}
        <div class="card p-5 sm:p-6 space-y-6">
            <div class="border-b border-line pb-4 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="rounded-xl bg-forest/10 p-2.5 text-forest">
                        <x-ui-icon name="clipboard-check" class="h-6 w-6" />
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-ink">{{ $attendance->activity_type }}</h3>
                        <p class="text-xs text-ink-soft">Cabang {{ $attendance->branch }}</p>
                    </div>
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div class="rounded-xl bg-canvas/60 p-4 border border-line/60">
                    <p class="text-[0.7rem] uppercase tracking-wider text-ink-soft font-semibold">1. Nama Lengkap</p>
                    <p class="mt-1 text-base font-bold text-ink">{{ $attendance->full_name }}</p>
                    @if ($attendance->user)
                        <p class="text-[0.72rem] text-ink-soft">Akun: {{ $attendance->user->name }} ({{ $attendance->user->email }})</p>
                    @endif
                </div>

                <div class="rounded-xl bg-canvas/60 p-4 border border-line/60">
                    <p class="text-[0.7rem] uppercase tracking-wider text-ink-soft font-semibold">2. Jabatan</p>
                    <p class="mt-1 text-base font-bold text-ink">{{ $attendance->position }}</p>
                </div>

                <div class="rounded-xl bg-canvas/60 p-4 border border-line/60">
                    <p class="text-[0.7rem] uppercase tracking-wider text-ink-soft font-semibold">3. Cabang</p>
                    <span class="mt-1 inline-block rounded-lg bg-leaf-50 px-2.5 py-1 text-xs font-bold text-forest border border-leaf/30">
                        {{ $attendance->branch }}
                    </span>
                </div>

                <div class="rounded-xl bg-canvas/60 p-4 border border-line/60">
                    <p class="text-[0.7rem] uppercase tracking-wider text-ink-soft font-semibold">4. Jenis Kegiatan</p>
                    <p class="mt-1 text-sm font-bold text-ink">{{ $attendance->activity_type }}</p>
                </div>

                <div class="rounded-xl bg-canvas/60 p-4 border border-line/60">
                    <p class="text-[0.7rem] uppercase tracking-wider text-ink-soft font-semibold">5. Tanggal Pelaksanaan</p>
                    <p class="mt-1 text-sm font-bold text-ink">{{ $attendance->event_date?->translatedFormat('l, d F Y') }}</p>
                </div>

                <div class="rounded-xl bg-canvas/60 p-4 border border-line/60">
                    <p class="text-[0.7rem] uppercase tracking-wider text-ink-soft font-semibold">6. Pimpinan Rapat / Pemateri</p>
                    <p class="mt-1 text-sm font-bold text-ink">{{ $attendance->meeting_leader }}</p>
                </div>

                <div class="sm:col-span-2 rounded-xl bg-canvas/60 p-4 border border-line/60">
                    <p class="text-[0.7rem] uppercase tracking-wider text-ink-soft font-semibold">7. Kegiatan / Materi yang Disampaikan</p>
                    <p class="mt-2 text-sm text-ink leading-relaxed whitespace-pre-line">{{ $attendance->material_summary }}</p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
