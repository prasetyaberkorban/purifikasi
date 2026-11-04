<?php

namespace App\Enums;

enum LoanStatus: string
{
    case Dipinjam = 'dipinjam';
    case Dikembalikan = 'dikembalikan';

    public function label(): string
    {
        return match ($this) {
            self::Dipinjam => 'Dipinjam',
            self::Dikembalikan => 'Dikembalikan',
        };
    }

    public function tone(): string
    {
        return match ($this) {
            self::Dipinjam => 'sky',
            self::Dikembalikan => 'leaf',
        };
    }

    /** @return array<string, string> */
    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn (self $c) => [$c->value => $c->label()])->all();
    }
}
