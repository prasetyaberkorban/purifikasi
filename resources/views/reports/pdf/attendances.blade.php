<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Daftar Hadir Rapat & Kegiatan</title>
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
        .totals { margin-top: 8px; font-size: 9px; }
    </style>
</head>
<body>
    <div class="head">
        <h1>LAPORAN DAFTAR HADIR RAPAT & KEGIATAN</h1>
        <div class="muted">{{ $company }} &middot; {{ $unit }}</div>
    </div>

    <div class="meta muted">
        Dicetak: {{ $printedAt->translatedFormat('d F Y H:i') }}
        @if (!empty($filters['branch']))
            &middot; Cabang: {{ $filters['branch'] }}
        @endif
        @if (!empty($filters['activity_type']))
            &middot; Kegiatan: {{ $filters['activity_type'] }}
        @endif
        @if (!empty($filters['q']))
            &middot; Pencarian: "{{ $filters['q'] }}"
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th>Kode</th>
                <th>Tgl Kegiatan</th>
                <th>Nama Peserta</th>
                <th>Jabatan</th>
                <th>Cabang</th>
                <th>Jenis Kegiatan</th>
                <th>Pimpinan / Pemateri</th>
                <th>Materi / Bahasan</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($rows as $item)
                <tr>
                    <td><strong>{{ $item->code }}</strong></td>
                    <td>{{ $item->event_date?->format('d/m/Y') }}</td>
                    <td>{{ $item->full_name }}</td>
                    <td>{{ $item->position }}</td>
                    <td>{{ $item->branch }}</td>
                    <td>{{ $item->activity_type }}</td>
                    <td>{{ $item->meeting_leader }}</td>
                    <td>{{ $item->material_summary }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center; padding: 10px;">Tidak ada data daftar hadir.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="totals">
        <strong>Total:</strong> {{ $rows->count() }} peserta tercatat
    </div>
</body>
</html>
