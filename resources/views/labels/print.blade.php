<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cetak Label · {{ config('app.name') }}</title>
    @vite(['resources/css/app.css'])
    <style>
        @page { size: A4; margin: 10mm; }
        @media print {
            .no-print { display: none !important; }
            body { background: #fff; }
            .label-card { break-inside: avoid; page-break-inside: avoid; }
        }
    </style>
</head>
<body class="bg-canvas p-4 sm:p-8">
    <div class="no-print mx-auto mb-6 flex max-w-5xl flex-wrap items-center justify-between gap-3 rounded-xl border border-line bg-white px-4 py-3">
        <div>
            <p class="text-sm font-bold text-ink">Pratinjau Label</p>
            <p class="text-[0.72rem] text-ink-soft">{{ $assets->count() }} label &middot; tipe {{ strtoupper($mode) }} &middot; ukuran A4</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('labels.index') }}" class="btn-ghost">Kembali</a>
            <button type="button" onclick="window.print()" class="btn-primary">Cetak Sekarang</button>
        </div>
    </div>

    <div class="mx-auto grid max-w-5xl grid-cols-2 gap-3 sm:grid-cols-3">
        @foreach ($assets as $asset)
            @php $meta = $labels->labelMeta($asset); @endphp
            <article class="label-card rounded-xl border border-slate-300 bg-white p-3">
                <div class="flex gap-3">
                    @if (in_array($mode, ['qr', 'both'], true))
                        <div class="w-[4.5rem] shrink-0 [&>svg]:h-[4.5rem] [&>svg]:w-[4.5rem]">{!! $labels->qrSvg($asset) !!}</div>
                    @endif

                    <div class="min-w-0 flex-1">
                        <p class="text-[0.5rem] font-semibold tracking-[0.14em] text-slate-500 uppercase">{{ $meta['unit'] }}</p>
                        <p class="text-[0.55rem] font-bold tracking-[0.1em] text-[#0D4C1E] uppercase">{{ $meta['company'] }}</p>
                        <p class="mt-1 line-clamp-2 text-[0.72rem] leading-tight font-bold text-slate-900">{{ $asset->name }}</p>
                        <p class="font-mono text-[0.55rem] text-slate-600">{{ $asset->code }}</p>
                        <p class="mt-0.5 text-[0.55rem] text-slate-500">{{ $meta['category'] }} &middot; {{ $meta['location'] }}</p>
                        <span class="mt-1 inline-block rounded px-1.5 py-0.5 text-[0.5rem] font-bold uppercase"
                              style="background:#EAF4EE;color:#1E6B38">{{ $meta['status'] }}</span>
                    </div>
                </div>

                @if (in_array($mode, ['barcode', 'both'], true))
                    <div class="mt-2 border-t border-dashed border-slate-200 pt-1.5 [&>svg]:h-9 [&>svg]:w-full">
                        {!! $labels->barcodeSvg($asset, 1.1, 40) !!}
                    </div>
                @endif
            </article>
        @endforeach
    </div>

    <p class="no-print mx-auto mt-6 max-w-5xl text-center text-[0.7rem] text-ink-soft">
        Tempel label pada barang. Scan QR dengan kamera HP untuk membuka detail aset di sistem.
    </p>
</body>
</html>