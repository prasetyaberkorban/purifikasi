<?php

namespace App\Filament\Widgets;

use App\Enums\AssetCondition;
use App\Enums\AssetStatus;
use App\Models\Asset;
use App\Models\AssetLoan;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class InventoryOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $summary = app(\App\Services\InventoryStats::class)->summary();

        return [
            Stat::make('Total Aset', number_format($summary['asset_count'], 0, ',', '.'))
                ->description($summary['total_units'].' unit tercatat')
                ->descriptionIcon('heroicon-m-archive-box')
                ->color('success'),
            Stat::make('Unit Dipinjam', number_format($summary['borrowed_units'], 0, ',', '.'))
                ->description(AssetLoan::query()->active()->count().' transaksi aktif')
                ->descriptionIcon('heroicon-m-arrows-right-left')
                ->color('info'),
            Stat::make('Stok Kritis', (string) $summary['critical_count'])
                ->description('Stok tersedia di bawah minimal')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('warning'),
            Stat::make('Nilai Aset', 'Rp '.number_format($summary['value_total'] / 1000000, 1, ',', '.').' Jt')
                ->description($summary['activity_today'].' aktivitas hari ini')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('primary'),
        ];
    }
}