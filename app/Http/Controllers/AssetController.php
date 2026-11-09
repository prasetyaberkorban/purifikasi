<?php

namespace App\Http\Controllers;

use App\Enums\AssetCondition;
use App\Enums\AssetStatus;
use App\Http\Requests\AssetRequest;
use App\Models\Asset;
use App\Models\AssetActivity;
use App\Models\Category;
use App\Models\Location;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AssetController extends Controller
{
    public function index(Request $request): View
    {
        $filters = [
            'q' => $request->string('q')->toString(),
            'category' => $request->string('category')->toString(),
            'location' => $request->string('location')->toString(),
            'status' => $request->string('status')->toString(),
            'condition' => $request->string('condition')->toString(),
            'owner' => $request->string('owner')->toString(),
            'view' => $request->string('view', 'table')->toString(),
        ];

        $assets = Asset::query()
            ->with(['category', 'location'])
            ->search($filters['q'])
            ->when($filters['category'] !== '', fn ($q) => $q->where('category_id', $filters['category']))
            ->when($filters['location'] !== '', fn ($q) => $q->where('location_id', $filters['location']))
            ->when($filters['status'] !== '', fn ($q) => $q->where('status', $filters['status']))
            ->when($filters['condition'] !== '', fn ($q) => $q->where('condition', $filters['condition']))
            ->when($filters['owner'] !== '', fn ($q) => $q->where('owner_unit', $filters['owner']))
            ->orderByDesc('created_at')
            ->paginate(12)
            ->withQueryString();

        return view('assets.index', [
            'assets' => $assets,
            'filters' => $filters,
            'categories' => Category::query()->orderBy('name')->get(),
            'locations' => Location::query()->orderBy('name')->get(),
            'owners' => Asset::query()->whereNotNull('owner_unit')->distinct()->orderBy('owner_unit')->pluck('owner_unit'),
            'stats' => [
                'total' => Asset::query()->count(),
                'available' => Asset::query()->where('status', AssetStatus::Tersedia->value)->count(),
                'borrowed' => Asset::query()->where('status', AssetStatus::Dipinjam->value)->count(),
                'empty' => Asset::query()->where('status', AssetStatus::Habis->value)->count(),
                'damaged' => Asset::query()->where('condition', AssetCondition::Rusak->value)->count(),
            ],
        ]);
    }

    public function create(Request $request): View
    {
        $asset = new Asset(['quantity' => 1, 'minimum_stock' => (int) \App\Models\Setting::get('low_stock_threshold', '3')]);

        return view('assets.create', [
            'asset' => $asset,
            'categories' => Category::query()->orderBy('name')->get(),
            'locations' => Location::query()->orderBy('name')->get(),
            'presetCategory' => $request->integer('category') ?: null,
            'presetLocation' => $request->integer('location') ?: null,
        ]);
    }

    public function store(AssetRequest $request): RedirectResponse
    {
        $data = $request->safe()->except(['photo', 'remove_photo']);

        if (! isset($data['minimum_stock']) || $data['minimum_stock'] === null) {
            $data['minimum_stock'] = (int) \App\Models\Setting::get('low_stock_threshold', '1');
        }

        $category = Category::query()->findOrFail($data['category_id']);
        $location = filled($data['location_id'] ?? null) ? Location::query()->find($data['location_id']) : null;

        $asset = new Asset($data);
        $asset->code = ($data['code'] ?? null) ?: Asset::nextCode($category, $location);
        $asset->condition = AssetCondition::from($data['condition']);
        $asset->borrowed_quantity = 0;

        if ($request->hasFile('photo')) {
            $asset->photo_path = $request->file('photo')->store('assets', 'public');
        }

        $asset->syncStatus()->save();

        AssetActivity::query()->create([
            'asset_id' => $asset->getKey(),
            'user_id' => $request->user()->getKey(),
            'type' => 'created',
            'subject' => $asset->code,
            'description' => "{$request->user()->name} menambahkan aset {$asset->name} ({$asset->code}) sebanyak {$asset->quantity} unit.",
        ]);

        return redirect()
            ->route('assets.show', $asset)
            ->with('status', "Aset {$asset->name} berhasil ditambahkan dengan kode {$asset->code}.");
    }

    public function show(Asset $asset): View
    {
        $asset->syncItems();
        $asset->load(['category', 'location', 'items']);

        return view('assets.show', [
            'asset' => $asset,
            'loans' => $asset->loans()->with(["borrower", "handler"])->orderByDesc("borrowed_at")->orderByDesc("id")->limit(10)->get(),
            'activities' => $asset->activities()->with('user')->latestFirst()->limit(12)->get(),
        ]);
    }

    public function edit(Asset $asset): View
    {
        return view('assets.edit', [
            'asset' => $asset,
            'categories' => Category::query()->orderBy('name')->get(),
            'locations' => Location::query()->orderBy('name')->get(),
        ]);
    }

    public function update(AssetRequest $request, Asset $asset): RedirectResponse
    {
        $data = $request->safe()->except(['photo', 'remove_photo', 'code']);

        $before = $asset->only(['name', 'quantity', 'condition', 'location_id', 'room']);

        if ($request->boolean('remove_photo') && $asset->photo_path) {
            Storage::disk('public')->delete($asset->photo_path);
            $data['photo_path'] = null;
        }

        if ($request->hasFile('photo')) {
            if ($asset->photo_path) {
                Storage::disk('public')->delete($asset->photo_path);
            }
            $data['photo_path'] = $request->file('photo')->store('assets', 'public');
        }

        if (($data['quantity'] ?? $asset->quantity) < $asset->borrowed_quantity) {
            return back()->withErrors(['quantity' => "Stok tidak boleh lebih kecil dari jumlah yang sedang dipinjam ({$asset->borrowed_quantity} unit)."])->withInput();
        }

        if ($request->filled('code') && $request->string('code')->toString() !== $asset->code) {
            $data['code'] = $request->string('code')->toString();
        }

        $asset->fill($data)->syncStatus()->save();

        $changes = collect($asset->only(['name', 'quantity', 'condition', 'location_id', 'room']))
            ->filter(fn ($value, $key) => (string) $value !== (string) ($before[$key] ?? ''))
            ->keys();

        AssetActivity::query()->create([
            'asset_id' => $asset->getKey(),
            'user_id' => $request->user()->getKey(),
            'type' => 'updated',
            'subject' => $asset->code,
            'description' => $changes->isEmpty()
                ? "Perubahan disimpan pada {$asset->name} ({$asset->code})."
                : "{$request->user()->name} memperbarui ".$changes->implode(', ')." pada {$asset->name} ({$asset->code}).",
            'meta' => ['perubahan' => $changes->all()],
        ]);

        return redirect()->route('assets.show', $asset)->with('status', "Perubahan aset {$asset->name} tersimpan.");
    }

    public function destroy(Request $request, Asset $asset): RedirectResponse
    {
        $activeLoans = $asset->loans()->active()->count();

        if ($activeLoans > 0) {
            return back()->withErrors(['asset' => 'Aset masih memiliki peminjaman aktif. Selesaikan pengembalian terlebih dahulu.']);
        }

        $name = $asset->name;
        $code = $asset->code;

        if ($asset->photo_path) {
            Storage::disk('public')->delete($asset->photo_path);
        }

        $asset->delete();

        AssetActivity::query()->create([
            'user_id' => $request->user()->getKey(),
            'type' => 'deleted',
            'subject' => $code,
            'description' => "{$request->user()->name} menghapus aset {$name} ({$code}).",
        ]);

        return redirect()->route('assets.index')->with('status', "Aset {$name} dihapus dari inventaris.");
    }

    public function bulkDestroy(Request $request): RedirectResponse
    {
        if (! $request->user()->isAdmin()) {
            abort(403, 'Hanya Admin yang dapat menghapus data aset.');
        }

        $rawIds = $request->input('ids', []);
        if (is_string($rawIds)) {
            $rawIds = explode(',', $rawIds);
        }
        if ($request->filled('ids_csv')) {
            $rawIds = array_merge((array) $rawIds, explode(',', (string) $request->input('ids_csv')));
        }
        $ids = array_filter(array_unique(array_map('intval', (array) $rawIds)));

        if (empty($ids)) {
            return back()->withErrors(['ids' => 'Tidak ada data aset yang dipilih.']);
        }

        $deletedCount = 0;
        $skippedCount = 0;

        foreach ($ids as $id) {
            $asset = Asset::find($id);
            if (! $asset) {
                continue;
            }

            if ($asset->loans()->active()->count() > 0) {
                $skippedCount++;
                continue;
            }

            if ($asset->photo_path) {
                Storage::disk('public')->delete($asset->photo_path);
            }

            $name = $asset->name;
            $code = $asset->code;
            $asset->delete();

            AssetActivity::query()->create([
                'user_id' => $request->user()->getKey(),
                'type' => 'deleted',
                'subject' => $code,
                'description' => "{$request->user()->name} menghapus aset {$name} ({$code}) secara massal.",
            ]);

            $deletedCount++;
        }

        $message = "Berhasil menghapus {$deletedCount} aset.";
        if ($skippedCount > 0) {
            $message .= " {$skippedCount} aset dilewati karena masih memiliki peminjaman aktif.";
        }

        return redirect()->route('assets.index')->with('status', $message);
    }

    public function check(Asset $asset): RedirectResponse
    {
        $asset->update(['last_checked_at' => now()]);

        AssetActivity::query()->create([
            'asset_id' => $asset->getKey(),
            'user_id' => auth()->id(),
            'type' => 'condition',
            'subject' => $asset->code,
            'description' => "Pemeriksaan kondisi {$asset->name} ({$asset->code}) tercatat: ".$asset->condition->label().'.',
        ]);

        return back()->with('status', "Pemeriksaan {$asset->name} tercatat.");
    }

    public function updateItem(Request $request, Asset $asset, \App\Models\AssetItem $item): RedirectResponse
    {
        $validated = $request->validate([
            'serial_number'  => ['nullable', 'string', 'max:255'],
            'condition'      => ['required', 'string'],
            'photo'          => ['nullable', 'image', 'max:5120'],
            'notes'          => ['nullable', 'string', 'max:255'],
            'room'           => ['nullable', 'string', 'max:255'],
            'purchase_price' => ['nullable', 'numeric', 'min:0'],
        ]);

        if ($request->hasFile('photo')) {
            if ($item->photo_path) {
                Storage::disk('public')->delete($item->photo_path);
            }
            $validated['photo_path'] = $request->file('photo')->store('asset-items', 'public');
        }

        unset($validated['photo']);
        $item->update($validated);

        return back()->with('status', "Kondisi unit {$item->item_code} berhasil diperbarui.");
    }

    public function addItem(Request $request, Asset $asset): RedirectResponse
    {
        $validated = $request->validate([
            'serial_number'  => ['nullable', 'string', 'max:255'],
            'condition'      => ['required', 'string'],
            'photo'          => ['nullable', 'image', 'max:5120'],
            'notes'          => ['nullable', 'string', 'max:255'],
            'room'           => ['nullable', 'string', 'max:255'],
            'purchase_price' => ['nullable', 'numeric', 'min:0'],
        ]);

        $nextUnit = ((int) $asset->items()->max('unit_number')) + 1;
        $itemCode = sprintf('%s-%02d', $asset->code, $nextUnit);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('asset-items', 'public');
        }

        $asset->items()->create([
            'item_code'      => $itemCode,
            'unit_number'    => $nextUnit,
            'serial_number'  => $validated['serial_number'] ?? null,
            'condition'      => $validated['condition'],
            'status'         => \App\Enums\AssetStatus::Tersedia,
            'photo_path'     => $photoPath,
            'notes'          => $validated['notes'] ?? null,
            'room'           => $validated['room'] ?? null,
            'purchase_price' => $validated['purchase_price'] ?? null,
        ]);

        $asset->increment('quantity');

        AssetActivity::query()->create([
            'user_id'     => $request->user()->getKey(),
            'type'        => 'created',
            'subject'     => $asset->code,
            'description' => "{$request->user()->name} menambahkan unit baru #{$nextUnit} ({$itemCode}) untuk aset {$asset->name}.",
        ]);

        return back()->with('status', "Unit baru #{$nextUnit} ({$itemCode}) berhasil ditambahkan.");
    }

    public function destroyItem(Request $request, Asset $asset, \App\Models\AssetItem $item): RedirectResponse
    {
        if ($item->status === \App\Enums\AssetStatus::Dipinjam) {
            return back()->withErrors(['asset' => "Unit {$item->item_code} sedang dipinjam dan tidak dapat dihapus."]);
        }

        if ($asset->quantity <= 1) {
            return back()->withErrors(['asset' => "Unit terakhir tidak dapat dihapus. Jika ingin menghapus seluruh aset, gunakan tombol Hapus Aset."]);
        }

        if ($item->photo_path) {
            Storage::disk('public')->delete($item->photo_path);
        }

        $unitCode = $item->item_code;
        $unitNum = $item->unit_number;
        $item->delete();

        $asset->decrement('quantity');

        AssetActivity::query()->create([
            'user_id'     => $request->user()->getKey(),
            'type'        => 'deleted',
            'subject'     => $asset->code,
            'description' => "{$request->user()->name} menghapus unit #{$unitNum} ({$unitCode}) dari aset {$asset->name}.",
        ]);

        return back()->with('status', "Unit #{$unitNum} ({$unitCode}) berhasil dihapus.");
    }
}