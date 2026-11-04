<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Peminjaman Aset</title>
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
        .badge { padding: 1px 5px; border-radius: 6px; font-size: 7px; background: #EEF4FF; color: #2f5fbf; }
        .badge-done { background: #EAF4EE; color: #1E6B38; }
        .totals { margin-top: 8px; font-size: 9px; }
        .footer { margin-top: 10px; font-size: 7px; color: #6b7280; text-align: right; }
    </style>
</head>
<body>
    <div class="head">
        <h1>LAPORAN PEMINJAMAN ASET</h1>
        <div class="muted">{{ $company }} &middot; {{ $unit }}</div>
    </div>

    <div class="meta muted">
        Periode: {{ $filters['from'] ?: 'awal' }} s/d {{ $filters['to'] ?: 'sekarang' }} &middot;
        Dicetak: {{ $printedAt->translatedFormat('d F Y H:i') }}
    </div>

    <table>
        <thead>
            <tr>
                <th>Kode Pinjam</th>
                <th>Kode Aset</th>
                <th>Nama Barang</th>
                <th>Peminjam</th>
                <th class="right">Jumlah</th>
                <th>Keperluan</th>
                <th>Tgl Pinjam</th>
                <th>Tenggat</th>
                <th>Tgl Kembali</th>
                <th>Status</th>
                <th>Kondisi Kembali</th>
                <th>Petugas</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($rows as $loan)
                <tr>
                    <td>{{ $loan->code }}</td>
                    <td>{{ $loan->asset?->code }}</td>
                    <td>{{ $loan->asset?->name }}</td>
                    <td>{{ $loan->borrower?->name }}</td>
                    <td class="right">{{ $loan->quantity }}</td>
                    <td>{{ $loan->purpose }}</td>
                    <td>{{ $loan->borrowed_at?->format('d/m/Y') }}</td>
                    <td>{{ $loan->due_at?->format('d/m/Y') }}</td>
                    <td>{{ $loan->returned_at?->format('d/m/Y') }}</td>
                    <td>
                        <span class="badge {{ $loan->status === \App\Enums\LoanStatus::Dikembalikan ? 'badge-done' : '' }}">
                            {{ $loan->status->label() }}
                        </span>
                    </td>
                    <td>{{ $loan->condition_on_return?->label() }}</td>
                    <td>{{ $loan->handler?->name }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <strong>Total:</strong> {{ $rows->count() }} transaksi &middot;
        {{ number_format($totals['loans'], 0, ',', '.') }} unit masih dipinjam
    </div>

    <div class="footer">SGI - Sistem Inventaris Kantor Purifikasi</div>
</body>
</html>