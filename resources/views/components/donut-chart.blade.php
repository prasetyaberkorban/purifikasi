@props(['items' => [], 'data' => []])

@php
    $items = collect($items)->filter(fn ($i) => ($i['value'] ?? 0) >= 0);
    $data = collect($data)->values();

    $total = max(1, $items->sum('value'));
    $radius = 46;
    $circumference = 2 * M_PI * $radius;

    $segments = [];
    $offset = 0.0;

    foreach ($items as $index => $item) {
        $fraction = ($item['value'] ?? 0) / $total;
        $length = $fraction * $circumference;
        $segments[] = [
            'index' => $index,
            'label' => $item['label'] ?? '',
            'value' => $item['value'] ?? 0,
            'color' => $item['color'] ?? '#6495ED',
            'dash' => round($length, 3),
            'gap' => round($circumference - $length, 3),
            'offset' => round(-$offset, 3),
        ];
        $offset += $length;
    }

    $palette = ['#0d6efd', '#22c55e', '#f59e0b', '#ef4444', '#8b8540', '#6495ed'];
@endphp

<div {{ $attributes->merge(['class' => 'flex flex-col items-center gap-5 sm:flex-row sm:gap-7']) }}
     x-data="{ activeIndex: null }">
    <div class="relative shrink-0 p-2">
        <svg viewBox="0 0 120 120" class="h-32 w-32 -rotate-90 sm:h-36 sm:w-36 overflow-visible" role="img" aria-label="Diagram kondisi aset">
            <circle cx="60" cy="60" r="{{ $radius }}" fill="none" stroke="#eef2ef" stroke-width="16" />
            @foreach ($segments as $segment)
                <circle cx="60" cy="60" r="{{ $radius }}" fill="none"
                        stroke="{{ $segment['color'] }}" stroke-width="16"
                        stroke-dasharray="{{ $segment['dash'] }} {{ $segment['gap'] }}"
                        stroke-dashoffset="{{ $segment['offset'] }}"
                        stroke-linecap="butt"
                        class="transition-all duration-300 origin-center cursor-pointer"
                        style="transform-origin: 60px 60px;"
                        :class="{
                            'scale-110 brightness-110 drop-shadow-xl stroke-[19]': activeIndex === {{ $segment['index'] }},
                            'opacity-40 scale-95': activeIndex !== null && activeIndex !== {{ $segment['index'] }}
                        }"
                        @mouseenter="activeIndex = {{ $segment['index'] }}"
                        @mouseleave="activeIndex = null" />
            @endforeach
        </svg>
        <div class="pointer-events-none absolute inset-0 flex flex-col items-center justify-center transition-all duration-200">
            @foreach ($items as $index => $item)
                <template x-if="activeIndex === {{ $index }}">
                    <div class="flex flex-col items-center justify-center text-center">
                        <span class="text-xl font-black text-ink">{{ number_format($item['value'] ?? 0, 0, ',', '.') }}</span>
                        <span class="text-[0.62rem] font-bold text-forest uppercase truncate max-w-[4.8rem]">{{ $item['label'] }}</span>
                    </div>
                </template>
            @endforeach
            <template x-if="activeIndex === null">
                <div class="flex flex-col items-center justify-center text-center">
                    <span class="text-xl font-bold text-ink">{{ number_format($items->sum('value'), 0, ',', '.') }}</span>
                    <span class="text-[0.65rem] tracking-wide text-ink-soft uppercase">Aset</span>
                </div>
            </template>
        </div>
    </div>

    <ul class="w-full space-y-1.5">
        @foreach ($items as $index => $item)
            <li class="flex items-center justify-between gap-3 text-sm cursor-pointer rounded-lg px-2.5 py-1.5 transition-all duration-300"
                :class="{
                    'scale-105 shadow-sm bg-canvas/80 font-bold': activeIndex === {{ $index }},
                    'opacity-40 scale-95': activeIndex !== null && activeIndex !== {{ $index }}
                }"
                @mouseenter="activeIndex = {{ $index }}"
                @mouseleave="activeIndex = null">
                <span class="flex items-center gap-2.5 text-ink-soft" :class="activeIndex === {{ $index }} ? 'text-forest font-semibold' : ''">
                    <span class="h-3 w-3 shrink-0 rounded-full transition-transform"
                          :class="activeIndex === {{ $index }} ? 'scale-125' : ''"
                          style="background: {{ $item['color'] ?? ($palette[$index % count($palette)]) }}"></span>
                    {{ $item['label'] }}
                </span>
                <span class="font-semibold text-ink">{{ number_format($item['value'] ?? 0, 0, ',', '.') }}</span>
            </li>
        @endforeach
    </ul>
</div>
