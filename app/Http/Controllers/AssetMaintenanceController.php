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

        return view('maintenances.index', [
            'maintenances' => $query->paginate(12)->withQueryString(),
            'search' => $request->string('q')->toString(),
        ]);
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
        $maintenance->load(['user', 'asset']);

        return view('maintenances.show', [
            'maintenance' => $maintenance,
        ]);
    }
}
