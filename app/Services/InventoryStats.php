<?php

namespace App\Services;

use App\Enums\AssetCondition;
use App\Models\Asset;
use App\Models\AssetLoan;
use Illuminate\Support\Collection;

class InventoryStats
{
    /** @return array<string, int|float|string> */
    public function summary(): array
    {
        $totalUnits = (int) Asset::query()->sum('quantity');
        $assetCount = (int) Asset::query()->count();
        $activeLoans = (int) AssetLoan::query()->active()->sum('quantity');

        $critical = Asset::query()
            ->get(['id', 'quantity', 'minimum_stock', 'borrowed_quantity'])
            ->filter(fn (Asset $a) => $a->isCritical())
            ->count();

        $activityToday = (int) \App\Models\AssetActivity::query()->whereDate('created_at', today())->count();

        return [
            'asset_count' => $assetCount,
            'total_units' => $totalUnits,
            'borrowed_units' => $activeLoans,
            'critical_count' => $critical,
            'activity_today' => $activityToday,
            'value_total' => (float) Asset::with('items')->get()->sum(fn (Asset $a) => $a->totalValue()),
        ];
    }

    /**
     * Distribusi kondisi aset untuk grafik donat.
     *
     * @return Collection<int, array{label: string, value: int, color: string}>
     */
    public function conditionBreakdown(): Collection
    {
        $counts = Asset::query()
            ->selectRaw('`condition`, COUNT(*) as total')
            ->groupBy('condition')
            ->pluck('total', 'condition');

        return collect(AssetCondition::cases())
            ->map(fn (AssetCondition $c) => [
                'label' => $c->label(),
                'value' => (int) ($counts[$c->value] ?? 0),
                'color' => $c->color(),
            ])
            ->values();
    }

    /**
     * Jumlah unit per kategori untuk grafik batang.
     *
     * @return Collection<int, array{label: string, value: int}>
     */
    public function categoryBreakdown(int $limit = 8): Collection
    {
        $palette = ['#1E6B38', '#2563EB', '#D97706', '#7C3AED', '#DB2777', '#0891B2', '#059669', '#4F46E5'];

        return Asset::query()
            ->join('categories', 'categories.id', '=', 'assets.category_id')
            ->selectRaw('categories.name as label, categories.color as cat_color, SUM(assets.quantity) as value')
            ->groupBy('categories.id', 'categories.name', 'categories.color')
            ->orderByDesc('value')
            ->limit($limit)
            ->get()
            ->values()
            ->map(fn ($row, $i) => [
                'label' => (string) $row->label,
                'value' => (int) $row->value,
                'color' => $row->cat_color ?: $palette[$i % count($palette)],
            ]);
    }

    /**
     * Nilai perolehan aset per tahun.
     *
     * @return Collection<int, array{label: string, value: float, color: string}>
     */
    public function purchaseByYear(int $limit = 4): Collection
    {
        $freshPalette = ['#0284c7', '#0d9488', '#10b981', '#16a34a'];

        return Asset::query()
            ->whereNotNull('purchase_date')
            ->with('items')
            ->get()
            ->groupBy(fn (Asset $a) => $a->purchase_date->format('Y'))
            ->map(fn (Collection $rows, string $year) => [
                'label' => $year,
                'value' => (float) $rows->sum(fn (Asset $a) => $a->totalValue()),
            ])
            ->sortKeys()
            ->take(-$limit)
            ->values()
            ->map(fn ($row, $i) => [
                'label' => $row['label'],
                'value' => $row['value'],
                'color' => $freshPalette[$i % count($freshPalette)],
            ]);
    }

    /**
     * Peminjaman aktif terbaru untuk panel "Aktivitas Terbaru".
     *
     * @return Collection<int, AssetLoan>
     */
    public function recentLoans(int $limit = 5): Collection
    {
        return AssetLoan::query()
            ->with(['asset.category', 'borrower'])
            ->latestFirst()
            ->limit($limit)
            ->get();
    }
}