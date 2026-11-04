@props(['items' => [], 'label' => 'Statistik', 'suffix' => '', 'height' => 160, 'formatter' => null, 'defaultColor' => '#1E6B38'])

@php
    $items = collect($items);
    $max = max(1, (float) $items->max('value'));
@endphp

<div {{ $attributes->merge(['class' => 'w-full']) }} x-data="{ activeIndex: null }">
    <div class="relative flex items-end justify-between gap-2 sm:gap-4 pt-8" style="height: {{ $height }}px">
        @foreach ($items as $index => $item)
            @php
                $value = (float) $item['value'];
                $pct = max(5, ($value / $max) * 100);
                $display = $formatter ? $formatter($value) : number_format($value, 0, ',', '.');
                $color = $item['color'] ?? $defaultColor;
            @endphp
            <div class="group relative flex h-full min-w-0 flex-1 flex-col items-center justify-end cursor-pointer transition-all duration-300 ease-out origin-bottom"
                 :class="{
                     'scale-110 z-20': activeIndex === {{ $index }},
                     'opacity-40 scale-95': activeIndex !== null && activeIndex !== {{ $index }}
                 }"
                 @mouseenter="activeIndex = {{ $index }}"
                 @mouseleave="activeIndex = null">
                
                {{-- Modern Floating Tooltip --}}
                <div class="pointer-events-none absolute -top-3 z-30 flex flex-col items-center transition-all duration-200"
                     :class="activeIndex === {{ $index }} ? 'opacity-100 -translate-y-2 scale-100' : 'opacity-0 scale-90 translate-y-0'">
                    <div class="whitespace-nowrap rounded-lg bg-forest-900/95 px-3 py-1.5 text-xs font-bold text-white shadow-xl backdrop-blur ring-1 ring-white/15">
                        {{ $item['label'] }}: <span class="font-black text-leaf-100">{{ $display }}{{ $suffix }}</span>
                    </div>
                    <div class="h-1.5 w-1.5 -translate-y-1 rotate-45 bg-forest-900/95"></div>
                </div>

                {{-- Value Label --}}
                <span class="mb-1.5 text-[0.7rem] font-bold transition-all duration-200"
                      :class="activeIndex === {{ $index }} ? 'text-forest font-black scale-110' : 'text-ink-soft/80'">
                    {{ $display }}{{ $suffix }}
                </span>

                {{-- Bar Track & Modern Rounded Bar with Shadow --}}
                <div class="relative flex w-full flex-1 items-end rounded-t-xl bg-canvas/60 p-0.5 transition-all duration-300"
                     :class="activeIndex === {{ $index }} ? 'bg-canvas ring-2 ring-forest/20 shadow-md' : ''">
                    <div class="w-full rounded-t-lg transition-all duration-300 ease-out origin-bottom"
                         :class="activeIndex === {{ $index }} ? 'shadow-xl brightness-110 scale-y-[1.03]' : 'shadow-sm'"
                         style="height: {{ $pct }}%; background: linear-gradient(to top, {{ $color }}, {{ $color }}cc);"
                         role="img" aria-label="{{ $label }} {{ $item['label'] }}: {{ $display }}">
                    </div>
                </div>

                {{-- Category/Year Label --}}
                <span class="mt-2 block w-full text-center text-[0.72rem] font-semibold leading-tight transition-all duration-300 break-words line-clamp-2"
                      :class="activeIndex === {{ $index }} ? 'text-forest font-bold scale-105' : 'text-ink-soft'"
                      title="{{ $item['label'] }}">
                    {{ $item['label'] }}
                </span>
            </div>
        @endforeach
    </div>
</div>
