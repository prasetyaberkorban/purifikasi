<?php

namespace Database\Seeders;

use App\Models\PelaksanaanGuideBobotProses;
use App\Models\PelaksanaanGuidePanduan;
use App\Models\PelaksanaanProject;
use App\Models\PelaksanaanRefPic;
use App\Models\PelaksanaanRefTahap;
use Illuminate\Database\Seeder;

class PelaksanaanProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Master Tahapan Pelaksanaan (7 Tahap)
        $tahapan = [
            ['urutan' => 1, 'tahap' => 'Kick Off Meeting', 'bobot' => 0.00, 'keterangan' => 'Awal resmi pelaksanaan pekerjaan'],
            ['urutan' => 2, 'tahap' => 'Pengurusan Izin Kerja dan Survey Awal', 'bobot' => 0.20, 'keterangan' => 'Perizinan, koordinasi lokasi, dan verifikasi awal'],
            ['urutan' => 3, 'tahap' => 'Pengambilan Data', 'bobot' => 0.40, 'keterangan' => 'Survei/pengukuran/pengambilan data lapangan'],
            ['urutan' => 4, 'tahap' => 'Pengolahan Data', 'bobot' => 0.60, 'keterangan' => 'Analisis dan pengolahan hasil pengambilan data'],
            ['urutan' => 5, 'tahap' => 'Draft Laporan', 'bobot' => 0.75, 'keterangan' => 'Penyusunan draft laporan untuk review'],
            ['urutan' => 6, 'tahap' => 'Submit Laporan', 'bobot' => 0.90, 'keterangan' => 'Laporan telah disampaikan kepada client'],
            ['urutan' => 7, 'tahap' => 'Paparan Final', 'bobot' => 1.00, 'keterangan' => 'Presentasi/closing hasil pekerjaan'],
        ];

        foreach ($tahapan as $t) {
            PelaksanaanRefTahap::updateOrCreate(
                ['tahap' => $t['tahap']],
                ['bobot' => $t['bobot'], 'urutan' => $t['urutan'], 'keterangan' => $t['keterangan']]
            );
        }

        // 2. Master PIC (10 PIC Awal)
        $picList = [
            'Dika', 'Dhana', 'Januar', 'Janu', 'Faiz', 'Riyan', 'Nayaka', 'Siddiq', 'Deviyana', 'Deviayana'
        ];

        foreach ($picList as $pic) {
            PelaksanaanRefPic::firstOrCreate(['nama' => $pic]);
        }

        // 3. Petunjuk: Panduan Penggunaan
        $panduan = [
            [
                'bagian_warna' => 'Hijau',
                'fungsi' => 'Data terhubung ke sheet Project 2026',
                'cara_penggunaan' => 'Perubahan nama project, nilai, client, partner, pengelola, status, dan progress pada sheet asal akan terbaca di matriks.',
            ],
            [
                'bagian_warna' => 'Biru',
                'fungsi' => 'Data input pengguna',
                'cara_penggunaan' => 'Isi tanggal, tahap, hasil penawaran, tindak lanjut, target, serta catatan/kendala.',
            ],
            [
                'bagian_warna' => 'Abu-abu',
                'fungsi' => 'Formula otomatis',
                'cara_penggunaan' => 'Nomor, sisa hari, progress, status proses, durasi, dan indikator dihitung otomatis.',
            ],
            [
                'bagian_warna' => 'Seluruh project',
                'fungsi' => '20 project terhubung ke sheet Project 2026',
                'cara_penggunaan' => 'Semua project No. 1–20 ditampilkan. Project berlangsung/KOM-survei awal ditandai kontrak terbit; project berstatus Close dicatat Tidak Lanjut dan perlu konfirmasi.',
            ],
            [
                'bagian_warna' => 'Deadline',
                'fungsi' => 'Peringatan pengiriman penawaran',
                'cara_penggunaan' => 'Isi Deadline Penawaran. Sel akan berwarna oranye ketika tersisa 0–3 hari dan merah jika terlambat.',
            ],
            [
                'bagian_warna' => 'Kontrak terbit',
                'fungsi' => 'Tahap akhir monitoring',
                'cara_penggunaan' => 'Untuk project yang sudah berjalan, nilai kontrak sementara mengikuti Nilai Project. Lengkapi tanggal dan nomor kontrak serta sesuaikan nilainya bila berbeda.',
            ],
        ];

        foreach ($panduan as $p) {
            PelaksanaanGuidePanduan::updateOrCreate(
                ['bagian_warna' => $p['bagian_warna']],
                ['fungsi' => $p['fungsi'], 'cara_penggunaan' => $p['cara_penggunaan']]
            );
        }

        // 4. Petunjuk: Standar Bobot Tahap Proses Detail (16 Tahap)
        $bobotProses = [
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

        foreach ($bobotProses as $bp) {
            PelaksanaanGuideBobotProses::updateOrCreate(
                ['tahap' => $bp['tahap']],
                ['bobot' => $bp['bobot']]
            );
        }

        // 5. Matriks Pelaksanaan (17 Proyek Awal)
        $projects = [
            [
                'kode_project' => null,
                'nama_project' => 'Pengendalian Banjir Di Kantor Sumbagsel',
                'pengelola' => 'Bandung',
                'status_pekerjaan' => 'Draft Laporan',
                'progress_pekerjaan' => 0.75,
                'pic' => 'Deviayana',
                'kontrak_awal' => '2026-07-02',
                'kontrak_akhir' => '2026-10-30',
                'remaining_days' => -10,
            ],
            [
                'kode_project' => null,
                'nama_project' => 'Kajian Jetty Analisa Pipe Support RU IV Balongan',
                'pengelola' => 'Bandung',
                'status_pekerjaan' => 'Draft Laporan',
                'progress_pekerjaan' => 0.75,
                'pic' => 'Siddiq',
                'kontrak_awal' => '2026-08-03',
                'kontrak_akhir' => '2026-11-01',
                'remaining_days' => -8,
            ],
            [
                'kode_project' => null,
                'nama_project' => 'Kajian Assessment Struktur Unit Fluid Catalytic Cracking Unit (FCCU) dan Pondasi Pompa Kilang RU-III Plaju Tahun 2026',
                'pengelola' => 'Semarang',
                'status_pekerjaan' => 'Pengambilan Data',
                'progress_pekerjaan' => 0.40,
                'pic' => 'Dika',
                'kontrak_awal' => '2026-07-30',
                'kontrak_akhir' => '2026-12-27',
                'remaining_days' => 48,
            ],
            [
                'kode_project' => null,
                'nama_project' => 'Kajian Assessment Jembatan Pipa dan Jembatan Kendaraan di Area Kilang RU-III Plaju Tahun 2026',
                'pengelola' => 'Semarang',
                'status_pekerjaan' => 'Pengurusan Izin Kerja dan Survey Awal',
                'progress_pekerjaan' => 0.20,
                'pic' => 'Dika',
                'kontrak_awal' => '2026-09-21',
                'kontrak_akhir' => '2026-12-15',
                'remaining_days' => 36,
            ],
            [
                'kode_project' => null,
                'nama_project' => 'Kajian Environmental Sensitivity Index (ESI) PT Pertamina Patra Niaga - Regional Jawa Bagian Tengah',
                'pengelola' => 'Semarang',
                'status_pekerjaan' => 'Draft Laporan',
                'progress_pekerjaan' => 0.75,
                'pic' => 'Nayaka',
                'kontrak_awal' => '2026-08-21',
                'kontrak_akhir' => '2026-12-19',
                'remaining_days' => 40,
            ],
            [
                'kode_project' => null,
                'nama_project' => 'Kajian Environmental Sensitivity Index (ESI) PT Pertamina Patra Niaga - RPD Sulawesi',
                'pengelola' => 'Semarang',
                'status_pekerjaan' => 'Pengambilan Data',
                'progress_pekerjaan' => 0.40,
                'pic' => 'Nayaka',
                'kontrak_awal' => '2026-10-09',
                'kontrak_akhir' => '2027-01-07',
                'remaining_days' => 59,
            ],
            [
                'kode_project' => null,
                'nama_project' => 'Kajian Environmental Sensitivity Index (ESI) PT Pertamina Patra Niaga - Regional Sumbagsel',
                'pengelola' => 'Semarang',
                'status_pekerjaan' => 'Draft Laporan',
                'progress_pekerjaan' => 0.75,
                'pic' => 'Nayaka',
                'kontrak_awal' => '2026-09-11',
                'kontrak_akhir' => '2026-12-10',
                'remaining_days' => 31,
            ],
            [
                'kode_project' => null,
                'nama_project' => 'Kajian Environmental Sensitivity Index (ESI) PT Pertamina Patra Niaga - FT Jambi',
                'pengelola' => 'Semarang',
                'status_pekerjaan' => 'Pengurusan Izin Kerja dan Survey Awal',
                'progress_pekerjaan' => 0.20,
                'pic' => 'Nayaka',
                'kontrak_awal' => '2026-10-16',
                'kontrak_akhir' => '2027-01-14',
                'remaining_days' => 66,
            ],
            [
                'kode_project' => null,
                'nama_project' => 'Kajian Environmental Sensitivity Index (ESI) PT Pertamina Patra Niaga Regional Papua dan Maluku - FT Ternate',
                'pengelola' => 'Semarang',
                'status_pekerjaan' => 'Pengurusan Izin Kerja dan Survey Awal',
                'progress_pekerjaan' => 0.20,
                'pic' => 'Nayaka',
                'kontrak_awal' => '2026-10-26',
                'kontrak_akhir' => '2027-01-24',
                'remaining_days' => 76,
            ],
            [
                'kode_project' => null,
                'nama_project' => 'Kajian Geolistrik dan Geohidrologi Di IT Cilacap',
                'pengelola' => 'Semarang',
                'status_pekerjaan' => 'Paparan Final',
                'progress_pekerjaan' => 1.00,
                'pic' => 'Dhana',
                'kontrak_awal' => '2026-03-25',
                'kontrak_akhir' => '2026-06-23',
                'remaining_days' => -139,
            ],
            [
                'kode_project' => null,
                'nama_project' => 'Kajian Geolistrik dan Geohidrologi Di IT Lomanis',
                'pengelola' => 'Semarang',
                'status_pekerjaan' => 'Draft Laporan',
                'progress_pekerjaan' => 0.75,
                'pic' => 'Dhana',
                'kontrak_awal' => '2026-07-23',
                'kontrak_akhir' => '2026-10-21',
                'remaining_days' => -19,
            ],
            [
                'kode_project' => null,
                'nama_project' => 'Kajian Geolistrik dan Geohidrologi IT Makassar dan IT Bitung',
                'pengelola' => 'Semarang',
                'status_pekerjaan' => 'Pengolahan Data',
                'progress_pekerjaan' => 0.60,
                'pic' => 'Dhana',
                'kontrak_awal' => '2026-08-03',
                'kontrak_akhir' => '2026-10-02',
                'remaining_days' => -38,
            ],
            [
                'kode_project' => null,
                'nama_project' => 'Kajian Geolistrik dan Geohidrologi IT Palembang dan IT Lubuklinggau',
                'pengelola' => 'Semarang',
                'status_pekerjaan' => 'Pengolahan Data',
                'progress_pekerjaan' => 0.60,
                'pic' => 'Dhana',
                'kontrak_awal' => '2026-08-05',
                'kontrak_akhir' => '2026-10-04',
                'remaining_days' => -36,
            ],
            [
                'kode_project' => null,
                'nama_project' => 'Kajian Geolistrik dan Geohidrologi IT Sanggaran dan IT Palaran',
                'pengelola' => 'Semarang',
                'status_pekerjaan' => 'Pengambilan Data',
                'progress_pekerjaan' => 0.40,
                'pic' => 'Dhana',
                'kontrak_awal' => '2026-10-07',
                'kontrak_akhir' => '2026-12-31',
                'remaining_days' => 52,
            ],
            [
                'kode_project' => null,
                'nama_project' => 'Kajian Geolistrik dan Geohidrologi IT Biak',
                'pengelola' => 'Semarang',
                'status_pekerjaan' => 'Pengurusan Izin Kerja dan Survey Awal',
                'progress_pekerjaan' => 0.20,
                'pic' => 'Dhana',
                'kontrak_awal' => '2026-10-19',
                'kontrak_akhir' => '2027-02-16',
                'remaining_days' => 99,
            ],
            [
                'kode_project' => null,
                'nama_project' => 'Audit Energi RU II Dumai dan Sei Pakning',
                'pengelola' => 'Semarang',
                'status_pekerjaan' => 'Submit Laporan',
                'progress_pekerjaan' => 0.90,
                'pic' => 'Janu',
                'kontrak_awal' => '2026-08-18',
                'kontrak_akhir' => '2026-10-17',
                'remaining_days' => -23,
            ],
            [
                'kode_project' => null,
                'nama_project' => 'Audit Energi dan Air Perusahaan Gas Negara SOR I Stasiun Gas Panaran dan Semare',
                'pengelola' => 'Semarang',
                'status_pekerjaan' => 'Submit Laporan',
                'progress_pekerjaan' => 0.90,
                'pic' => 'Janu',
                'kontrak_awal' => '2026-06-26',
                'kontrak_akhir' => '2026-08-25',
                'remaining_days' => -76,
            ],
        ];

        foreach ($projects as $item) {
            PelaksanaanProject::firstOrCreate(
                ['nama_project' => $item['nama_project']],
                $item
            );
        }
    }
}
