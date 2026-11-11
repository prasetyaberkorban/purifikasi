@props(['active' => 'matriks'])

<div class="mb-5 flex flex-wrap items-center gap-1.5 border-b border-line pb-3">
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
