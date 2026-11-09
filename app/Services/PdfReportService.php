<?php

namespace App\Services;

use App\Models\Asset;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Collection;

class PdfReportService
{
    /**
     * @param  Collection<int, mixed>  $rows
     * @param  array<string, string>  $filters
     * @param  array<string, int|float>  $totals
     */
    public function render(string $type, Collection $rows, array $filters = [], array $totals = []): string
    {
        $viewMap = [
            'loans' => 'reports.pdf.loans',
            'maintenances' => 'reports.pdf.maintenances',
            'procurements' => 'reports.pdf.procurements',
            'leaves' => 'reports.pdf.leaves',
            'vehicle-loans' => 'reports.pdf.vehicle-loans',
            'attendances' => 'reports.pdf.attendances',
            'contracts' => 'reports.pdf.contracts',
        ];

        $view = $viewMap[$type] ?? 'reports.pdf.assets';

        $pdf = Pdf::loadView($view, [
            'rows' => $rows,
            'filters' => $filters,
            'totals' => $totals,
            'company' => Setting::get('company_name', 'PT. Purifikasi'),
            'unit' => Setting::get('company_unit', 'OFFICE DATABASE KANTOR'),
            'printedAt' => now(),
        ])->setPaper('a4', 'landscape');

        return $pdf->output();
    }

    /**
     * Render laporan konsolidasi seluruh modul di website ke dalam satu file PDF.
     *
     * @param  array<string, mixed>  $datasets
     * @param  array<string, string>  $filters
     */
    public function renderAll(array $datasets, array $filters = []): string
    {
        $pdf = Pdf::loadView('reports.pdf.all', [
            'datasets' => $datasets,
            'filters' => $filters,
            'company' => Setting::get('company_name', 'PT. Purifikasi'),
            'unit' => Setting::get('company_unit', 'OFFICE DATABASE KANTOR'),
            'printedAt' => now(),
        ])->setPaper('a4', 'landscape');

        return $pdf->output();
    }
}
