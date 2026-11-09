<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetActivity;
use App\Models\AssetMaintenance;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AssetMaintenanceController extends Controller
{
    public function index(Request $request): View
    {
        return view('maintenances.index', [
            'maintenances' => $this->buildQuery($request)->paginate(12)->withQueryString(),
            'search' => $request->string('q')->toString(),
        ]);
    }

    public function exportPdf(Request $request): \Symfony\Component\HttpFoundation\Response
    {
        $rows = $this->buildQuery($request)->get();
        $filters = ['q' => $request->string('q')->toString()];
        $pdf = app(\App\Services\PdfReportService::class)->render('maintenances', $rows, $filters);

        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="laporan-service-'.now()->format('Ymd-His').'.pdf"',
        ]);
    }

    public function exportExcel(Request $request): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $rows = $this->buildQuery($request)->get();
        $filters = ['q' => $request->string('q')->toString()];

        return app(\App\Services\ExcelReportService::class)->stream('maintenances', $rows, $filters);
    }

    private function buildQuery(Request $request)
    {
        $query = AssetMaintenance::query()->with(['user', 'asset'])->latest();

        if ($request->filled('q')) {
            $term = $request->string('q')->toString();
            $query->where(function ($q) use ($term) {
                $q->where('code', 'like', "%{$term}%")
                    ->orWhere('pic_name', 'like', "%{$term}%")
                    ->orWhere('asset_name', 'like', "%{$term}%")
                    ->orWhere('brand_type', 'like', "%{$term}%")
                    ->orWhere('service_location', 'like', "%{$term}%");
            });
        }

        if (! $request->user()->isAdmin()) {
            $query->where('user_id', $request->user()->id);
        }

        return $query;
    }

    public function create(Request $request): View
    {
        return view('maintenances.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'pic_name' => ['required', 'string', 'max:255'],
            'asset_name' => ['required', 'string', 'max:255'],
            'brand_type' => ['required', 'string', 'max:255'],
            'damage_description' => ['required', 'string'],
            'previously_serviced' => ['required', 'in:sudah,belum'],
            'service_date' => ['required', 'date'],
            'service_location' => ['required', 'string', 'max:500'],
            'condition_photo' => ['required', 'image', 'max:5120'],
            'invoice_photo' => ['required', 'image', 'max:5120'],
            'asset_id' => ['nullable', 'exists:assets,id'],
        ], [
            'pic_name.required' => 'Nama Pengguna PIC wajib diisi.',
            'asset_name.required' => 'Nama Aset / Barang wajib diisi.',
            'brand_type.required' => 'Merk / Type wajib diisi.',
            'damage_description.required' => 'Deskripsi Kerusakan / Keluhan wajib diisi.',
            'previously_serviced.required' => 'Pilihan riwayat service sebelumnya wajib dipilih.',
            'service_date.required' => 'Tanggal service wajib diisi.',
            'service_location.required' => 'Alamat Tempat Service wajib diisi.',
            'condition_photo.required' => 'Dokumentasi Kondisi Aset wajib diupload.',
            'invoice_photo.required' => 'Nota Service / Pemeliharaan wajib diupload.',
        ]);

        $conditionPhotoPath = $request->file('condition_photo')->store('maintenances/conditions', 'public');
        $invoicePhotoPath = $request->file('invoice_photo')->store('maintenances/invoices', 'public');

        $maintenance = AssetMaintenance::create([
            'code' => AssetMaintenance::nextCode(),
            'user_id' => $request->user()->id,
            'asset_id' => $validated['asset_id'] ?? null,
            'pic_name' => $validated['pic_name'],
            'asset_name' => $validated['asset_name'],
            'brand_type' => $validated['brand_type'],
            'damage_description' => $validated['damage_description'],
            'previously_serviced' => $validated['previously_serviced'],
            'service_date' => $validated['service_date'],
            'service_location' => $validated['service_location'],
            'condition_photo_path' => $conditionPhotoPath,
            'invoice_photo_path' => $invoicePhotoPath,
            'status' => 'selesai',
        ]);

        if (! empty($validated['asset_id'])) {
            $asset = Asset::find($validated['asset_id']);
            if ($asset) {
                AssetActivity::create([
                    'asset_id' => $asset->id,
                    'user_id' => $request->user()->id,
                    'type' => 'condition',
                    'subject' => $maintenance->code,
                    'description' => "Laporan service & pemeliharaan dicatat oleh {$maintenance->pic_name} ({$maintenance->code}).",
                ]);
            }
        }

        return redirect()
            ->route('services.show', $maintenance)
            ->with('status', "Formulir service {$maintenance->code} untuk '{$maintenance->asset_name}' berhasil disimpan.");
    }

    public function show(AssetMaintenance $maintenance): View
    {
        if (! auth()->user()->isAdmin() && $maintenance->user_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki hak akses untuk riwayat service ini.');
        }

        $maintenance->load(['user', 'asset']);

        return view('maintenances.show', [
            'maintenance' => $maintenance,
        ]);
    }

    public function destroy(Request $request, AssetMaintenance $maintenance): RedirectResponse
    {
        if (! $request->user()->isAdmin() && $maintenance->user_id !== $request->user()->id) {
            abort(403, 'Anda tidak memiliki hak akses untuk menghapus riwayat service ini.');
        }

        if ($maintenance->condition_photo_path) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($maintenance->condition_photo_path);
        }
        if ($maintenance->invoice_photo_path) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($maintenance->invoice_photo_path);
        }

        $code = $maintenance->code;
        $maintenance->delete();

        return redirect()->route('services.index')->with('status', "Riwayat service {$code} berhasil dihapus.");
    }

    public function bulkDestroy(Request $request): RedirectResponse
    {
        $rawIds = $request->input('ids', []);
        if (is_string($rawIds)) {
            $rawIds = explode(',', $rawIds);
        }
        if ($request->filled('ids_csv')) {
            $rawIds = array_merge((array) $rawIds, explode(',', (string) $request->input('ids_csv')));
        }
        $ids = array_filter(array_unique(array_map('intval', (array) $rawIds)));

        if (empty($ids)) {
            return back()->withErrors(['ids' => 'Tidak ada data service yang dipilih.']);
        }

        $query = AssetMaintenance::whereIn('id', $ids);
        if (! $request->user()->isAdmin()) {
            $query->where('user_id', $request->user()->id);
        }

        $items = $query->get();
        $count = 0;
        foreach ($items as $item) {
            if ($item->condition_photo_path) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($item->condition_photo_path);
            }
            if ($item->invoice_photo_path) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($item->invoice_photo_path);
            }
            $item->delete();
            $count++;
        }

        return back()->with('status', "Berhasil menghapus {$count} data riwayat service terpilih.");
    }
}
