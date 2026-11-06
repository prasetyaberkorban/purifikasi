<?php

namespace App\Http\Controllers;

use App\Models\AssetProcurement;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AssetProcurementController extends Controller
{
    public function index(Request $request): View
    {
        return view('procurements.index', [
            'procurements' => $this->buildQuery($request)->paginate(12)->withQueryString(),
            'search' => $request->string('q')->toString(),
        ]);
    }

    public function exportPdf(Request $request): \Symfony\Component\HttpFoundation\Response
    {
        $rows = $this->buildQuery($request)->get();
        $filters = ['q' => $request->string('q')->toString()];
        $pdf = app(\App\Services\PdfReportService::class)->render('procurements', $rows, $filters);

        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="laporan-realisasi-'.now()->format('Ymd-His').'.pdf"',
        ]);
    }

    public function exportExcel(Request $request): StreamedResponse
    {
        $rows = $this->buildQuery($request)->get();
        $filters = ['q' => $request->string('q')->toString()];

        return app(\App\Services\ExcelReportService::class)->stream('procurements', $rows, $filters);
    }

    private function buildQuery(Request $request)
    {
        $query = AssetProcurement::query()->with('user')->latest();

        if ($request->filled('q')) {
            $term = $request->string('q')->toString();
            $query->where(function ($q) use ($term) {
                $q->where('code', 'like', "%{$term}%")
                    ->orWhere('pic_name', 'like', "%{$term}%")
                    ->orWhere('item_name', 'like', "%{$term}%")
                    ->orWhere('brand_type', 'like', "%{$term}%")
                    ->orWhere('store_name', 'like', "%{$term}%")
                    ->orWhere('quantity_size', 'like', "%{$term}%");
            });
        }

        if (! $request->user()->isAdmin()) {
            $query->where('user_id', $request->user()->id);
        }

        return $query;
    }

    public function create(): View
    {
        return view('procurements.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'pic_name' => ['required', 'string', 'max:255'],
            'realization_date' => ['required', 'date'],
            'item_name' => ['required', 'string', 'max:255'],
            'brand_type' => ['required', 'string', 'max:255'],
            'quantity_size' => ['required', 'string', 'max:255'],
            'store_name' => ['required', 'string'],
            'proof_file' => [
                'required',
                'file',
                'mimes:pdf,doc,docx,odt,rtf,txt,xls,xlsx,csv,ods',
                'max:15360', // max 15MB
            ],
        ], [
            'pic_name.required' => 'PIC wajib diisi.',
            'realization_date.required' => 'Tanggal Realisasi wajib diisi.',
            'item_name.required' => 'Nama Barang wajib diisi.',
            'brand_type.required' => 'Merk Type wajib diisi.',
            'quantity_size.required' => 'Jumlah / Ukuran wajib diisi.',
            'store_name.required' => 'Nama Toko (Isi link jika beli online) wajib diisi.',
            'proof_file.required' => 'Bukti Realisasi wajib diunggah.',
            'proof_file.mimes' => 'Bukti Realisasi harus berupa file PDF, dokumen (DOC, DOCX), atau spreadsheet (XLS, XLSX, CSV).',
            'proof_file.max' => 'Ukuran file bukti realisasi maksimal 15 MB.',
        ]);

        $file = $request->file('proof_file');
        $originalName = $file->getClientOriginalName();
        $fileSize = $file->getSize();
        $mimeType = $file->getClientMimeType();
        $storedPath = $file->store('procurements/proofs', 'public');

        $procurement = AssetProcurement::create([
            'code' => AssetProcurement::nextCode(),
            'user_id' => $request->user()->id,
            'pic_name' => $validated['pic_name'],
            'realization_date' => $validated['realization_date'],
            'item_name' => $validated['item_name'],
            'brand_type' => $validated['brand_type'],
            'quantity_size' => $validated['quantity_size'],
            'store_name' => $validated['store_name'],
            'proof_file_path' => $storedPath,
            'proof_file_name' => $originalName,
            'proof_file_type' => $mimeType,
            'proof_file_size' => $fileSize,
            'status' => 'selesai',
        ]);

        return redirect()
            ->route('procurements.show', $procurement)
            ->with('status', "Formulir realisasi {$procurement->code} untuk '{$procurement->item_name}' berhasil disimpan.");
    }

    public function show(AssetProcurement $procurement): View
    {
        $procurement->load('user');

        return view('procurements.show', [
            'procurement' => $procurement,
        ]);
    }

    public function download(AssetProcurement $procurement): StreamedResponse
    {
        if (! Storage::disk('public')->exists($procurement->proof_file_path)) {
            abort(404, 'File bukti realisasi tidak ditemukan.');
        }

        return Storage::disk('public')->download(
            $procurement->proof_file_path,
            $procurement->proof_file_name
        );
    }
}
