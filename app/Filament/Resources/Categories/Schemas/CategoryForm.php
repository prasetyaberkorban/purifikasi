<?php

namespace App\Filament\Resources\Categories\Schemas;

use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Data Kategori')
                    ->schema([
                        TextInput::make('code')
                            ->label('Kode')
                            ->required()
                            ->maxLength(24)
                            ->unique(ignoreRecord: true)
                            ->helperText('Dipakai dalam pembentukan kode aset, misal PL.'),
                        TextInput::make('name')
                            ->label('Nama Kategori')
                            ->required()
                            ->maxLength(120),
                        Textarea::make('description')
                            ->label('Deskripsi')
                            ->rows(3)
                            ->columnSpanFull(),
                        ColorPicker::make('color')
                            ->label('Warna Label')
                            ->default('#1E6B38')
                            ->required(),
                    ])
                    ->columns(2),
            ]);
    }
}