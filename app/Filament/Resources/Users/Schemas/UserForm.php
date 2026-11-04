<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Enums\UserRole;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Akun')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama')
                            ->required()
                            ->maxLength(120),
                        TextInput::make('username')
                            ->label('Username')
                            ->required()
                            ->maxLength(64)
                            ->regex('/^[a-z0-9._-]+$/')
                            ->helperText('Huruf kecil, angka, titik, garis bawah, atau tanda hubung.')
                            ->unique(ignoreRecord: true),
                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->maxLength(160)
                            ->unique(ignoreRecord: true),
                        TextInput::make('password')
                            ->label('Kata Sandi')
                            ->password()
                            ->revealable()
                            ->dehydrated(fn (?string $state) => filled($state))
                            ->required(fn (string $operation) => $operation === 'create')
                            ->helperText('Kosongkan bila tidak ingin mengubah.'),
                        Select::make('role')
                            ->label('Peran')
                            ->options(UserRole::options())
                            ->required()
                            ->default(UserRole::Karyawan->value),
                        TextInput::make('unit')
                            ->label('Unit')
                            ->maxLength(64),
                        TextInput::make('phone')
                            ->label('Telepon')
                            ->maxLength(32),
                        Toggle::make('is_active')
                            ->label('Akun Aktif')
                            ->default(true),
                    ])
                    ->columns(2),
            ]);
    }
}