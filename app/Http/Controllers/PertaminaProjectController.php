<?php

namespace App\Http\Controllers;

use App\Models\PertaminaGuideBobotProses;
use App\Models\PertaminaGuidePanduan;
use App\Models\PertaminaProject;
use App\Models\PertaminaRefHasilPenawaran;
use App\Models\PertaminaRefPic;
use App\Models\PertaminaRefStatusProses;
use App\Models\PertaminaRefTahapPelaksanaan;
use App\Models\PertaminaRefTahapPraKontrak;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PertaminaProjectController extends Controller
{
    /**
     * Sub Bab 3 - A: Matriks Pra-Kontrak
     */
    public function matriks(Request $request): View
    {
        $this->ensureTablesSeeded();

        $query = PertaminaProject::query();

        if ($request->filled('q')) {
            $query->search($request->string('q')->toString());
        }

        if ($request->filled('pengelola')) {
            $query->where('pengelola', $request->string('pengelola')->toString());
        }

        if ($request->filled('hasil_penawaran')) {
            $query->where('hasil_penawaran', $request->string('hasil_penawaran')->toString());
        }

        if ($request->filled('partner')) {
            $query->where('partner', $request->string('partner')->toString());
        }

        if ($request->filled('pic')) {
            $query->where('pic', $request->string('pic')->toString());
        }

        // Header Summary KPI Cards (Dihitung dari keseluruhan data)
        $allProjects = PertaminaProject::all();
        $totalProjects = $allProjects->count();
        $totalNilaiPenawaran = (float) $allProjects->sum('nilai_project');
        $menangProjects = $allProjects->filter(fn ($p) => strcasecmp($p->hasil_penawaran ?? '', 'Menang') === 0);
        $totalMenang = $menangProjects->count();
        $totalNilaiMenang = (float) $menangProjects->sum('nilai_project');
        $lastUpdate = $allProjects->max('updated_at') ?: now();

        $projects = $query->orderBy('id')->paginate(15)->withQueryString();

        // Master Data untuk Dropdown Modal Form
        $refTahapPraKontrak = PertaminaRefTahapPraKontrak::orderBy('urutan')->get();
        $refHasilPenawaran = PertaminaRefHasilPenawaran::orderBy('id')->get();
        $refTahapPelaksanaan = PertaminaRefTahapPelaksanaan::orderBy('urutan')->get();
        $refPic = PertaminaRefPic::orderBy('nama')->pluck('nama');

        $partnerOptions = ['FT UNDIP', 'LPPM UNDIP', 'PT SYNERGY', 'PT PURIFIKASI'];
        $pengelolaOptions = ['Semarang', 'Bandung'];
        $bobotOptions = [0.0, 0.2, 0.4, 0.6, 0.8, 1.0];

        return view('pertamina.matriks', [
            'projects' => $projects,
            'totalProjects' => $totalProjects,
            'totalNilaiPenawaran' => $totalNilaiPenawaran,
            'totalMenang' => $totalMenang,
            'totalNilaiMenang' => $totalNilaiMenang,
            'lastUpdate' => $lastUpdate,
            'refTahapPraKontrak' => $refTahapPraKontrak,
            'refHasilPenawaran' => $refHasilPenawaran,
            'refTahapPelaksanaan' => $refTahapPelaksanaan,
            'refPic' => $refPic,
            'partnerOptions' => $partnerOptions,
            'pengelolaOptions' => $pengelolaOptions,
            'bobotOptions' => $bobotOptions,
            'filters' => [
                'q' => $request->string('q')->toString(),
                'pengelola' => $request->string('pengelola')->toString(),
                'hasil_penawaran' => $request->string('hasil_penawaran')->toString(),
                'partner' => $request->string('partner')->toString(),
                'pic' => $request->string('pic')->toString(),
            ],
        ]);
    }

    /**
     * Sub Bab 3 - B: Dashboard Pra-Kontrak
     */
    public function dashboard(): View
    {
        $this->ensureTablesSeeded();

        $allProjects = PertaminaProject::all();
        $totalProjects = $allProjects->count();
        $totalNilaiPenawaran = (float) $allProjects->sum('nilai_project');

        $menangProjects = $allProjects->filter(fn ($p) => strcasecmp($p->hasil_penawaran ?? '', 'Menang') === 0);
        $totalNilaiMenang = (float) $menangProjects->sum('nilai_project');

        $avgProgress = $totalProjects > 0
            ? ($allProjects->sum('progress_pra_kontrak') / $totalProjects) * 100
            : 0;

        // Ringkasan 1: Hasil Penawaran
        $hasilGroups = $allProjects->groupBy(fn ($p) => $p->hasil_penawaran ?: 'Lainnya')->map(function ($items, $key) use ($totalProjects) {
            $cnt = $items->count();
            return [
                'name' => $key,
                'count' => $cnt,
                'total_nilai' => (float) $items->sum('nilai_project'),
                'percentage' => $totalProjects > 0 ? ($cnt / $totalProjects) * 100 : 0,
            ];
        });

        // Ringkasan 2: Pengelola
        $pengelolaGroups = $allProjects->groupBy(fn ($p) => $p->pengelola ?: 'Semarang')->map(function ($items, $key) {
            return [
                'name' => $key,
                'count' => $items->count(),
                'total_nilai' => (float) $items->sum('nilai_project'),
            ];
        });

        // Ringkasan 3: Tahap Proses Pra-Kontrak (Progress Penawaran)
        $tahapGroups = $allProjects->groupBy(fn ($p) => $p->progress_penawaran ?: 'Belum Ada')->map(function ($items, $key) {
            return [
                'name' => $key,
                'count' => $items->count(),
                'total_nilai' => (float) $items->sum('nilai_project'),
            ];
        });

        // Ringkasan 4: PIC
        $picGroups = $allProjects->filter(fn ($p) => !empty($p->pic))->groupBy('pic')->map(function ($items, $key) {
            return [
                'name' => $key,
                'count' => $items->count(),
                'total_nilai' => (float) $items->sum('nilai_project'),
            ];
        })->sortByDesc('count');

        return view('pertamina.dashboard', [
            'totalProjects' => $totalProjects,
            'totalNilaiPenawaran' => $totalNilaiPenawaran,
            'totalNilaiMenang' => $totalNilaiMenang,
            'avgProgress' => $avgProgress,
            'hasilGroups' => $hasilGroups,
            'pengelolaGroups' => $pengelolaGroups,
            'tahapGroups' => $tahapGroups,
            'picGroups' => $picGroups,
        ]);
    }

    /**
     * Sub Bab 3 - C: Referensi Pra-Kontrak
     */
    public function refPraKontrak(): View
    {
        $this->ensureTablesSeeded();

        return view('pertamina.ref-pra-kontrak', [
            'tahapList' => PertaminaRefTahapPraKontrak::orderBy('urutan')->get(),
            'hasilList' => PertaminaRefHasilPenawaran::orderBy('id')->get(),
            'statusList' => PertaminaRefStatusProses::orderBy('id')->get(),
        ]);
    }

    /**
     * Sub Bab 3 - D: Referensi Pelaksanaan
     */
    public function refPelaksanaan(): View
    {
        $this->ensureTablesSeeded();

        return view('pertamina.ref-pelaksanaan', [
            'tahapList' => PertaminaRefTahapPelaksanaan::orderBy('urutan')->get(),
            'picList' => PertaminaRefPic::orderBy('nama')->get(),
        ]);
    }

    /**
     * Sub Bab 3 - E: Petunjuk Pra-Kontrak
     */
    public function petunjuk(): View
    {
        $this->ensureTablesSeeded();

        return view('pertamina.petunjuk', [
            'panduanList' => PertaminaGuidePanduan::orderBy('id')->get(),
            'bobotList' => PertaminaGuideBobotProses::orderBy('bobot')->get(),
        ]);
    }

    /**
     * Simpan Proyek Baru (Matriks Pra-Kontrak)
     */
    public function storeProject(Request $request): RedirectResponse
    {
        if ($request->has('nilai_project')) {
            $clean = preg_replace('/[^0-9]/', '', (string) $request->input('nilai_project'));
            $request->merge(['nilai_project' => $clean !== '' ? (float) $clean : 0]);
        }

        $validated = $request->validate([
            'kode_project' => ['required', 'string', 'max:64', 'unique:pertamina_projects,kode_project'],
            'nama_project' => ['required', 'string'],
            'nilai_project' => ['required', 'numeric', 'min:0'],
            'client' => ['required', 'string', 'max:255'],
            'partner' => ['nullable', 'string', 'max:128'],
            'pengelola' => ['required', 'string', 'in:Semarang,Bandung'],
            'progress_penawaran' => ['nullable', 'string', 'max:128'],
            'progress_pra_kontrak' => ['nullable', 'numeric', 'min:0', 'max:1'],
            'hasil_penawaran' => ['nullable', 'string', 'max:64'],
            'status_pekerjaan' => ['nullable', 'string', 'max:128'],
            'progress_pekerjaan' => ['nullable', 'numeric', 'min:0', 'max:1'],
            'pic' => ['nullable', 'string', 'max:128'],
        ]);

        // LOGIKA AUTO-SAVE PIC BARU:
        if (!empty($validated['pic'])) {
            $picTrimmed = trim($validated['pic']);
            PertaminaRefPic::firstOrCreate(['nama' => $picTrimmed]);
        }

        PertaminaProject::create($validated + ['user_id' => $request->user()?->id]);

        return redirect()->route('pertamina.matriks')
            ->with('status', "Proyek {$validated['kode_project']} berhasil ditambahkan ke Matriks Pra-Kontrak.");
    }

    /**
     * Update Proyek (Matriks Pra-Kontrak)
     */
    public function updateProject(Request $request, PertaminaProject $project): RedirectResponse
    {
        if ($request->has('nilai_project')) {
            $clean = preg_replace('/[^0-9]/', '', (string) $request->input('nilai_project'));
            $request->merge(['nilai_project' => $clean !== '' ? (float) $clean : 0]);
        }

        $validated = $request->validate([
            'kode_project' => ['required', 'string', 'max:64', 'unique:pertamina_projects,kode_project,' . $project->id],
            'nama_project' => ['required', 'string'],
            'nilai_project' => ['required', 'numeric', 'min:0'],
            'client' => ['required', 'string', 'max:255'],
            'partner' => ['nullable', 'string', 'max:128'],
            'pengelola' => ['required', 'string', 'in:Semarang,Bandung'],
            'progress_penawaran' => ['nullable', 'string', 'max:128'],
            'progress_pra_kontrak' => ['nullable', 'numeric', 'min:0', 'max:1'],
            'hasil_penawaran' => ['nullable', 'string', 'max:64'],
            'status_pekerjaan' => ['nullable', 'string', 'max:128'],
            'progress_pekerjaan' => ['nullable', 'numeric', 'min:0', 'max:1'],
            'pic' => ['nullable', 'string', 'max:128'],
        ]);

        // LOGIKA AUTO-SAVE PIC BARU:
        if (!empty($validated['pic'])) {
            $picTrimmed = trim($validated['pic']);
            PertaminaRefPic::firstOrCreate(['nama' => $picTrimmed]);
        }

        $project->update($validated);

        return redirect()->route('pertamina.matriks')
            ->with('status', "Perubahan proyek {$project->kode_project} berhasil disimpan.");
    }

    /**
     * Hapus Proyek (Matriks Pra-Kontrak)
     */
    public function destroyProject(PertaminaProject $project): RedirectResponse
    {
        $code = $project->kode_project;
        $project->delete();

        return redirect()->route('pertamina.matriks')
            ->with('status', "Proyek {$code} berhasil dihapus.");
    }

    /**
     * Simpan Data Master / Referensi / Petunjuk
     */
    public function storeReference(Request $request, string $type): RedirectResponse
    {
        switch ($type) {
            case 'tahap-pra-kontrak':
                $v = $request->validate([
                    'tahap' => ['required', 'string', 'max:128'],
                    'bobot' => ['required', 'numeric', 'min:0', 'max:1'],
                ]);
                $maxUrut = (int) PertaminaRefTahapPraKontrak::max('urutan');
                PertaminaRefTahapPraKontrak::create($v + ['urutan' => $maxUrut + 1]);
                $targetRoute = 'pertamina.ref-pra-kontrak';
                $msg = 'Tahap Pra-Kontrak berhasil ditambahkan.';
                break;

            case 'hasil-penawaran':
                $v = $request->validate([
                    'nama' => ['required', 'string', 'max:64'],
                ]);
                PertaminaRefHasilPenawaran::create($v);
                $targetRoute = 'pertamina.ref-pra-kontrak';
                $msg = 'Hasil Penawaran berhasil ditambahkan.';
                break;

            case 'status-proses':
                $v = $request->validate([
                    'nama' => ['required', 'string', 'max:128'],
                ]);
                PertaminaRefStatusProses::create($v);
                $targetRoute = 'pertamina.ref-pra-kontrak';
                $msg = 'Status Proses berhasil ditambahkan.';
                break;

            case 'tahap-pelaksanaan':
                $v = $request->validate([
                    'tahap' => ['required', 'string', 'max:128'],
                    'bobot' => ['required', 'numeric', 'min:0', 'max:1'],
                ]);
                $maxUrut = (int) PertaminaRefTahapPelaksanaan::max('urutan');
                PertaminaRefTahapPelaksanaan::create($v + ['urutan' => $maxUrut + 1]);
                $targetRoute = 'pertamina.ref-pelaksanaan';
                $msg = 'Tahap Pelaksanaan berhasil ditambahkan.';
                break;

            case 'pic':
                $v = $request->validate([
                    'nama' => ['required', 'string', 'max:128', 'unique:pertamina_ref_pic,nama'],
                ]);
                PertaminaRefPic::create($v);
                $targetRoute = 'pertamina.ref-pelaksanaan';
                $msg = 'Nama PIC baru berhasil ditambahkan.';
                break;

            case 'panduan':
                $v = $request->validate([
                    'bagian_warna' => ['required', 'string', 'max:128'],
                    'fungsi' => ['required', 'string', 'max:255'],
                    'cara_penggunaan' => ['required', 'string'],
                ]);
                PertaminaGuidePanduan::create($v);
                $targetRoute = 'pertamina.petunjuk';
                $msg = 'Item Panduan Penggunaan berhasil ditambahkan.';
                break;

            case 'bobot-proses':
                $v = $request->validate([
                    'tahap' => ['required', 'string', 'max:128'],
                    'bobot' => ['required', 'numeric', 'min:0', 'max:1'],
                ]);
                PertaminaGuideBobotProses::create($v);
                $targetRoute = 'pertamina.petunjuk';
                $msg = 'Standar Bobot Tahap Proses berhasil ditambahkan.';
                break;

            default:
                abort(404, 'Tipe referensi tidak valid.');
        }

        return redirect()->route($targetRoute)->with('status', $msg);
    }

    /**
     * Update Data Master / Referensi / Petunjuk
     */
    public function updateReference(Request $request, string $type, int $id): RedirectResponse
    {
        switch ($type) {
            case 'tahap-pra-kontrak':
                $item = PertaminaRefTahapPraKontrak::findOrFail($id);
                $v = $request->validate([
                    'tahap' => ['required', 'string', 'max:128'],
                    'bobot' => ['required', 'numeric', 'min:0', 'max:1'],
                ]);
                $item->update($v);
                $targetRoute = 'pertamina.ref-pra-kontrak';
                break;

            case 'hasil-penawaran':
                $item = PertaminaRefHasilPenawaran::findOrFail($id);
                $v = $request->validate(['nama' => ['required', 'string', 'max:64']]);
                $item->update($v);
                $targetRoute = 'pertamina.ref-pra-kontrak';
                break;

            case 'status-proses':
                $item = PertaminaRefStatusProses::findOrFail($id);
                $v = $request->validate(['nama' => ['required', 'string', 'max:128']]);
                $item->update($v);
                $targetRoute = 'pertamina.ref-pra-kontrak';
                break;

            case 'tahap-pelaksanaan':
                $item = PertaminaRefTahapPelaksanaan::findOrFail($id);
                $v = $request->validate([
                    'tahap' => ['required', 'string', 'max:128'],
                    'bobot' => ['required', 'numeric', 'min:0', 'max:1'],
                ]);
                $item->update($v);
                $targetRoute = 'pertamina.ref-pelaksanaan';
                break;

            case 'pic':
                $item = PertaminaRefPic::findOrFail($id);
                $v = $request->validate(['nama' => ['required', 'string', 'max:128', 'unique:pertamina_ref_pic,nama,' . $item->id]]);
                $item->update($v);
                $targetRoute = 'pertamina.ref-pelaksanaan';
                break;

            case 'panduan':
                $item = PertaminaGuidePanduan::findOrFail($id);
                $v = $request->validate([
                    'bagian_warna' => ['required', 'string', 'max:128'],
                    'fungsi' => ['required', 'string', 'max:255'],
                    'cara_penggunaan' => ['required', 'string'],
                ]);
                $item->update($v);
                $targetRoute = 'pertamina.petunjuk';
                break;

            case 'bobot-proses':
                $item = PertaminaGuideBobotProses::findOrFail($id);
                $v = $request->validate([
                    'tahap' => ['required', 'string', 'max:128'],
                    'bobot' => ['required', 'numeric', 'min:0', 'max:1'],
                ]);
                $item->update($v);
                $targetRoute = 'pertamina.petunjuk';
                break;

            default:
                abort(404, 'Tipe referensi tidak valid.');
        }

        return redirect()->route($targetRoute)->with('status', 'Perubahan referensi berhasil disimpan.');
    }

    /**
     * Hapus Data Master / Referensi / Petunjuk
     */
    public function destroyReference(string $type, int $id): RedirectResponse
    {
        switch ($type) {
            case 'tahap-pra-kontrak':
                PertaminaRefTahapPraKontrak::findOrFail($id)->delete();
                $targetRoute = 'pertamina.ref-pra-kontrak';
                break;

            case 'hasil-penawaran':
                PertaminaRefHasilPenawaran::findOrFail($id)->delete();
                $targetRoute = 'pertamina.ref-pra-kontrak';
                break;

            case 'status-proses':
                PertaminaRefStatusProses::findOrFail($id)->delete();
                $targetRoute = 'pertamina.ref-pra-kontrak';
                break;

            case 'tahap-pelaksanaan':
                PertaminaRefTahapPelaksanaan::findOrFail($id)->delete();
                $targetRoute = 'pertamina.ref-pelaksanaan';
                break;

            case 'pic':
                PertaminaRefPic::findOrFail($id)->delete();
                $targetRoute = 'pertamina.ref-pelaksanaan';
                break;

            case 'panduan':
                PertaminaGuidePanduan::findOrFail($id)->delete();
                $targetRoute = 'pertamina.petunjuk';
                break;

            case 'bobot-proses':
                PertaminaGuideBobotProses::findOrFail($id)->delete();
                $targetRoute = 'pertamina.petunjuk';
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
        if (PertaminaProject::count() === 0) {
            app(\Database\Seeders\PertaminaProjectSeeder::class)->run();
        }
    }
}
