<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Riwayat Service & Pemeliharaan</title>
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
        .badge { padding: 1px 5px; border-radius: 6px; font-size: 7px; background: #EAF4EE; color: #1E6B38; }
        .totals { margin-top: 8px; font-size: 9px; }
    </style>
</head>
<body>
    <div class="head">
        <h1>LAPORAN RIWAYAT SERVICE & PEMELIHARAAN</h1>
        <div class="muted">{{ $company }} &middot; {{ $unit }}</div>
    </div>

    <div class="meta muted">
        Dicetak: {{ $printedAt->translatedFormat('d F Y H:i') }}
        @if (!empty($filters['q']))
            &middot; Filter pencarian: "{{ $filters['q'] }}"
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th>Kode</th>
                <th>Tgl Service</th>
                <th>PIC</th>
                <th>Nama Barang</th>
                <th>Merk / Type</th>
                <th>Tempat Service</th>
                <th>Pernah Service</th>
                <th>Kerusakan</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($rows as $item)
                <tr>
                    <td><strong>{{ $item->code }}</strong></td>
                    <td>{{ $item->service_date?->format('d/m/Y') }}</td>
                    <td>{{ $item->pic_name }}</td>
                    <td>{{ $item->asset_name }}</td>
                    <td>{{ $item->brand_type }}</td>
                    <td>{{ $item->service_location }}</td>
                    <td>{{ $item->previously_serviced === 'sudah' ? 'Sudah Pernah' : 'Baru Pertama' }}</td>
                    <td>{{ $item->damage_description }}</td>
                    <td><span class="badge">{{ ucfirst($item->status) }}</span></td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" style="text-align: center; padding: 10px;">Tidak ada data service.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="totals">
        <strong>Total:</strong> {{ $rows->count() }} catatan service
    </div>
</body>
</html>
