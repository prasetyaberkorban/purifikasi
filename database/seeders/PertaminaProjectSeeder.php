<?php

namespace Database\Seeders;

use App\Models\PertaminaGuideBobotProses;
use App\Models\PertaminaGuidePanduan;
use App\Models\PertaminaProject;
use App\Models\PertaminaRefHasilPenawaran;
use App\Models\PertaminaRefPic;
use App\Models\PertaminaRefStatusProses;
use App\Models\PertaminaRefTahapPelaksanaan;
use App\Models\PertaminaRefTahapPraKontrak;
use Illuminate\Database\Seeder;

class PertaminaProjectSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Matriks Pra-Kontrak (29 Proyek)
        $projects = [
            [
                'kode_project' => 'ENG-2026-001',
                'nama_project' => 'Pengendalian Banjir Di Kantor Sumbagsel',
                'nilai_project' => 580000000,
                'client' => 'PT Pertamina Patra Niaga - Reg Sumbagsel',
                'partner' => 'FT UNDIP',
                'pengelola' => 'Bandung',
                'progress_penawaran' => 'SPK',
                'progress_pra_kontrak' => 1.0,
                'hasil_penawaran' => 'Menang',
                'status_pekerjaan' => 'Laporan',
                'progress_pekerjaan' => 1.0,
                'pic' => 'Deviayana',
            ],
            [
                'kode_project' => 'ENG-2026-002',
                'nama_project' => 'Pengendalian Banjir dan Perbaikan Drainase IT Surabaya',
                'nilai_project' => 448000000,
                'client' => 'PT Pertamina Patra Niaga - IT Surabaya',
                'partner' => 'FT UNDIP',
                'pengelola' => 'Semarang',
                'progress_penawaran' => 'Penawaran Awal',
                'progress_pra_kontrak' => 0.2,
                'hasil_penawaran' => 'Tidak Lanjut',
                'status_pekerjaan' => null,
                'progress_pekerjaan' => null,
                'pic' => null,
            ],
            [
                'kode_project' => 'ENG-2026-003',
                'nama_project' => 'Kajian Jetty Analisa Pipe Support RU IV Balongan',
                'nilai_project' => 510500000,
                'client' => 'PT Pertamina Patra Niaga - RU VI Balongan',
                'partner' => 'LPPM UNDIP',
                'pengelola' => 'Bandung',
                'progress_penawaran' => 'SPK',
                'progress_pra_kontrak' => 1.0,
                'hasil_penawaran' => 'Menang',
                'status_pekerjaan' => 'Laporan',
                'progress_pekerjaan' => 1.0,
                'pic' => 'Siddiq',
            ],
            [
                'kode_project' => 'ENG-2026-004',
                'nama_project' => 'Kajian Assessment Struktur Unit Fluid Catalytic Cracking Unit (FCCU) dan Pondasi Pompa Kilang RU-III Plaju Tahun 2026',
                'nilai_project' => 1198000000,
                'client' => 'PT Pertamina Patra Niaga - RU III Plaju',
                'partner' => 'LPPM UNDIP',
                'pengelola' => 'Semarang',
                'progress_penawaran' => 'SPK',
                'progress_pra_kontrak' => 1.0,
                'hasil_penawaran' => 'Menang',
                'status_pekerjaan' => 'Pekerjaan Lapangan',
                'progress_pekerjaan' => 0.6,
                'pic' => 'Dika',
            ],
            [
                'kode_project' => 'ENG-2026-005',
                'nama_project' => 'Kajian Assessment Jembatan Pipa dan Jembatan Kendaraan di Area Kilang RU-III Plaju Tahun 2026',
                'nilai_project' => 1452000000,
                'client' => 'PT Pertamina Patra Niaga - RU III Plaju',
                'partner' => 'LPPM UNDIP',
                'pengelola' => 'Semarang',
                'progress_penawaran' => 'SPK',
                'progress_pra_kontrak' => 1.0,
                'hasil_penawaran' => 'Menang',
                'status_pekerjaan' => 'Pengurusan Izin Kerja dan Survey Awal',
                'progress_pekerjaan' => 0.4,
                'pic' => 'Dika',
            ],
            [
                'kode_project' => 'ENG-2026-006',
                'nama_project' => 'Kajian kelayakan / Feasibility Study skema sewa sarfas penyaluran bunker MFO milik PT Delarohita di FT Medan Group - Belawan',
                'nilai_project' => 799000000,
                'client' => 'PT Pertamina Patra Niaga - Reg Sumbagut',
                'partner' => 'FT UNDIP',
                'pengelola' => 'Semarang',
                'progress_penawaran' => 'Project Gagal',
                'progress_pra_kontrak' => 0.0,
                'hasil_penawaran' => 'Tidak Lanjut',
                'status_pekerjaan' => null,
                'progress_pekerjaan' => null,
                'pic' => null,
            ],
            [
                'kode_project' => 'ENG-2026-007',
                'nama_project' => 'Kajian Assessment Dampak Kontaminasi Crude Oil Terhadap Kelayakan Stuktur Tanki T171 - T173',
                'nilai_project' => 1952000000,
                'client' => 'PT Pertamina Patra Niaga - RU III Plaju',
                'partner' => 'LPPM UNDIP',
                'pengelola' => 'Bandung',
                'progress_penawaran' => 'Penawaran Awal',
                'progress_pra_kontrak' => 0.2,
                'hasil_penawaran' => 'Dalam Proses',
                'status_pekerjaan' => null,
                'progress_pekerjaan' => null,
                'pic' => null,
            ],
            [
                'kode_project' => 'ENG-2026-008',
                'nama_project' => 'Kajian Assesment WWTP PLTU Pelabuhan Ratu',
                'nilai_project' => 221000000,
                'client' => 'PLTU Pelabuhan Ratu',
                'partner' => 'LPPM UNDIP',
                'pengelola' => 'Semarang',
                'progress_penawaran' => 'Penawaran Awal',
                'progress_pra_kontrak' => 0.2,
                'hasil_penawaran' => 'Dalam Proses',
                'status_pekerjaan' => null,
                'progress_pekerjaan' => null,
                'pic' => null,
            ],
            [
                'kode_project' => 'ENG-2026-009',
                'nama_project' => 'Kajian Assesment Bottom Product System CDU dan Tube Pass Flow Heater HVU RU II Dumai',
                'nilai_project' => 944000000,
                'client' => 'PT Pertamina Patra Niaga - RU II Dumai',
                'partner' => 'LPPM UNDIP',
                'pengelola' => 'Bandung',
                'progress_penawaran' => 'Project Gagal',
                'progress_pra_kontrak' => 0.0,
                'hasil_penawaran' => 'Tidak Lanjut',
                'status_pekerjaan' => null,
                'progress_pekerjaan' => null,
                'pic' => null,
            ],
            [
                'kode_project' => 'ENG-2026-010',
                'nama_project' => 'Kajian Environmental Sensitivity Index (ESI) PT Pertamina Patra Niaga - Regional Jawa Bagian Tengah',
                'nilai_project' => 410000000,
                'client' => 'PT Pertamina Patra Niaga - Reg Jawa Bagian Tengah',
                'partner' => 'FT UNDIP',
                'pengelola' => 'Semarang',
                'progress_penawaran' => 'SPK',
                'progress_pra_kontrak' => 1.0,
                'hasil_penawaran' => 'Menang',
                'status_pekerjaan' => 'Laporan',
                'progress_pekerjaan' => 1.0,
                'pic' => 'Nayaka',
            ],
            [
                'kode_project' => 'ENG-2026-011',
                'nama_project' => 'Kajian Environmental Sensitivity Index (ESI) PT Pertamina Patra Niaga - RPD Sulawesi',
                'nilai_project' => 805000000,
                'client' => 'PT Pertamina Patra Niaga - RPD Sulawesi',
                'partner' => 'FT UNDIP',
                'pengelola' => 'Semarang',
                'progress_penawaran' => 'SP3MK',
                'progress_pra_kontrak' => 0.8,
                'hasil_penawaran' => 'Menang',
                'status_pekerjaan' => 'Pekerjaan Lapangan',
                'progress_pekerjaan' => 0.6,
                'pic' => null,
            ],
            [
                'kode_project' => 'ENG-2026-012',
                'nama_project' => 'Kajian Environmental Sensitivity Index (ESI) PT Pertamina Patra Niaga - Regional Jatimbalinus',
                'nilai_project' => 791000000,
                'client' => 'PT Pertamina Patra Niaga - Reg Jatimbalinus',
                'partner' => 'FT UNDIP',
                'pengelola' => 'Semarang',
                'progress_penawaran' => 'Penawaran Awal',
                'progress_pra_kontrak' => 0.2,
                'hasil_penawaran' => 'Dalam Proses',
                'status_pekerjaan' => null,
                'progress_pekerjaan' => null,
                'pic' => null,
            ],
            [
                'kode_project' => 'ENG-2026-013',
                'nama_project' => 'Kajian Environmental Sensitivity Index (ESI) PT Pertamina Patra Niaga - Regional Sumbagsel',
                'nilai_project' => 375000000,
                'client' => 'PT Pertamina Patra Niaga - Reg Sumbagsel',
                'partner' => 'FT UNDIP',
                'pengelola' => 'Semarang',
                'progress_penawaran' => 'SPK',
                'progress_pra_kontrak' => 1.0,
                'hasil_penawaran' => 'Menang',
                'status_pekerjaan' => 'Laporan',
                'progress_pekerjaan' => 1.0,
                'pic' => 'Nayaka',
            ],
            [
                'kode_project' => 'ENG-2026-014',
                'nama_project' => 'Kajian Environmental Sensitivity Index (ESI) PT Pertamina Patra Niaga - FT Jambi',
                'nilai_project' => 365000000,
                'client' => 'PT Pertamina Patra Niaga - Reg Sumbagsel',
                'partner' => 'FT UNDIP',
                'pengelola' => 'Semarang',
                'progress_penawaran' => 'SP3MK',
                'progress_pra_kontrak' => 0.8,
                'hasil_penawaran' => 'Menang',
                'status_pekerjaan' => 'Pengurusan Izin Kerja dan Survey Awal',
                'progress_pekerjaan' => 0.4,
                'pic' => null,
            ],
            [
                'kode_project' => 'ENG-2026-015',
                'nama_project' => 'Kajian Environmental Sensitivity Index (ESI) PT Pertamina Patra Niaga Regional Papua dan Maluku - FT Ternate',
                'nilai_project' => 432500000,
                'client' => 'PT Pertamina Patra Niaga - Reg Papua Maluku',
                'partner' => 'FT UNDIP',
                'pengelola' => 'Semarang',
                'progress_penawaran' => 'SP3MK',
                'progress_pra_kontrak' => 0.8,
                'hasil_penawaran' => 'Menang',
                'status_pekerjaan' => 'Pengurusan Izin Kerja dan Survey Awal',
                'progress_pekerjaan' => 0.4,
                'pic' => null,
            ],
            [
                'kode_project' => 'ENG-2026-016',
                'nama_project' => 'Borehole dan geolistrik IT Cilacap',
                'nilai_project' => 196000000,
                'client' => 'PT Pertamina Patra Niaga - Reg Jawa Bagian Tengah',
                'partner' => 'PT SYNERGY',
                'pengelola' => 'Semarang',
                'progress_penawaran' => 'SPK',
                'progress_pra_kontrak' => 1.0,
                'hasil_penawaran' => 'Menang',
                'status_pekerjaan' => 'Laporan',
                'progress_pekerjaan' => 1.0,
                'pic' => 'Dhana',
            ],
            [
                'kode_project' => 'ENG-2026-017',
                'nama_project' => 'Borehole dan geolistrik FT Lomanis',
                'nilai_project' => 330000000,
                'client' => 'PT Pertamina Patra Niaga - Reg Jawa Bagian Tengah',
                'partner' => 'PT SYNERGY',
                'pengelola' => 'Semarang',
                'progress_penawaran' => 'SPK',
                'progress_pra_kontrak' => 1.0,
                'hasil_penawaran' => 'Menang',
                'status_pekerjaan' => 'Laporan',
                'progress_pekerjaan' => 1.0,
                'pic' => 'Faiz',
            ],
            [
                'kode_project' => 'ENG-2026-018',
                'nama_project' => 'Geolistrik dan Geohidrologi IT Makassar dan IT Bitung',
                'nilai_project' => 550000000,
                'client' => 'PT Pertamina Patra Niaga - RPD Sulawesi',
                'partner' => 'FT UNDIP',
                'pengelola' => 'Semarang',
                'progress_penawaran' => 'SPK',
                'progress_pra_kontrak' => 1.0,
                'hasil_penawaran' => 'Menang',
                'status_pekerjaan' => 'Pengolahan Data',
                'progress_pekerjaan' => 0.8,
                'pic' => 'Dhana',
            ],
            [
                'kode_project' => 'ENG-2026-019',
                'nama_project' => 'Geolistrik dan Geohidrologi IT Palembang dan IT Lubuk Linggau',
                'nilai_project' => 520000000,
                'client' => 'PT Pertamina Patra Niaga - Reg Sumbagsel',
                'partner' => 'FT UNDIP',
                'pengelola' => 'Semarang',
                'progress_penawaran' => 'SPK',
                'progress_pra_kontrak' => 1.0,
                'hasil_penawaran' => 'Menang',
                'status_pekerjaan' => 'Pengolahan Data',
                'progress_pekerjaan' => 0.8,
                'pic' => 'Dika',
            ],
            [
                'kode_project' => 'ENG-2026-020',
                'nama_project' => 'Geolistrik dan Geohidrologi IT Surabaya dan IT Bima',
                'nilai_project' => 604000000,
                'client' => 'PT Pertamina Patra Niaga - Reg Jatimbalinus',
                'partner' => 'FT UNDIP',
                'pengelola' => 'Semarang',
                'progress_penawaran' => 'Penawaran Awal',
                'progress_pra_kontrak' => 0.2,
                'hasil_penawaran' => 'Dalam Proses',
                'status_pekerjaan' => null,
                'progress_pekerjaan' => null,
                'pic' => null,
            ],
            [
                'kode_project' => 'ENG-2026-021',
                'nama_project' => 'Geolistrik dan Geohidrologi IT Sanggaran dan IT Palaran',
                'nilai_project' => 629300000,
                'client' => 'PT Pertamina Patra Niaga',
                'partner' => 'FT UNDIP',
                'pengelola' => 'Semarang',
                'progress_penawaran' => 'SP3MK',
                'progress_pra_kontrak' => 0.8,
                'hasil_penawaran' => 'Menang',
                'status_pekerjaan' => 'Pekerjaan Lapangan',
                'progress_pekerjaan' => 0.6,
                'pic' => null,
            ],
            [
                'kode_project' => 'ENG-2026-022',
                'nama_project' => 'Geolistrik dan Geohidrologi IT Biak',
                'nilai_project' => 430200000,
                'client' => 'PT Pertamina Patra Niaga - Reg Papua Maluku',
                'partner' => 'FT UNDIP',
                'pengelola' => 'Semarang',
                'progress_penawaran' => 'SP3MK',
                'progress_pra_kontrak' => 0.8,
                'hasil_penawaran' => 'Menang',
                'status_pekerjaan' => 'Pengurusan Izin Kerja dan Survey Awal',
                'progress_pekerjaan' => 0.4,
                'pic' => null,
            ],
            [
                'kode_project' => 'ENG-2026-023',
                'nama_project' => 'Audit Energi RU IV Cilacap',
                'nilai_project' => 391000000,
                'client' => 'PT Pertamina Patra Niaga - RU IV Cilacap',
                'partner' => 'FT UNDIP',
                'pengelola' => 'Semarang',
                'progress_penawaran' => 'Project Gagal',
                'progress_pra_kontrak' => 0.0,
                'hasil_penawaran' => 'Tidak Lanjut',
                'status_pekerjaan' => null,
                'progress_pekerjaan' => null,
                'pic' => null,
            ],
            [
                'kode_project' => 'ENG-2026-024',
                'nama_project' => 'Audit Energi RU II Dumai dan Sei Pakning',
                'nilai_project' => 598000000,
                'client' => 'PT Pertamina Patra Niaga - RU II Dumai dan Sei Pakning',
                'partner' => 'LPPM UNDIP',
                'pengelola' => 'Semarang',
                'progress_penawaran' => 'SPK',
                'progress_pra_kontrak' => 1.0,
                'hasil_penawaran' => 'Menang',
                'status_pekerjaan' => 'Laporan',
                'progress_pekerjaan' => 1.0,
                'pic' => 'Janu',
            ],
            [
                'kode_project' => 'ENG-2026-025',
                'nama_project' => 'Audit Energi dan Air Perusahaan Gas Negara SOR I Stasiun Gas Panaran dan Semare',
                'nilai_project' => 198900000,
                'client' => 'PT Perusahaan Gas Negara',
                'partner' => 'PT PURIFIKASI',
                'pengelola' => 'Semarang',
                'progress_penawaran' => 'SPK',
                'progress_pra_kontrak' => 1.0,
                'hasil_penawaran' => 'Menang',
                'status_pekerjaan' => 'Laporan',
                'progress_pekerjaan' => 1.0,
                'pic' => 'Janu',
            ],
            [
                'kode_project' => 'ENG-2026-026',
                'nama_project' => 'Pembuatan Buku Putih Keproyekan. Mengacu pada SE KBUMN',
                'nilai_project' => 1909000000,
                'client' => 'PT Perusahaan Gas',
                'partner' => 'LPPM UNDIP',
                'pengelola' => 'Semarang',
                'progress_penawaran' => 'Project Gagal',
                'progress_pra_kontrak' => 0.0,
                'hasil_penawaran' => 'Tidak Lanjut',
                'status_pekerjaan' => null,
                'progress_pekerjaan' => null,
                'pic' => null,
            ],
            [
                'kode_project' => 'ENG-2026-027',
                'nama_project' => 'Desain Masterplan Kawasan Industri Demak',
                'nilai_project' => 50000000,
                'client' => 'PT Sidorejo',
                'partner' => 'PT PURIFIKASI',
                'pengelola' => 'Semarang',
                'progress_penawaran' => 'SPK',
                'progress_pra_kontrak' => 1.0,
                'hasil_penawaran' => 'Menang',
                'status_pekerjaan' => 'Laporan',
                'progress_pekerjaan' => 1.0,
                'pic' => 'Riyan',
            ],
            [
                'kode_project' => 'ENG-2026-028',
                'nama_project' => 'Analisis Kinerja Sistem Perpipaan Dan Valves Pengaliran Minyak Dan Penyusunan Solusi Permasalahannya Di Kilang Pertamina UP Balikpapan Dengan Teknologi Computational Fluid Dynamics (CFD)',
                'nilai_project' => 198000000,
                'client' => 'PT Pertamina UP Balikpapan',
                'partner' => 'LPPM UNDIP',
                'pengelola' => 'Semarang',
                'progress_penawaran' => 'Penawaran Awal',
                'progress_pra_kontrak' => 0.2,
                'hasil_penawaran' => 'Tidak Lanjut',
                'status_pekerjaan' => null,
                'progress_pekerjaan' => null,
                'pic' => null,
            ],
            [
                'kode_project' => 'ENG-2026-029',
                'nama_project' => 'Assesment Lab',
                'nilai_project' => 68000000,
                'client' => 'PT Pertamina',
                'partner' => 'LPPM UNDIP',
                'pengelola' => 'Semarang',
                'progress_penawaran' => 'SPK',
                'progress_pra_kontrak' => 1.0,
                'hasil_penawaran' => 'Menang',
                'status_pekerjaan' => 'Kick Off Meeting',
                'progress_pekerjaan' => 0.2,
                'pic' => null,
            ],
        ];

        foreach ($projects as $proj) {
            PertaminaProject::updateOrCreate(
                ['kode_project' => $proj['kode_project']],
                $proj
            );
        }

        // 2. Referensi Pra-Kontrak: Tahap Pra-Kontrak
        $tahapPraKontrak = [
            ['tahap' => 'Project Gagal', 'bobot' => 0.0, 'urutan' => 1],
            ['tahap' => 'Penawaran Awal', 'bobot' => 0.2, 'urutan' => 2],
            ['tahap' => 'Negosiasi', 'bobot' => 0.4, 'urutan' => 3],
            ['tahap' => 'Penawaran Setelah Negosiasi', 'bobot' => 0.6, 'urutan' => 4],
            ['tahap' => 'SP3MK', 'bobot' => 0.8, 'urutan' => 5],
            ['tahap' => 'SPK', 'bobot' => 1.0, 'urutan' => 6],
        ];

        foreach ($tahapPraKontrak as $tpk) {
            PertaminaRefTahapPraKontrak::updateOrCreate(
                ['tahap' => $tpk['tahap']],
                $tpk
            );
        }

        // 3. Referensi Pra-Kontrak: Hasil Penawaran
        $hasilPenawaran = ['Dalam Proses', 'Menang', 'Kalah', 'Batal', 'Tidak Lanjut'];
        foreach ($hasilPenawaran as $hp) {
            PertaminaRefHasilPenawaran::firstOrCreate(['nama' => $hp]);
        }

        // 4. Referensi Pra-Kontrak: Status Proses
        $statusProses = ['Menunggu Permintaan', 'Penyusunan Penawaran', 'Menunggu Klien', 'Proses Kontrak', 'Selesai', 'Tidak Lanjut'];
        foreach ($statusProses as $sp) {
            PertaminaRefStatusProses::firstOrCreate(['nama' => $sp]);
        }

        // 5. Referensi Pelaksanaan: Tahap Pelaksanaan
        $tahapPelaksanaan = [
            ['tahap' => 'Kick Off Meeting', 'bobot' => 0.2, 'urutan' => 1],
            ['tahap' => 'Pengurusan Izin Kerja dan Survey Awal', 'bobot' => 0.4, 'urutan' => 2],
            ['tahap' => 'Pekerjaan Lapangan', 'bobot' => 0.6, 'urutan' => 3],
            ['tahap' => 'Pengolahan Data', 'bobot' => 0.8, 'urutan' => 4],
            ['tahap' => 'Laporan', 'bobot' => 1.0, 'urutan' => 5],
        ];
        foreach ($tahapPelaksanaan as $tp) {
            PertaminaRefTahapPelaksanaan::updateOrCreate(
                ['tahap' => $tp['tahap']],
                $tp
            );
        }

        // 6. Referensi Pelaksanaan: Master PIC
        $picList = ['Dika', 'Dhana', 'Januar', 'Janu', 'Faiz', 'Riyan', 'Siddiq', 'Deviyana', 'Deviayana', 'Nayaka'];
        foreach ($picList as $pic) {
            PertaminaRefPic::firstOrCreate(['nama' => $pic]);
        }

        // 7. Petunjuk Pra-Kontrak: Panduan Penggunaan
        $panduan = [
            ['bagian_warna' => 'Hijau', 'fungsi' => 'Data terhubung ke sheet Project 2026', 'cara_penggunaan' => 'Perubahan nama project, nilai, client, partner, pengelola, status, dan progress pada sheet asal akan terbaca di matriks.'],
            ['bagian_warna' => 'Biru', 'fungsi' => 'Data input pengguna', 'cara_penggunaan' => 'Isi tanggal, tahap, hasil penawaran, tindak lanjut, target, serta catatan/kendala.'],
            ['bagian_warna' => 'Abu-abu', 'fungsi' => 'Formula otomatis', 'cara_penggunaan' => 'Nomor, sisa hari, progress, status proses, durasi, dan indikator dihitung otomatis.'],
            ['bagian_warna' => 'Seluruh project', 'fungsi' => '20+ project terhubung ke sheet Project 2026', 'cara_penggunaan' => 'Semua project ditampilkan. Project berlangsung/KOM-survei awal ditandai kontrak terbit; project berstatus Close dicatat Tidak Lanjut dan perlu konfirmasi.'],
            ['bagian_warna' => 'Deadline', 'fungsi' => 'Peringatan pengiriman penawaran', 'cara_penggunaan' => 'Isi Deadline Penawaran. Sel akan berwarna oranye ketika tersisa 0–3 hari dan merah jika terlambat.'],
            ['bagian_warna' => 'Kontrak terbit', 'fungsi' => 'Tahap akhir monitoring', 'cara_penggunaan' => 'Untuk project yang sudah berjalan, nilai kontrak sementara mengikuti Nilai Project. Lengkapi tanggal dan nomor kontrak serta sesuaikan nilainya bila berbeda.'],
        ];
        foreach ($panduan as $p) {
            PertaminaGuidePanduan::updateOrCreate(
                ['bagian_warna' => $p['bagian_warna'], 'fungsi' => $p['fungsi']],
                $p
            );
        }

        // 8. Petunjuk Pra-Kontrak: Standar Bobot Tahap Proses
        $tahapProsesDetail = [
            ['tahap' => 'Prospek / Menunggu Permintaan Resmi', 'bobot' => 0.02],
            ['tahap' => 'Undangan / TOR Diterima', 'bobot' => 0.05],
            ['tahap' => 'Telaah TOR / RKS', 'bobot' => 0.10],
            ['tahap' => 'Penyusunan Penawaran Teknis', 'bobot' => 0.25],
            ['tahap' => 'Penyusunan Penawaran Harga', 'bobot' => 0.40],
            ['tahap' => 'Review Internal', 'bobot' => 0.50],
            ['tahap' => 'Persetujuan & TTD Penawaran', 'bobot' => 0.60],
            ['tahap' => 'Penawaran Terkirim', 'bobot' => 0.65],
            ['tahap' => 'Klarifikasi', 'bobot' => 0.72],
            ['tahap' => 'Negosiasi', 'bobot' => 0.78],
            ['tahap' => 'Menunggu Penetapan', 'bobot' => 0.82],
            ['tahap' => 'Ditetapkan Menang', 'bobot' => 0.86],
            ['tahap' => 'Draft Kontrak / SPK / PO', 'bobot' => 0.90],
            ['tahap' => 'Review Kontrak', 'bobot' => 0.94],
            ['tahap' => 'Tanda Tangan Kontrak', 'bobot' => 0.97],
            ['tahap' => 'Kontrak / SPK / PO Terbit', 'bobot' => 1.00],
        ];
        foreach ($tahapProsesDetail as $tpd) {
            PertaminaGuideBobotProses::updateOrCreate(
                ['tahap' => $tpd['tahap']],
                $tpd
            );
        }
    }
}
