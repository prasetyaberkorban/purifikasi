<?php

namespace App\Services;

use App\Models\Asset;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Collection;

class PdfReportService
{
    /**
     * @param  Collection<int, Asset|AssetLoan>  $rows
     * @param  array<string, string>  $filters
     * @param  array<string, int|float>  $totals
     */
    public function render(string $type, Collection $rows, array $filters, array $totals): string
    {
        $pdf = Pdf::loadView($type === 'loans' ? 'reports.pdf.loans' : 'reports.pdf.assets', [
            'rows' => $rows,
            'filters' => $filters,
            'totals' => $totals,
            'company' => Setting::get('company_name', 'PT. Purifikasi'),
            'unit' => Setting::get('company_unit', 'OFFICE DATABASE KANTOR'),
            'printedAt' => now(),
        ])->setPaper('a4', 'landscape');

        return $pdf->output();
    }
}