<?php

namespace App\Filament\Widgets;

use App\Models\Asset;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class CriticalStockTable extends TableWidget
{
    protected static ?string $heading = 'Perlu Restock';

    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Asset::query()
                    ->with(['category', 'location'])
                    ->whereRaw('(quantity - borrowed_quantity) <= minimum_stock')
                    ->orderByRaw('(quantity - borrowed_quantity) asc')
            )
            ->columns([
                TextColumn::make('code')
                    ->label('Kode')
                    ->fontFamily('mono')
                    ->size('sm'),
                TextColumn::make('name')
                    ->label('Barang')
                    ->weight('semibold'),
                TextColumn::make('category.name')
                    ->label('Kategori')
                    ->badge()
                    ->color('gray'),
                TextColumn::make('location.name')
                    ->label('Lokasi'),
                TextColumn::make('quantity')
                    ->label('Stok')
                    ->numeric(),
                TextColumn::make('borrowed_quantity')
                    ->label('Dipinjam')
                    ->numeric(),
                TextColumn::make('minimum_stock')
                    ->label('Minimal')
                    ->numeric(),
            ])
            ->paginated([5, 10]);
    }
}