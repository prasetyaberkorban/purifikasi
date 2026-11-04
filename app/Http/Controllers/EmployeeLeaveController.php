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

        return view('leaves.index', [
            'leaves' => $query->paginate(12)->withQueryString(),
            'search' => $request->string('q')->toString(),
            'selectedType' => $request->string('type')->toString(),
            'leaveTypes' => EmployeeLeave::leaveTypeOptions(),
        ]);
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
        $leave->load('user');

        return view('leaves.show', [
            'leave' => $leave,
        ]);
    }

    public function download(EmployeeLeave $leave): StreamedResponse
    {
        if (! $leave->attachment_path || ! Storage::disk('public')->exists($leave->attachment_path)) {
            abort(404, 'File lampiran tidak ditemukan.');
        }

        return Storage::disk('public')->download(
            $leave->attachment_path,
            $leave->attachment_name
        );
    }
}
