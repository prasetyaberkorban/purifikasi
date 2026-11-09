<?php

namespace App\Http\Controllers;

use App\Models\EmployeeLeave;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class EmployeeLeaveController extends Controller
{
    public function index(Request $request): View
    {
        return view('leaves.index', [
            'leaves' => $this->buildQuery($request)->paginate(12)->withQueryString(),
            'search' => $request->string('q')->toString(),
            'selectedType' => $request->string('type')->toString(),
            'leaveTypes' => EmployeeLeave::leaveTypeOptions(),
        ]);
    }

    public function exportPdf(Request $request): \Symfony\Component\HttpFoundation\Response
    {
        $rows = $this->buildQuery($request)->get();
        $filters = [
            'q' => $request->string('q')->toString(),
            'type' => $request->string('type')->toString(),
        ];
        $pdf = app(\App\Services\PdfReportService::class)->render('leaves', $rows, $filters);

        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="laporan-izin-cuti-'.now()->format('Ymd-His').'.pdf"',
        ]);
    }

    public function exportExcel(Request $request): StreamedResponse
    {
        $rows = $this->buildQuery($request)->get();
        $filters = [
            'q' => $request->string('q')->toString(),
            'type' => $request->string('type')->toString(),
        ];

        return app(\App\Services\ExcelReportService::class)->stream('leaves', $rows, $filters);
    }

    private function buildQuery(Request $request)
    {
        $query = EmployeeLeave::query()->with('user')->latest();

        if ($request->filled('q')) {
            $term = $request->string('q')->toString();
            $query->where(function ($q) use ($term) {
                $q->where('code', 'like', "%{$term}%")
                    ->orWhere('name', 'like', "%{$term}%")
                    ->orWhere('leave_type', 'like', "%{$term}%")
                    ->orWhere('description', 'like', "%{$term}%");
            });
        }

        if ($request->filled('type')) {
            $query->where('leave_type', $request->string('type')->toString());
        }

        if (! $request->user()->isAdmin()) {
            $query->where('user_id', $request->user()->id);
        }

        return $query;
    }

    public function create(): View
    {
        return view('leaves.create', [
            'leaveTypes' => EmployeeLeave::leaveTypeOptions(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'leave_type' => ['required', 'in:Cuti,Tahunan,Izin sakit,Izin keperluan pribadi,Izin pelatihan / sertifikasi,WFA'],
            'days_count' => ['required', 'integer', 'min:1'],
            'description' => ['required', 'string'],
            'attachment' => ['nullable', 'file', 'max:15360'],
        ], [
            'name.required' => 'Nama wajib diisi.',
            'leave_type.required' => 'Jenis izin yang diambil wajib dipilih.',
            'leave_type.in' => 'Pilihan jenis izin tidak valid.',
            'days_count.required' => 'Jumlah hari izin / cuti / WFA wajib diisi.',
            'days_count.min' => 'Jumlah hari minimal 1 hari.',
            'description.required' => 'Keterangan wajib diisi.',
            'attachment.max' => 'Ukuran file lampiran maksimal 15 MB.',
        ]);

        $storedPath = null;
        $originalName = null;
        $fileSize = 0;

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $originalName = $file->getClientOriginalName();
            $fileSize = $file->getSize();
            $storedPath = $file->store('leaves/attachments', 'public');
        }

        $leave = EmployeeLeave::create([
            'code' => EmployeeLeave::nextCode(),
            'user_id' => $request->user()->id,
            'name' => $validated['name'],
            'leave_type' => $validated['leave_type'],
            'days_count' => $validated['days_count'],
            'description' => $validated['description'],
            'attachment_path' => $storedPath,
            'attachment_name' => $originalName,
            'attachment_size' => $fileSize,
            'status' => 'diajukan',
        ]);

        return redirect()
            ->route('leaves.show', $leave)
            ->with('status', "Pengajuan {$leave->code} ({$leave->leave_type}) berhasil dikirim.");
    }

    public function show(EmployeeLeave $leave): View
    {
        if (! auth()->user()->isAdmin() && $leave->user_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki hak akses untuk riwayat pengajuan ini.');
        }

        $leave->load('user');

        return view('leaves.show', [
            'leave' => $leave,
        ]);
    }

    public function download(EmployeeLeave $leave): StreamedResponse
    {
        if (! auth()->user()->isAdmin() && $leave->user_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki hak akses untuk lampiran ini.');
        }

        if (! $leave->attachment_path || ! Storage::disk('public')->exists($leave->attachment_path)) {
            abort(404, 'File lampiran tidak ditemukan.');
        }

        return Storage::disk('public')->download(
            $leave->attachment_path,
            $leave->attachment_name
        );
    }

    public function destroy(Request $request, EmployeeLeave $leave): RedirectResponse
    {
        if (! $request->user()->isAdmin() && $leave->user_id !== $request->user()->id) {
            abort(403, 'Anda tidak memiliki hak akses untuk menghapus riwayat izin/cuti ini.');
        }

        if ($leave->attachment_path) {
            Storage::disk('public')->delete($leave->attachment_path);
        }

        $code = $leave->code;
        $leave->delete();

        return redirect()->route('leaves.index')->with('status', "Riwayat izin/cuti {$code} berhasil dihapus.");
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
            return back()->withErrors(['ids' => 'Tidak ada data izin/cuti yang dipilih.']);
        }

        $query = EmployeeLeave::whereIn('id', $ids);
        if (! $request->user()->isAdmin()) {
            $query->where('user_id', $request->user()->id);
        }

        $items = $query->get();
        $count = 0;
        foreach ($items as $item) {
            if ($item->attachment_path) {
                Storage::disk('public')->delete($item->attachment_path);
            }
            $item->delete();
            $count++;
        }

        return back()->with('status', "Berhasil menghapus {$count} data riwayat izin/cuti terpilih.");
    }
}
