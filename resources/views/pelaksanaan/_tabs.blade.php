<div class="mb-6 border-b border-line">
    <nav class="-mb-px flex space-x-2 sm:space-x-6 overflow-x-auto">
        <a href="{{ route('pelaksanaan.matriks') }}"
           class="whitespace-nowrap pb-3 px-1 border-b-2 font-semibold text-xs sm:text-sm transition-colors {{ request()->routeIs('pelaksanaan.matriks') ? 'border-emerald-600 text-emerald-800' : 'border-transparent text-ink-soft hover:text-ink hover:border-slate-300' }}">
            <span class="flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                </svg>
                Matriks Pelaksanaan
            </span>
        </a>

        <a href="{{ route('pelaksanaan.rekap') }}"
           class="whitespace-nowrap pb-3 px-1 border-b-2 font-semibold text-xs sm:text-sm transition-colors {{ request()->routeIs('pelaksanaan.rekap') ? 'border-emerald-600 text-emerald-800' : 'border-transparent text-ink-soft hover:text-ink hover:border-slate-300' }}">
            <span class="flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                Rekap Pelaksanaan
            </span>
        </a>

        <a href="{{ route('pelaksanaan.referensi') }}"
           class="whitespace-nowrap pb-3 px-1 border-b-2 font-semibold text-xs sm:text-sm transition-colors {{ request()->routeIs('pelaksanaan.referensi') ? 'border-emerald-600 text-emerald-800' : 'border-transparent text-ink-soft hover:text-ink hover:border-slate-300' }}">
            <span class="flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
                Referensi Pelaksanaan
            </span>
        </a>

        <a href="{{ route('pelaksanaan.petunjuk') }}"
           class="whitespace-nowrap pb-3 px-1 border-b-2 font-semibold text-xs sm:text-sm transition-colors {{ request()->routeIs('pelaksanaan.petunjuk') ? 'border-emerald-600 text-emerald-800' : 'border-transparent text-ink-soft hover:text-ink hover:border-slate-300' }}">
            <span class="flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Petunjuk Pra-Kontrak
            </span>
        </a>
    </nav>
</div>
