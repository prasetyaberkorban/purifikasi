<?php

namespace App\Enums;

enum AssetStatus: string
{
    case Tersedia = 'tersedia';
    case Dipinjam = 'dipinjam';
    case Habis = 'habis';

    public function label(): string
    {
        return match ($this) {
            self::Tersedia => 'Tersedia',
            self::Dipinjam => 'Dipinjam',
            self::Habis => 'Habis',
        };
    }

    /** Warna badge sesuai palet Purifikasi. */
    public function tone(): string
    {
        return match ($this) {
            self::Tersedia => 'leaf',
            self::Dipinjam => 'sky',
            self::Habis => 'danger',
        };
    }

    /** @return array<string, string> */
    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn (self $c) => [$c->value => $c->label()])->all();
    }
}
