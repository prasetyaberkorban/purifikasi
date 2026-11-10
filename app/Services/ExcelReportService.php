<?php

namespace App\Services;

use App\Models\Asset;
use App\Models\AssetLoan;
use App\Models\AssetMaintenance;
use App\Models\AssetProcurement;
use App\Models\EmployeeLeave;
use App\Models\MeetingAttendance;
use App\Models\ProjectContract;
use App\Models\VehicleLoan;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExcelReportService
{
    /**
     * @param  Collection<int, mixed>  $rows
     * @param  array<string, string>  $filters
     */
    public function stream(string $type, Collection $rows, array $filters = []): StreamedResponse
    {
        $spreadsheet = match ($type) {
            'loans' => $this->loansSheet($rows, $filters),
            'maintenances' => $this->maintenancesSheet($rows, $filters),
            'procurements' => $this->procurementsSheet($rows, $filters),
            'leaves' => $this->leavesSheet($rows, $filters),
            'vehicle-loans' => $this->vehicleLoansSheet($rows, $filters),
            'attendances' => $this->attendancesSheet($rows, $filters),
            'contracts' => $this->contractsSheet($rows, $filters),
            default => $this->assetsSheet($rows, $filters),
        };

        $filename = 'laporan-'.$type.'-'.now()->format('Ymd-His').'.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            (new Xlsx($spreadsheet))->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    /**
     * Ekspor seluruh data yang ada di website ke dalam satu file Excel multi-sheet.
     *
     * @param  array<string, Collection<int, mixed>>  $datasets
     * @param  array<string, string>  $filters
     */
    public function streamAll(array $datasets, array $filters = []): StreamedResponse
    {
        $spreadsheet = new Spreadsheet;

        // Sheet 1: Ringkasan Eksekutif
        $sheetSummary = $spreadsheet->getActiveSheet();
        $this->populateSummarySheet($sheetSummary, $datasets, $filters);

        // Sheet 2: Inventaris Aset
        $sheetAssets = $spreadsheet->createSheet();
        $this->populateAssetsWorksheet($sheetAssets, $datasets['assets'] ?? collect(), $filters);

        // Sheet 3: Peminjaman Barang
        $sheetLoans = $spreadsheet->createSheet();
        $this->populateLoansWorksheet($sheetLoans, $datasets['loans'] ?? collect(), $filters);

        // Sheet 4: Service Kendaraan & Aset
        $sheetMaint = $spreadsheet->createSheet();
        $this->populateMaintenancesWorksheet($sheetMaint, $datasets['maintenances'] ?? collect(), $filters);

        // Sheet 5: Realisasi Pengadaan
        $sheetProc = $spreadsheet->createSheet();
        $this->populateProcurementsWorksheet($sheetProc, $datasets['procurements'] ?? collect(), $filters);

        // Sheet 6: Izin & Cuti
        $sheetLeaves = $spreadsheet->createSheet();
        $this->populateLeavesWorksheet($sheetLeaves, $datasets['leaves'] ?? collect(), $filters);

        // Sheet 7: Peminjaman Kendaraan
        $sheetVehicles = $spreadsheet->createSheet();
        $this->populateVehicleLoansWorksheet($sheetVehicles, $datasets['vehicleLoans'] ?? collect(), $filters);

        // Sheet 8: Daftar Hadir
        $sheetAtt = $spreadsheet->createSheet();
        $this->populateAttendancesWorksheet($sheetAtt, $datasets['attendances'] ?? collect(), $filters);

        // Sheet 9: Kontrak AMDAL
        $sheetAmdal = $spreadsheet->createSheet();
        $this->populateContractsWorksheet($sheetAmdal, $datasets['contractsAmdal'] ?? collect(), array_merge($filters, ['category_label' => 'AMDAL']), 'Kontrak AMDAL');

        // Sheet 10: Kontrak Engineering
        $sheetEng = $spreadsheet->createSheet();
        $this->populateContractsWorksheet($sheetEng, $datasets['contractsEngineering'] ?? collect(), array_merge($filters, ['category_label' => 'ENGINEERING']), 'Kontrak Engineering');

        $spreadsheet->setActiveSheetIndex(0);

        $filename = 'laporan-konsolidasi-seluruh-data-purifikasi-'.now()->format('Ymd-His').'.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            (new Xlsx($spreadsheet))->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    private function populateSummarySheet(Worksheet $sheet, array $datasets, array $filters): void
    {
        $sheet->setTitle('Ringkasan');

        $headers = ['No', 'Modul / Entitas Data', 'Total Data / Catatan', 'Keterangan Metrik'];
        $this->writeHeader($sheet, 'RINGKASAN EKSEKUTIF SELURUH DATA SISTEM PURIFIKASI', $headers);
        $this->writeFilters($sheet, count($headers), $filters, ['from' => 'Periode dari', 'to' => 'Periode sampai']);

        $contractsAmdalValue = (float) ($datasets['contractsAmdal'] ?? collect())->sum('contract_value');
        $contractsEngValue = (float) ($datasets['contractsEngineering'] ?? collect())->sum('contract_value');
        $assetsValue = (float) ($datasets['assets'] ?? collect())->sum(fn ($a) => (float) $a->totalValue());

        $summaryData = [
            [1, 'Inventaris Aset & Barang', count($datasets['assets'] ?? []), 'Total Nilai: Rp '.number_format($assetsValue, 0, ',', '.')],
            [2, 'Peminjaman Barang', count($datasets['loans'] ?? []), 'Transaksi Pinjam / Kembali'],
            [3, 'Service & Pemeliharaan', count($datasets['maintenances'] ?? []), 'Riwayat Servis'],
            [4, 'Realisasi Pengadaan', count($datasets['procurements'] ?? []), 'Riwayat Belanja / Pengadaan'],
            [5, 'Izin, Cuti & WFA', count($datasets['leaves'] ?? []), 'Pengajuan Karyawan'],
            [6, 'Peminjaman Kendaraan Operasional', count($datasets['vehicleLoans'] ?? []), 'Riwayat Peminjaman'],
            [7, 'Daftar Hadir Rapat & Kegiatan', count($datasets['attendances'] ?? []), 'Peserta / Notulensi Rapat'],
            [8, 'Rekapitulasi Kontrak / SPK - AMDAL', count($datasets['contractsAmdal'] ?? []), 'Nilai Total: Rp '.number_format($contractsAmdalValue, 0, ',', '.')],
            [9, 'Rekapitulasi Kontrak / SPK - Engineering', count($datasets['contractsEngineering'] ?? []), 'Nilai Total: Rp '.number_format($contractsEngValue, 0, ',', '.')],
            [10, 'Penilaian Fatik Individual', count($datasets['fatigue'] ?? []), 'Pemeriksaan Kesehatan & Kesiapan'],
        ];

        $rowNumber = 4;
        foreach ($summaryData as $row) {
            $sheet->fromArray($row, null, 'A'.$rowNumber);
            $rowNumber++;
        }

        $this->styleBody($sheet, 4, $rowNumber - 1, count($headers));
        $sheet->freezePane('A4');
    }

    /** @param  Collection<int, Asset>  $rows */
    private function assetsSheet(Collection $rows, array $filters): Spreadsheet
    {
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $this->populateAssetsWorksheet($sheet, $rows, $filters);

        return $spreadsheet;
    }

    private function populateAssetsWorksheet(Worksheet $sheet, Collection $rows, array $filters): void
    {
        $sheet->setTitle('Inventaris');

        $headers = ['Kode Aset', 'Nama Barang', 'Cabang', 'Kategori', 'Ruangan', 'Stok', 'Dipinjam', 'Tersedia', 'Nilai Total'];

        $this->writeHeader($sheet, 'LAPORAN INVENTARIS ASET', $headers);
        $this->writeFilters($sheet, count($headers), $filters, [
            'from' => 'Tanggal dari',
            'to' => 'Tanggal sampai',
        ]);

        $rowNumber = 4;
        foreach ($rows as $asset) {
            $sheet->fromArray([
                $asset->code,
                $asset->name,
                $asset->branch ?? 'Semarang 1',
                $asset->category?->name,
                $asset->items->pluck('room')->filter()->unique()->implode(', ') ?: ($asset->room ?? '-'),
                $asset->quantity,
                $asset->borrowed_quantity,
                $asset->availableQuantity(),
                (float) $asset->totalValue(),
            ], null, 'A'.$rowNumber);

            $rowNumber++;
        }

        $this->styleBody($sheet, 4, $rowNumber - 1, count($headers));
        $sheet->getStyle('I4:I'.max(4, $rowNumber - 1))->getNumberFormat()->setFormatCode('#,##0');
        $sheet->freezePane('A4');
    }

    /** @param  Collection<int, AssetLoan>  $rows */
    private function loansSheet(Collection $rows, array $filters): Spreadsheet
    {
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $this->populateLoansWorksheet($sheet, $rows, $filters);

        return $spreadsheet;
    }

    private function populateLoansWorksheet(Worksheet $sheet, Collection $rows, array $filters): void
    {
        $sheet->setTitle('Peminjaman');

        $headers = ['Kode Pinjam', 'Kode Aset', 'Nama Barang', 'Peminjam', 'Jumlah', 'Keperluan', 'Ruangan', 'Tgl Pinjam', 'Tenggat', 'Tgl Kembali', 'Status', 'Kondisi Kembali', 'Petugas'];

        $this->writeHeader($sheet, 'LAPORAN PEMINJAMAN ASET', $headers);
        $this->writeFilters($sheet, count($headers), $filters, [
            'from' => 'Tanggal dari',
            'to' => 'Tanggal sampai',
        ]);

        $rowNumber = 4;
        foreach ($rows as $loan) {
            $sheet->fromArray([
                $loan->code,
                $loan->asset?->code,
                $loan->asset?->name,
                $loan->borrower?->name,
                $loan->quantity,
                $loan->purpose,
                $loan->room ?? '-',
                $loan->borrowed_at?->format('d/m/Y H:i'),
                $loan->due_at?->format('d/m/Y'),
                $loan->returned_at?->format('d/m/Y H:i') ?? '-',
                $loan->status->label(),
                $loan->condition_on_return ? ucfirst(str_replace('_', ' ', $loan->condition_on_return)) : '-',
                $loan->handler?->name ?? '-',
            ], null, 'A'.$rowNumber);

            $rowNumber++;
        }

        $this->styleBody($sheet, 4, $rowNumber - 1, count($headers));
        $sheet->freezePane('A4');
    }

    /** @param  Collection<int, AssetMaintenance>  $rows */
    private function maintenancesSheet(Collection $rows, array $filters): Spreadsheet
    {
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $this->populateMaintenancesWorksheet($sheet, $rows, $filters);

        return $spreadsheet;
    }

    private function populateMaintenancesWorksheet(Worksheet $sheet, Collection $rows, array $filters): void
    {
        $sheet->setTitle('Service');

        $headers = ['Kode', 'Tanggal Service', 'PIC', 'Nama Kendaraan/Aset', 'Merk / Type', 'Tempat Service', 'Status Service', 'Keterangan Kerusakan', 'Status'];

        $this->writeHeader($sheet, 'LAPORAN SERVICE & PEMELIHARAAN KENDARAAN / ASET', $headers);
        $this->writeFilters($sheet, count($headers), $filters, ['q' => 'Pencarian']);

        $rowNumber = 4;
        foreach ($rows as $item) {
            $sheet->fromArray([
                $item->code,
                $item->service_date?->format('d/m/Y'),
                $item->pic_name,
                $item->asset_name,
                $item->brand_type,
                $item->service_location,
                $item->previously_serviced === 'sudah' ? 'Sudah Pernah' : 'Baru Pertama',
                $item->damage_description,
                ucfirst($item->status),
            ], null, 'A'.$rowNumber);
            $rowNumber++;
        }

        $this->styleBody($sheet, 4, $rowNumber - 1, count($headers));
        $sheet->freezePane('A4');
    }

    /** @param  Collection<int, AssetProcurement>  $rows */
    private function procurementsSheet(Collection $rows, array $filters): Spreadsheet
    {
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $this->populateProcurementsWorksheet($sheet, $rows, $filters);

        return $spreadsheet;
    }

    private function populateProcurementsWorksheet(Worksheet $sheet, Collection $rows, array $filters): void
    {
        $sheet->setTitle('Pengadaan');

        $headers = ['Kode', 'Tanggal Realisasi', 'PIC', 'Nama Barang', 'Merk / Type', 'Jumlah / Ukuran', 'Nama Toko / Link', 'Status'];

        $this->writeHeader($sheet, 'LAPORAN REALISASI PENGADAAN & ASET KANTOR', $headers);
        $this->writeFilters($sheet, count($headers), $filters, ['q' => 'Pencarian']);

        $rowNumber = 4;
        foreach ($rows as $item) {
            $sheet->fromArray([
                $item->code,
                $item->realization_date?->format('d/m/Y'),
                $item->pic_name,
                $item->item_name,
                $item->brand_type,
                $item->quantity_size,
                $item->store_name,
                ucfirst($item->status),
            ], null, 'A'.$rowNumber);
            $rowNumber++;
        }

        $this->styleBody($sheet, 4, $rowNumber - 1, count($headers));
        $sheet->freezePane('A4');
    }

    /** @param  Collection<int, EmployeeLeave>  $rows */
    private function leavesSheet(Collection $rows, array $filters): Spreadsheet
    {
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $this->populateLeavesWorksheet($sheet, $rows, $filters);

        return $spreadsheet;
    }

    private function populateLeavesWorksheet(Worksheet $sheet, Collection $rows, array $filters): void
    {
        $sheet->setTitle('Izin-Cuti-WFA');

        $headers = ['Kode', 'Tanggal Pengajuan', 'Nama Karyawan/Magang', 'Jenis Izin', 'Durasi (Hari)', 'Keterangan', 'Status'];

        $this->writeHeader($sheet, 'LAPORAN PENGAJUAN IZIN / CUTI / WFA', $headers);
        $this->writeFilters($sheet, count($headers), $filters, ['q' => 'Pencarian', 'type' => 'Jenis']);

        $rowNumber = 4;
        foreach ($rows as $item) {
            $sheet->fromArray([
                $item->code,
                $item->created_at?->format('d/m/Y'),
                $item->name,
                $item->leave_type,
                $item->days_count,
                $item->description,
                ucfirst($item->status),
            ], null, 'A'.$rowNumber);
            $rowNumber++;
        }

        $this->styleBody($sheet, 4, $rowNumber - 1, count($headers));
        $sheet->freezePane('A4');
    }

    /** @param  Collection<int, VehicleLoan>  $rows */
    private function vehicleLoansSheet(Collection $rows, array $filters): Spreadsheet
    {
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $this->populateVehicleLoansWorksheet($sheet, $rows, $filters);

        return $spreadsheet;
    }

    private function populateVehicleLoansWorksheet(Worksheet $sheet, Collection $rows, array $filters): void
    {
        $sheet->setTitle('Peminjaman Kendaraan');

        $headers = ['Kode', 'Tgl Berangkat', 'Tgl Kembali', 'PIC', 'Driver', 'Kendaraan', 'KM Berangkat', 'Tujuan', 'Kebersihan', 'Saran', 'Status'];

        $this->writeHeader($sheet, 'LAPORAN PEMINJAMAN KENDARAAN OPERASIONAL', $headers);
        $this->writeFilters($sheet, count($headers), $filters, ['q' => 'Pencarian', 'vehicle_type' => 'Kendaraan']);

        $rowNumber = 4;
        foreach ($rows as $item) {
            $sheet->fromArray([
                $item->code,
                $item->departure_date?->format('d/m/Y'),
                $item->return_date?->format('d/m/Y'),
                $item->pic_name,
                $item->driver_name,
                $item->vehicle_type,
                $item->departure_km,
                $item->purpose,
                ucfirst($item->cleanliness_condition),
                $item->suggestions ?: '-',
                ucfirst($item->status),
            ], null, 'A'.$rowNumber);
            $rowNumber++;
        }

        $this->styleBody($sheet, 4, $rowNumber - 1, count($headers));
        $sheet->freezePane('A4');
    }

    /** @param  Collection<int, MeetingAttendance>  $rows */
    private function attendancesSheet(Collection $rows, array $filters): Spreadsheet
    {
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $this->populateAttendancesWorksheet($sheet, $rows, $filters);

        return $spreadsheet;
    }

    private function populateAttendancesWorksheet(Worksheet $sheet, Collection $rows, array $filters): void
    {
        $sheet->setTitle('Daftar Hadir');

        $headers = ['Kode', 'Tanggal Kegiatan', 'Nama Peserta', 'Jabatan', 'Cabang', 'Jenis Kegiatan', 'Pimpinan / Pemateri', 'Ringkasan Materi'];

        $this->writeHeader($sheet, 'LAPORAN DAFTAR HADIR RAPAT & KEGIATAN', $headers);
        $this->writeFilters($sheet, count($headers), $filters, ['q' => 'Pencarian', 'branch' => 'Cabang', 'activity_type' => 'Kegiatan']);

        $rowNumber = 4;
        foreach ($rows as $item) {
            $sheet->fromArray([
                $item->code,
                $item->event_date?->format('d/m/Y'),
                $item->full_name,
                $item->position,
                $item->branch,
                $item->activity_type,
                $item->meeting_leader,
                $item->material_summary,
            ], null, 'A'.$rowNumber);
            $rowNumber++;
        }

        $this->styleBody($sheet, 4, $rowNumber - 1, count($headers));
        $sheet->freezePane('A4');
    }

    /** @param  Collection<int, ProjectContract>  $rows */
    private function contractsSheet(Collection $rows, array $filters): Spreadsheet
    {
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheetTitle = ! empty($filters['category_label']) ? 'Kontrak '.$filters['category_label'] : 'Rekap Kontrak SPK';
        $this->populateContractsWorksheet($sheet, $rows, $filters, $sheetTitle);

        return $spreadsheet;
    }

    private function populateContractsWorksheet(Worksheet $sheet, Collection $rows, array $filters, string $sheetTitle = 'Rekap Kontrak SPK'): void
    {
        $sheet->setTitle(substr($sheetTitle, 0, 31));

        $headers = [
            'No', 'No. SPK / PO', 'Tanggal SPK', 'Proyek / Kegiatan',
            'Nama Pihak Kedua', 'Jabatan / Uraian Pekerjaan', 'Nilai Kontrak (Rp)',
            'Durasi (Bulan)', 'Termin I (%)', 'Termin II (%)', 'Termin III (%)', 'Termin IV (%)',
            'Bank', 'Nomor Rekening', 'Atas Nama Rekening', 'Dokumen Teks / Link',
            'File Lampiran', 'Catatan Kontrak',
        ];

        $categoryLabel = ! empty($filters['category_label']) ? ' - '.$filters['category_label'] : '';
        $this->writeHeader($sheet, 'REKAPITULASI KONTRAK / SPK'.$categoryLabel, $headers);
        $this->writeFilters($sheet, count($headers), $filters, [
            'q' => 'Pencarian',
            'bank' => 'Bank',
            'from' => 'Dari Tanggal',
            'to' => 'Sampai Tanggal',
        ]);

        $rowNumber = 4;
        foreach ($rows as $index => $item) {
            $sheet->fromArray([
                $index + 1,
                $item->spk_number,
                $item->spk_date?->format('d/m/Y'),
                $item->project_name,
                $item->second_party_name,
                $item->job_description,
                (float) $item->contract_value,
                $item->duration_months,
                $item->termin_1 > 0 ? $item->termin_1.'%' : '0%',
                $item->termin_2 > 0 ? $item->termin_2.'%' : '0%',
                $item->termin_3 > 0 ? $item->termin_3.'%' : '0%',
                $item->termin_4 > 0 ? $item->termin_4.'%' : '0%',
                $item->bank_name,
                ' '.$item->bank_account_number,
                $item->bank_account_holder,
                $item->source_document_text ?: '-',
                $item->source_document_name ?: '-',
                $item->contract_notes ?: '-',
            ], null, 'A'.$rowNumber);
            $rowNumber++;
        }

        $this->styleBody($sheet, 4, $rowNumber - 1, count($headers));
        $sheet->getStyle('G4:G'.max(4, $rowNumber - 1))->getNumberFormat()->setFormatCode('#,##0');
        $sheet->freezePane('A4');
    }

    /** @param  array<int, string>  $headers */
    private function writeHeader(Worksheet $sheet, string $title, array $headers): void
    {
        $lastColumn = $this->columnLetter(count($headers));

        $sheet->mergeCells("A1:{$lastColumn}1");
        $sheet->setCellValue('A1', $title);
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

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

    /** @param  array<string, string>  $filters */
    private function writeFilters(Worksheet $sheet, int $columnCount, array $filters, array $labels): void
    {
        $parts = [];

        foreach ($labels as $key => $label) {
            if (! empty($filters[$key])) {
                $parts[] = "{$label}: {$filters[$key]}";
            }
        }

        $sheet->mergeCells('A2:'.$this->columnLetter($columnCount).'2');
        $sheet->setCellValue('A2', $parts ? implode('  |  ', $parts) : 'Semua data');
        $sheet->getStyle('A2')->getFont()->setItalic(true)->setSize(9);
    }

    private function styleBody(Worksheet $sheet, int $from, int $to, int $columnCount): void
    {
        if ($to < $from) {
            return;
        }

        $lastColumn = $this->columnLetter($columnCount);
        $sheet->getStyle("A{$from}:{$lastColumn}{$to}")
            ->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setARGB('FFE5E9E6');
    }

    private function columnLetter(int $index): string
    {
        return \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($index);
    }
}
