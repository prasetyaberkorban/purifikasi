<?php

namespace App\Http\Controllers;

use App\Models\ProjectContract;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProjectContractController extends Controller
{
    public function index(Request $request): View
    {
        $this->ensureCategoryColumnExists();
        $this->ensureTermin4ColumnExists();

        $category = $this->resolveCategory($request);
        $query = $this->buildQuery($request, $category);

        $totalContracts = (clone $query)->count();
        $totalValue = (float) (clone $query)->sum('contract_value');

        return view('contracts.index', [
            'category' => $category,
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
        $this->ensureCategoryColumnExists();
        $this->ensureTermin4ColumnExists();

        $category = $this->resolveCategory($request);
        $stickyProject = old('project_name', session("last_project_name_{$category}", ''));

        $existingProjects = ProjectContract::query()
            ->where('category', $category)
            ->whereNotNull('project_name')
            ->where('project_name', '!=', '')
            ->distinct()
            ->orderBy('project_name')
            ->pluck('project_name');

        return view('contracts.create', [
            'category' => $category,
            'stickyProject' => $stickyProject,
            'existingProjects' => $existingProjects,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->ensureCategoryColumnExists();
        $this->ensureTermin4ColumnExists();

        // Bersihkan format rupiah jika string mengandung titik/koma
        if ($request->has('contract_value')) {
            $cleaned = preg_replace('/[^0-9]/', '', (string) $request->input('contract_value'));
            $request->merge(['contract_value' => $cleaned !== '' ? (float) $cleaned : 0]);
        }

        $validated = $request->validate([
            'category' => ['required', 'in:amdal,engineering'],
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
            'termin_4' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'bank_name' => ['required', 'string', 'max:255'],
            'bank_account_number' => ['required', 'string', 'max:255'],
            'bank_account_holder' => ['required', 'string', 'max:255'],
            'contract_notes' => ['nullable', 'string'],
            'source_document_text' => ['nullable', 'string'],
            'source_document_file' => ['nullable', 'file', 'max:20480'], // max 20MB
        ]);

        $category = $validated['category'];

        // Simpan nama proyek ke sesi untuk sticky input sesuai kategori
        session(["last_project_name_{$category}" => $validated['project_name']]);

        $filePath = null;
        $fileName = null;
        if ($request->hasFile('source_document_file')) {
            $file = $request->file('source_document_file');
            $filePath = $file->store('contracts', 'public');
            $fileName = $file->getClientOriginalName();
        }

        ProjectContract::create([
            'user_id' => $request->user()?->id,
            'category' => $category,
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
            'termin_4' => $validated['termin_4'] ?? 0,
            'bank_name' => $validated['bank_name'],
            'bank_account_number' => $validated['bank_account_number'],
            'bank_account_holder' => $validated['bank_account_holder'],
            'contract_notes' => $validated['contract_notes'] ?? null,
            'source_document_text' => $validated['source_document_text'] ?? null,
            'source_document_path' => $filePath,
            'source_document_name' => $fileName,
        ]);

        $label = $category === 'engineering' ? 'Engineering' : 'AMDAL';

        return redirect()->route('contracts.index', ['category' => $category])
            ->with('status', "Data Rekapitulasi Kontrak / SPK ({$label}) berhasil ditambahkan.");
    }

    public function show(Request $request, ProjectContract $contract): View
    {
        $this->authorizeAccess($request, $contract);

        return view('contracts.show', [
            'contract' => $contract,
        ]);
    }

    public function edit(Request $request, ProjectContract $contract): View
    {
        $this->authorizeAccess($request, $contract);

        $existingProjects = ProjectContract::query()
            ->where('category', $contract->category ?? 'amdal')
            ->whereNotNull('project_name')
            ->where('project_name', '!=', '')
            ->distinct()
            ->orderBy('project_name')
            ->pluck('project_name');

        return view('contracts.edit', [
            'contract' => $contract,
            'category' => $contract->category ?? 'amdal',
            'existingProjects' => $existingProjects,
        ]);
    }

    public function update(Request $request, ProjectContract $contract): RedirectResponse
    {
        $this->authorizeAccess($request, $contract);

        if ($request->has('contract_value')) {
            $cleaned = preg_replace('/[^0-9]/', '', (string) $request->input('contract_value'));
            $request->merge(['contract_value' => $cleaned !== '' ? (float) $cleaned : 0]);
        }

        $validated = $request->validate([
            'category' => ['required', 'in:amdal,engineering'],
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
            'termin_4' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'bank_name' => ['required', 'string', 'max:255'],
            'bank_account_number' => ['required', 'string', 'max:255'],
            'bank_account_holder' => ['required', 'string', 'max:255'],
            'contract_notes' => ['nullable', 'string'],
            'source_document_text' => ['nullable', 'string'],
            'source_document_file' => ['nullable', 'file', 'max:20480'],
            'remove_source_file' => ['nullable', 'boolean'],
        ]);

        $filePath = $contract->source_document_path;
        $fileName = $contract->source_document_name;

        if ($request->boolean('remove_source_file') && $filePath) {
            Storage::disk('public')->delete($filePath);
            $filePath = null;
            $fileName = null;
        }

        if ($request->hasFile('source_document_file')) {
            if ($filePath) {
                Storage::disk('public')->delete($filePath);
            }
            $file = $request->file('source_document_file');
            $filePath = $file->store('contracts', 'public');
            $fileName = $file->getClientOriginalName();
        }

        $contract->update([
            'category' => $validated['category'],
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
            'termin_4' => $validated['termin_4'] ?? 0,
            'bank_name' => $validated['bank_name'],
            'bank_account_number' => $validated['bank_account_number'],
            'bank_account_holder' => $validated['bank_account_holder'],
            'contract_notes' => $validated['contract_notes'] ?? null,
            'source_document_text' => $validated['source_document_text'] ?? null,
            'source_document_path' => $filePath,
            'source_document_name' => $fileName,
        ]);

        return redirect()->route('contracts.index', ['category' => $contract->category])
            ->with('status', 'Data Rekapitulasi Kontrak / SPK berhasil diperbarui.');
    }

    public function destroy(Request $request, ProjectContract $contract): RedirectResponse
    {
        $this->authorizeAccess($request, $contract);
        $category = $contract->category ?? 'amdal';

        if ($contract->source_document_path) {
            Storage::disk('public')->delete($contract->source_document_path);
        }

        $contract->delete();

        return redirect()->route('contracts.index', ['category' => $category])
            ->with('status', 'Data Kontrak / SPK berhasil dihapus.');
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
            return back()->withErrors(['ids' => 'Tidak ada data kontrak yang dipilih.']);
        }

        $query = ProjectContract::whereIn('id', $ids);
        if (! $request->user()->isAdmin()) {
            $query->where('user_id', $request->user()->id);
        }

        $contracts = $query->get();
        $count = 0;
        foreach ($contracts as $contract) {
            if ($contract->source_document_path) {
                Storage::disk('public')->delete($contract->source_document_path);
            }
            $contract->delete();
            $count++;
        }

        return back()->with('status', "Berhasil menghapus {$count} data kontrak / SPK terpilih.");
    }

    public function download(Request $request, ProjectContract $contract): StreamedResponse
    {
        $this->authorizeAccess($request, $contract);

        if (! $contract->source_document_path || ! Storage::disk('public')->exists($contract->source_document_path)) {
            abort(404, 'Lampiran tidak ditemukan.');
        }

        return Storage::disk('public')->download($contract->source_document_path, $contract->source_document_name);
    }

    public function exportPdf(Request $request): \Symfony\Component\HttpFoundation\Response
    {
        $category = $this->resolveCategory($request);
        $rows = $this->buildQuery($request, $category)->get();
        $filters = [
            'category' => $category,
            'category_label' => $category === 'engineering' ? 'ENGINEERING' : 'AMDAL',
            'q' => $request->string('q')->toString(),
            'bank' => $request->string('bank')->toString(),
            'from' => $request->string('from')->toString(),
            'to' => $request->string('to')->toString(),
        ];

        $pdf = app(\App\Services\PdfReportService::class)->render('contracts', $rows, $filters);

        $filename = "rekapitulasi-kontrak-spk-{$category}-".now()->format('Ymd-His').'.pdf';

        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    public function exportExcel(Request $request): StreamedResponse
    {
        $category = $this->resolveCategory($request);
        $rows = $this->buildQuery($request, $category)->get();
        $filters = [
            'category' => $category,
            'category_label' => $category === 'engineering' ? 'ENGINEERING' : 'AMDAL',
            'q' => $request->string('q')->toString(),
            'bank' => $request->string('bank')->toString(),
            'from' => $request->string('from')->toString(),
            'to' => $request->string('to')->toString(),
        ];

        return app(\App\Services\ExcelReportService::class)->stream('contracts', $rows, $filters);
    }

    private function resolveCategory(Request $request): string
    {
        $cat = $request->string('category', 'amdal')->toString();
        if ($cat === '' && $request->routeIs('contracts.engineering')) {
            $cat = 'engineering';
        }

        return in_array($cat, ['amdal', 'engineering'], true) ? $cat : 'amdal';
    }

    private function buildQuery(Request $request, ?string $category = null)
    {
        $category = $category ?? $this->resolveCategory($request);

        $query = ProjectContract::query()
            ->with('user')
            ->where('category', $category)
            ->latest('spk_date')
            ->latest('id');

        // Batasi akses: Hanya admin yang bisa melihat semua data, user biasa hanya miliknya sendiri
        if (! $request->user()->isAdmin()) {
            $query->where('user_id', $request->user()->id);
        }

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

    private function authorizeAccess(Request $request, ProjectContract $contract): void
    {
        if (! $request->user()->isAdmin() && $contract->user_id !== $request->user()->id) {
            abort(403, 'Anda tidak memiliki hak akses untuk data kontrak ini.');
        }
    }

    private function ensureCategoryColumnExists(): void
    {
        if (Schema::hasTable('project_contracts') && ! Schema::hasColumn('project_contracts', 'category')) {
            Schema::table('project_contracts', function (Blueprint $table) {
                $table->string('category', 50)->default('amdal')->index()->after('id');
            });
        }
    }

    private function ensureTermin4ColumnExists(): void
    {
        if (Schema::hasTable('project_contracts') && ! Schema::hasColumn('project_contracts', 'termin_4')) {
            Schema::table('project_contracts', function (Blueprint $table) {
                $table->decimal('termin_4', 6, 2)->nullable()->default(0)->after('termin_3');
            });
        }
    }
}
