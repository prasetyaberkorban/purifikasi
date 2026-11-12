<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Migrasi & Sinkronisasi Online · Purifikasi Inventaris</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        mono: ['"JetBrains Mono"', 'monospace'],
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen py-10 px-4 font-sans antialiased selection:bg-emerald-500 selection:text-white">

    <div class="max-w-4xl mx-auto space-y-6">

        <!-- Header Card -->
        <div class="bg-gradient-to-br from-slate-900 via-slate-900 to-emerald-950/40 border border-slate-800/80 rounded-2xl p-6 sm:p-8 shadow-2xl relative overflow-hidden">
            <div class="absolute -right-12 -top-12 w-48 h-48 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>

            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center shrink-0">
                        <svg class="w-7 h-7 text-emerald-400 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-ping"></span>
                                Live Sync Runner
                            </span>
                            <span class="text-xs text-slate-400 font-mono">{{ $runTime }}</span>
                        </div>
                        <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-white mt-1">
                            Migrasi Database & Sinkronisasi Online
                        </h1>
                        <p class="text-sm text-slate-400 mt-0.5">
                            Purifikasi Inventaris & Manajemen Proyek Pertamina
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2 w-full sm:w-auto">
                    <a href="{{ url()->current() }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold bg-emerald-600 hover:bg-emerald-500 text-white shadow-lg shadow-emerald-900/30 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Jalankan Ulang
                    </a>
                </div>
            </div>

            <!-- Status Banner -->
            <div class="mt-6 p-4 rounded-xl {{ $hasError ? 'bg-rose-950/40 border border-rose-500/30 text-rose-200' : 'bg-emerald-950/40 border border-emerald-500/30 text-emerald-200' }} flex items-start gap-3">
                @if($hasError)
                    <svg class="w-5 h-5 text-rose-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    <div>
                        <div class="font-bold text-sm text-rose-300">Ada kendala dalam migrasi database</div>
                        <p class="text-xs text-rose-400/90 mt-0.5">Periksa log detail langkah di bawah ini untuk melihat pesan galat.</p>
                    </div>
                @else
                    <svg class="w-5 h-5 text-emerald-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <div class="font-bold text-sm text-emerald-300">Database & Sistem Telah Siap Digunakan!</div>
                        <p class="text-xs text-emerald-400/90 mt-0.5">Seluruh skema tabel, master data referensi, serta cache aplikasi telah disinkronkan.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Tabel Status Tabel Pertamina -->
        <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-6 shadow-xl">
            <h2 class="text-base font-bold text-white flex items-center gap-2 mb-4">
                <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2 1.5 3 3.5 3h9c2 0 3.5-1 3.5-3V7c0-2-1.5-3-3.5-3h-9C5.5 4 4 5 4 7zm0 5h16M4 12c0 2 1.5 3 3.5 3h9c2 0 3.5-1 3.5-3"></path>
                </svg>
                Status Tabel Database Proyek Pertamina
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                @foreach($tableChecks as $tbl)
                    <div class="bg-slate-950/60 border {{ $tbl['exists'] ? 'border-emerald-500/20' : 'border-rose-500/30' }} rounded-xl p-3.5 flex flex-col justify-between">
                        <div>
                            <div class="text-[11px] font-mono text-slate-400 truncate" title="{{ $tbl['table'] }}">
                                {{ $tbl['table'] }}
                            </div>
                            <div class="text-xs font-semibold text-slate-200 mt-0.5">
                                {{ $tbl['label'] }}
                            </div>
                        </div>
                        <div class="mt-3 flex items-center justify-between pt-2 border-t border-slate-800/60">
                            @if($tbl['exists'])
                                <span class="inline-flex items-center gap-1 text-[11px] font-medium text-emerald-400">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Ada
                                </span>
                                <span class="text-xs font-bold font-mono text-slate-300">
                                    {{ $tbl['count'] }} baris
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 text-[11px] font-medium text-rose-400">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                    Belum Ada
                                </span>
                                <span class="text-xs font-mono text-slate-500">-</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Detail Langkah Eksekusi -->
        <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-6 shadow-xl space-y-4">
            <h2 class="text-base font-bold text-white flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                Log Eksekusi & Riwayat Proses
            </h2>

            <div class="space-y-3">
                @foreach($logs as $idx => $log)
                    <div class="bg-slate-950/80 border border-slate-800/80 rounded-xl overflow-hidden">
                        <div class="p-3.5 flex items-center justify-between gap-3 bg-slate-900/40">
                            <div class="flex items-center gap-2.5">
                                <span class="w-6 h-6 rounded-lg bg-slate-800 font-mono text-xs font-bold flex items-center justify-center text-slate-300">
                                    {{ $idx + 1 }}
                                </span>
                                <span class="text-sm font-semibold text-slate-200">{{ $log['step'] }}</span>
                            </div>

                            @if($log['status'] === 'success')
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                    Sukses
                                </span>
                            @elseif($log['status'] === 'danger')
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-500/10 text-rose-400 border border-rose-500/20">
                                    Gagal
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-sky-500/10 text-sky-400 border border-sky-500/20">
                                    Info
                                </span>
                            @endif
                        </div>

                        <div class="p-3.5 bg-slate-950 font-mono text-xs text-slate-300 leading-relaxed overflow-x-auto whitespace-pre-wrap border-t border-slate-800/60 max-h-48">
{{ $log['detail'] }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Quick Access Navigation Buttons -->
        <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-6 shadow-xl">
            <h3 class="text-sm font-bold text-slate-300 mb-3">Tautan Langsung Aplikasi:</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                <a href="{{ route('pelaksanaan.matriks') }}" class="flex items-center justify-between p-3.5 rounded-xl bg-slate-950 hover:bg-emerald-950/40 border border-slate-800 hover:border-emerald-500/30 text-white transition-all group">
                    <div>
                        <div class="text-xs font-bold text-white group-hover:text-emerald-300">Matriks Pelaksanaan 2026</div>
                        <div class="text-[11px] text-slate-400">17 Proyek Pelaksanaan Aktif</div>
                    </div>
                    <svg class="w-4 h-4 text-slate-500 group-hover:text-emerald-400 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>

                <a href="{{ route('pelaksanaan.rekap') }}" class="flex items-center justify-between p-3.5 rounded-xl bg-slate-950 hover:bg-emerald-950/40 border border-slate-800 hover:border-emerald-500/30 text-white transition-all group">
                    <div>
                        <div class="text-xs font-bold text-white group-hover:text-emerald-300">Rekap Pelaksanaan 2026</div>
                        <div class="text-[11px] text-slate-400">Tracking 7 Tahap & Checklist</div>
                    </div>
                    <svg class="w-4 h-4 text-slate-500 group-hover:text-emerald-400 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>

                <a href="{{ route('pertamina.matriks') }}" class="flex items-center justify-between p-3.5 rounded-xl bg-slate-950 hover:bg-emerald-950/40 border border-slate-800 hover:border-emerald-500/30 text-white transition-all group">
                    <div>
                        <div class="text-xs font-bold text-white group-hover:text-emerald-300">Matriks Pertamina</div>
                        <div class="text-[11px] text-slate-400">Rekap 29 proyek penawaran</div>
                    </div>
                    <svg class="w-4 h-4 text-slate-500 group-hover:text-emerald-400 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>

                <a href="{{ route('dashboard') }}" class="flex items-center justify-between p-3.5 rounded-xl bg-slate-950 hover:bg-emerald-950/40 border border-slate-800 hover:border-emerald-500/30 text-white transition-all group">
                    <div>
                        <div class="text-xs font-bold text-white group-hover:text-emerald-300">Dashboard Utama</div>
                        <div class="text-[11px] text-slate-400">Inventaris & Sistem Purifikasi</div>
                    </div>
                    <svg class="w-4 h-4 text-slate-500 group-hover:text-emerald-400 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
            </div>
        </div>

    </div>

</body>
</html>
