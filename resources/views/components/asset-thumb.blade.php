@props(['asset', 'class' => ''])

@if ($asset->photoUrl())
    <img {{ $attributes->merge(['class' => $class]) }} src="{{ $asset->photoUrl() }}" alt="{{ $asset->name }}" loading="lazy">
@else
    <div {{ $attributes->merge(['class' => $class.' grid place-items-center bg-gradient-to-br from-leaf-50 to-[#e6efe9] text-leaf/70']) }}
         role="img" aria-label="Belum ada foto {{ $asset->name }}">
        <x-ui-icon name="box" class="h-7 w-7" />
    </div>
@endif