<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetLoan;
use App\Models\AssetMaintenance;
use App\Models\AssetProcurement;
use App\Models\Category;
use App\Models\EmployeeLeave;
use App\Models\FatigueAssessment;
use App\Models\Location;
use App\Models\MeetingAttendance;
use App\Models\ProjectContract;
use App\Models\User;
use App\Models\VehicleLoan;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $this->filters($request);

        return view('reports.index', [
            'filters' => $filters,
            'rows' => $this->assetRows($filters),
            'loanRows' => $this->loanRows($filters),
            'totals' => $this->totals($filters),
        ]);
    }

    public function exportPdf(Request $request, string $type): \Symfony\Component\HttpFoundation\Response
    {
        $filters = $this->filters($request);
        $rows = $type === 'loans' ? $this->loanRows($filters) : $this->assetRows($filters);

        $pdf = app(\App\Services\PdfReportService::class)->render($type, $rows, $filters, $this->totals($filters));

        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="laporan-'.$type.'-'.now()->format('Ymd-His').'.pdf"',
        ]);
    }

    public function exportExcel(Request $request, string $type): StreamedResponse
    {
        $filters = $this->filters($request);
        $rows = $type === 'loans' ? $this->loanRows($filters) : $this->assetRows($filters);

        return app(\App\Services\ExcelReportService::class)->stream($type, $rows, $filters);
    }

    /**
     * Ekspor seluruh data dari semua modul di website menjadi satu file PDF konsolidasi.
     */
    public function exportAllPdf(Request $request): \Symfony\Component\HttpFoundation\Response
    {
        $datasets = $this->allDatasets();
        $filters = $this->filters($request);

        $pdf = app(\App\Services\PdfReportService::class)->renderAll($datasets, $filters);

        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="laporan-lengkap-semua-data-purifikasi-'.now()->format('Ymd-His').'.pdf"',
        ]);
    }

    /**
     * Ekspor seluruh data dari semua modul di website menjadi satu file Excel (multi-sheet).
     */
    public function exportAllExcel(Request $request): StreamedResponse
    {
        $datasets = $this->allDatasets();
        $filters = $this->filters($request);

        return app(\App\Services\ExcelReportService::class)->streamAll($datasets, $filters);
    }

    /**
     * @return array<string, Collection<int, mixed>>
     */
    private function allDatasets(): array
    {
        return [
            'assets' => Asset::query()->with(['category', 'location', 'items'])->orderBy('code')->get(),
            'loans' => AssetLoan::query()->with(['asset.category', 'borrower', 'handler'])->latestFirst()->get(),
            'maintenances' => AssetMaintenance::query()->with(['user', 'asset'])->latest()->get(),
            'procurements' => AssetProcurement::query()->with('user')->latest()->get(),
            'leaves' => EmployeeLeave::query()->with('user')->latest()->get(),
            'vehicleLoans' => VehicleLoan::query()->with('user')->latest()->get(),
            'attendances' => MeetingAttendance::query()->with('user')->latest()->get(),
            'contractsAmdal' => ProjectContract::query()->with('user')->where('category', 'amdal')->latest('spk_date')->latest('id')->get(),
            'contractsEngineering' => ProjectContract::query()->with('user')->where('category', 'engineering')->latest('spk_date')->latest('id')->get(),
            'fatigue' => FatigueAssessment::query()->with('user')->latest()->get(),
        ];
    }

    /** @return array<string, string> */
    private function filters(Request $request): array
    {
        return [
            'from' => $request->string('from')->toString(),
            'to' => $request->string('to')->toString(),
            'category' => $request->string('category')->toString(),
            'location' => $request->string('location')->toString(),
            'status' => $request->string('status')->toString(),
        ];
    }

    /** @return Collection<int, Asset> */
    private function assetRows(array $filters): Collection
    {
        return Asset::query()
            ->with(['category', 'location', 'items'])
            ->when($filters['category'] !== '', fn ($q) => $q->where('category_id', $filters['category']))
            ->when($filters['location'] !== '', fn ($q) => $q->where('location_id', $filters['location']))
            ->when($filters['status'] !== '', fn ($q) => $q->where('status', $filters['status']))
            ->orderBy('code')
            ->get();
    }

    /** @return Collection<int, AssetLoan> */
    private function loanRows(array $filters): Collection
    {
        return AssetLoan::query()
            ->with(['asset.category', 'borrower', 'handler'])
            ->when($filters['from'] !== '', fn ($q) => $q->whereDate('borrowed_at', '>=', $filters['from']))
            ->when($filters['to'] !== '', fn ($q) => $q->whereDate('borrowed_at', '<=', $filters['to']))
            ->latestFirst()
            ->get();
    }

    /** @return array<string, int|float> */
    private function totals(array $filters): array
    {
        $assets = $this->assetRows($filters);

        return [
            'units' => (int) $assets->sum('quantity'),
            'loans' => (int) $this->loanRows($filters)->where('status', \App\Enums\LoanStatus::Dipinjam)->sum('quantity'),
            'value' => (float) $assets->sum(fn ($a) => (float) $a->totalValue()),
            'skus' => $assets->count(),
        ];
    }

    public static function filterOptions(): array
    {
        return [
            'categories' => Category::query()->orderBy('name')->get(),
            'locations' => Location::query()->orderBy('name')->get(),
        ];
    }
}
