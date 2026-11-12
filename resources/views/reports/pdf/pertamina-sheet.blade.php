<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Proyek Pertamina - {{ ucfirst($sheet) }}</title>
    <style>
        * { font-family: DejaVu Sans, sans-serif; }
        body { font-size: 8px; color: #16211a; margin: 15px; }
        h1 { font-size: 13px; margin: 0 0 2px; color: #0D4C1E; text-transform: uppercase; }
        .muted { color: #6b7280; }
        .head { border-bottom: 2px solid #0D4C1E; padding-bottom: 5px; margin-bottom: 8px; }
        .meta { margin-bottom: 8px; font-size: 7.5px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th { background: #0D4C1E; color: #fff; text-align: left; padding: 4px; font-size: 7.5px; }
        td { border-bottom: 1px solid #e5e9e6; padding: 3px 4px; vertical-align: top; }
        tr:nth-child(even) td { background: #f8f9fa; }
        .right { text-align: right; }
        .center { text-align: center; }
        .bold { font-weight: bold; }
        .badge { display: inline-block; padding: 1px 4px; border-radius: 3px; font-size: 7px; font-weight: bold; }
        .badge-green { background: #d1fae5; color: #065f46; }
        .badge-blue { background: #e0f2fe; color: #0369a1; }
        .badge-amber { background: #fef3c7; color: #92400e; }
        .badge-red { background: #fee2e2; color: #991b1b; }
    </style>
</head>
<body>
    <div class="head">
        <h1>REKAP PROJECT PERTAMINA PENAWARAN · {{ strtoupper(str_replace('-', ' ', $sheet)) }}</h1>
        <div class="muted">{{ $company }} &middot; {{ $unit }}</div>
    </div>

    <div class="meta muted">
        Dicetak: {{ $printedAt->translatedFormat('d F Y H:i') }}
        @if (!empty($filters['q'])) &middot; Cari: "{{ $filters['q'] }}" @endif
        @if (!empty($filters['pengelola'])) &middot; Pengelola: {{ $filters['pengelola'] }} @endif
        @if (!empty($filters['hasil_penawaran'])) &middot; Hasil: {{ $filters['hasil_penawaran'] }} @endif
    </div>

    @if ($sheet === 'matriks')
        <table>
            <thead>
                <tr>
                    <th class="center" style="width: 20px;">No</th>
                    <th>Kode</th>
                    <th>Nama Project</th>
                    <th class="right">Nilai Project (Rp)</th>
                    <th>Client</th>
                    <th>Partner</th>
                    <th>Pengelola</th>
                    <th>Progress Penawaran</th>
                    <th class="center">Bobot</th>
                    <th class="center">Hasil</th>
                    <th>Status Pekerjaan</th>
                    <th>PIC</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($projects as $idx => $p)
                    <tr>
                        <td class="center">{{ $idx + 1 }}</td>
                        <td><strong>{{ $p->kode_project }}</strong></td>
                        <td>{{ $p->nama_project }}</td>
                        <td class="right">{{ number_format($p->nilai_project, 0, ',', '.') }}</td>
                        <td>{{ $p->client }}</td>
                        <td>{{ $p->partner ?: '-' }}</td>
                        <td>{{ $p->pengelola }}</td>
                        <td>{{ $p->progress_penawaran ?: '-' }}</td>
                        <td class="center">{{ $p->progress_pra_kontrak !== null ? round($p->progress_pra_kontrak * 100) . '%' : '-' }}</td>
                        <td class="center">
                            @if (strcasecmp($p->hasil_penawaran, 'Menang') === 0)
                                <span class="badge badge-green">Menang</span>
                            @elseif (strcasecmp($p->hasil_penawaran, 'Kalah') === 0)
                                <span class="badge badge-red">Kalah</span>
                            @else
                                <span class="badge badge-amber">{{ $p->hasil_penawaran ?: 'Dalam Proses' }}</span>
                            @endif
                        </td>
                        <td>{{ $p->status_pekerjaan ?: '-' }}</td>
                        <td>{{ $p->pic ?: '-' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="12" class="center muted">Tidak ada data proyek.</td></tr>
                @endforelse
            </tbody>
        </table>
    @elseif ($sheet === 'dashboard')
        @php
            $total = $projects->count();
            $totalNilai = (float) $projects->sum('nilai_project');
            $menang = $projects->filter(fn ($p) => strcasecmp($p->hasil_penawaran ?? '', 'Menang') === 0);
            $totalMenang = $menang->count();
            $totalNilaiMenang = (float) $menang->sum('nilai_project');
        @endphp
        <table>
            <thead>
                <tr>
                    <th>Indikator Utama</th>
                    <th class="center">Jumlah Proyek</th>
                    <th class="right">Total Nilai Penawaran (Rp)</th>
                    <th class="center">Persentase</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="bold">TOTAL KESELURUHAN PROYEK</td>
                    <td class="center bold">{{ $total }}</td>
                    <td class="right bold">{{ number_format($totalNilai, 0, ',', '.') }}</td>
                    <td class="center bold">100%</td>
                </tr>
                <tr>
                    <td class="bold" style="color: #065f46;">PROYEK MENANG (WIN)</td>
                    <td class="center">{{ $totalMenang }}</td>
                    <td class="right">{{ number_format($totalNilaiMenang, 0, ',', '.') }}</td>
                    <td class="center">{{ $total > 0 ? round(($totalMenang / $total) * 100, 1) . '%' : '0%' }}</td>
                </tr>
                <tr>
                    <td class="bold" style="color: #92400e;">PROYEK DALAM PROSES / KALAH</td>
                    <td class="center">{{ $total - $totalMenang }}</td>
                    <td class="right">{{ number_format($totalNilai - $totalNilaiMenang, 0, ',', '.') }}</td>
                    <td class="center">{{ $total > 0 ? round((($total - $totalMenang) / $total) * 100, 1) . '%' : '0%' }}</td>
                </tr>
            </tbody>
        </table>
    @elseif ($sheet === 'ref-pra-kontrak')
        <table>
            <thead>
                <tr><th class="center" style="width: 25px;">Urutan</th><th>Tahap Pra-Kontrak</th><th class="center">Bobot Kumulatif</th><th class="center">Persentase</th></tr>
            </thead>
            <tbody>
                @foreach ($tahapPraKontrak as $t)
                    <tr><td class="center">{{ $t->urutan }}</td><td class="bold">{{ $t->tahap }}</td><td class="center">{{ number_format($t->bobot, 2) }}</td><td class="center">{{ round($t->bobot * 100) }}%</td></tr>
                @endforeach
            </tbody>
        </table>
    @elseif ($sheet === 'ref-pelaksanaan')
        <table>
            <thead>
                <tr><th class="center" style="width: 25px;">Urutan</th><th>Tahap Pelaksanaan</th><th class="center">Bobot</th><th>Daftar Master PIC</th></tr>
            </thead>
            <tbody>
                @foreach ($tahapPelaksanaan as $idx => $t)
                    <tr>
                        <td class="center">{{ $t->urutan }}</td>
                        <td class="bold">{{ $t->tahap }}</td>
                        <td class="center">{{ number_format($t->bobot, 2) }}</td>
                        <td>{{ $pics[$idx]->nama ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @elseif ($sheet === 'petunjuk')
        <table>
            <thead>
                <tr><th class="center" style="width: 25px;">No</th><th>Bagian / Warna</th><th>Fungsi</th><th>Cara Penggunaan</th></tr>
            </thead>
            <tbody>
                @foreach ($panduan as $idx => $p)
                    <tr><td class="center">{{ $idx + 1 }}</td><td class="bold">{{ $p->bagian_warna }}</td><td>{{ $p->fungsi }}</td><td>{{ $p->cara_penggunaan }}</td></tr>
                @endforeach
            </tbody>
        </table>
    @endif
</body>
</html>
