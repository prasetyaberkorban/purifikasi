<?php

namespace App\Filament\Resources\Locations\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class LocationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Data Lokasi')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Lokasi')
                            ->required()
                            ->maxLength(120),
                        TextInput::make('city')
                            ->label('Kota')
                            ->maxLength(80),
                        Textarea::make('address')
                            ->label('Alamat')
                            ->rows(2)
                            ->columnSpanFull(),
                        TextInput::make('pic_name')
                            ->label('Penanggung Jawab')
                            ->maxLength(120),
                        TextInput::make('pic_phone')
                            ->label('Telepon PJ')
                            ->maxLength(32),
                    ])
                    ->columns(2),
            ]);
    }
}