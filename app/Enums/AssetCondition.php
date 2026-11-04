<?php

namespace App\Enums;

enum AssetCondition: string
{
    case Baik = 'baik';
    case BelumDicek = 'belum_dicek';
    case RusakRingan = 'rusak_ringan';
    case Rusak = 'rusak';

    public function label(): string
    {
        return match ($this) {
            self::Baik => 'Baik',
            self::BelumDicek => 'Belum Dicek',
            self::RusakRingan => 'Rusak Ringan',
            self::Rusak => 'Rusak',
        };
    }

    /** Warna segmen grafik donat "Kondisi Aset". */
    public function color(): string
    {
        return match ($this) {
            self::Baik => '#0d6efd',
            self::BelumDicek => '#22c55e',
            self::Rusak => '#f59e0b',
            self::RusakRingan => '#ef4444',
        };
    }

    /** @return array<string, string> */
    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn (self $c) => [$c->value => $c->label()])->all();
    }
}
