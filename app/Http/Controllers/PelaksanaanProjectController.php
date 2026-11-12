<?php

namespace App\Http\Controllers;

use App\Models\PelaksanaanGuideBobotProses;
use App\Models\PelaksanaanGuidePanduan;
use App\Models\PelaksanaanProject;
use App\Models\PelaksanaanRefPic;
use App\Models\PelaksanaanRefTahap;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class PelaksanaanProjectController extends Controller
{
    /**
     * Sub Bab 3 - A: Matriks Pelaksanaan
     */
    public function matriks(Request $request): View|RedirectResponse
    {
        $this->ensureTablesSeeded();

        if (! Schema::hasTable('pelaksanaan_projects')) {
            return redirect()->route('system.migrate-online');
        }

        $query = PelaksanaanProject::query();

        if ($request->filled('q')) {
            $query->search($request->string('q')->toString());
        }

        if ($request->filled('pengelola')) {
            $query->where('pengelola', $request->string('pengelola')->toString());
        }

        if ($request->filled('status_pekerjaan')) {
            $query->where('status_pekerjaan', $request->string('status_pekerjaan')->toString());
        }

        if ($request->filled('pic')) {
            $query->where('pic', $request->string('pic')->toString());
        }

        // Header Summary Info
        $allProjects = PelaksanaanProject::all();
        $totalProjects = $allProjects->count();
        $lastUpdate = $allProjects->max('updated_at') ?: now();

        $projects = $query->orderBy('id')->paginate(15)->withQueryString();

        // Master Data untuk Dropdown Modal Form
        $refTahap = PelaksanaanRefTahap::orderBy('urutan')->get();
        $refPic = PelaksanaanRefPic::orderBy('nama')->pluck('nama');
        $pengelolaOptions = ['Semarang', 'Bandung'];
        $progressOptions = [
            ['value' => 0.00, 'label' => '0% (0.0)'],
            ['value' => 0.20, 'label' => '20% (0.2)'],
            ['value' => 0.40, 'label' => '40% (0.4)'],
            ['value' => 0.60, 'label' => '60% (0.6)'],
            ['value' => 0.75, 'label' => '75% (0.75)'],
            ['value' => 0.90, 'label' => '90% (0.9)'],
            ['value' => 1.00, 'label' => '100% (1.0)'],
        ];

        return view('pelaksanaan.matriks', [
            'projects' => $projects,
            'totalProjects' => $totalProjects,
            'lastUpdate' => $lastUpdate,
            'refTahap' => $refTahap,
            'refPic' => $refPic,
            'pengelolaOptions' => $pengelolaOptions,
            'progressOptions' => $progressOptions,
            'filters' => [
                'q' => $request->string('q')->toString(),
                'pengelola' => $request->string('pengelola')->toString(),
                'status_pekerjaan' => $request->string('status_pekerjaan')->toString(),
                'pic' => $request->string('pic')->toString(),
            ],
        ]);
    }

    /**
     * Sub Bab 3 - B: Rekap Pelaksanaan (Monitoring & Matriks Tracking Tahapan Proyek)
     */
    public function rekap(): View|RedirectResponse
    {
        $this->ensureTablesSeeded();

        if (! Schema::hasTable('pelaksanaan_projects')) {
            return redirect()->route('system.migrate-online');
        }

        $allProjects = PelaksanaanProject::orderBy('id')->get();
        $totalProjects = $allProjects->count();
        $tahapList = PelaksanaanRefTahap::orderBy('urutan')->get();

        // Normalisasi nama tahap agar pencocokan "Paparan Akhir" dan "Paparan Final" presisi
        $normalizeStage = function (?string $stage): string {
            $trimmed = trim(strtolower((string) $stage));
            if (in_array($trimmed, ['paparan akhir', 'paparan final'])) {
                return 'paparan final / paparan akhir';
            }
            return $trimmed;
        };

        // 1. Ringkasan 7 Tahapan (Tabel Atas)
        $ringkasanTahap = $tahapList->map(function ($item) use ($allProjects, $normalizeStage) {
            $normItem = $normalizeStage($item->tahap);
            $count = $allProjects->filter(function ($p) use ($normItem, $normalizeStage) {
                return $normalizeStage($p->status_pekerjaan) === $normItem;
            })->count();

            return [
                'no' => $item->urutan,
                'tahap' => $item->tahap,
                'jumlah_proyek' => $count,
                'keterangan' => $item->keterangan,
            ];
        });

        // 2. Tracking Matriks Tahapan Proyek (Tabel Bawah)
        $trackingProjects = $allProjects->map(function ($project) use ($tahapList, $normalizeStage) {
            $currentNorm = $normalizeStage($project->status_pekerjaan);

            // Cari index tahap saat ini (1 sampai 7)
            $currentIndex = 0;
            foreach ($tahapList as $idx => $t) {
                if ($normalizeStage($t->tahap) === $currentNorm) {
                    $currentIndex = $t->urutan;
                    break;
                }
            }

            // Jika tidak cocok persis, fallback ke bobot progress
            if ($currentIndex === 0) {
                if ($project->progress_pekerjaan >= 1.0) $currentIndex = 7;
                elseif ($project->progress_pekerjaan >= 0.9) $currentIndex = 6;
                elseif ($project->progress_pekerjaan >= 0.75) $currentIndex = 5;
                elseif ($project->progress_pekerjaan >= 0.6) $currentIndex = 4;
                elseif ($project->progress_pekerjaan >= 0.4) $currentIndex = 3;
                elseif ($project->progress_pekerjaan >= 0.2) $currentIndex = 2;
                else $currentIndex = 1;
            }

            $stageChecklist = [];
            foreach ($tahapList as $t) {
                $order = $t->urutan;
                if ($order < $currentIndex) {
                    $status = 'completed'; // Centang ✓
                } elseif ($order === $currentIndex) {
                    $status = 'active'; // Bulatan ●
                } else {
                    $status = 'future'; // Kosong
                }
                $stageChecklist[$order] = $status;
            }

            return [
                'model' => $project,
                'current_stage_order' => "{$currentIndex}/7",
                'checklist' => $stageChecklist,
            ];
        });

        return view('pelaksanaan.rekap', [
            'ringkasanTahap' => $ringkasanTahap,
            'totalProjects' => $totalProjects,
            'tahapList' => $tahapList,
            'trackingProjects' => $trackingProjects,
        ]);
    }

    /**
     * Sub Bab 3 - C: Referensi Pelaksanaan
     */
    public function referensi(): View|RedirectResponse
    {
        $this->ensureTablesSeeded();

        if (! Schema::hasTable('pelaksanaan_ref_tahap')) {
            return redirect()->route('system.migrate-online');
        }

        return view('pelaksanaan.referensi', [
            'tahapList' => PelaksanaanRefTahap::orderBy('urutan')->get(),
            'picList' => PelaksanaanRefPic::orderBy('nama')->get(),
        ]);
    }

    /**
     * Sub Bab 3 - D: Petunjuk Pra-Kontrak / Pelaksanaan
     */
    public function petunjuk(): View|RedirectResponse
    {
        $this->ensureTablesSeeded();

        if (! Schema::hasTable('pelaksanaan_guide_panduan')) {
            return redirect()->route('system.migrate-online');
        }

        return view('pelaksanaan.petunjuk', [
            'panduanList' => PelaksanaanGuidePanduan::orderBy('id')->get(),
            'bobotList' => PelaksanaanGuideBobotProses::orderBy('bobot')->get(),
        ]);
    }

    /**
     * Simpan Proyek Baru (Matriks Pelaksanaan)
     */
    public function storeProject(Request $request): RedirectResponse
    {
        $this->ensureTablesSeeded();

        $validated = $request->validate([
            'kode_project' => ['nullable', 'string', 'max:64'],
            'nama_project' => ['required', 'string'],
            'pengelola' => ['required', 'string', 'in:Semarang,Bandung'],
            'status_pekerjaan' => ['required', 'string', 'max:128'],
            'progress_pekerjaan' => ['required', 'numeric', 'min:0', 'max:1'],
            'pic' => ['nullable', 'string', 'max:128'],
            'kontrak_awal' => ['nullable', 'date'],
            'kontrak_akhir' => ['nullable', 'date'],
            'remaining_days' => ['nullable', 'integer'],
        ]);

        // LOGIKA AUTO-SAVE PIC BARU KE REFERENSI PIC:
        if (!empty($validated['pic'])) {
            $picTrimmed = trim($validated['pic']);
            PelaksanaanRefPic::firstOrCreate(['nama' => $picTrimmed]);
        }

        // Hitung remaining days otomatis jika ada kontrak_akhir
        if (!empty($validated['kontrak_akhir'])) {
            $validated['remaining_days'] = (int) Carbon::today()->diffInDays(Carbon::parse($validated['kontrak_akhir']), false);
        }

        PelaksanaanProject::create($validated + ['user_id' => $request->user()?->id]);

        return redirect()->route('pelaksanaan.matriks')
            ->with('status', 'Proyek baru berhasil ditambahkan ke Matriks Pelaksanaan.');
    }

    /**
     * Update Proyek (Matriks Pelaksanaan)
     */
    public function updateProject(Request $request, PelaksanaanProject $project): RedirectResponse
    {
        $this->ensureTablesSeeded();

        $validated = $request->validate([
            'kode_project' => ['nullable', 'string', 'max:64'],
            'nama_project' => ['required', 'string'],
            'pengelola' => ['required', 'string', 'in:Semarang,Bandung'],
            'status_pekerjaan' => ['required', 'string', 'max:128'],
            'progress_pekerjaan' => ['required', 'numeric', 'min:0', 'max:1'],
            'pic' => ['nullable', 'string', 'max:128'],
            'kontrak_awal' => ['nullable', 'date'],
            'kontrak_akhir' => ['nullable', 'date'],
            'remaining_days' => ['nullable', 'integer'],
        ]);

        // Auto-save PIC jika baru
        if (!empty($validated['pic'])) {
            $picTrimmed = trim($validated['pic']);
            PelaksanaanRefPic::firstOrCreate(['nama' => $picTrimmed]);
        }

        if (!empty($validated['kontrak_akhir'])) {
            $validated['remaining_days'] = (int) Carbon::today()->diffInDays(Carbon::parse($validated['kontrak_akhir']), false);
        }

        $project->update($validated);

        return redirect()->route('pelaksanaan.matriks')
            ->with('status', "Data proyek #{$project->id} berhasil diperbarui.");
    }

    /**
     * Hapus Proyek (Matriks Pelaksanaan)
     */
    public function destroyProject(PelaksanaanProject $project): RedirectResponse
    {
        $this->ensureTablesSeeded();

        $project->delete();

        return redirect()->route('pelaksanaan.matriks')
            ->with('status', 'Data proyek berhasil dihapus.');
    }

    /**
     * Store Referensi
     */
    public function storeReferensi(Request $request, string $type): RedirectResponse
    {
        $this->ensureTablesSeeded();

        switch ($type) {
            case 'tahap':
                $validated = $request->validate([
                    'tahap' => ['required', 'string', 'max:128'],
                    'bobot' => ['required', 'numeric', 'min:0', 'max:1'],
                    'urutan' => ['nullable', 'integer'],
                    'keterangan' => ['nullable', 'string'],
                ]);
                PelaksanaanRefTahap::create($validated);
                $targetRoute = 'pelaksanaan.referensi';
                break;

            case 'pic':
                $validated = $request->validate([
                    'nama' => ['required', 'string', 'max:128', 'unique:pelaksanaan_ref_pic,nama'],
                ]);
                PelaksanaanRefPic::create($validated);
                $targetRoute = 'pelaksanaan.referensi';
                break;

            case 'panduan':
                $validated = $request->validate([
                    'bagian_warna' => ['required', 'string', 'max:128'],
                    'fungsi' => ['required', 'string', 'max:255'],
                    'cara_penggunaan' => ['required', 'string'],
                ]);
                PelaksanaanGuidePanduan::create($validated);
                $targetRoute = 'pelaksanaan.petunjuk';
                break;

            case 'bobot-proses':
                $validated = $request->validate([
                    'tahap' => ['required', 'string', 'max:128'],
                    'bobot' => ['required', 'numeric', 'min:0', 'max:1'],
                ]);
                PelaksanaanGuideBobotProses::create($validated);
                $targetRoute = 'pelaksanaan.petunjuk';
                break;

            default:
                abort(404, 'Tipe referensi tidak valid.');
        }

        return redirect()->route($targetRoute)->with('status', 'Item referensi berhasil ditambahkan.');
    }

    /**
     * Update Referensi
     */
    public function updateReferensi(Request $request, string $type, int $id): RedirectResponse
    {
        $this->ensureTablesSeeded();

        switch ($type) {
            case 'tahap':
                $item = PelaksanaanRefTahap::findOrFail($id);
                $validated = $request->validate([
                    'tahap' => ['required', 'string', 'max:128'],
                    'bobot' => ['required', 'numeric', 'min:0', 'max:1'],
                    'urutan' => ['nullable', 'integer'],
                    'keterangan' => ['nullable', 'string'],
                ]);
                $item->update($validated);
                $targetRoute = 'pelaksanaan.referensi';
                break;

            case 'pic':
                $item = PelaksanaanRefPic::findOrFail($id);
                $validated = $request->validate([
                    'nama' => ['required', 'string', 'max:128', 'unique:pelaksanaan_ref_pic,nama,' . $id],
                ]);
                $item->update($validated);
                $targetRoute = 'pelaksanaan.referensi';
                break;

            case 'panduan':
                $item = PelaksanaanGuidePanduan::findOrFail($id);
                $validated = $request->validate([
                    'bagian_warna' => ['required', 'string', 'max:128'],
                    'fungsi' => ['required', 'string', 'max:255'],
                    'cara_penggunaan' => ['required', 'string'],
                ]);
                $item->update($validated);
                $targetRoute = 'pelaksanaan.petunjuk';
                break;

            case 'bobot-proses':
                $item = PelaksanaanGuideBobotProses::findOrFail($id);
                $validated = $request->validate([
                    'tahap' => ['required', 'string', 'max:128'],
                    'bobot' => ['required', 'numeric', 'min:0', 'max:1'],
                ]);
                $item->update($validated);
                $targetRoute = 'pelaksanaan.petunjuk';
                break;

            default:
                abort(404, 'Tipe referensi tidak valid.');
        }

        return redirect()->route($targetRoute)->with('status', 'Item referensi berhasil diperbarui.');
    }

    /**
     * Destroy Referensi
     */
    public function destroyReferensi(string $type, int $id): RedirectResponse
    {
        $this->ensureTablesSeeded();

        switch ($type) {
            case 'tahap':
                PelaksanaanRefTahap::findOrFail($id)->delete();
                $targetRoute = 'pelaksanaan.referensi';
                break;

            case 'pic':
                PelaksanaanRefPic::findOrFail($id)->delete();
                $targetRoute = 'pelaksanaan.referensi';
                break;

            case 'panduan':
                PelaksanaanGuidePanduan::findOrFail($id)->delete();
                $targetRoute = 'pelaksanaan.petunjuk';
                break;

            case 'bobot-proses':
                PelaksanaanGuideBobotProses::findOrFail($id)->delete();
                $targetRoute = 'pelaksanaan.petunjuk';
                break;

            default:
                abort(404, 'Tipe referensi tidak valid.');
        }

        return redirect()->route($targetRoute)->with('status', 'Item referensi berhasil dihapus.');
    }

    /**
     * Fallback auto-seed jika tabel masih kosong saat dibuka
     */
    private function ensureTablesSeeded(): void
    {
        try {
            if (! Schema::hasTable('pelaksanaan_projects')) {
                Artisan::call('migrate', ['--force' => true]);

                if (! Schema::hasTable('pelaksanaan_projects')) {
                    self::createPelaksanaanTablesDirectly();
                }
            }

            if (Schema::hasTable('pelaksanaan_projects') && PelaksanaanProject::count() === 0) {
                app(\Database\Seeders\PelaksanaanProjectSeeder::class)->run();
            }
        } catch (\Throwable $e) {
            Log::error('Auto-migration / seed Pelaksanaan failed: ' . $e->getMessage());
        }
    }

    /**
     * Buat langsung seluruh tabel Pelaksanaan secara native
     */
    public static function createPelaksanaanTablesDirectly(): void
    {
        if (! Schema::hasTable('pelaksanaan_projects')) {
            Schema::create('pelaksanaan_projects', function (Blueprint $table) {
                $table->id();
                $table->string('kode_project', 64)->nullable();
                $table->text('nama_project');
                $table->string('pengelola', 64)->default('Semarang');
                $table->string('status_pekerjaan', 128);
                $table->decimal('progress_pekerjaan', 4, 2)->default(0);
                $table->string('pic', 128)->nullable();
                $table->date('kontrak_awal')->nullable();
                $table->date('kontrak_akhir')->nullable();
                $table->integer('remaining_days')->nullable();
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
                $table->timestamps();

                $table->index('pengelola');
                $table->index('status_pekerjaan');
                $table->index('pic');
            });
        }

        if (! Schema::hasTable('pelaksanaan_ref_tahap')) {
            Schema::create('pelaksanaan_ref_tahap', function (Blueprint $table) {
                $table->id();
                $table->string('tahap', 128);
                $table->decimal('bobot', 4, 2)->default(0);
                $table->integer('urutan')->default(0);
                $table->text('keterangan')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('pelaksanaan_ref_pic')) {
            Schema::create('pelaksanaan_ref_pic', function (Blueprint $table) {
                $table->id();
                $table->string('nama', 128)->unique();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('pelaksanaan_guide_panduan')) {
            Schema::create('pelaksanaan_guide_panduan', function (Blueprint $table) {
                $table->id();
                $table->string('bagian_warna', 128);
                $table->string('fungsi', 255);
                $table->text('cara_penggunaan');
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('pelaksanaan_guide_bobot_proses')) {
            Schema::create('pelaksanaan_guide_bobot_proses', function (Blueprint $table) {
                $table->id();
                $table->string('tahap', 128);
                $table->decimal('bobot', 4, 2)->default(0);
                $table->timestamps();
            });
        }
    }
}
