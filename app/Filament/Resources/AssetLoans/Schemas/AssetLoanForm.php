<?php

namespace App\Filament\Resources\AssetLoans\Schemas;

use App\Enums\AssetCondition;
use App\Enums\LoanStatus;
use App\Models\Asset;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AssetLoanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Data Peminjaman')
                    ->schema([
                        Select::make('asset_id')
                            ->label('Barang')
                            ->relationship('asset', 'name')
                            ->getOptionLabelFromRecordUsing(fn (Asset $record) => "{$record->name} — {$record->code}")
                            ->searchable(['name', 'code'])
                            ->preload()
                            ->required(),
                        Select::make('borrower_id')
                            ->label('Peminjam')
                            ->relationship('borrower', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        TextInput::make('quantity')
                            ->label('Jumlah Unit')
                            ->numeric()
                            ->minValue(1)
                            ->default(1)
                            ->required(),
                        TextInput::make('purpose')
                            ->label('Keperluan')
                            ->maxLength(160),
                        TextInput::make('room')
                            ->label('Ruangan')
                            ->maxLength(80),
                        DatePicker::make('borrowed_at')
                            ->label('Tanggal Pinjam')
                            ->default(now())
                            ->required(),
                        DatePicker::make('due_at')
                            ->label('Tenggat Pengembalian')
                            ->default(now()->addDays(7)),
                        DateTimePicker::make('returned_at')
                            ->label('Tanggal Kembali'),
                        Select::make('status')
                            ->label('Status')
                            ->options(LoanStatus::options())
                            ->default(LoanStatus::Dipinjam->value)
                            ->required(),
                        Select::make('condition_on_return')
                            ->label('Kondisi Kembali')
                            ->options(AssetCondition::options()),
                        Textarea::make('note')
                            ->label('Catatan')
                            ->rows(2)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }
}