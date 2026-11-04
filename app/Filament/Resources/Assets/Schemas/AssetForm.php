<?php

namespace App\Filament\Resources\Assets\Schemas;

use App\Enums\AssetCondition;
use App\Models\Asset;
use App\Models\Category;
use App\Models\Location;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AssetForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Identitas Barang')
                    ->description('Kode aset otomatis dibuat dari kota, kode kategori, tahun, dan urutan.')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Barang')
                            ->required()
                            ->maxLength(120)
                            ->columnSpanFull(),
                        TextInput::make('code')
                            ->label('Kode Aset')
                            ->maxLength(48)
                            ->unique(ignoreRecord: true)
                            ->placeholder('Otomatis bila dikosongkan')
                            ->helperText('Contoh: DEP.PL/2026/SGI.0007'),
                        TextInput::make('serial_number')
                            ->label('Serial Number')
                            ->maxLength(120),
                        TextInput::make('brand')
                            ->label('Merek / Tipe')
                            ->maxLength(80),
                        TextInput::make('owner_unit')
                            ->label('Unit Pemilik')
                            ->maxLength(64),
                        Select::make('category_id')
                            ->label('Kategori')
                            ->relationship('category', 'name')
                            ->getOptionLabelFromRecordUsing(fn (Category $record) => "{$record->name} ({$record->code})")
                            ->searchable()
                            ->preload()
                            ->required(),
                        Select::make('location_id')
                            ->label('Lokasi')
                            ->relationship('location', 'name')
                            ->getOptionLabelFromRecordUsing(fn (Location $record) => $record->label)
                            ->searchable()
                            ->preload(),
                        TextInput::make('room')
                            ->label('Ruangan / Rak')
                            ->maxLength(80),
                        Select::make('condition')
                            ->label('Kondisi')
                            ->options(AssetCondition::options())
                            ->default(AssetCondition::BelumDicek->value)
                            ->required(),
                        Textarea::make('description')
                            ->label('Keterangan')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Stok & Perolehan')
                    ->schema([
                        TextInput::make('quantity')
                            ->label('Jumlah Stok')
                            ->numeric()
                            ->minValue(0)
                            ->default(1)
                            ->required(),
                        TextInput::make('minimum_stock')
                            ->label('Stok Minimal')
                            ->numeric()
                            ->minValue(0)
                            ->default(3)
                            ->required()
                            ->helperText('Batas penanda "stok kritis" di dashboard.'),
                        DatePicker::make('purchase_date')
                            ->label('Tanggal Beli')
                            ->maxDate(today()),
                        TextInput::make('purchase_price')
                            ->label('Harga Beli (Rp)')
                            ->numeric()
                            ->minValue(0)
                            ->prefix('Rp'),
                        FileUpload::make('photo_path')
                            ->label('Foto Barang')
                            ->image()
                            ->imageEditor()
                            ->directory('assets')
                            ->disk('public')
                            ->maxSize(4096)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }
}