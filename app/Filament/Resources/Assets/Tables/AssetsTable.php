<?php

namespace App\Filament\Resources\Assets\Tables;

use App\Enums\AssetCondition;
use App\Enums\AssetStatus;
use App\Models\Asset;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AssetsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                ImageColumn::make('photo_path')
                    ->label('Foto')
                    ->disk('public')
                    ->square()
                    ->height(40),
                TextColumn::make('code')
                    ->label('Kode Aset')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->fontFamily('mono')
                    ->size('sm'),
                TextColumn::make('name')
                    ->label('Nama Barang')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold')
                    ->description(fn (Asset $record) => $record->owner_unit),
                TextColumn::make('category.name')
                    ->label('Kategori')
                    ->sortable()
                    ->badge()
                    ->color('gray'),
                TextColumn::make('location.name')
                    ->label('Lokasi')
                    ->description(fn (Asset $record) => $record->room)
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('quantity')
                    ->label('Stok')
                    ->numeric()
                    ->sortable()
                    ->description(fn (Asset $record) => 'Tersedia '.$record->availableQuantity()),
                TextColumn::make('borrowed_quantity')
                    ->label('Dipinjam')
                    ->numeric()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (AssetStatus $state) => $state->label())
                    ->color(fn (AssetStatus $state) => match ($state) {
                        AssetStatus::Tersedia => 'success',
                        AssetStatus::Dipinjam => 'info',
                        AssetStatus::Habis => 'danger',
                    }),
                TextColumn::make('condition')
                    ->label('Kondisi')
                    ->badge()
                    ->formatStateUsing(fn (AssetCondition $state) => $state->label())
                    ->color(fn (AssetCondition $state) => match ($state) {
                        AssetCondition::Baik => 'success',
                        AssetCondition::BelumDicek => 'gray',
                        AssetCondition::RusakRingan => 'warning',
                        AssetCondition::Rusak => 'danger',
                    }),
                TextColumn::make('purchase_date')
                    ->label('Tgl Beli')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('purchase_price')
                    ->label('Harga Beli')
                    ->money('IDR', divideBy: 1)
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label('Tgl Input')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('category_id')
                    ->label('Kategori')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('location_id')
                    ->label('Lokasi')
                    ->relationship('location', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(AssetStatus::options()),
                SelectFilter::make('condition')
                    ->label('Kondisi')
                    ->options(AssetCondition::options()),
                TernaryFilter::make('low_stock')
                    ->label('Stok kritis')
                    ->placeholder('Semua aset')
                    ->trueLabel('Hanya stok kritis')
                    ->falseLabel('Stok aman')
                    ->queries(
                        true: fn (Builder $query) => $query->whereRaw('(quantity - borrowed_quantity) <= minimum_stock'),
                        false: fn (Builder $query) => $query->whereRaw('(quantity - borrowed_quantity) > minimum_stock'),
                    ),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}