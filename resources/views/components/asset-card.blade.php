@props(['asset', 'showImage' => false])

<article class="card flex flex-col overflow-hidden">
    @if ($showImage)
        <a href="{{ route('assets.show', $asset) }}" class="relative block aspect-[5/4] max-h-32 overflow-hidden bg-[#eef2ef] sm:max-h-none">
            <x-asset-thumb :asset="$asset" class="h-full w-full object-cover transition duration-300 hover:scale-105" />
            <span class="absolute top-2.5 left-2.5">
                <x-status-badge :status="$asset->status" />
            </span>
            @if ($asset->isCritical())
                <span class="absolute top-2.5 right-2.5 badge-danger">Stok kritis</span>
            @endif
        </a>
    @endif

    <div class="flex flex-1 flex-col p-3.5">
        <div class="flex items-center justify-between gap-1.5">
            <p class="text-[0.65rem] font-medium tracking-wide text-ink-soft/80 uppercase">{{ $asset->code }}</p>
            @if (! $showImage)
                <x-status-badge :status="$asset->status" />
            @endif
        </div>
        <a href="{{ route('assets.show', $asset) }}" class="mt-1 line-clamp-2 text-sm font-bold text-ink hover:text-forest">{{ $asset->name }}</a>
        <p class="mt-1 text-[0.72rem] text-ink-soft">
            {{ $asset->category?->name ?? '-' }} &middot; Stok {{ $asset->availableQuantity() }}/{{ $asset->quantity }}
        </p>

        <div class="mt-3 grid grid-cols-2 gap-1.5">
            @can('create', \App\Models\AssetLoan::class)
                <a href="{{ route('loans.create', ['asset' => $asset->id]) }}"
                   class="flex flex-col items-center gap-1 rounded-lg bg-sky-50 py-2 text-[0.62rem] font-semibold text-[#2f5fbf] {{ $asset->availableQuantity() < 1 ? 'pointer-events-none opacity-40' : '' }}">
                    <x-ui-icon name="swap" class="h-4 w-4" /> Pinjam
                </a>
            @else
                <span class="flex flex-col items-center gap-1 rounded-lg bg-sky-50 py-2 text-[0.62rem] font-semibold text-[#2f5fbf]/40">
                    <x-ui-icon name="swap" class="h-4 w-4" /> Pinjam
                </span>
            @endcan
            <a href="{{ route('assets.show', $asset) }}" class="flex flex-col items-center gap-1 rounded-lg bg-olive-50 py-2 text-[0.62rem] font-semibold text-[#6b6524]">
                <x-ui-icon name="box" class="h-4 w-4" /> Detail
            </a>
        </div>
    </div>
</article>
