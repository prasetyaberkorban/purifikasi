<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case Karyawan = 'karyawan';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Admin',
            self::Karyawan => 'Karyawan',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::Admin => 'Akses penuh: kelola aset, kategori, peminjaman, laporan, dan pengaturan pengguna.',
            self::Karyawan => 'Akses operasional: lihat inventaris, ajukan pinjam barang, dan riwayat.',
        };
    }

    /** @return array<string, string> */
    public static function options(): array
    {
        return [
            self::Admin->value => self::Admin->label(),
            self::Karyawan->value => self::Karyawan->label(),
        ];
    }
}
