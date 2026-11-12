@props(['active' => 'matriks'])

@php
    $queryParams = request()->query();
    $sheetLabels = [
        'matriks' => 'Matriks Pra-Kontrak',
        'dashboard' => 'Dashboard Pra-Kontrak',
        'ref-pra-kontrak' => 'Ref Pra-Kontrak',
        'ref-pelaksanaan' => 'Ref Pelaksanaan',
        'petunjuk' => 'Petunjuk Pra-Kontrak',
    ];
    $currentSheetLabel = $sheetLabels[$active] ?? 'Sheet Ini';
@endphp

<div class="mb-6 flex flex-col xl:flex-row xl:items-center justify-between gap-3 border-b border-line pb-4">
    <!-- Sub Bab 3 Level 3 Navigation Tabs -->
    <div class="flex flex-wrap items-center gap-1.5">
        <a href="{{ route('pertamina.matriks') }}"
           class="inline-flex items-center gap-2 rounded-xl px-3.5 py-2 text-xs font-semibold transition {{ $active === 'matriks' ? 'bg-forest text-white shadow-sm' : 'bg-white text-ink-soft hover:bg-canvas hover:text-ink border border-line' }}">
            <x-ui-icon name="box" class="h-3.5 w-3.5" />
            <span>Matriks Pra-Kontrak</span>
        </a>

        <a href="{{ route('pertamina.dashboard') }}"
           class="inline-flex items-center gap-2 rounded-xl px-3.5 py-2 text-xs font-semibold transition {{ $active === 'dashboard' ? 'bg-forest text-white shadow-sm' : 'bg-white text-ink-soft hover:bg-canvas hover:text-ink border border-line' }}">
            <x-ui-icon name="chart" class="h-3.5 w-3.5" />
            <span>Dashboard Pra-Kontrak</span>
        </a>

        <a href="{{ route('pertamina.ref-pra-kontrak') }}"
           class="inline-flex items-center gap-2 rounded-xl px-3.5 py-2 text-xs font-semibold transition {{ $active === 'ref-pra-kontrak' ? 'bg-forest text-white shadow-sm' : 'bg-white text-ink-soft hover:bg-canvas hover:text-ink border border-line' }}">
            <x-ui-icon name="clipboard-check" class="h-3.5 w-3.5" />
            <span>Referensi Pra-Kontrak</span>
        </a>

        <a href="{{ route('pertamina.ref-pelaksanaan') }}"
           class="inline-flex items-center gap-2 rounded-xl px-3.5 py-2 text-xs font-semibold transition {{ $active === 'ref-pelaksanaan' ? 'bg-forest text-white shadow-sm' : 'bg-white text-ink-soft hover:bg-canvas hover:text-ink border border-line' }}">
            <x-ui-icon name="wrench" class="h-3.5 w-3.5" />
            <span>Referensi Pelaksanaan</span>
        </a>

        <a href="{{ route('pertamina.petunjuk') }}"
           class="inline-flex items-center gap-2 rounded-xl px-3.5 py-2 text-xs font-semibold transition {{ $active === 'petunjuk' ? 'bg-forest text-white shadow-sm' : 'bg-white text-ink-soft hover:bg-canvas hover:text-ink border border-line' }}">
            <x-ui-icon name="document" class="h-3.5 w-3.5" />
            <span>Petunjuk Pra-Kontrak</span>
        </a>
    </div>

    <!-- Tombol Export PDF & Excel (Pilihan Sheet Ini / Semua Sheet) -->
    <div class="flex items-center gap-2 self-start xl:self-auto">
        <!-- DROPDOWN EXPORT PDF -->
        <div x-data="{ open: false }" class="relative inline-block text-left">
            <button @click="open = !open" type="button"
                    class="btn-ghost py-2 px-3 text-xs font-semibold inline-flex items-center gap-1.5 border border-line bg-white hover:bg-slate-50 text-slate-700 shadow-xs rounded-xl"
                    style="background-color: #ffffff !important;">
                <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                </svg>
                <span>Export PDF</span>
                <svg class="w-3.5 h-3.5 text-slate-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>

            <div x-show="open" @click.outside="open = false" x-cloak
                 class="absolute right-0 z-40 mt-1.5 w-60 rounded-xl shadow-2xl border border-slate-200 py-1.5 text-xs animate-in fade-in zoom-in-95"
                 style="background-color: #ffffff !important; color: #0f172a !important;">
                <div class="px-3 py-1 text-[10px] font-bold uppercase tracking-wider text-slate-400 border-b border-slate-100">
                    Opsi Ekspor PDF
                </div>
                <a href="{{ route('pertamina.export.pdf', array_merge($queryParams, ['sheet' => $active])) }}"
                   class="flex items-center justify-between px-3 py-2 text-slate-700 hover:bg-rose-50 hover:text-rose-800 transition-colors">
                    <span class="flex items-center gap-2">
                        <svg class="w-3.5 h-3.5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        <span>Halaman Ini ({{ $currentSheetLabel }})</span>
                    </span>
                    <span class="text-[10px] text-slate-400 font-mono">PDF</span>
                </a>
                <a href="{{ route('pertamina.export.pdf', array_merge($queryParams, ['sheet' => 'all'])) }}"
                   class="flex items-center justify-between px-3 py-2 text-slate-700 hover:bg-rose-50 hover:text-rose-800 transition-colors border-t border-slate-100 font-semibold">
                    <span class="flex items-center gap-2">
                        <svg class="w-3.5 h-3.5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path></svg>
                        <span>Semua Sheet (Lengkap 5 Sheet)</span>
                    </span>
                    <span class="text-[10px] px-1.5 py-0.5 rounded bg-rose-100 text-rose-700 font-mono font-bold">ALL</span>
                </a>
            </div>
        </div>

        <!-- DROPDOWN EXPORT EXCEL -->
        <div x-data="{ open: false }" class="relative inline-block text-left">
            <button @click="open = !open" type="button"
                    class="btn-ghost py-2 px-3 text-xs font-semibold inline-flex items-center gap-1.5 border border-line bg-white hover:bg-slate-50 text-slate-700 shadow-xs rounded-xl"
                    style="background-color: #ffffff !important;">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <span>Export Excel</span>
                <svg class="w-3.5 h-3.5 text-slate-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>

            <div x-show="open" @click.outside="open = false" x-cloak
                 class="absolute right-0 z-40 mt-1.5 w-60 rounded-xl shadow-2xl border border-slate-200 py-1.5 text-xs animate-in fade-in zoom-in-95"
                 style="background-color: #ffffff !important; color: #0f172a !important;">
                <div class="px-3 py-1 text-[10px] font-bold uppercase tracking-wider text-slate-400 border-b border-slate-100">
                    Opsi Ekspor Excel (.xlsx)
                </div>
                <a href="{{ route('pertamina.export.excel', array_merge($queryParams, ['sheet' => $active])) }}"
                   class="flex items-center justify-between px-3 py-2 text-slate-700 hover:bg-emerald-50 hover:text-emerald-800 transition-colors">
                    <span class="flex items-center gap-2">
                        <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        <span>Halaman Ini ({{ $currentSheetLabel }})</span>
                    </span>
                    <span class="text-[10px] text-slate-400 font-mono">XLSX</span>
                </a>
                <a href="{{ route('pertamina.export.excel', array_merge($queryParams, ['sheet' => 'all'])) }}"
                   class="flex items-center justify-between px-3 py-2 text-slate-700 hover:bg-emerald-50 hover:text-emerald-800 transition-colors border-t border-slate-100 font-semibold">
                    <span class="flex items-center gap-2">
                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path></svg>
                        <span>Semua Sheet (Multi-sheet Workbook)</span>
                    </span>
                    <span class="text-[10px] px-1.5 py-0.5 rounded bg-emerald-100 text-emerald-700 font-mono font-bold">ALL</span>
                </a>
            </div>
        </div>
    </div>
</div>
