@props([
    'label',
    'value',
    'hint' => null,
    'icon' => 'box',
    'tone' => 'leaf',
    'href' => null,
])

@php
    $tones = [
        'leaf' => ['bg' => 'bg-leaf-50', 'text' => 'text-leaf', 'ring' => 'ring-leaf/15'],
        'sky' => ['bg' => 'bg-sky-50', 'text' => 'text-[#2f5fbf]', 'ring' => 'ring-sky/20'],
        'olive' => ['bg' => 'bg-olive-50', 'text' => 'text-[#6b6524]', 'ring' => 'ring-olive/20'],
        'forest' => ['bg' => 'bg-forest/5', 'text' => 'text-forest', 'ring' => 'ring-forest/10'],
    ][$tone] ?? ['bg' => 'bg-leaf-50', 'text' => 'text-leaf', 'ring' => 'ring-leaf/15'];
@endphp

<{{ $href ? 'a' : 'div' }} @if ($href) href="{{ $href }}" @endif
    {{ $attributes->merge(['class' => 'stat-card group relative overflow-hidden']) }}>
    <div class="flex items-start justify-between gap-3">
        <p class="text-[0.72rem] font-semibold tracking-wide text-ink-soft uppercase">{{ $label }}</p>
        <span class="grid h-9 w-9 shrink-0 place-items-center rounded-xl {{ $tones['bg'] }} {{ $tones['text'] }} ring-1 {{ $tones['ring'] }}">
            <x-ui-icon :name="$icon" class="h-[1.05rem] w-[1.05rem]" />
        </span>
    </div>
    <p class="mt-2 text-[1.65rem] leading-none font-extrabold tracking-tight text-ink">{{ $value }}</p>
    @if ($hint)
        <p class="mt-2 text-[0.72rem] text-ink-soft">{{ $hint }}</p>
    @endif
</{{ $href ? 'a' : 'div' }}>