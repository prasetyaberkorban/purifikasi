<?php

namespace App\Http\Controllers;

use App\Models\ProjectContract;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProjectContractController extends Controller
{
    public function index(Request $request): View
    {
        $query = $this->buildQuery($request);

        $totalContracts = (clone $query)->count();
        $totalValue = (float) (clone $query)->sum('contract_value');

        return view('contracts.index', [
            'contracts' => $query->paginate(15)->withQueryString(),
            'search' => $request->string('q')->toString(),
            'bank' => $request->string('bank')->toString(),
            'from' => $request->string('from')->toString(),
            'to' => $request->string('to')->toString(),
            'totalContracts' => $totalContracts,
            'totalValue' => $totalValue,
        ]);
    }

    public function create(Request $request): View
    {
        $stickyProject = old('project_name', session('last_project_name', ''));

        return view('contracts.create', [
            'stickyProject' => $stickyProject,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        // Bersihkan format rupiah jika string mengandung titik/koma
        if ($request->has('contract_value')) {
            $cleaned = preg_replace('/[^0-9]/', '', (string) $request->input('contract_value'));
            $request->merge(['contract_value' => $cleaned !== '' ? (float) $cleaned : 0]);
        }

        $validated = $request->validate([
            'spk_number' => ['required', 'string', 'max:255'],
            'spk_date' => ['required', 'date'],
            'project_name' => ['required', 'string', 'max:255'],
            'second_party_name' => ['required', 'string', 'max:255'],
            'job_description' => ['required', 'string'],
            'contract_value' => ['required', 'numeric', 'min:0'],
            'duration_months' => ['required', 'integer', 'min:1'],
            'termin_1' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'termin_2' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'termin_3' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'bank_name' => ['required', 'string', 'max:255'],
            'bank_account_number' => ['required', 'string', 'max:255'],
            'bank_account_holder' => ['required', 'string', 'max:255'],
            'contract_notes' => ['nullable', 'string'],
            'source_document_text' => ['nullable', 'string'],
            'source_document_file' => ['nullable', 'file', 'max:20480'], // max 20MB
        ]);

        // Simpan nama proyek ke sesi untuk sticky input
        session(['last_project_name' => $validated['project_name']]);

        $filePath = null;
        $fileName = null;
        if ($request->hasFile('source_document_file')) {
            $file = $request->file('source_document_file');
            $filePath = $file->store('contracts', 'public');
            $fileName = $file->getClientOriginalName();
        }

        ProjectContract::create([
            'user_id' => $request->user()?->id,
            'spk_number' => $validated['spk_number'],
            'spk_date' => $validated['spk_date'],
            'project_name' => $validated['project_name'],
            'second_party_name' => $validated['second_party_name'],
            'job_description' => $validated['job_description'],
            'contract_value' => $validated['contract_value'],
            'duration_months' => $validated['duration_months'],
            'termin_1' => $validated['termin_1'] ?? 0,
            'termin_2' => $validated['termin_2'] ?? 0,
            'termin_3' => $validated['termin_3'] ?? 0,
            'bank_name' => $validated['bank_name'],
            'bank_account_number' => $validated['bank_account_number'],
            'bank_account_holder' => $validated['bank_account_holder'],
            'contract_notes' => $validated['contract_notes'] ?? null,
            'source_document_text' => $validated['source_document_text'] ?? null,
            'source_document_path' => $filePath,
            'source_document_name' => $fileName,
        ]);

        return redirect()->route('contracts.index')
            ->with('status', 'Data Rekapitulasi Kontrak / SPK berhasil ditambahkan.');
    }

    public function show(ProjectContract $contract): View
    {
        return view('contracts.show', [
            'contract' => $contract,
        ]);
    }

    public function edit(ProjectContract $contract): View
    {
        return view('contracts.edit', [
            'contract' => $contract,
        ]);
    }

    public function update(Request $request, ProjectContract $contract): RedirectResponse
    {
        if ($request->has('contract_value')) {
            $cleaned = preg_replace('/[^0-9]/', '', (string) $request->input('contract_value'));
            $request->merge(['contract_value' => $cleaned !== '' ? (float) $cleaned : 0]);
        }

        $validated = $request->validate([
            'spk_number' => ['required', 'string', 'max:255'],
            'spk_date' => ['required', 'date'],
            'project_name' => ['required', 'string', 'max:255'],
            'second_party_name' => ['required', 'string', 'max:255'],
            'job_description' => ['required', 'string'],
            'contract_value' => ['required', 'numeric', 'min:0'],
            'duration_months' => ['required', 'integer', 'min:1'],
            'termin_1' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'termin_2' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'termin_3' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'bank_name' => ['required', 'string', 'max:255'],
            'bank_account_number' => ['required', 'string', 'max:255'],
            'bank_account_holder' => ['required', 'string', 'max:255'],
            'contract_notes' => ['nullable', 'string'],
            'source_document_text' => ['nullable', 'string'],
            'source_document_file' => ['nullable', 'file', 'max:20480'],
        ]);

        session(['last_project_name' => $validated['project_name']]);

        $filePath = $contract->source_document_path;
        $fileName = $contract->source_document_name;

        if ($request->hasFile('source_document_file')) {
            if ($filePath && Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }
            $file = $request->file('source_document_file');
            $filePath = $file->store('contracts', 'public');
            $fileName = $file->getClientOriginalName();
        }

        $contract->update([
            'spk_number' => $validated['spk_number'],
            'spk_date' => $validated['spk_date'],
            'project_name' => $validated['project_name'],
            'second_party_name' => $validated['second_party_name'],
            'job_description' => $validated['job_description'],
            'contract_value' => $validated['contract_value'],
            'duration_months' => $validated['duration_months'],
            'termin_1' => $validated['termin_1'] ?? 0,
            'termin_2' => $validated['termin_2'] ?? 0,
            'termin_3' => $validated['termin_3'] ?? 0,
            'bank_name' => $validated['bank_name'],
            'bank_account_number' => $validated['bank_account_number'],
            'bank_account_holder' => $validated['bank_account_holder'],
            'contract_notes' => $validated['contract_notes'] ?? null,
            'source_document_text' => $validated['source_document_text'] ?? null,
            'source_document_path' => $filePath,
            'source_document_name' => $fileName,
        ]);

        return redirect()->route('contracts.index')
            ->with('status', 'Data Rekapitulasi Kontrak / SPK berhasil diperbarui.');
    }

    public function destroy(ProjectContract $contract): RedirectResponse
    {
        if ($contract->source_document_path && Storage::disk('public')->exists($contract->source_document_path)) {
            Storage::disk('public')->delete($contract->source_document_path);
        }

        $contract->delete();

        return redirect()->route('contracts.index')
            ->with('status', 'Data Kontrak / SPK berhasil dihapus.');
    }

    public function download(ProjectContract $contract): StreamedResponse
    {
        if (! $contract->source_document_path || ! Storage::disk('public')->exists($contract->source_document_path)) {
            abort(404, 'Lampiran tidak ditemukan.');
        }

        return Storage::disk('public')->download($contract->source_document_path, $contract->source_document_name);
    }

    public function exportPdf(Request $request): \Symfony\Component\HttpFoundation\Response
    {
        $rows = $this->buildQuery($request)->get();
        $filters = [
            'q' => $request->string('q')->toString(),
            'bank' => $request->string('bank')->toString(),
            'from' => $request->string('from')->toString(),
            'to' => $request->string('to')->toString(),
        ];

        $pdf = app(\App\Services\PdfReportService::class)->render('contracts', $rows, $filters);

        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="rekapitulasi-kontrak-spk-'.now()->format('Ymd-His').'.pdf"',
        ]);
    }

    public function exportExcel(Request $request): StreamedResponse
    {
        $rows = $this->buildQuery($request)->get();
        $filters = [
            'q' => $request->string('q')->toString(),
            'bank' => $request->string('bank')->toString(),
            'from' => $request->string('from')->toString(),
            'to' => $request->string('to')->toString(),
        ];

        return app(\App\Services\ExcelReportService::class)->stream('contracts', $rows, $filters);
    }

    private function buildQuery(Request $request)
    {
        $query = ProjectContract::query()->with('user')->latest('spk_date')->latest('id');

        if ($request->filled('q')) {
            $term = $request->string('q')->toString();
            $query->where(function ($q) use ($term) {
                $q->where('spk_number', 'like', "%{$term}%")
                    ->orWhere('project_name', 'like', "%{$term}%")
                    ->orWhere('second_party_name', 'like', "%{$term}%")
                    ->orWhere('job_description', 'like', "%{$term}%");
            });
        }

        if ($request->filled('bank')) {
            $bank = $request->string('bank')->toString();
            $query->where('bank_name', 'like', "%{$bank}%");
        }

        if ($request->filled('from')) {
            $query->whereDate('spk_date', '>=', $request->string('from')->toString());
        }

        if ($request->filled('to')) {
            $query->whereDate('spk_date', '<=', $request->string('to')->toString());
        }

        return $query;
    }
}
