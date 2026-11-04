<?php

namespace App\Services;

use App\Models\Asset;
use App\Models\AssetLoan;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExcelReportService
{
    /**
     * @param  Collection<int, Asset|AssetLoan>  $rows
     * @param  array<string, string>  $filters
     */
    public function stream(string $type, Collection $rows, array $filters): StreamedResponse
    {
        $spreadsheet = $type === 'loans' ? $this->loansSheet($rows, $filters) : $this->assetsSheet($rows, $filters);

        $filename = 'laporan-'.$type.'-'.now()->format('Ymd-His').'.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            (new Xlsx($spreadsheet))->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    /** @param  Collection<int, Asset>  $rows */
    private function assetsSheet(Collection $rows, array $filters): Spreadsheet
    {
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Inventaris');

        $headers = ['Kode Aset', 'Nama Barang', 'Serial Number', 'Kategori', 'Lokasi', 'Ruangan', 'Unit Pemilik', 'Stok', 'Dipinjam', 'Tersedia', 'Stok Minimal', 'Status', 'Kondisi', 'Tgl Beli', 'Harga Beli', 'Nilai Total'];

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
                $asset->serial_number,
                $asset->category?->name,
                $asset->location?->label,
                $asset->items->pluck('room')->filter()->unique()->implode(', ') ?: ($asset->room ?? '-'),
                $asset->owner_unit,
                $asset->quantity,
                $asset->borrowed_quantity,
                $asset->availableQuantity(),
                $asset->minimum_stock,
                $asset->status->label(),
                $asset->condition->label(),
                $asset->purchase_date?->format('d/m/Y'),
                (float) $asset->purchase_price,
                (float) $asset->totalValue(),
            ], null, 'A'.$rowNumber);

            $rowNumber++;
        }

        $this->styleBody($sheet, 4, $rowNumber - 1, count($headers));
        $sheet->getStyle('O4:P'.max(4, $rowNumber - 1))->getNumberFormat()->setFormatCode('#,##0');
        $sheet->freezePane('A4');

        return $spreadsheet;
    }

    /** @param  Collection<int, AssetLoan>  $rows */
    private function loansSheet(Collection $rows, array $filters): Spreadsheet
    {
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
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
                $loan->room,
                $loan->borrowed_at?->format('d/m/Y'),
                $loan->due_at?->format('d/m/Y'),
                $loan->returned_at?->format('d/m/Y'),
                $loan->status->label(),
                $loan->condition_on_return?->label(),
                $loan->handler?->name,
            ], null, 'A'.$rowNumber);

            $rowNumber++;
        }

        $this->styleBody($sheet, 4, $rowNumber - 1, count($headers));
        $sheet->freezePane('A4');

        return $spreadsheet;
    }

    /** @param  array<int, string>  $headers */
    private function writeHeader(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet, string $title, array $headers): void
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
    private function writeFilters(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet, int $columnCount, array $filters, array $labels): void
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

    private function styleBody(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet, int $from, int $to, int $columnCount): void
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