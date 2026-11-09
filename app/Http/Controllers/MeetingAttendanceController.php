<?php

namespace App\Http\Controllers;

use App\Models\MeetingAttendance;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class MeetingAttendanceController extends Controller
{
    public function index(Request $request): View
    {
        return view('attendances.index', [
            'attendances' => $this->buildQuery($request)->paginate(15)->withQueryString(),
            'search' => $request->string('q')->toString(),
            'selectedBranch' => $request->string('branch')->toString(),
            'selectedActivity' => $request->string('activity_type')->toString(),
            'branchOptions' => MeetingAttendance::branchOptions(),
            'activityOptions' => MeetingAttendance::activityTypeOptions(),
        ]);
    }

    public function exportPdf(Request $request): \Symfony\Component\HttpFoundation\Response
    {
        $rows = $this->buildQuery($request)->get();
        $filters = [
            'q' => $request->string('q')->toString(),
            'branch' => $request->string('branch')->toString(),
            'activity_type' => $request->string('activity_type')->toString(),
        ];
        $pdf = app(\App\Services\PdfReportService::class)->render('attendances', $rows, $filters);

        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="laporan-daftar-hadir-'.now()->format('Ymd-His').'.pdf"',
        ]);
    }

    public function exportExcel(Request $request): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $rows = $this->buildQuery($request)->get();
        $filters = [
            'q' => $request->string('q')->toString(),
            'branch' => $request->string('branch')->toString(),
            'activity_type' => $request->string('activity_type')->toString(),
        ];

        return app(\App\Services\ExcelReportService::class)->stream('attendances', $rows, $filters);
    }

    private function buildQuery(Request $request)
    {
        $query = MeetingAttendance::query()->with('user')->latest();

        if ($request->filled('q')) {
            $term = $request->string('q')->toString();
            $query->where(function ($q) use ($term) {
                $q->where('code', 'like', "%{$term}%")
                    ->orWhere('full_name', 'like', "%{$term}%")
                    ->orWhere('position', 'like', "%{$term}%")
                    ->orWhere('branch', 'like', "%{$term}%")
                    ->orWhere('activity_type', 'like', "%{$term}%")
                    ->orWhere('meeting_leader', 'like', "%{$term}%")
                    ->orWhere('material_summary', 'like', "%{$term}%");
            });
        }

        if ($request->filled('branch')) {
            $query->where('branch', $request->string('branch')->toString());
        }

        if ($request->filled('activity_type')) {
            $query->where('activity_type', $request->string('activity_type')->toString());
        }

        if (! $request->user()->isAdmin()) {
            $query->where('user_id', $request->user()->id);
        }

        return $query;
    }

    public function create(): View
    {
        return view('attendances.create', [
            'branchOptions' => MeetingAttendance::branchOptions(),
            'activityOptions' => MeetingAttendance::activityTypeOptions(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'position' => ['required', 'string', 'max:255'],
            'branch_choice' => ['required', 'string'],
            'branch_custom' => ['nullable', 'string', 'max:255'],
            'activity_type_choice' => ['required', 'string'],
            'activity_type_custom' => ['nullable', 'string', 'max:255'],
            'event_date' => ['required', 'date'],
            'meeting_leader' => ['required', 'string', 'max:255'],
            'material_summary' => ['required', 'string'],
        ], [
            'full_name.required' => 'Nama lengkap wajib diisi.',
            'position.required' => 'Jabatan wajib diisi.',
            'branch_choice.required' => 'Cabang wajib dipilih.',
            'activity_type_choice.required' => 'Jenis kegiatan wajib dipilih.',
            'event_date.required' => 'Tanggal pelaksanaan wajib diisi.',
            'meeting_leader.required' => 'Pimpinan rapat / pemateri wajib diisi.',
            'material_summary.required' => 'Kegiatan / materi yang disampaikan wajib diisi.',
        ]);

        $branch = $validated['branch_choice'];
        if ($branch === 'other') {
            $custom = trim((string) $request->input('branch_custom'));
            if ($custom === '') {
                throw ValidationException::withMessages([
                    'branch_custom' => 'Sebutkan cabang lainnya.',
                ]);
            }
            $branch = $custom;
        }

        $activityType = $validated['activity_type_choice'];
        if ($activityType === 'other') {
            $custom = trim((string) $request->input('activity_type_custom'));
            if ($custom === '') {
                throw ValidationException::withMessages([
                    'activity_type_custom' => 'Sebutkan jenis kegiatan lainnya.',
                ]);
            }
            $activityType = $custom;
        }

        $attendance = MeetingAttendance::create([
            'code' => MeetingAttendance::nextCode(),
            'user_id' => $request->user()->id,
            'full_name' => $validated['full_name'],
            'position' => $validated['position'],
            'branch' => $branch,
            'activity_type' => $activityType,
            'event_date' => $validated['event_date'],
            'meeting_leader' => $validated['meeting_leader'],
            'material_summary' => $validated['material_summary'],
        ]);

        return redirect()
            ->route('attendances.show', $attendance)
            ->with('status', "Daftar hadir {$attendance->code} ({$attendance->full_name}) berhasil disimpan.");
    }

    public function show(MeetingAttendance $attendance): View
    {
        if (! auth()->user()->isAdmin() && $attendance->user_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki hak akses untuk riwayat daftar hadir ini.');
        }

        $attendance->load('user');

        return view('attendances.show', [
            'attendance' => $attendance,
        ]);
    }
}
