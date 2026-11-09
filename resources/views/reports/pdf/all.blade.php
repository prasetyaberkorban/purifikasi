<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Konsolidasi Seluruh Data Sistem - {{ $company }}</title>
    <style>
        * { font-family: DejaVu Sans, sans-serif; }
        body { font-size: 8px; color: #16211a; margin: 10px; }
        h1 { font-size: 14px; margin: 0 0 3px; color: #0D4C1E; font-weight: bold; }
        h2 { font-size: 11px; margin: 0 0 4px; color: #0D4C1E; border-bottom: 1.5px solid #0D4C1E; padding-bottom: 3px; }
        .muted { color: #556058; }
        .head { border-bottom: 2px solid #0D4C1E; padding-bottom: 6px; margin-bottom: 8px; }
        .meta { margin-bottom: 8px; font-size: 8px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        th { background: #0D4C1E; color: #fff; text-align: left; padding: 4px; font-size: 7.5px; }
        td { border-bottom: 1px solid #e5e9e6; padding: 3px 4px; vertical-align: top; font-size: 7.5px; }
        tr:nth-child(even) td { background: #f8faf8; }
        .right { text-align: right; }
        .center { text-align: center; }
        .badge { display: inline-block; padding: 1px 4px; border-radius: 3px; font-size: 7px; background: #eaf4ee; color: #0D4C1E; font-weight: bold; }
        .page-break { page-break-before: always; }
        .summary-card { border: 1px solid #d3e9dc; background: #f4fbf6; border-radius: 4px; padding: 8px; margin-bottom: 10px; }
    </style>
</head>
<body>
    {{-- Cover / Ringkasan Eksekutif --}}
    <div class="head">
        <h1>LAPORAN KONSOLIDASI SELURUH DATA SISTEM</h1>
        <div class="muted">{{ $company }} &middot; {{ $unit }}</div>
    </div>

    <div class="meta muted">
        Tanggal Dokumen Dicetak: <strong>{{ $printedAt->translatedFormat('d F Y - H:i:s') }} WIB</strong> &middot; Status: <strong>Resmi Terverifikasi</strong>
    </div>

    <div class="summary-card">
        <h2 style="border:none; margin-bottom:6px;">RINGKASAN EKSEKUTIF SEMUA MODUL</h2>
        <table>
            <thead>
                <tr>
                    <th class="center" style="width: 25px;">No</th>
                    <th>Nama Modul / Entitas Sistem</th>
                    <th class="center" style="width: 100px;">Total Catatan</th>
                    <th>Keterangan Ringkas</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $assetsValue = (float) ($datasets['assets'] ?? collect())->sum(fn ($a) => (float) $a->totalValue());
                    $amdalValue = (float) ($datasets['contractsAmdal'] ?? collect())->sum('contract_value');
                    $engValue = (float) ($datasets['contractsEngineering'] ?? collect())->sum('contract_value');
                @endphp
                <tr>
                    <td class="center">1</td>
                    <td><strong>Inventaris Aset &amp; Barang</strong></td>
                    <td class="center">{{ count($datasets['assets'] ?? []) }} Item</td>
                    <td>Total Estimasi Nilai: Rp {{ number_format($assetsValue, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td class="center">2</td>
                    <td><strong>Peminjaman Barang</strong></td>
                    <td class="center">{{ count($datasets['loans'] ?? []) }} Transaksi</td>
                    <td>Catatan pinjam, jatuh tempo &amp; pengembalian</td>
                </tr>
                <tr>
                    <td class="center">3</td>
                    <td><strong>Service &amp; Pemeliharaan</strong></td>
                    <td class="center">{{ count($datasets['maintenances'] ?? []) }} Laporan</td>
                    <td>Perawatan kendaraan &amp; aset kantor</td>
                </tr>
                <tr>
                    <td class="center">4</td>
                    <td><strong>Realisasi Pengadaan</strong></td>
                    <td class="center">{{ count($datasets['procurements'] ?? []) }} Realisasi</td>
                    <td>Belanja aset dan operasional</td>
                </tr>
                <tr>
                    <td class="center">5</td>
                    <td><strong>Izin, Cuti &amp; WFA</strong></td>
                    <td class="center">{{ count($datasets['leaves'] ?? []) }} Pengajuan</td>
                    <td>Permohonan ketidakhadiran &amp; WFA</td>
                </tr>
                <tr>
                    <td class="center">6</td>
                    <td><strong>Peminjaman Kendaraan Operasional</strong></td>
                    <td class="center">{{ count($datasets['vehicleLoans'] ?? []) }} Peminjaman</td>
                    <td>Monitoring armada dinas &amp; speedometer</td>
                </tr>
                <tr>
                    <td class="center">7</td>
                    <td><strong>Daftar Hadir Rapat &amp; Kegiatan</strong></td>
                    <td class="center">{{ count($datasets['attendances'] ?? []) }} Notulensi</td>
                    <td>Presensi pertemuan &amp; kegiatan divisi</td>
                </tr>
                <tr>
                    <td class="center">8</td>
                    <td><strong>Rekapitulasi Kontrak / SPK - AMDAL</strong></td>
                    <td class="center">{{ count($datasets['contractsAmdal'] ?? []) }} Kontrak</td>
                    <td>Total Nilai Kontrak: Rp {{ number_format($amdalValue, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td class="center">9</td>
                    <td><strong>Rekapitulasi Kontrak / SPK - Engineering</strong></td>
                    <td class="center">{{ count($datasets['contractsEngineering'] ?? []) }} Kontrak</td>
                    <td>Total Nilai Kontrak: Rp {{ number_format($engValue, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td class="center">10</td>
                    <td><strong>Penilaian Fatik Individual</strong></td>
                    <td class="center">{{ count($datasets['fatigue'] ?? []) }} Catatan</td>
                    <td>Kesiapan mengemudi &amp; keselamatan kerja</td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- Section 1: Inventaris Aset --}}
    <div class="page-break"></div>
    <h2>1. INVENTARIS ASET &amp; BARANG</h2>
    <table>
        <thead>
            <tr>
                <th style="width: 70px;">Kode</th>
                <th>Nama Barang</th>
                <th>Kategori</th>
                <th>Ruangan</th>
                <th class="center" style="width: 45px;">Stok</th>
                <th class="center" style="width: 45px;">Dipinjam</th>
                <th class="center" style="width: 45px;">Tersedia</th>
                <th class="right" style="width: 80px;">Nilai Total (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($datasets['assets'] ?? [] as $a)
                <tr>
                    <td><strong>{{ $a->code }}</strong></td>
                    <td>{{ $a->name }}</td>
                    <td>{{ $a->category?->name ?? '-' }}</td>
                    <td>{{ $a->items->pluck('room')->filter()->unique()->implode(', ') ?: ($a->room ?? '-') }}</td>
                    <td class="center">{{ $a->quantity }}</td>
                    <td class="center">{{ $a->borrowed_quantity }}</td>
                    <td class="center">{{ $a->availableQuantity() }}</td>
                    <td class="right">{{ number_format($a->totalValue(), 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr><td colspan="8" class="center muted">Tidak ada data aset.</td></tr>
            @endforelse
        </tbody>
    </table>

    {{-- Section 2: Peminjaman Barang --}}
    <div class="page-break"></div>
    <h2>2. RIWAYAT PEMINJAMAN BARANG</h2>
    <table>
        <thead>
            <tr>
                <th style="width: 65px;">Kode</th>
                <th>Aset</th>
                <th>Peminjam</th>
                <th class="center" style="width: 35px;">Jml</th>
                <th>Keperluan</th>
                <th style="width: 65px;">Tgl Pinjam</th>
                <th style="width: 65px;">Tenggat</th>
                <th class="center" style="width: 65px;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($datasets['loans'] ?? [] as $l)
                <tr>
                    <td><strong>{{ $l->code }}</strong></td>
                    <td>{{ $l->asset?->name }} ({{ $l->asset?->code }})</td>
                    <td>{{ $l->borrower?->name }}</td>
                    <td class="center">{{ $l->quantity }}</td>
                    <td>{{ $l->purpose }}</td>
                    <td>{{ $l->borrowed_at?->format('d/m/Y') }}</td>
                    <td>{{ $l->due_at?->format('d/m/Y') }}</td>
                    <td class="center"><span class="badge">{{ $l->status->label() }}</span></td>
                </tr>
            @empty
                <tr><td colspan="8" class="center muted">Tidak ada riwayat peminjaman barang.</td></tr>
            @endforelse
        </tbody>
    </table>

    {{-- Section 3: Service & Pemeliharaan --}}
    <div class="page-break"></div>
    <h2>3. RIWAYAT SERVICE &amp; PEMELIHARAAN</h2>
    <table>
        <thead>
            <tr>
                <th style="width: 65px;">Kode</th>
                <th style="width: 65px;">Tanggal</th>
                <th>PIC</th>
                <th>Kendaraan / Aset</th>
                <th>Merk / Type</th>
                <th>Tempat Servis</th>
                <th>Kerusakan</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($datasets['maintenances'] ?? [] as $m)
                <tr>
                    <td><strong>{{ $m->code }}</strong></td>
                    <td>{{ $m->service_date?->format('d/m/Y') }}</td>
                    <td>{{ $m->pic_name }}</td>
                    <td>{{ $m->asset_name }}</td>
                    <td>{{ $m->brand_type }}</td>
                    <td>{{ $m->service_location }}</td>
                    <td>{{ $m->damage_description }}</td>
                </tr>
            @empty
                <tr><td colspan="7" class="center muted">Tidak ada data service.</td></tr>
            @endforelse
        </tbody>
    </table>

    {{-- Section 4: Realisasi Pengadaan --}}
    <div class="page-break"></div>
    <h2>4. RIWAYAT REALISASI PENGADAAN &amp; ASET KANTOR</h2>
    <table>
        <thead>
            <tr>
                <th style="width: 65px;">Kode</th>
                <th style="width: 65px;">Tanggal</th>
                <th>PIC</th>
                <th>Nama Barang</th>
                <th>Merk / Type</th>
                <th>Jumlah / Ukuran</th>
                <th>Nama Toko / Link</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($datasets['procurements'] ?? [] as $p)
                <tr>
                    <td><strong>{{ $p->code }}</strong></td>
                    <td>{{ $p->realization_date?->format('d/m/Y') }}</td>
                    <td>{{ $p->pic_name }}</td>
                    <td>{{ $p->item_name }}</td>
                    <td>{{ $p->brand_type }}</td>
                    <td>{{ $p->quantity_size }}</td>
                    <td>{{ $p->store_name }}</td>
                </tr>
            @empty
                <tr><td colspan="7" class="center muted">Tidak ada data pengadaan.</td></tr>
            @endforelse
        </tbody>
    </table>

    {{-- Section 5: Izin, Cuti & WFA --}}
    <div class="page-break"></div>
    <h2>5. RIWAYAT IZIN, CUTI &amp; WFA</h2>
    <table>
        <thead>
            <tr>
                <th style="width: 65px;">Kode</th>
                <th style="width: 65px;">Tanggal</th>
                <th>Nama Pegawai</th>
                <th>Jenis</th>
                <th class="center" style="width: 45px;">Durasi</th>
                <th>Keterangan / Alasan</th>
                <th class="center" style="width: 55px;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($datasets['leaves'] ?? [] as $lv)
                <tr>
                    <td><strong>{{ $lv->code }}</strong></td>
                    <td>{{ $lv->created_at?->format('d/m/Y') }}</td>
                    <td>{{ $lv->name }}</td>
                    <td>{{ $lv->leave_type }}</td>
                    <td class="center">{{ $lv->days_count }} Hari</td>
                    <td>{{ $lv->description }}</td>
                    <td class="center"><span class="badge">{{ ucfirst($lv->status) }}</span></td>
                </tr>
            @empty
                <tr><td colspan="7" class="center muted">Tidak ada data izin/cuti/wfa.</td></tr>
            @endforelse
        </tbody>
    </table>

    {{-- Section 6: Peminjaman Kendaraan --}}
    <div class="page-break"></div>
    <h2>6. RIWAYAT PEMINJAMAN KENDARAAN OPERASIONAL</h2>
    <table>
        <thead>
            <tr>
                <th style="width: 65px;">Kode</th>
                <th style="width: 60px;">Berangkat</th>
                <th style="width: 60px;">Kembali</th>
                <th>PIC &amp; Driver</th>
                <th>Kendaraan</th>
                <th class="center" style="width: 50px;">KM</th>
                <th>Tujuan</th>
                <th>Kebersihan</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($datasets['vehicleLoans'] ?? [] as $vl)
                <tr>
                    <td><strong>{{ $vl->code }}</strong></td>
                    <td>{{ $vl->departure_date?->format('d/m/Y') }}</td>
                    <td>{{ $vl->return_date?->format('d/m/Y') }}</td>
                    <td>{{ $vl->pic_name }} (Driver: {{ $vl->driver_name }})</td>
                    <td>{{ $vl->vehicle_type }}</td>
                    <td class="center">{{ $vl->departure_km }}</td>
                    <td>{{ $vl->purpose }}</td>
                    <td>{{ ucfirst($vl->cleanliness_condition) }}</td>
                </tr>
            @empty
                <tr><td colspan="8" class="center muted">Tidak ada data peminjaman kendaraan.</td></tr>
            @endforelse
        </tbody>
    </table>

    {{-- Section 7: Daftar Hadir --}}
    <div class="page-break"></div>
    <h2>7. RIWAYAT DAFTAR HADIR RAPAT &amp; KEGIATAN</h2>
    <table>
        <thead>
            <tr>
                <th style="width: 65px;">Kode</th>
                <th style="width: 65px;">Tanggal</th>
                <th>Nama Peserta</th>
                <th>Jabatan / Cabang</th>
                <th>Kegiatan</th>
                <th>Pemimpin Rapat</th>
                <th>Materi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($datasets['attendances'] ?? [] as $at)
                <tr>
                    <td><strong>{{ $at->code }}</strong></td>
                    <td>{{ $at->event_date?->format('d/m/Y') }}</td>
                    <td>{{ $at->full_name }}</td>
                    <td>{{ $at->position }} ({{ $at->branch }})</td>
                    <td>{{ $at->activity_type }}</td>
                    <td>{{ $at->meeting_leader }}</td>
                    <td>{{ $at->material_summary }}</td>
                </tr>
            @empty
                <tr><td colspan="7" class="center muted">Tidak ada data daftar hadir.</td></tr>
            @endforelse
        </tbody>
    </table>

    {{-- Section 8: Rekapitulasi Kontrak AMDAL --}}
    <div class="page-break"></div>
    <h2>8. REKAPITULASI KONTRAK / SPK - AMDAL</h2>
    <table>
        <thead>
            <tr>
                <th style="width: 70px;">No. SPK &amp; Tgl</th>
                <th>Proyek / Kegiatan</th>
                <th>Pihak Kedua &amp; Uraian</th>
                <th class="right" style="width: 75px;">Nilai Kontrak (Rp)</th>
                <th class="center" style="width: 40px;">Durasi</th>
                <th class="center" style="width: 60px;">Termin</th>
                <th>Bank &amp; Rekening</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($datasets['contractsAmdal'] ?? [] as $ca)
                <tr>
                    <td>
                        <strong>{{ $ca->spk_number }}</strong><br>
                        <span class="muted">{{ $ca->spk_date?->format('d/m/Y') }}</span>
                    </td>
                    <td><strong>{{ $ca->project_name }}</strong></td>
                    <td>
                        <strong>{{ $ca->second_party_name }}</strong><br>
                        <span class="muted">{{ $ca->job_description }}</span>
                    </td>
                    <td class="right"><strong>{{ number_format($ca->contract_value, 0, ',', '.') }}</strong></td>
                    <td class="center">{{ $ca->duration_months }} Bln</td>
                    <td class="center">{{ $ca->termin_1 }}% / {{ $ca->termin_2 }}% / {{ $ca->termin_3 }}%</td>
                    <td>
                        {{ $ca->bank_name }} - {{ $ca->bank_account_number }}<br>
                        <span class="muted">a.n {{ $ca->bank_account_holder }}</span>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="center muted">Tidak ada data kontrak AMDAL.</td></tr>
            @endforelse
        </tbody>
    </table>

    {{-- Section 9: Rekapitulasi Kontrak Engineering --}}
    <div class="page-break"></div>
    <h2>9. REKAPITULASI KONTRAK / SPK - ENGINEERING</h2>
    <table>
        <thead>
            <tr>
                <th style="width: 70px;">No. SPK &amp; Tgl</th>
                <th>Proyek / Kegiatan</th>
                <th>Pihak Kedua &amp; Uraian</th>
                <th class="right" style="width: 75px;">Nilai Kontrak (Rp)</th>
                <th class="center" style="width: 40px;">Durasi</th>
                <th class="center" style="width: 60px;">Termin</th>
                <th>Bank &amp; Rekening</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($datasets['contractsEngineering'] ?? [] as $ce)
                <tr>
                    <td>
                        <strong>{{ $ce->spk_number }}</strong><br>
                        <span class="muted">{{ $ce->spk_date?->format('d/m/Y') }}</span>
                    </td>
                    <td><strong>{{ $ce->project_name }}</strong></td>
                    <td>
                        <strong>{{ $ce->second_party_name }}</strong><br>
                        <span class="muted">{{ $ce->job_description }}</span>
                    </td>
                    <td class="right"><strong>{{ number_format($ce->contract_value, 0, ',', '.') }}</strong></td>
                    <td class="center">{{ $ce->duration_months }} Bln</td>
                    <td class="center">{{ $ce->termin_1 }}% / {{ $ce->termin_2 }}% / {{ $ce->termin_3 }}%</td>
                    <td>
                        {{ $ce->bank_name }} - {{ $ce->bank_account_number }}<br>
                        <span class="muted">a.n {{ $ce->bank_account_holder }}</span>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="center muted">Tidak ada data kontrak Engineering.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
