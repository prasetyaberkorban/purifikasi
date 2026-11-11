<?php

namespace App\Http\Controllers;

use App\Models\PertaminaProject;
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
        $gitOutput = 'Fitur exec/shell_exec tidak aktif atau dinonaktifkan hosting.';
        $gitStatus = 'info';
        if (function_exists('exec')) {
            $rawGit = [];
            $exitCode = 0;
            @exec('git pull origin master 2>&1', $rawGit, $exitCode);
            if (!empty($rawGit)) {
                $gitOutput = implode("\n", $rawGit);
                $gitStatus = ($exitCode === 0) ? 'success' : 'warning';
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

        // 4. Seeder Pertamina Project & Referensi
        $seedStatus = 'info';
        $seedMsg = '';
        try {
            if (Schema::hasTable('pertamina_projects')) {
                $projectCount = PertaminaProject::count();
                if ($projectCount === 0) {
                    app(PertaminaProjectSeeder::class)->run();
                    $afterCount = PertaminaProject::count();
                    $seedStatus = 'success';
                    $seedMsg = "Seeder berhasil dieksekusi. Berhasil memasukkan {$afterCount} data proyek & master data referensi.";
                } else {
                    $seedStatus = 'info';
                    $seedMsg = "Tabel pertamina_projects sudah terisi {$projectCount} data proyek. Seeder dilewati agar tidak menimpa data.";
                }
            } else {
                $seedStatus = 'danger';
                $seedMsg = 'Tabel pertamina_projects tidak ditemukan, seeding dilewati.';
            }
        } catch (\Throwable $e) {
            $seedStatus = 'danger';
            $seedMsg = 'Seeder Pertamina: ' . $e->getMessage();
            $hasError = true;
            Log::error('OnlineMigrate Pertamina seeder failed: ' . $e->getMessage());
        }
        $logs[] = [
            'step' => 'Seeding Data Awal Pertamina',
            'status' => $seedStatus,
            'detail' => $seedMsg,
        ];

        // 5. Bersihkan Cache (Route, View, Config)
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

        // Status daftar tabel terkait Pertamina di DB
        $pertaminaTables = [
            'pertamina_projects' => 'Matriks Pra-Kontrak',
            'pertamina_ref_tahap_pra_kontrak' => 'Ref: Tahap Pra-Kontrak',
            'pertamina_ref_hasil_penawaran' => 'Ref: Hasil Penawaran',
            'pertamina_ref_status_proses' => 'Ref: Status Proses',
            'pertamina_ref_tahap_pelaksanaan' => 'Ref: Tahap Pelaksanaan',
            'pertamina_ref_pic' => 'Ref: PIC Master',
            'pertamina_guide_panduan' => 'Petunjuk: Panduan',
            'pertamina_guide_bobot_proses' => 'Petunjuk: Standar Bobot',
        ];

        $tableChecks = [];
        foreach ($pertaminaTables as $table => $label) {
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
