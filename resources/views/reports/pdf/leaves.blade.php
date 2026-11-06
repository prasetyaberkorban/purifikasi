<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Pengajuan Izin / Cuti / WFA</title>
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
        .badge { padding: 1px 5px; border-radius: 6px; font-size: 7px; background: #EAF4EE; color: #1E6B38; }
        .totals { margin-top: 8px; font-size: 9px; }
    </style>
</head>
<body>
    <div class="head">
        <h1>LAPORAN PENGAJUAN IZIN / CUTI / WFA</h1>
        <div class="muted">{{ $company }} &middot; {{ $unit }}</div>
    </div>

    <div class="meta muted">
        Dicetak: {{ $printedAt->translatedFormat('d F Y H:i') }}
        @if (!empty($filters['type']))
            &middot; Jenis: {{ $filters['type'] }}
        @endif
        @if (!empty($filters['q']))
            &middot; Pencarian: "{{ $filters['q'] }}"
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th>Kode</th>
                <th>Tgl Pengajuan</th>
                <th>Nama Karyawan/Magang</th>
                <th>Jenis Izin</th>
                <th class="right">Durasi</th>
                <th>Keterangan</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($rows as $item)
                <tr>
                    <td><strong>{{ $item->code }}</strong></td>
                    <td>{{ $item->created_at?->format('d/m/Y') }}</td>
                    <td>{{ $item->name }}</td>
                    <td>{{ $item->leaveTypeLabel() }}</td>
                    <td class="right">{{ $item->days_count }} hari</td>
                    <td>{{ $item->description }}</td>
                    <td><span class="badge">{{ ucfirst($item->status) }}</span></td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 10px;">Tidak ada data izin/cuti/WFA.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="totals">
        <strong>Total:</strong> {{ $rows->count() }} pengajuan &middot; {{ $rows->sum('days_count') }} total hari
    </div>
</body>
</html>
