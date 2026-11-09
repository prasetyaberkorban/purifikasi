<?php

namespace App\Http\Controllers;

use App\Models\VehicleLoan;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class VehicleLoanController extends Controller
{
    public function index(Request $request): View
    {
        return view('vehicle-loans.index', [
            'loans' => $this->buildQuery($request)->paginate(12)->withQueryString(),
            'search' => $request->string('q')->toString(),
            'selectedVehicle' => $request->string('vehicle_type')->toString(),
            'vehicleTypes' => VehicleLoan::vehicleTypeOptions(),
        ]);
    }

    public function exportPdf(Request $request): \Symfony\Component\HttpFoundation\Response
    {
        $rows = $this->buildQuery($request)->get();
        $filters = [
            'q' => $request->string('q')->toString(),
            'vehicle_type' => $request->string('vehicle_type')->toString(),
        ];
        $pdf = app(\App\Services\PdfReportService::class)->render('vehicle-loans', $rows, $filters);

        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="laporan-peminjaman-kendaraan-'.now()->format('Ymd-His').'.pdf"',
        ]);
    }

    public function exportExcel(Request $request): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $rows = $this->buildQuery($request)->get();
        $filters = [
            'q' => $request->string('q')->toString(),
            'vehicle_type' => $request->string('vehicle_type')->toString(),
        ];

        return app(\App\Services\ExcelReportService::class)->stream('vehicle-loans', $rows, $filters);
    }

    private function buildQuery(Request $request)
    {
        $query = VehicleLoan::query()->with('user')->latest();

        if ($request->filled('q')) {
            $term = $request->string('q')->toString();
            $query->where(function ($q) use ($term) {
                $q->where('code', 'like', "%{$term}%")
                    ->orWhere('pic_name', 'like', "%{$term}%")
                    ->orWhere('driver_name', 'like', "%{$term}%")
                    ->orWhere('vehicle_type', 'like', "%{$term}%")
                    ->orWhere('purpose', 'like', "%{$term}%");
            });
        }

        if ($request->filled('vehicle_type')) {
            $query->where('vehicle_type', $request->string('vehicle_type')->toString());
        }

        if (! $request->user()->isAdmin()) {
            $query->where('user_id', $request->user()->id);
        }

        return $query;
    }

    public function create(): View
    {
        return view('vehicle-loans.create', [
            'vehicleTypes' => VehicleLoan::vehicleTypeOptions(),
            'cleanlinessOptions' => VehicleLoan::cleanlinessOptions(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'pic_name' => ['required', 'string', 'max:255'],
            'driver_name' => ['required', 'string', 'max:255'],
            'departure_date' => ['required', 'date'],
            'return_date' => ['required', 'date', 'after_or_equal:departure_date'],
            'departure_km' => ['required', 'string', 'max:100'],
            'vehicle_type_choice' => ['required', 'string'],
            'vehicle_type_custom' => ['nullable', 'string', 'max:255'],
            'purpose' => ['required', 'string'],
            'cleanliness_choice' => ['required', 'string'],
            'cleanliness_custom' => ['nullable', 'string', 'max:255'],
            'suggestions' => ['nullable', 'string'],
            'departure_speedometer_photo' => ['required', 'image', 'max:10240'],
            'return_speedometer_photo' => ['required', 'image', 'max:10240'],
        ], [
            'pic_name.required' => 'PIC wajib diisi.',
            'driver_name.required' => 'Nama driver wajib diisi.',
            'departure_date.required' => 'Tanggal pergi wajib diisi.',
            'return_date.required' => 'Tanggal kembali wajib diisi.',
            'return_date.after_or_equal' => 'Tanggal kembali tidak boleh lebih awal dari tanggal pergi.',
            'departure_km.required' => 'Kilometer (Km) berangkat wajib diketik.',
            'vehicle_type_choice.required' => 'Jenis kendaraan wajib dipilih.',
            'purpose.required' => 'Tujuan peminjaman wajib diisi.',
            'cleanliness_choice.required' => 'Kondisi kebersihan kendaraan wajib dipilih.',
            'departure_speedometer_photo.required' => 'Foto speedometer berangkat wajib dilampirkan.',
            'departure_speedometer_photo.image' => 'File foto speedometer berangkat harus berupa gambar.',
            'departure_speedometer_photo.max' => 'Ukuran foto speedometer berangkat maksimal 10 MB.',
            'return_speedometer_photo.required' => 'Foto speedometer kembali wajib dilampirkan.',
            'return_speedometer_photo.image' => 'File foto speedometer kembali harus berupa gambar.',
            'return_speedometer_photo.max' => 'Ukuran foto speedometer kembali maksimal 10 MB.',
        ]);

        $vehicleType = $validated['vehicle_type_choice'];
        if ($vehicleType === 'other') {
            $custom = trim((string) $request->input('vehicle_type_custom'));
            if ($custom === '') {
                throw ValidationException::withMessages([
                    'vehicle_type_custom' => 'Sebutkan jenis kendaraan lainnya.',
                ]);
            }
            $vehicleType = $custom;
        }

        $cleanliness = $validated['cleanliness_choice'];
        if ($cleanliness === 'other') {
            $custom = trim((string) $request->input('cleanliness_custom'));
            if ($custom === '') {
                throw ValidationException::withMessages([
                    'cleanliness_custom' => 'Sebutkan kondisi kebersihan kendaraan lainnya.',
                ]);
            }
            $cleanliness = $custom;
        }

        $depPhoto = $request->file('departure_speedometer_photo')->store('vehicle-loans/speedometers', 'public');
        $retPhoto = $request->file('return_speedometer_photo')->store('vehicle-loans/speedometers', 'public');

        $loan = VehicleLoan::create([
            'code' => VehicleLoan::nextCode(),
            'user_id' => $request->user()->id,
            'pic_name' => $validated['pic_name'],
            'driver_name' => $validated['driver_name'],
            'departure_date' => $validated['departure_date'],
            'return_date' => $validated['return_date'],
            'departure_km' => $validated['departure_km'],
            'vehicle_type' => $vehicleType,
            'purpose' => $validated['purpose'],
            'cleanliness_condition' => $cleanliness,
            'suggestions' => $validated['suggestions'] ?? null,
            'departure_speedometer_photo_path' => $depPhoto,
            'return_speedometer_photo_path' => $retPhoto,
            'status' => 'selesai',
        ]);

        return redirect()
            ->route('vehicle-loans.show', $loan)
            ->with('status', "Peminjaman kendaraan {$loan->code} ({$loan->vehicle_type}) berhasil disimpan.");
    }

    public function show(VehicleLoan $vehicleLoan): View
    {
        if (! auth()->user()->isAdmin() && $vehicleLoan->user_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki hak akses untuk riwayat peminjaman kendaraan ini.');
        }

        $vehicleLoan->load('user');

        return view('vehicle-loans.show', [
            'loan' => $vehicleLoan,
        ]);
    }
}
