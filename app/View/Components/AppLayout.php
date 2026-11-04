<?php

namespace App\View\Components;

use Illuminate\Support\Collection;
use Illuminate\View\Component;
use Illuminate\View\View;

class AppLayout extends Component
{
    public function __construct(
        public string $title = 'Dashboard',
        public ?string $subtitle = null,
        public string $active = 'dashboard',
    ) {}

    /**
     * Menu utama sidebar. Dropdown berisi submenu sesuai struktur desain.
     *
     * @return Collection<int, array<string, mixed>>
     */
    public function menu(): Collection
    {
        $user = auth()->user();

        return collect([
            [
                'key' => 'dashboard',
                'label' => 'Dashboard',
                'icon' => 'home',
                'route' => route('dashboard'),
                'active' => ['dashboard'],
            ],
            [
                'key' => 'assets',
                'label' => 'Aset & Barang',
                'icon' => 'box',
                'children' => collect([
                    ['label' => 'Daftar Inventaris', 'route' => route('assets.index'), 'active' => ['assets.index', 'assets.show', 'assets.edit']],
                    ['label' => 'Tambah Barang', 'route' => route('assets.create'), 'active' => ['assets.create']],
                    ['label' => 'Kategori', 'route' => route('categories.index'), 'active' => ['categories.*']],
                ])->filter(fn ($i) => $user?->isAdmin() || $i['label'] === 'Daftar Inventaris')->values()->all(),
                'active' => ['assets.*', 'categories.*'],
            ],
            [
                'key' => 'loans',
                'label' => 'Peminjaman',
                'icon' => 'swap',
                'children' => [
                    ['label' => 'Pinjam Barang', 'route' => route('loans.create'), 'active' => ['loans.create']],
                    ['label' => 'Peminjaman Aktif', 'route' => route('loans.active'), 'active' => ['loans.active']],
                    ['label' => 'Riwayat', 'route' => route('loans.history'), 'active' => ['loans.history', 'loans.show']],
                ],
                'active' => ['loans.*'],
            ],
            [
                'key' => 'services',
                'label' => 'Service & Pemeliharaan',
                'icon' => 'wrench',
                'children' => [
                    ['label' => 'Formulir Service', 'route' => route('services.create'), 'active' => ['services.create']],
                    ['label' => 'Riwayat Service', 'route' => route('services.index'), 'active' => ['services.index', 'services.show']],
                ],
                'active' => ['services.*'],
            ],
            [
                'key' => 'procurements',
                'label' => 'Realisasi Pengadaan',
                'icon' => 'cart',
                'children' => [
                    ['label' => 'Formulir Realisasi', 'route' => route('procurements.create'), 'active' => ['procurements.create']],
                    ['label' => 'Riwayat Realisasi', 'route' => route('procurements.index'), 'active' => ['procurements.index', 'procurements.show']],
                ],
                'active' => ['procurements.*'],
            ],
            [
                'key' => 'leaves',
                'label' => 'Izin, Cuti & WFA',
                'icon' => 'user',
                'children' => [
                    ['label' => 'Formulir Izin/Cuti/WFA', 'route' => route('leaves.create'), 'active' => ['leaves.create']],
                    ['label' => 'Riwayat Pengajuan', 'route' => route('leaves.index'), 'active' => ['leaves.index', 'leaves.show']],
                ],
                'active' => ['leaves.*'],
            ],
            [
                'key' => 'vehicle_loans',
                'label' => 'Peminjaman Kendaraan',
                'icon' => 'car',
                'children' => [
                    ['label' => 'Formulir Peminjaman', 'route' => route('vehicle-loans.create'), 'active' => ['vehicle-loans.create']],
                    ['label' => 'Riwayat Peminjaman', 'route' => route('vehicle-loans.index'), 'active' => ['vehicle-loans.index', 'vehicle-loans.show']],
                ],
                'active' => ['vehicle-loans.*'],
            ],
            [
                'key' => 'attendances',
                'label' => 'Daftar Hadir',
                'icon' => 'clipboard-check',
                'children' => [
                    ['label' => 'Formulir Daftar Hadir', 'route' => route('attendances.create'), 'active' => ['attendances.create']],
                    ['label' => 'Riwayat Daftar Hadir', 'route' => route('attendances.index'), 'active' => ['attendances.index', 'attendances.show']],
                ],
                'active' => ['attendances.*'],
            ],
            [
                'key' => 'survey',
                'label' => 'Formulir Survei',
                'icon' => 'document',
                'route' => 'https://docs.google.com/document/d/1KXSq1aEiXf0rEdAX-AHzh5W7rdbi1k-h/edit',
                'active' => [],
                'external' => true,
            ],
            [
                'key' => 'ppe_survey',
                'label' => 'Form Pengadaan APD/Alat Survei',
                'icon' => 'cart',
                'route' => 'https://docs.google.com/spreadsheets/d/18yoEGQM-VYhVji9W-J_V4MzgG4cKMkkH/edit?rtpof=true',
                'active' => [],
                'external' => true,
            ],
            [
                'key' => 'fatigue',
                'label' => 'Penilaian Fatik Individual',
                'icon' => 'shield-check',
                'children' => [
                    ['label' => 'Formulir Penilaian', 'route' => route('fatigue.create'), 'active' => ['fatigue.create']],
                    ['label' => 'Riwayat Penilaian', 'route' => route('fatigue.index'), 'active' => ['fatigue.index', 'fatigue.show']],
                ],
                'active' => ['fatigue.*'],
            ],
            [
                'key' => 'submissions_folder',
                'label' => 'Kumpulan Pengajuan',
                'icon' => 'folder',
                'route' => 'https://onedrive.live.com/?id=%2Fpersonal%2F2e830b3238f2a5c2%2FDocuments2%2FKumpulan%20Pengajuan&listurl=%2Fpersonal%2F2e830b3238f2a5c2%2FDocuments2&ithint=folder&e=ocstCD&migratedtospo=true&redeem=aHR0cHM6Ly8xZHJ2Lm1zL2YvYy8yZTgzMGIzMjM4ZjJhNWMyL0VyVmVzUG11OW1GS3RHU0tialdDUHJNQkxDcV84emtRSGRGa1B3MXpGVHNaZGc%5FZT1vY3N0Q0Q&ga=1',
                'active' => [],
                'external' => true,
            ],
            [
                'key' => 'reports',
                'label' => 'Laporan',
                'icon' => 'chart',
                'route' => route('reports.index'),
                'active' => ['reports.*'],
                'visible' => (bool) $user?->isAdmin(),
            ],
            [
                'key' => 'users',
                'label' => 'Pengaturan Pengguna',
                'icon' => 'users',
                'route' => route('users.index'),
                'active' => ['users.*'],
                'visible' => (bool) $user?->isAdmin(),
            ],
        ])->filter(fn (array $item) => $item['visible'] ?? true)->values();
    }

    /**
     * Item bottom navigation untuk tampilan mobile.
     *
     * @return array<int, array<string, mixed>>
     */
    public function bottomNav(): array
    {
        return [
            ['key' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'home', 'route' => route('dashboard'), 'active' => ['dashboard']],
            ['key' => 'scan', 'label' => 'Scan', 'icon' => 'scan', 'route' => route('scan.index'), 'active' => ['scan.*', 'assets.scan']],
            ['key' => 'history', 'label' => 'Riwayat', 'icon' => 'clock', 'route' => route('loans.history'), 'active' => ['loans.*']],
            ['key' => 'profile', 'label' => 'Profil', 'icon' => 'user', 'route' => route('profile.edit'), 'active' => ['profile.*']],
        ];
    }

    public function render(): View
    {
        return view('layouts.app');
    }
}