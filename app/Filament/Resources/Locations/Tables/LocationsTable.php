<?php

namespace App\Filament\Resources\Locations\Tables;

use App\Models\Location;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class LocationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('name')
            ->columns([
                TextColumn::make('name')
                    ->label('Lokasi')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold')
                    ->description(fn (Location $record) => $record->address),
                TextColumn::make('city')
                    ->label('Kota')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('gray'),
                TextColumn::make('pic_name')
                    ->label('Penanggung Jawab')
                    ->description(fn (Location $record) => $record->pic_phone)
                    ->searchable(),
                TextColumn::make('assets_count')
                    ->label('Jumlah Aset')
                    ->counts('assets')
                    ->numeric()
                    ->sortable(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}