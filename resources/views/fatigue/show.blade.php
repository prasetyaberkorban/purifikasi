<x-app-layout title="Detail Penilaian Fatik Individual" :subtitle="$assessment->code.' · '.$assessment->name" active="fatigue">
    <nav class="mb-4 flex items-center gap-2 text-[0.72rem] text-ink-soft">
        <a href="{{ route('fatigue.index') }}" class="hover:text-forest">Riwayat Penilaian</a>
        <span>/</span>
        <span class="font-medium text-ink">{{ $assessment->code }}</span>
    </nav>

    <div class="grid gap-4 lg:grid-cols-3">
        <div class="space-y-4 lg:col-span-2">
            {{-- Data Detail Penilaian --}}
            <section class="card p-4 sm:p-6 space-y-4">
                <div class="flex items-center justify-between border-b border-line pb-3">
                    <div>
                        <span class="font-mono text-xs font-bold text-forest">{{ $assessment->code }}</span>
                        <h2 class="text-base font-bold text-ink">{{ $assessment->name }}</h2>
                    </div>
                    @if ($assessment->isFullyFit())
                        <span class="rounded-lg bg-leaf-50 px-2.5 py-1 text-xs font-bold text-forest border border-leaf/30">
                            Fit / Kondisi Prima
                        </span>
                    @else
                        <span class="rounded-lg bg-amber-50 px-2.5 py-1 text-xs font-bold text-amber-800 border border-amber-200">
                            Perlu Perhatian / Risiko Kelelahan
                        </span>
                    @endif
                </div>

                <dl class="grid gap-4 text-xs">
                    <div class="grid sm:grid-cols-2 gap-4 pb-3 border-b border-line">
                        <div>
                            <dt class="text-ink-soft">1. Nama Karyawan</dt>
                            <dd class="mt-0.5 font-bold text-ink text-sm">{{ $assessment->name }}</dd>
                        </div>
                        <div>
                            <dt class="text-ink-soft">2. Tanggal Penilaian</dt>
                            <dd class="mt-0.5 font-bold text-ink text-sm">{{ $assessment->assessment_date?->translatedFormat('d F Y') }}</dd>
                        </div>
                    </div>

                    {{-- Pertanyaan 3 s/d 7 --}}
                    <div class="space-y-3 pt-1">
                        <div class="rounded-xl border border-line bg-canvas/40 p-3.5">
                            <dt class="text-ink font-semibold leading-relaxed">
                                3. Dalam 1 minggu terakhir apakah Anda mengalami sakit ringan seperti flu, demam, batuk, sesak nafas dll?
                            </dt>
                            <dd class="mt-1.5 flex items-center gap-2">
                                <span class="font-bold text-xs {{ $assessment->recent_illness === 'Ya' ? 'text-amber-700 bg-amber-50 px-2.5 py-0.5 rounded border border-amber-200' : 'text-emerald-700 bg-emerald-50 px-2.5 py-0.5 rounded border border-emerald-200' }}">
                                    Jawaban: {{ $assessment->recent_illness }}
                                </span>
                            </dd>
                        </div>

                        <div class="rounded-xl border border-line bg-canvas/40 p-3.5">
                            <dt class="text-ink font-semibold leading-relaxed">
                                4. Apakah Anda mengkonsumsi obat atau zat yang dapat menyebabkan kantuk atau menyebabkan Anda tidak layak berkendara?
                            </dt>
                            <dd class="mt-1.5 flex items-center gap-2">
                                <span class="font-bold text-xs {{ $assessment->medication_drowsy === 'Ya' ? 'text-amber-700 bg-amber-50 px-2.5 py-0.5 rounded border border-amber-200' : 'text-emerald-700 bg-emerald-50 px-2.5 py-0.5 rounded border border-emerald-200' }}">
                                    Jawaban: {{ $assessment->medication_drowsy }}
                                </span>
                            </dd>
                        </div>

                        <div class="rounded-xl border border-line bg-canvas/40 p-3.5">
                            <dt class="text-ink font-semibold leading-relaxed">
                                5. Apakah ada hal lain yang menggangu Anda bekerja saat ini? misal : keluarga meninggal / sakit dll
                            </dt>
                            <dd class="mt-1.5 flex items-center gap-2">
                                <span class="font-bold text-xs {{ $assessment->personal_distraction === 'Ya' ? 'text-amber-700 bg-amber-50 px-2.5 py-0.5 rounded border border-amber-200' : 'text-emerald-700 bg-emerald-50 px-2.5 py-0.5 rounded border border-emerald-200' }}">
                                    Jawaban: {{ $assessment->personal_distraction }}
                                </span>
                            </dd>
                        </div>

                        <div class="rounded-xl border border-line bg-canvas/40 p-3.5">
                            <dt class="text-ink font-semibold leading-relaxed">
                                6. Tadi malam Anda tidur berapa jam ?
                            </dt>
                            <dd class="mt-1.5 flex items-center gap-2">
                                <span class="font-bold text-xs {{ $assessment->sleep_hours === 'kurang dari 6 jam' ? 'text-red-700 bg-red-50 px-2.5 py-0.5 rounded border border-red-200' : 'text-emerald-700 bg-emerald-50 px-2.5 py-0.5 rounded border border-emerald-200' }}">
                                    Jawaban: {{ ucfirst($assessment->sleep_hours) }}
                                </span>
                            </dd>
                        </div>

                        <div class="rounded-xl border border-line bg-canvas/40 p-3.5">
                            <dt class="text-ink font-semibold leading-relaxed">
                                7. Apakah Anda yakin/ mampu mengendarai kendaraan dalam waktu perjalanan dinas ini?
                            </dt>
                            <dd class="mt-1.5 flex items-center gap-2">
                                <span class="font-bold text-xs {{ $assessment->fit_to_drive === 'Tidak' ? 'text-red-700 bg-red-50 px-2.5 py-0.5 rounded border border-red-200' : 'text-emerald-700 bg-emerald-50 px-2.5 py-0.5 rounded border border-emerald-200' }}">
                                    Jawaban: {{ $assessment->fit_to_drive }}
                                </span>
                            </dd>
                        </div>
                    </div>
                </dl>
            </section>
        </div>

        {{-- Panel Kanan --}}
        <div class="space-y-4">
            <section class="card p-4 sm:p-5">
                <h3 class="mb-3 text-sm font-bold text-ink">Informasi Penilaian</h3>
                <dl class="space-y-2.5 text-xs">
                    <div class="flex items-center justify-between">
                        <dt class="text-ink-soft">Waktu Kirim</dt>
                        <dd class="font-semibold text-ink">{{ $assessment->created_at?->translatedFormat('d M Y, H:i') }}</dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt class="text-ink-soft">Akun Pelapor</dt>
                        <dd class="font-semibold text-ink">{{ $assessment->user?->name ?? $assessment->name }}</dd>
                    </div>
                    <div class="flex items-center justify-between border-t border-line pt-2">
                        <dt class="text-ink-soft">Kesimpulan</dt>
                        <dd class="font-bold {{ $assessment->isFullyFit() ? 'text-forest' : 'text-amber-800' }}">
                            {{ $assessment->evaluationLabel() }}
                        </dd>
                    </div>
                </dl>

                <div class="mt-5 flex flex-col gap-2">
                    <a href="{{ route('fatigue.create') }}" class="btn-primary w-full text-center text-xs">
                        + Buat Penilaian Baru
                    </a>
                    <a href="{{ route('fatigue.index') }}" class="btn-ghost w-full text-center text-xs">
                        Kembali ke Riwayat
                    </a>
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
