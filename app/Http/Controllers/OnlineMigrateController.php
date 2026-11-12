<?php

namespace App\Http\Controllers;

use App\Models\PelaksanaanProject;
use App\Models\PertaminaProject;
use Database\Seeders\PelaksanaanProjectSeeder;
use Database\Seeders\PertaminaProjectSeeder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class OnlineMigrateController extends Controller
{
    /**
     * Web runner untuk migrasi, seed, dan sinkronisasi database langsung lewat browser.
     */
    public function __invoke(Request $request): Response
    {
        $logs = [];
        $hasError = false;

        // 1. Eksekusi Git Pull jika exec/shell_exec tersedia
        $gitOutput = 'Fitur exec/shell_exec tidak aktif atau dinonaktifkan hosting. Silakan lakukan Git Pull manual melalui cPanel Git Version Control.';
        $gitStatus = 'info';
        $baseDir = escapeshellarg(base_path());
        $gitCandidates = ['git', '/usr/bin/git', '/usr/local/cpanel/3rdparty/bin/git', '/opt/cpanel/ea-git/usr/bin/git'];
        $pulled = false;

        foreach ($gitCandidates as $gitBin) {
            if (function_exists('shell_exec')) {
                $out = @shell_exec("{$gitBin} -C {$baseDir} pull origin master 2>&1");
                if ($out !== null && trim($out) !== '') {
                    $gitOutput = trim($out);
                    $gitStatus = (str_contains(strtolower($out), 'error') || str_contains(strtolower($out), 'fatal')) ? 'warning' : 'success';
                    $pulled = true;
                    break;
                }
            }

            if (!$pulled && function_exists('exec')) {
                $rawGit = [];
                $exitCode = 0;
                @exec("{$gitBin} -C {$baseDir} pull origin master 2>&1", $rawGit, $exitCode);
                if (!empty($rawGit)) {
                    $gitOutput = implode("\n", $rawGit);
                    $gitStatus = ($exitCode === 0) ? 'success' : 'warning';
                    $pulled = true;
                    break;
                }
            }
        }
        $logs[] = [
            'step' => 'Git Pull (Sinkronisasi Kode Repository)',
            'status' => $gitStatus,
            'detail' => $gitOutput,
        ];

        // 2. Eksekusi Artisan Migrate
        $migrateOutput = '';
        $migrateStatus = 'success';
        try {
            Artisan::call('migrate', ['--force' => true]);
            $migrateOutput = trim(Artisan::output()) ?: 'Database sudah up-to-date (Nothing to migrate).';
        } catch (\Throwable $e) {
            $migrateStatus = 'warning';
            $migrateOutput = 'Artisan Migrate: ' . $e->getMessage();
            Log::warning('OnlineMigrate Artisan migrate warning: ' . $e->getMessage());
        }
        $logs[] = [
            'step' => 'Artisan Migrate (Skema Database)',
            'status' => $migrateStatus,
            'detail' => $migrateOutput,
        ];

        // 3. Verifikasi & Native Fallback Tabel Pertamina
        $pertaminaDirectStatus = 'info';
        $pertaminaDirectMsg = 'Tabel pertamina sudah ada di database.';
        try {
            if (!Schema::hasTable('pertamina_projects')) {
                PertaminaProjectController::createPertaminaTablesDirectly();
                $pertaminaDirectStatus = 'success';
                $pertaminaDirectMsg = 'Semua 8 tabel Pertamina berhasil dibuat langsung via Schema Builder!';
            }
        } catch (\Throwable $e) {
            $pertaminaDirectStatus = 'danger';
            $pertaminaDirectMsg = 'Gagal membuat tabel Pertamina: ' . $e->getMessage();
            $hasError = true;
            Log::error('OnlineMigrate Pertamina direct table creation failed: ' . $e->getMessage());
        }
        $logs[] = [
            'step' => 'Verifikasi Tabel Proyek Pertamina',
            'status' => $pertaminaDirectStatus,
            'detail' => $pertaminaDirectMsg,
        ];

        // 4. Verifikasi & Native Fallback Tabel Rekap Pelaksanaan 2026
        $pelaksanaanDirectStatus = 'info';
        $pelaksanaanDirectMsg = 'Tabel pelaksanaan sudah ada di database.';
        try {
            if (!Schema::hasTable('pelaksanaan_projects')) {
                PelaksanaanProjectController::createPelaksanaanTablesDirectly();
                $pelaksanaanDirectStatus = 'success';
                $pelaksanaanDirectMsg = 'Semua 5 tabel Rekap Pelaksanaan 2026 berhasil dibuat langsung via Schema Builder!';
            }
        } catch (\Throwable $e) {
            $pelaksanaanDirectStatus = 'danger';
            $pelaksanaanDirectMsg = 'Gagal membuat tabel Rekap Pelaksanaan: ' . $e->getMessage();
            $hasError = true;
            Log::error('OnlineMigrate Pelaksanaan direct table creation failed: ' . $e->getMessage());
        }
        $logs[] = [
            'step' => 'Verifikasi Tabel Rekap Pelaksanaan 2026',
            'status' => $pelaksanaanDirectStatus,
            'detail' => $pelaksanaanDirectMsg,
        ];

        // 5. Seeder Pertamina Project & Referensi
        $seedPertaminaStatus = 'info';
        $seedPertaminaMsg = '';
        try {
            if (Schema::hasTable('pertamina_projects')) {
                $projectCount = PertaminaProject::count();
                if ($projectCount === 0) {
                    app(PertaminaProjectSeeder::class)->run();
                    $afterCount = PertaminaProject::count();
                    $seedPertaminaStatus = 'success';
                    $seedPertaminaMsg = "Seeder Pertamina berhasil dieksekusi. Berhasil memasukkan {$afterCount} data proyek & master data referensi.";
                } else {
                    $seedPertaminaStatus = 'info';
                    $seedPertaminaMsg = "Tabel pertamina_projects sudah terisi {$projectCount} data proyek. Seeder dilewati.";
                }
            } else {
                $seedPertaminaStatus = 'danger';
                $seedPertaminaMsg = 'Tabel pertamina_projects tidak ditemukan, seeding dilewati.';
            }
        } catch (\Throwable $e) {
            $seedPertaminaStatus = 'danger';
            $seedPertaminaMsg = 'Seeder Pertamina: ' . $e->getMessage();
            $hasError = true;
            Log::error('OnlineMigrate Pertamina seeder failed: ' . $e->getMessage());
        }
        $logs[] = [
            'step' => 'Seeding Data Awal Pertamina',
            'status' => $seedPertaminaStatus,
            'detail' => $seedPertaminaMsg,
        ];

        // 6. Seeder Rekap Pelaksanaan 2026 & Referensi
        $seedPelaksanaanStatus = 'info';
        $seedPelaksanaanMsg = '';
        try {
            if (Schema::hasTable('pelaksanaan_projects')) {
                $projectCount = PelaksanaanProject::count();
                if ($projectCount === 0) {
                    app(PelaksanaanProjectSeeder::class)->run();
                    $afterCount = PelaksanaanProject::count();
                    $seedPelaksanaanStatus = 'success';
                    $seedPelaksanaanMsg = "Seeder Rekap Pelaksanaan berhasil dieksekusi. Berhasil memasukkan {$afterCount} proyek pelaksanaan & master data.";
                } else {
                    $seedPelaksanaanStatus = 'info';
                    $seedPelaksanaanMsg = "Tabel pelaksanaan_projects sudah terisi {$projectCount} data proyek. Seeder dilewati.";
                }
            } else {
                $seedPelaksanaanStatus = 'danger';
                $seedPelaksanaanMsg = 'Tabel pelaksanaan_projects tidak ditemukan, seeding dilewati.';
            }
        } catch (\Throwable $e) {
            $seedPelaksanaanStatus = 'danger';
            $seedPelaksanaanMsg = 'Seeder Pelaksanaan: ' . $e->getMessage();
            $hasError = true;
            Log::error('OnlineMigrate Pelaksanaan seeder failed: ' . $e->getMessage());
        }
        $logs[] = [
            'step' => 'Seeding Data Awal Rekap Pelaksanaan 2026',
            'status' => $seedPelaksanaanStatus,
            'detail' => $seedPelaksanaanMsg,
        ];

        // 7. Bersihkan Cache (Route, View, Config)
        $cacheStatus = 'success';
        $cacheMsg = '';
        try {
            Artisan::call('optimize:clear');
            $cacheMsg = trim(Artisan::output()) ?: 'Cache route, config, dan views berhasil dibersihkan.';
        } catch (\Throwable $e) {
            $cacheStatus = 'info';
            $cacheMsg = 'Info pembersihan cache: ' . $e->getMessage();
        }
        $logs[] = [
            'step' => 'Optimize & Cache Clear',
            'status' => $cacheStatus,
            'detail' => $cacheMsg,
        ];

        // Status daftar tabel terkait Manajemen Proyek di DB
        $allTables = [
            // Proyek Pertamina
            'pertamina_projects' => 'Pertamina: Matriks Pra-Kontrak',
            'pertamina_ref_tahap_pra_kontrak' => 'Pertamina: Ref Tahap Pra-Kontrak',
            'pertamina_ref_hasil_penawaran' => 'Pertamina: Ref Hasil Penawaran',
            'pertamina_ref_status_proses' => 'Pertamina: Ref Status Proses',
            'pertamina_ref_tahap_pelaksanaan' => 'Pertamina: Ref Tahap Pelaksanaan',
            'pertamina_ref_pic' => 'Pertamina: Ref Master PIC',
            'pertamina_guide_panduan' => 'Pertamina: Petunjuk Panduan',
            'pertamina_guide_bobot_proses' => 'Pertamina: Petunjuk Standar Bobot',
            // Rekap Pelaksanaan 2026
            'pelaksanaan_projects' => 'Pelaksanaan: Matriks Pelaksanaan',
            'pelaksanaan_ref_tahap' => 'Pelaksanaan: Ref 7 Tahap Pekerjaan',
            'pelaksanaan_ref_pic' => 'Pelaksanaan: Ref Master PIC',
            'pelaksanaan_guide_panduan' => 'Pelaksanaan: Panduan Penggunaan',
            'pelaksanaan_guide_bobot_proses' => 'Pelaksanaan: Standar Bobot Proses',
        ];

        $tableChecks = [];
        foreach ($allTables as $table => $label) {
            $exists = Schema::hasTable($table);
            $count = $exists ? DB::table($table)->count() : 0;
            $tableChecks[] = [
                'table' => $table,
                'label' => $label,
                'exists' => $exists,
                'count' => $count,
            ];
        }

        $html = view('system.migrate-online', [
            'logs' => $logs,
            'hasError' => $hasError,
            'tableChecks' => $tableChecks,
            'runTime' => now()->translatedFormat('d F Y, H:i:s'),
        ])->render();

        return new Response($html, 200, ['Content-Type' => 'text/html']);
    }
}
