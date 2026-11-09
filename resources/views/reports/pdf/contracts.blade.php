<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Rekapitulasi Kontrak / SPK</title>
    <style>
        * { font-family: DejaVu Sans, sans-serif; }
        body { font-size: 8px; color: #16211a; }
        h1 { font-size: 13px; margin: 0 0 2px; color: #0D4C1E; }
        .muted { color: #6b7280; }
        .head { border-bottom: 2px solid #0D4C1E; padding-bottom: 5px; margin-bottom: 6px; }
        .meta { margin-bottom: 6px; font-size: 7.5px; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #0D4C1E; color: #fff; text-align: left; padding: 4px; font-size: 7.5px; }
        td { border-bottom: 1px solid #e5e9e6; padding: 3px 4px; vertical-align: top; }
        tr:nth-child(even) td { background: #f8f9fa; }
        .right { text-align: right; }
        .center { text-align: center; }
        .totals { margin-top: 8px; font-size: 8.5px; }
    </style>
</head>
<body>
    <div class="head">
        <h1>REKAPITULASI KONTRAK / SPK{{ !empty($filters['category_label']) ? ' - ' . $filters['category_label'] : '' }}</h1>
        <div class="muted">{{ $company }} &middot; {{ $unit }}</div>
    </div>

    <div class="meta muted">
        Dicetak: {{ $printedAt->translatedFormat('d F Y H:i') }}
        @if (!empty($filters['q']))
            &middot; Pencarian: "{{ $filters['q'] }}"
        @endif
        @if (!empty($filters['bank']))
            &middot; Bank: "{{ $filters['bank'] }}"
        @endif
        @if (!empty($filters['from']) || !empty($filters['to']))
            &middot; Periode: {{ $filters['from'] ?: 'awal' }} s/d {{ $filters['to'] ?: 'sekarang' }}
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th class="center" style="width: 20px;">No</th>
                <th>No. SPK / PO & Tgl</th>
                <th>Proyek / Kegiatan</th>
                <th>Pihak Kedua & Uraian</th>
                <th class="right">Nilai Kontrak (Rp)</th>
                <th class="center">Durasi</th>
                <th class="center">Termin (%)</th>
                <th>Rekening Bank</th>
                <th>Dokumen Sumber</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($rows as $index => $item)
                <tr>
                    <td class="center">{{ $index + 1 }}</td>
                    <td>
                        <strong>{{ $item->spk_number }}</strong><br>
                        <span class="muted">{{ $item->spk_date?->format('d/m/Y') }}</span>
                    </td>
                    <td><strong>{{ $item->project_name }}</strong></td>
                    <td>
                        <strong>{{ $item->second_party_name }}</strong><br>
                        <span class="muted">{{ $item->job_description }}</span>
                    </td>
                    <td class="right"><strong>Rp {{ number_format($item->contract_value, 0, ',', '.') }}</strong></td>
                    <td class="center">{{ $item->duration_months }} Bln</td>
                    <td class="center">
                        I: {{ $item->termin_1 }}%<br>
                        II: {{ $item->termin_2 }}%<br>
                        III: {{ $item->termin_3 }}%
                    </td>
                    <td>
                        {{ $item->bank_name }}<br>
                        <span class="muted">{{ $item->bank_account_number }}</span><br>
                        a.n {{ $item->bank_account_holder }}
                    </td>
                    <td>
                        @if ($item->source_document_text)
                            <div>{{ $item->source_document_text }}</div>
                        @endif
                        @if ($item->source_document_name)
                            <div class="muted">File: {{ $item->source_document_name }}</div>
                        @endif
                        @if (!$item->source_document_text && !$item->source_document_name)
                            <span class="muted">-</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" style="text-align: center; padding: 12px;">Tidak ada data kontrak / SPK.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="totals">
        <strong>Total:</strong> {{ $rows->count() }} Kontrak &middot; Total Nilai: <strong>Rp {{ number_format($rows->sum('contract_value'), 0, ',', '.') }}</strong>
    </div>
</body>
</html>
