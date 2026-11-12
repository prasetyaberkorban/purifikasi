<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Lengkap Rekap Tahapan Pelaksanaan 2026 (Semua Sheet)</title>
    <style>
        * { font-family: DejaVu Sans, sans-serif; }
        body { font-size: 7.5px; color: #16211a; margin: 15px; }
        h1 { font-size: 13px; margin: 0 0 2px; color: #0D4C1E; }
        h2 { font-size: 10px; margin: 10px 0 4px; color: #0D4C1E; border-bottom: 1px solid #0D4C1E; padding-bottom: 2px; }
        .muted { color: #6b7280; }
        .head { border-bottom: 2px solid #0D4C1E; padding-bottom: 5px; margin-bottom: 8px; }
        .meta { margin-bottom: 8px; font-size: 7px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        th { background: #0D4C1E; color: #fff; text-align: left; padding: 3px 4px; font-size: 7px; }
        td { border-bottom: 1px solid #e5e9e6; padding: 2.5px 4px; vertical-align: top; }
        tr:nth-child(even) td { background: #f8f9fa; }
        .right { text-align: right; }
        .center { text-align: center; }
        .bold { font-weight: bold; }
        .page-break { page-break-after: always; }
        .badge { display: inline-block; padding: 1px 3px; border-radius: 2px; font-size: 6.5px; font-weight: bold; }
        .badge-green { background: #d1fae5; color: #065f46; }
        .badge-amber { background: #fef3c7; color: #92400e; }
        .badge-red { background: #fee2e2; color: #991b1b; }
    </style>
</head>
<body>
    <div class="head">
        <h1>LAPORAN LENGKAP REKAP TAHAPAN PELAKSANAAN 2026 (SEMUA SHEET)</h1>
        <div class="muted">{{ $company }} &middot; {{ $unit }} &middot; Dicetak: {{ $printedAt->translatedFormat('d F Y H:i') }}</div>
    </div>

    <!-- SHEET 1: MATRIKS PELAKSANAAN -->
    <h2>1. MATRIKS PELAKSANAAN ({{ $projects->count() }} PROYEK)</h2>
    <table>
        <thead>
            <tr>
                <th class="center" style="width: 15px;">No</th>
                <th>Kode</th>
                <th>Nama Project</th>
                <th>Pengelola</th>
                <th>Status Pekerjaan</th>
                <th class="center">Progress</th>
                <th>PIC</th>
                <th>Kontrak Awal</th>
                <th>Kontrak Akhir</th>
                <th class="center">Sisa Hari</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($projects as $idx => $p)
                @php $days = $p->calculated_remaining_days; @endphp
                <tr>
                    <td class="center">{{ $idx + 1 }}</td>
                    <td>{{ $p->kode_project ?: '-' }}</td>
                    <td class="bold">{{ $p->nama_project }}</td>
                    <td>{{ $p->pengelola }}</td>
                    <td>{{ $p->status_pekerjaan }}</td>
                    <td class="center bold">{{ round($p->progress_pekerjaan * 100) }}%</td>
                    <td>{{ $p->pic ?: '-' }}</td>
                    <td>{{ $p->kontrak_awal ? $p->kontrak_awal->format('d/m/Y') : '-' }}</td>
                    <td>{{ $p->kontrak_akhir ? $p->kontrak_akhir->format('d/m/Y') : '-' }}</td>
                    <td class="center">
                        @if ($days !== null)
                            <span class="badge {{ $days < 0 ? 'badge-red' : ($days <= 14 ? 'badge-amber' : 'badge-green') }}">
                                {{ $days }} Hari
                            </span>
                        @else
                            -
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="page-break"></div>

    <!-- SHEET 2: REKAP PELAKSANAAN -->
    <h2>2. REKAPITULASI 7 TAHAPAN PELAKSANAAN PEKERJAAN</h2>
    <table>
        <thead>
            <tr><th class="center" style="width: 20px;">No</th><th>Tahap Pelaksanaan</th><th class="center" style="width: 80px;">Jumlah Proyek</th><th>Keterangan</th></tr>
        </thead>
        <tbody>
            @foreach ($ringkasanTahap as $t)
                <tr>
                    <td class="center">{{ $t['no'] }}</td>
                    <td class="bold">{{ $t['tahap'] }}</td>
                    <td class="center bold">{{ $t['count'] }} Proyek</td>
                    <td>{{ $t['keterangan'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h2>3. MATRIKS TRACKING TAHAPAN PROYEK (✓ = Selesai, ● = Tahap Saat Ini)</h2>
    <table>
        <thead>
            <tr>
                <th class="center" style="width: 15px;">No</th>
                <th>Nama Project</th>
                <th>PIC</th>
                <th>Tahap Saat Ini</th>
                <th class="center">Urutan</th>
                <th>Kontrak Akhir</th>
                <th class="center">Sisa</th>
                <th class="center">KOM</th>
                <th class="center">Izin</th>
                <th class="center">Ambil</th>
                <th class="center">Olah</th>
                <th class="center">Draft</th>
                <th class="center">Submit</th>
                <th class="center">Paparan</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($trackingProjects as $idx => $t)
                @php $p = $t['model']; $days = $p->calculated_remaining_days; @endphp
                <tr>
                    <td class="center">{{ $idx + 1 }}</td>
                    <td class="bold">{{ $p->nama_project }}</td>
                    <td>{{ $p->pic ?: '-' }}</td>
                    <td>{{ $p->status_pekerjaan }}</td>
                    <td class="center bold">{{ $t['order_str'] }}</td>
                    <td>{{ $p->kontrak_akhir ? $p->kontrak_akhir->format('d/m/y') : '-' }}</td>
                    <td class="center">{{ $days !== null ? $days : '-' }}</td>
                    @for ($k = 1; $k <= 7; $k++)
                        <td class="center">
                            @if (($t['checklist'][$k] ?? '') === 'completed')
                                <span style="color: #065f46; font-weight: bold;">✓</span>
                            @elseif (($t['checklist'][$k] ?? '') === 'active')
                                <span style="color: #0369a1; font-weight: bold;">●</span>
                            @else
                                <span class="muted">-</span>
                            @endif
                        </td>
                    @endfor
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- SHEET 3: REFERENSI -->
    <h2>4. REFERENSI PELAKSANAAN (TAHAP & MASTER PIC)</h2>
    <table>
        <thead>
            <tr><th class="center" style="width: 20px;">No</th><th>Tahap Pelaksanaan</th><th class="center">Bobot</th><th>Master PIC Terdaftar</th></tr>
        </thead>
        <tbody>
            @foreach ($tahapList as $idx => $t)
                <tr>
                    <td class="center">{{ $t->urutan }}</td>
                    <td class="bold">{{ $t->tahap }}</td>
                    <td class="center">{{ number_format($t->bobot, 2) }} ({{ round($t->bobot * 100) }}%)</td>
                    <td>{{ $pics[$idx]->nama ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- SHEET 4: PETUNJUK -->
    <h2>5. PANDUAN PENGGUNAAN & STANDAR BOBOT PROSES</h2>
    <table>
        <thead>
            <tr><th class="center" style="width: 20px;">No</th><th style="width: 70px;">Bagian / Warna</th><th>Fungsi</th><th>Cara Penggunaan</th></tr>
        </thead>
        <tbody>
            @foreach ($panduan as $idx => $p)
                <tr><td class="center">{{ $idx + 1 }}</td><td class="bold">{{ $p->bagian_warna }}</td><td>{{ $p->fungsi }}</td><td>{{ $p->cara_penggunaan }}</td></tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
