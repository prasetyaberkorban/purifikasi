<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Inventaris Aset</title>
    <style>
        * { font-family: DejaVu Sans, sans-serif; }
        body { font-size: 9px; color: #16211a; }
        h1 { font-size: 14px; margin: 0 0 2px; color: #0D4C1E; }
        .muted { color: #6b7280; }
        .head { border-bottom: 2px solid #0D4C1E; padding-bottom: 6px; margin-bottom: 8px; }
        .meta { margin-bottom: 8px; font-size: 8px; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #0D4C1E; color: #fff; text-align: left; padding: 4px 5px; font-size: 8px; }
        td { border-bottom: 1px solid #e5e9e6; padding: 3px 5px; }
        tr:nth-child(even) td { background: #f8f9fa; }
        .right { text-align: right; }
        .totals { margin-top: 8px; font-size: 9px; }
        .badge { padding: 1px 5px; border-radius: 6px; font-size: 7px; background: #EAF4EE; color: #1E6B38; }
        .footer { margin-top: 10px; font-size: 7px; color: #6b7280; text-align: right; }
    </style>
</head>
<body>
    <div class="head">
        <h1>LAPORAN INVENTARIS ASET</h1>
        <div class="muted">{{ $company }} &middot; {{ $unit }}</div>
    </div>

    <div class="meta muted">
        Periode: {{ $filters['from'] ?: 'awal' }} s/d {{ $filters['to'] ?: 'sekarang' }} &middot;
        Dicetak: {{ $printedAt->translatedFormat('d F Y H:i') }}
    </div>

    <table>
        <thead>
            <tr>
                <th>Kode Aset</th>
                <th>Nama Barang</th>
                <th>Cabang</th>
                <th>Kategori</th>
                <th>Ruangan</th>
                <th class="right">Stok</th>
                <th class="right">Dipinjam</th>
                <th class="right">Tersedia</th>
                <th>Status</th>
                <th>Kondisi</th>
                <th class="right">Nilai (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($rows as $asset)
                <tr>
                    <td>{{ $asset->code }}</td>
                    <td>{{ $asset->name }}</td>
                    <td>{{ $asset->branch ?? 'Semarang 1' }}</td>
                    <td>{{ $asset->category?->name }}</td>
                    <td>
                        @php
                            $itemRooms = $asset->items->pluck('room')->filter()->unique();
                        @endphp
                        {{ $itemRooms->isNotEmpty() ? $itemRooms->implode(', ') : ($asset->room ?? '-') }}
                    </td>
                    <td class="right">{{ $asset->quantity }}</td>
                    <td class="right">{{ $asset->borrowed_quantity }}</td>
                    <td class="right">{{ $asset->availableQuantity() }}</td>
                    <td><span class="badge">{{ $asset->status->label() }}</span></td>
                    <td>{{ $asset->condition->label() }}</td>
                    <td class="right">{{ number_format($asset->totalValue(), 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <strong>Total:</strong>
        {{ number_format($totals['skus'], 0, ',', '.') }} jenis aset &middot;
        {{ number_format($totals['units'], 0, ',', '.') }} unit &middot;
        {{ number_format($totals['loans'], 0, ',', '.') }} unit sedang dipinjam &middot;
        nilai aset Rp {{ number_format($totals['value'], 0, ',', '.') }}
    </div>
</body>
</html>