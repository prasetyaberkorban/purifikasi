<?php

namespace App\Filament\Widgets;

use App\Models\Asset;
use Filament\Widgets\ChartWidget;

class AssetConditionChart extends ChartWidget
{
    protected ?string $heading = 'Kondisi Aset';

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 1;

    protected function getData(): array
    {
        $breakdown = app(\App\Services\InventoryStats::class)->conditionBreakdown();

        return [
            'datasets' => [[
                'label' => 'Jumlah Aset',
                'data' => $breakdown->pluck('value')->all(),
                'backgroundColor' => $breakdown->pluck('color')->all(),
                'borderWidth' => 0,
            ]],
            'labels' => $breakdown->pluck('label')->all(),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => ['position' => 'right'],
            ],
            'cutout' => '62%',
            'maintainAspectRatio' => false,
        ];
    }
}