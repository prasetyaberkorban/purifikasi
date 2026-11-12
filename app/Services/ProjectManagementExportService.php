<?php

namespace App\Services;

use App\Models\PelaksanaanGuideBobotProses;
use App\Models\PelaksanaanGuidePanduan;
use App\Models\PelaksanaanProject;
use App\Models\PelaksanaanRefPic;
use App\Models\PelaksanaanRefTahap;
use App\Models\PertaminaGuideBobotProses;
use App\Models\PertaminaGuidePanduan;
use App\Models\PertaminaProject;
use App\Models\PertaminaRefHasilPenawaran;
use App\Models\PertaminaRefPic;
use App\Models\PertaminaRefStatusProses;
use App\Models\PertaminaRefTahapPelaksanaan;
use App\Models\PertaminaRefTahapPraKontrak;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProjectManagementExportService
{
    // ====================================================================
    // PERTAMINA: EXCEL EXPORT (SEMUA / PER SHEET)
    // ====================================================================

    public function exportPertaminaExcel(string $sheet, array $filters = []): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();

        if ($sheet === 'all') {
            $sheet1 = $spreadsheet->getActiveSheet();
            $sheet1->setTitle('Matriks Pra-Kontrak');
            $this->populatePertaminaMatriksSheet($sheet1, $filters);

            $sheet2 = $spreadsheet->createSheet();
            $sheet2->setTitle('Ringkasan Dashboard');
            $this->populatePertaminaDashboardSheet($sheet2);

            $sheet3 = $spreadsheet->createSheet();
            $sheet3->setTitle('Ref Pra-Kontrak');
            $this->populatePertaminaRefPraKontrakSheet($sheet3);

            $sheet4 = $spreadsheet->createSheet();
            $sheet4->setTitle('Ref Pelaksanaan');
            $this->populatePertaminaRefPelaksanaanSheet($sheet4);

            $sheet5 = $spreadsheet->createSheet();
            $sheet5->setTitle('Petunjuk Pra-Kontrak');
            $this->populatePertaminaPetunjukSheet($sheet5);

            $filename = 'Pertamina-Project-Semua-Sheet-' . now()->format('Ymd-His') . '.xlsx';
        } else {
            $ws = $spreadsheet->getActiveSheet();
            switch ($sheet) {
                case 'dashboard':
                    $ws->setTitle('Dashboard Pra-Kontrak');
                    $this->populatePertaminaDashboardSheet($ws);
                    $filename = 'Pertamina-Dashboard-Pra-Kontrak-' . now()->format('Ymd-His') . '.xlsx';
                    break;
                case 'ref-pra-kontrak':
                    $ws->setTitle('Ref Pra-Kontrak');
                    $this->populatePertaminaRefPraKontrakSheet($ws);
                    $filename = 'Pertamina-Referensi-Pra-Kontrak-' . now()->format('Ymd-His') . '.xlsx';
                    break;
                case 'ref-pelaksanaan':
                    $ws->setTitle('Ref Pelaksanaan');
                    $this->populatePertaminaRefPelaksanaanSheet($ws);
                    $filename = 'Pertamina-Referensi-Pelaksanaan-' . now()->format('Ymd-His') . '.xlsx';
                    break;
                case 'petunjuk':
                    $ws->setTitle('Petunjuk Pra-Kontrak');
                    $this->populatePertaminaPetunjukSheet($ws);
                    $filename = 'Pertamina-Petunjuk-Pra-Kontrak-' . now()->format('Ymd-His') . '.xlsx';
                    break;
                case 'matriks':
                default:
                    $ws->setTitle('Matriks Pra-Kontrak');
                    $this->populatePertaminaMatriksSheet($ws, $filters);
                    $filename = 'Pertamina-Matriks-Pra-Kontrak-' . now()->format('Ymd-His') . '.xlsx';
                    break;
            }
        }

        return response()->streamDownload(function () use ($spreadsheet) {
            (new Xlsx($spreadsheet))->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    private function populatePertaminaMatriksSheet(Worksheet $sheet, array $filters = []): void
    {
        $headers = [
            'No.', 'Kode Project', 'Nama Project', 'Nilai Project (Rp)', 'Client',
            'Partner', 'Pengelola', 'Progress Penawaran', 'Bobot Pra-Kontrak',
            'Hasil Penawaran', 'Status Pekerjaan', 'Progress Pekerjaan', 'PIC',
        ];

        $this->writeHeader($sheet, 'MATRIKS PRA-KONTRAK - REKAP PROJECT PERTAMINA', $headers);

        $query = PertaminaProject::query();
        if (!empty($filters['q'])) $query->search($filters['q']);
        if (!empty($filters['pengelola'])) $query->where('pengelola', $filters['pengelola']);
        if (!empty($filters['hasil_penawaran'])) $query->where('hasil_penawaran', $filters['hasil_penawaran']);
        if (!empty($filters['partner'])) $query->where('partner', $filters['partner']);
        if (!empty($filters['pic'])) $query->where('pic', $filters['pic']);

        $projects = $query->orderBy('id')->get();
        $rowNum = 4;
        foreach ($projects as $idx => $p) {
            $sheet->fromArray([
                $idx + 1,
                $p->kode_project,
                $p->nama_project,
                (float) $p->nilai_project,
                $p->client,
                $p->partner ?: '-',
                $p->pengelola,
                $p->progress_penawaran ?: '-',
                $p->progress_pra_kontrak !== null ? ($p->progress_pra_kontrak * 100) . '%' : '-',
                $p->hasil_penawaran ?: 'Dalam Proses',
                $p->status_pekerjaan ?: '-',
                $p->progress_pekerjaan !== null ? ($p->progress_pekerjaan * 100) . '%' : '-',
                $p->pic ?: '-',
            ], null, 'A' . $rowNum);
            $rowNum++;
        }

        $this->styleBody($sheet, 4, $rowNum - 1, count($headers));
        $sheet->getStyle('D4:D' . max(4, $rowNum - 1))->getNumberFormat()->setFormatCode('#,##0');
        $sheet->freezePane('A4');
    }

    private function populatePertaminaDashboardSheet(Worksheet $sheet): void
    {
        $projects = PertaminaProject::all();
        $total = $projects->count();
        $totalNilai = (float) $projects->sum('nilai_project');
        $menang = $projects->filter(fn ($p) => strcasecmp($p->hasil_penawaran ?? '', 'Menang') === 0);
        $totalMenang = $menang->count();
        $totalNilaiMenang = (float) $menang->sum('nilai_project');

        $headers = ['Kategori / Indikator', 'Jumlah Proyek', 'Total Nilai (Rp)', 'Persentase'];
        $this->writeHeader($sheet, 'RINGKASAN DASHBOARD PRA-KONTRAK PERTAMINA', $headers);

        $rowNum = 4;
        // Ringkasan KPI Utama
        $sheet->fromArray(['TOTAL KESELURUHAN PROYEK', $total, $totalNilai, '100%'], null, 'A' . $rowNum++);
        $sheet->fromArray(['PROYEK MENANG', $totalMenang, $totalNilaiMenang, $total > 0 ? round(($totalMenang / $total) * 100, 1) . '%' : '0%'], null, 'A' . $rowNum++);
        $sheet->fromArray(['PROYEK DALAM PROSES / KALAH', $total - $totalMenang, $totalNilai - $totalNilaiMenang, $total > 0 ? round((($total - $totalMenang) / $total) * 100, 1) . '%' : '0%'], null, 'A' . $rowNum++);

        // Spacer
        $rowNum++;
        $sheet->setCellValue('A' . $rowNum, 'BREAKDOWN HASIL PENAWARAN:');
        $sheet->getStyle('A' . $rowNum)->getFont()->setBold(true);
        $rowNum++;

        $hasilGroups = $projects->groupBy(fn ($p) => $p->hasil_penawaran ?: 'Dalam Proses');
        foreach ($hasilGroups as $name => $items) {
            $sheet->fromArray([
                $name,
                $items->count(),
                (float) $items->sum('nilai_project'),
                $total > 0 ? round(($items->count() / $total) * 100, 1) . '%' : '0%',
            ], null, 'A' . $rowNum++);
        }

        // Spacer
        $rowNum++;
        $sheet->setCellValue('A' . $rowNum, 'BREAKDOWN PENGELOLA:');
        $sheet->getStyle('A' . $rowNum)->getFont()->setBold(true);
        $rowNum++;

        $pengelolaGroups = $projects->groupBy(fn ($p) => $p->pengelola ?: 'Semarang');
        foreach ($pengelolaGroups as $name => $items) {
            $sheet->fromArray([
                $name,
                $items->count(),
                (float) $items->sum('nilai_project'),
                $total > 0 ? round(($items->count() / $total) * 100, 1) . '%' : '0%',
            ], null, 'A' . $rowNum++);
        }

        $this->styleBody($sheet, 4, $rowNum - 1, count($headers));
        $sheet->getStyle('C4:C' . ($rowNum - 1))->getNumberFormat()->setFormatCode('#,##0');
    }

    private function populatePertaminaRefPraKontrakSheet(Worksheet $sheet): void
    {
        $headers = ['No.', 'Tahap Pra-Kontrak', 'Bobot Progress', 'Urutan'];
        $this->writeHeader($sheet, 'REFERENSI TAHAP PRA-KONTRAK', $headers);

        $tahap = PertaminaRefTahapPraKontrak::orderBy('urutan')->get();
        $rowNum = 4;
        foreach ($tahap as $idx => $t) {
            $sheet->fromArray([
                $idx + 1,
                $t->tahap,
                (float) $t->bobot,
                $t->urutan,
            ], null, 'A' . $rowNum++);
        }
        $this->styleBody($sheet, 4, $rowNum - 1, count($headers));
    }

    private function populatePertaminaRefPelaksanaanSheet(Worksheet $sheet): void
    {
        $headers = ['No.', 'Tahap Pelaksanaan', 'Bobot Progress', 'Urutan'];
        $this->writeHeader($sheet, 'REFERENSI TAHAP PELAKSANAAN & PIC', $headers);

        $tahap = PertaminaRefTahapPelaksanaan::orderBy('urutan')->get();
        $rowNum = 4;
        foreach ($tahap as $idx => $t) {
            $sheet->fromArray([
                $idx + 1,
                $t->tahap,
                (float) $t->bobot,
                $t->urutan,
            ], null, 'A' . $rowNum++);
        }

        $rowNum++;
        $sheet->setCellValue('A' . $rowNum, 'DAFTAR MASTER PIC:');
        $sheet->getStyle('A' . $rowNum)->getFont()->setBold(true);
        $rowNum++;

        $pics = PertaminaRefPic::orderBy('nama')->get();
        foreach ($pics as $idx => $pic) {
            $sheet->fromArray([$idx + 1, $pic->nama, '-', '-'], null, 'A' . $rowNum++);
        }

        $this->styleBody($sheet, 4, $rowNum - 1, count($headers));
    }

    private function populatePertaminaPetunjukSheet(Worksheet $sheet): void
    {
        $headers = ['No.', 'Bagian / Warna', 'Fungsi', 'Cara Penggunaan'];
        $this->writeHeader($sheet, 'PETUNJUK & PANDUAN PENGGUNAAN', $headers);

        $panduan = PertaminaGuidePanduan::orderBy('id')->get();
        $rowNum = 4;
        foreach ($panduan as $idx => $p) {
            $sheet->fromArray([
                $idx + 1,
                $p->bagian_warna,
                $p->fungsi,
                $p->cara_penggunaan,
            ], null, 'A' . $rowNum++);
        }
        $this->styleBody($sheet, 4, $rowNum - 1, count($headers));
    }

    // ====================================================================
    // PERTAMINA: PDF EXPORT (SEMUA / PER SHEET)
    // ====================================================================

    public function exportPertaminaPdf(string $sheet, array $filters = []): Response
    {
        $company = Setting::get('company_name', 'PT. Purifikasi');
        $unit = Setting::get('company_unit', 'OFFICE DATABASE KANTOR');
        $printedAt = now();

        $query = PertaminaProject::query();
        if (!empty($filters['q'])) $query->search($filters['q']);
        if (!empty($filters['pengelola'])) $query->where('pengelola', $filters['pengelola']);
        if (!empty($filters['hasil_penawaran'])) $query->where('hasil_penawaran', $filters['hasil_penawaran']);
        if (!empty($filters['partner'])) $query->where('partner', $filters['partner']);
        if (!empty($filters['pic'])) $query->where('pic', $filters['pic']);
        $projects = $query->orderBy('id')->get();

        $viewData = [
            'sheet' => $sheet,
            'projects' => $projects,
            'filters' => $filters,
            'company' => $company,
            'unit' => $unit,
            'printedAt' => $printedAt,
            'tahapPraKontrak' => PertaminaRefTahapPraKontrak::orderBy('urutan')->get(),
            'hasilPenawaran' => PertaminaRefHasilPenawaran::orderBy('id')->get(),
            'statusProses' => PertaminaRefStatusProses::orderBy('id')->get(),
            'tahapPelaksanaan' => PertaminaRefTahapPelaksanaan::orderBy('urutan')->get(),
            'pics' => PertaminaRefPic::orderBy('nama')->get(),
            'panduan' => PertaminaGuidePanduan::orderBy('id')->get(),
            'bobotProses' => PertaminaGuideBobotProses::orderBy('bobot')->get(),
        ];

        $view = ($sheet === 'all') ? 'reports.pdf.pertamina-all' : 'reports.pdf.pertamina-sheet';

        $pdf = Pdf::loadView($view, $viewData)->setPaper('a4', 'landscape');
        $filename = 'Pertamina-' . ($sheet === 'all' ? 'Semua-Sheet' : ucfirst($sheet)) . '-' . now()->format('Ymd-His') . '.pdf';

        return response($pdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    // ====================================================================
    // REKAP TAHAPAN PELAKSANAAN 2026: EXCEL EXPORT (SEMUA / PER SHEET)
    // ====================================================================

    public function exportPelaksanaanExcel(string $sheet, array $filters = []): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();

        if ($sheet === 'all') {
            $sheet1 = $spreadsheet->getActiveSheet();
            $sheet1->setTitle('Matriks Pelaksanaan');
            $this->populatePelaksanaanMatriksSheet($sheet1, $filters);

            $sheet2 = $spreadsheet->createSheet();
            $sheet2->setTitle('Rekap Tracking');
            $this->populatePelaksanaanRekapSheet($sheet2);

            $sheet3 = $spreadsheet->createSheet();
            $sheet3->setTitle('Referensi Pelaksanaan');
            $this->populatePelaksanaanReferensiSheet($sheet3);

            $sheet4 = $spreadsheet->createSheet();
            $sheet4->setTitle('Petunjuk Pra-Kontrak');
            $this->populatePelaksanaanPetunjukSheet($sheet4);

            $filename = 'Pelaksanaan-2026-Semua-Sheet-' . now()->format('Ymd-His') . '.xlsx';
        } else {
            $ws = $spreadsheet->getActiveSheet();
            switch ($sheet) {
                case 'rekap':
                    $ws->setTitle('Rekap Tracking');
                    $this->populatePelaksanaanRekapSheet($ws);
                    $filename = 'Pelaksanaan-Rekap-Tracking-' . now()->format('Ymd-His') . '.xlsx';
                    break;
                case 'referensi':
                    $ws->setTitle('Referensi Pelaksanaan');
                    $this->populatePelaksanaanReferensiSheet($ws);
                    $filename = 'Pelaksanaan-Referensi-' . now()->format('Ymd-His') . '.xlsx';
                    break;
                case 'petunjuk':
                    $ws->setTitle('Petunjuk Pra-Kontrak');
                    $this->populatePelaksanaanPetunjukSheet($ws);
                    $filename = 'Pelaksanaan-Petunjuk-' . now()->format('Ymd-His') . '.xlsx';
                    break;
                case 'matriks':
                default:
                    $ws->setTitle('Matriks Pelaksanaan');
                    $this->populatePelaksanaanMatriksSheet($ws, $filters);
                    $filename = 'Pelaksanaan-Matriks-' . now()->format('Ymd-His') . '.xlsx';
                    break;
            }
        }

        return response()->streamDownload(function () use ($spreadsheet) {
            (new Xlsx($spreadsheet))->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    private function populatePelaksanaanMatriksSheet(Worksheet $sheet, array $filters = []): void
    {
        $headers = [
            'No.', 'Kode Project', 'Nama Project', 'Pengelola', 'Status Pekerjaan',
            'Progress Pekerjaan', 'PIC', 'Kontrak Awal', 'Kontrak Akhir', 'Sisa Hari',
        ];

        $this->writeHeader($sheet, 'MATRIKS PELAKSANAAN PROYEK 2026', $headers);

        $query = PelaksanaanProject::query();
        if (!empty($filters['q'])) $query->search($filters['q']);
        if (!empty($filters['pengelola'])) $query->where('pengelola', $filters['pengelola']);
        if (!empty($filters['status_pekerjaan'])) $query->where('status_pekerjaan', $filters['status_pekerjaan']);
        if (!empty($filters['pic'])) $query->where('pic', $filters['pic']);

        $projects = $query->orderBy('id')->get();
        $rowNum = 4;
        foreach ($projects as $idx => $p) {
            $sheet->fromArray([
                $idx + 1,
                $p->kode_project ?: '-',
                $p->nama_project,
                $p->pengelola,
                $p->status_pekerjaan,
                round($p->progress_pekerjaan * 100) . '%',
                $p->pic ?: '-',
                $p->kontrak_awal ? $p->kontrak_awal->format('d/m/Y') : '-',
                $p->kontrak_akhir ? $p->kontrak_akhir->format('d/m/Y') : '-',
                $p->calculated_remaining_days !== null ? $p->calculated_remaining_days . ' Hari' : '-',
            ], null, 'A' . $rowNum++);
        }

        $this->styleBody($sheet, 4, $rowNum - 1, count($headers));
        $sheet->freezePane('A4');
    }

    private function populatePelaksanaanRekapSheet(Worksheet $sheet): void
    {
        $headers = ['No.', 'Tahap Pelaksanaan', 'Jumlah Proyek', 'Keterangan'];
        $this->writeHeader($sheet, 'RINGKASAN TAHAPAN PELAKSANAAN PEKERJAAN', $headers);

        $tahapList = PelaksanaanRefTahap::orderBy('urutan')->get();
        $allProjects = PelaksanaanProject::all();

        $normalizeStage = function (?string $stage): string {
            $trimmed = trim(strtolower((string) $stage));
            if (in_array($trimmed, ['paparan akhir', 'paparan final'])) return 'paparan final / paparan akhir';
            return $trimmed;
        };

        $rowNum = 4;
        foreach ($tahapList as $t) {
            $norm = $normalizeStage($t->tahap);
            $cnt = $allProjects->filter(fn ($p) => $normalizeStage($p->status_pekerjaan) === $norm)->count();
            $sheet->fromArray([
                $t->urutan,
                $t->tahap,
                $cnt,
                $t->keterangan,
            ], null, 'A' . $rowNum++);
        }
        $sheet->fromArray(['TOTAL', 'Total Proyek dalam Pelaksanaan', $allProjects->count(), '-'], null, 'A' . $rowNum++);
        $sheet->getStyle('A' . ($rowNum - 1) . ':D' . ($rowNum - 1))->getFont()->setBold(true);

        // Bagian 2: Tracking Matrix
        $rowNum += 2;
        $sheet->setCellValue('A' . $rowNum, 'MATRIKS TRACKING TAHAPAN PROYEK (✓ = Selesai, ● = Tahap Saat Ini):');
        $sheet->getStyle('A' . $rowNum)->getFont()->setBold(true);
        $rowNum++;

        $trackHeaders = [
            'No.', 'Nama Project', 'PIC', 'Tahap Saat Ini', 'Urutan',
            'Kontrak Awal', 'Kontrak Akhir', 'Sisa Hari',
            'Kick Off', 'Izin & Survey', 'Ambil Data', 'Olah Data', 'Draft Laporan', 'Submit Lap.', 'Paparan Akhir'
        ];
        $sheet->fromArray($trackHeaders, null, 'A' . $rowNum);
        $headerRow = $rowNum;
        $sheet->getStyle("A{$headerRow}:O{$headerRow}")->getFont()->setBold(true)->getColor()->setARGB('FFFFFFFF');
        $sheet->getStyle("A{$headerRow}:O{$headerRow}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF0D4C1E');
        $rowNum++;

        foreach ($allProjects as $idx => $proj) {
            $currentNorm = $normalizeStage($proj->status_pekerjaan);
            $currentIndex = 1;
            foreach ($tahapList as $t) {
                if ($normalizeStage($t->tahap) === $currentNorm) {
                    $currentIndex = $t->urutan;
                    break;
                }
            }

            $checkCols = [];
            for ($k = 1; $k <= 7; $k++) {
                if ($k < $currentIndex) $checkCols[] = '✓';
                elseif ($k === $currentIndex) $checkCols[] = '●';
                else $checkCols[] = '-';
            }

            $sheet->fromArray(array_merge([
                $idx + 1,
                $proj->nama_project,
                $proj->pic ?: '-',
                $proj->status_pekerjaan,
                "{$currentIndex}/7",
                $proj->kontrak_awal ? $proj->kontrak_awal->format('d/m/Y') : '-',
                $proj->kontrak_akhir ? $proj->kontrak_akhir->format('d/m/Y') : '-',
                $proj->calculated_remaining_days !== null ? $proj->calculated_remaining_days : '-',
            ], $checkCols), null, 'A' . $rowNum++);
        }

        $this->styleBody($sheet, 4, $rowNum - 1, 15);
    }

    private function populatePelaksanaanReferensiSheet(Worksheet $sheet): void
    {
        $headers = ['No.', 'Tahap Pelaksanaan', 'Bobot Progress', 'Urutan', 'Keterangan'];
        $this->writeHeader($sheet, 'REFERENSI TAHAP PELAKSANAAN & PIC', $headers);

        $tahap = PelaksanaanRefTahap::orderBy('urutan')->get();
        $rowNum = 4;
        foreach ($tahap as $t) {
            $sheet->fromArray([
                $t->urutan,
                $t->tahap,
                (float) $t->bobot,
                $t->urutan,
                $t->keterangan,
            ], null, 'A' . $rowNum++);
        }

        $rowNum++;
        $sheet->setCellValue('A' . $rowNum, 'DAFTAR MASTER PIC:');
        $sheet->getStyle('A' . $rowNum)->getFont()->setBold(true);
        $rowNum++;

        $pics = PelaksanaanRefPic::orderBy('nama')->get();
        foreach ($pics as $idx => $p) {
            $sheet->fromArray([$idx + 1, $p->nama, '-', '-', '-'], null, 'A' . $rowNum++);
        }

        $this->styleBody($sheet, 4, $rowNum - 1, count($headers));
    }

    private function populatePelaksanaanPetunjukSheet(Worksheet $sheet): void
    {
        $headers = ['No.', 'Bagian / Warna', 'Fungsi', 'Cara Penggunaan'];
        $this->writeHeader($sheet, 'PANDUAN PENGGUNAAN & STANDAR BOBOT PROSES', $headers);

        $panduan = PelaksanaanGuidePanduan::orderBy('id')->get();
        $rowNum = 4;
        foreach ($panduan as $idx => $p) {
            $sheet->fromArray([
                $idx + 1,
                $p->bagian_warna,
                $p->fungsi,
                $p->cara_penggunaan,
            ], null, 'A' . $rowNum++);
        }

        $rowNum++;
        $sheet->setCellValue('A' . $rowNum, 'STANDAR BOBOT TAHAP PROSES (16 TAHAP):');
        $sheet->getStyle('A' . $rowNum)->getFont()->setBold(true);
        $rowNum++;

        $bobotList = PelaksanaanGuideBobotProses::orderBy('bobot')->get();
        foreach ($bobotList as $idx => $b) {
            $sheet->fromArray([$idx + 1, $b->tahap, (float) $b->bobot, round($b->bobot * 100) . '%'], null, 'A' . $rowNum++);
        }

        $this->styleBody($sheet, 4, $rowNum - 1, count($headers));
    }

    // ====================================================================
    // REKAP TAHAPAN PELAKSANAAN 2026: PDF EXPORT (SEMUA / PER SHEET)
    // ====================================================================

    public function exportPelaksanaanPdf(string $sheet, array $filters = []): Response
    {
        $company = Setting::get('company_name', 'PT. Purifikasi');
        $unit = Setting::get('company_unit', 'OFFICE DATABASE KANTOR');
        $printedAt = now();

        $query = PelaksanaanProject::query();
        if (!empty($filters['q'])) $query->search($filters['q']);
        if (!empty($filters['pengelola'])) $query->where('pengelola', $filters['pengelola']);
        if (!empty($filters['status_pekerjaan'])) $query->where('status_pekerjaan', $filters['status_pekerjaan']);
        if (!empty($filters['pic'])) $query->where('pic', $filters['pic']);
        $projects = $query->orderBy('id')->get();

        $tahapList = PelaksanaanRefTahap::orderBy('urutan')->get();

        $normalizeStage = function (?string $stage): string {
            $trimmed = trim(strtolower((string) $stage));
            if (in_array($trimmed, ['paparan akhir', 'paparan final'])) return 'paparan final / paparan akhir';
            return $trimmed;
        };

        $trackingProjects = $projects->map(function ($project) use ($tahapList, $normalizeStage) {
            $currentNorm = $normalizeStage($project->status_pekerjaan);
            $currentIndex = 1;
            foreach ($tahapList as $t) {
                if ($normalizeStage($t->tahap) === $currentNorm) {
                    $currentIndex = $t->urutan;
                    break;
                }
            }

            $checklist = [];
            foreach ($tahapList as $t) {
                $order = $t->urutan;
                if ($order < $currentIndex) $checklist[$order] = 'completed';
                elseif ($order === $currentIndex) $checklist[$order] = 'active';
                else $checklist[$order] = 'future';
            }

            return [
                'model' => $project,
                'order_str' => "{$currentIndex}/7",
                'checklist' => $checklist,
            ];
        });

        $ringkasanTahap = $tahapList->map(function ($item) use ($projects, $normalizeStage) {
            $norm = $normalizeStage($item->tahap);
            $cnt = $projects->filter(fn ($p) => $normalizeStage($p->status_pekerjaan) === $norm)->count();
            return [
                'no' => $item->urutan,
                'tahap' => $item->tahap,
                'count' => $cnt,
                'keterangan' => $item->keterangan,
            ];
        });

        $viewData = [
            'sheet' => $sheet,
            'projects' => $projects,
            'filters' => $filters,
            'company' => $company,
            'unit' => $unit,
            'printedAt' => $printedAt,
            'tahapList' => $tahapList,
            'trackingProjects' => $trackingProjects,
            'ringkasanTahap' => $ringkasanTahap,
            'pics' => PelaksanaanRefPic::orderBy('nama')->get(),
            'panduan' => PelaksanaanGuidePanduan::orderBy('id')->get(),
            'bobotList' => PelaksanaanGuideBobotProses::orderBy('bobot')->get(),
        ];

        $view = ($sheet === 'all') ? 'reports.pdf.pelaksanaan-all' : 'reports.pdf.pelaksanaan-sheet';

        $pdf = Pdf::loadView($view, $viewData)->setPaper('a4', 'landscape');
        $filename = 'Pelaksanaan-2026-' . ($sheet === 'all' ? 'Semua-Sheet' : ucfirst($sheet)) . '-' . now()->format('Ymd-His') . '.pdf';

        return response($pdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    // ====================================================================
    // STYLING HELPERS
    // ====================================================================

    private function writeHeader(Worksheet $sheet, string $title, array $headers): void
    {
        $lastColumn = $this->columnLetter(count($headers));

        $sheet->mergeCells("A1:{$lastColumn}1");
        $sheet->setCellValue('A1', $title);
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(13);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells("A2:{$lastColumn}2");
        $sheet->setCellValue('A2', Setting::get('company_name', 'PT. Purifikasi') . ' · ' . Setting::get('company_unit', 'OFFICE DATABASE KANTOR') . ' | Dicetak: ' . now()->translatedFormat('d F Y H:i'));
        $sheet->getStyle('A2')->getFont()->setSize(9)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('666666'));
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->fromArray($headers, null, 'A3');
        $sheet->getStyle("A3:{$lastColumn}3")->getFont()->setBold(true)->getColor()->setARGB('FFFFFFFF');
        $sheet->getStyle("A3:{$lastColumn}3")->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FF0D4C1E');
        $sheet->getStyle("A3:{$lastColumn}3")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getRowDimension(3)->setRowHeight(22);

        foreach (range(1, count($headers)) as $index) {
            $sheet->getColumnDimension($this->columnLetter($index))->setAutoSize(true);
        }
    }

    private function styleBody(Worksheet $sheet, int $startRow, int $endRow, int $columnCount): void
    {
        if ($endRow < $startRow) {
            return;
        }

        $lastColumn = $this->columnLetter($columnCount);
        $range = "A{$startRow}:{$lastColumn}{$endRow}";

        $sheet->getStyle($range)->getBorders()->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN)
            ->getColor()->setARGB('FFE5E9E6');

        for ($r = $startRow; $r <= $endRow; $r++) {
            if ($r % 2 === 0) {
                $sheet->getStyle("A{$r}:{$lastColumn}{$r}")->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFF8F9FA');
            }
        }
    }

    private function columnLetter(int $index): string
    {
        $letter = '';
        while ($index > 0) {
            $mod = ($index - 1) % 26;
            $letter = chr(65 + $mod) . $letter;
            $index = (int) (($index - $mod) / 26);
        }
        return $letter;
    }
}
