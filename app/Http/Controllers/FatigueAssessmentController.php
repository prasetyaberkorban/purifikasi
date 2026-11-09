<?php

namespace App\Http\Controllers;

use App\Models\FatigueAssessment;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class FatigueAssessmentController extends Controller
{
    public function index(Request $request): View
    {
        $query = FatigueAssessment::query()->with('user')->latest();

        if ($request->filled('q')) {
            $term = $request->string('q')->toString();
            $query->where(function ($q) use ($term) {
                $q->where('code', 'like', "%{$term}%")
                    ->orWhere('name', 'like', "%{$term}%");
            });
        }

        if (! $request->user()->isAdmin()) {
            $query->where('user_id', $request->user()->id);
        }

        return view('fatigue.index', [
            'assessments' => $query->paginate(12)->withQueryString(),
            'search' => $request->string('q')->toString(),
        ]);
    }

    public function create(): View
    {
        return view('fatigue.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'assessment_date' => ['required', 'date'],
            'recent_illness' => ['required', 'in:Ya,Tidak'],
            'medication_drowsy' => ['required', 'in:Ya,Tidak'],
            'personal_distraction' => ['required', 'in:Ya,Tidak'],
            'sleep_hours' => ['required', 'in:kurang dari 6 jam,lebih dari 6 jam'],
            'fit_to_drive' => ['required', 'in:Ya,Tidak'],
        ], [
            'name.required' => 'Nama wajib diisi.',
            'assessment_date.required' => 'Tanggal Penilaian Fatik Individual wajib diisi.',
            'recent_illness.required' => 'Pertanyaan kondisi sakit ringan wajib dijawab.',
            'medication_drowsy.required' => 'Pertanyaan konsumsi obat/zat penyebab kantuk wajib dijawab.',
            'personal_distraction.required' => 'Pertanyaan gangguan lain wajib dijawab.',
            'sleep_hours.required' => 'Durasi jam tidur tadi malam wajib dipilih.',
            'fit_to_drive.required' => 'Pertanyaan kesiapan/kemampuan mengendarai kendaraan wajib dijawab.',
        ]);

        $assessment = FatigueAssessment::create([
            'code' => FatigueAssessment::nextCode(),
            'user_id' => $request->user()->id,
            'name' => $validated['name'],
            'assessment_date' => $validated['assessment_date'],
            'recent_illness' => $validated['recent_illness'],
            'medication_drowsy' => $validated['medication_drowsy'],
            'personal_distraction' => $validated['personal_distraction'],
            'sleep_hours' => $validated['sleep_hours'],
            'fit_to_drive' => $validated['fit_to_drive'],
            'status' => 'Selesai',
        ]);

        return redirect()
            ->route('fatigue.show', $assessment)
            ->with('status', "Penilaian Fatik Individual {$assessment->code} untuk {$assessment->name} berhasil disimpan.");
    }

    public function show(FatigueAssessment $fatigue): View
    {
        if (! auth()->user()->isAdmin() && $fatigue->user_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki hak akses untuk riwayat penilaian fatik ini.');
        }

        $fatigue->load('user');

        return view('fatigue.show', [
            'assessment' => $fatigue,
        ]);
    }
}
