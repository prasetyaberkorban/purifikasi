<?php

namespace Database\Seeders;

use App\Enums\AssetCondition;
use App\Enums\LoanStatus;
use App\Enums\UserRole;
use App\Models\Asset;
use App\Models\AssetActivity;
use App\Models\AssetLoan;
use App\Models\Category;
use App\Models\Location;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedUsers();
        $this->seedSettings();

        $categories = $this->seedCategories();
        $locations = $this->seedLocations();
        $assets = $this->seedAssets($categories, $locations);
        $this->seedLoans($assets);
    }

    private function seedUsers(): void
    {
        $users = [
            ['name' => 'Andi Prasetyo', 'username' => 'andi.prasetyo', 'email' => 'admin@purifikasi.co.id', 'role' => UserRole::Admin, 'unit' => 'Manajemen Kantor', 'phone' => '0812-1100-2201'],
            ['name' => 'Sri Wahyuni', 'username' => 'sri.wahyuni', 'email' => 'logistik@purifikasi.co.id', 'role' => UserRole::Admin, 'unit' => 'Logistik SGI', 'phone' => '0812-1100-2202'],
            ['name' => 'Bagus Nugroho', 'username' => 'bagus.nugroho', 'email' => 'pegawai@purifikasi.co.id', 'role' => UserRole::Karyawan, 'unit' => 'Operasional', 'phone' => '0812-1100-2203'],
            ['name' => 'Dewi Lestari', 'username' => 'dewi.lestari', 'email' => 'dewi@purifikasi.co.id', 'role' => UserRole::Karyawan, 'unit' => 'Quality Control', 'phone' => '0812-1100-2204'],
        ];

        foreach ($users as $data) {
            User::query()->updateOrCreate(
                ['email' => $data['email']],
                $data + ['password' => Hash::make('password'), 'is_active' => true],
            );
        }
    }

    private function seedSettings(): void
    {
        $settings = [
            ['key' => 'company_name', 'value' => 'PT. Purifikasi', 'group' => 'umum', 'label' => 'Nama Perusahaan'],
            ['key' => 'company_unit', 'value' => 'OFFICE DATABASE KANTOR', 'group' => 'umum', 'label' => 'Nama Unit'],
            ['key' => 'company_address', 'value' => 'Margonda, Depok - Jawa Barat', 'group' => 'umum', 'label' => 'Alamat'],
            ['key' => 'low_stock_threshold', 'value' => '3', 'group' => 'inventaris', 'label' => 'Ambang Stok Kritis'],
            ['key' => 'default_loan_days', 'value' => '7', 'group' => 'peminjaman', 'label' => 'Durasi Pinjam (hari)'],
            ['key' => 'label_footer', 'value' => 'SGI - Sistem Inventaris Kantor Purifikasi', 'group' => 'label', 'label' => 'Footer Label Aset'],
        ];

        foreach ($settings as $setting) {
            Setting::query()->updateOrCreate(['key' => $setting['key']], $setting);
        }
    }

    /** @return array<string, Category> */
    private function seedCategories(): array
    {
        $rows = [
            ['code' => 'PL', 'name' => 'Peralatan Lapangan', 'color' => '#1E6B38', 'description' => 'Perkakas dan peralatan kerja lapangan.'],
            ['code' => 'KK', 'name' => 'Kebersihan & Konsumabel', 'color' => '#8B8540', 'description' => 'Alat kebersihan dan bahan habis pakai.'],
            ['code' => 'PD', 'name' => 'Perangkat Digital', 'color' => '#6495ED', 'description' => 'Komputer, laptop, dan perangkat elektronik kantor.'],
            ['code' => 'BP', 'name' => 'Bahan & Part', 'color' => '#0D4C1E', 'description' => 'Suku cadang dan bahan teknik.'],
            ['code' => 'PE', 'name' => 'Perkakas Elektrik', 'color' => '#B45309', 'description' => 'Mesin dan perkakas bertenaga listrik.'],
            ['code' => 'F&I', 'name' => 'Furniture & Interior', 'color' => '#7C3AED', 'description' => 'Meja, kursi, lemari, dan perlengkapan ruang.'],
        ];

        $categories = [];

        foreach ($rows as $row) {
            $categories[$row['code']] = Category::query()->updateOrCreate(['code' => $row['code']], $row);
        }

        return $categories;
    }

    /** @return array<string, Location> */
    private function seedLocations(): array
    {
        $rows = [
            ['name' => 'Gudang Utama', 'city' => 'Depok', 'address' => 'Jl. Margonda Raya No. 12', 'pic_name' => 'Sri Wahyuni', 'pic_phone' => '0812-1100-2202'],
            ['name' => 'Kantor Pusat', 'city' => 'Jakarta', 'address' => 'Jl. TB Simatupang No. 8', 'pic_name' => 'Andi Prasetyo', 'pic_phone' => '0812-1100-2201'],
            ['name' => 'Workshop', 'city' => 'Depok', 'address' => 'Area Workshop SGI', 'pic_name' => 'Bagus Nugroho', 'pic_phone' => '0812-1100-2203'],
        ];

        $locations = [];

        foreach ($rows as $row) {
            $locations[$row['name']] = Location::query()->updateOrCreate(
                ['name' => $row['name'], 'city' => $row['city']],
                $row,
            );
        }

        return $locations;
    }

    /**
     * Data inventaris awal, mengikuti contoh pada desain (Linggis, Cangkul,
     * Palu Besi Martil, Sepatu Boot, Sekop, Gerobak Dorong, Sarung Tangan).
     *
     * @param  array<string, Category>  $categories
     * @param  array<string, Location>  $locations
     * @return array<int, Asset>
     */
    private function seedAssets(array $categories, array $locations): array
    {
        $rows = [
            ['Linggis', 'PL', 'Gudang Utama', 'Gudang A', 12, 3, 125000, AssetCondition::BelumDicek, 'SGI'],
            ['Cangkul', 'PL', 'Gudang Utama', 'Gudang A', 18, 4, 95000, AssetCondition::BelumDicek, 'SGI'],
            ['Palu Besi Martil', 'PL', 'Gudang Utama', 'Gudang A', 15, 3, 78000, AssetCondition::BelumDicek, 'SGI'],
            ['Sepatu Boot', 'PL', 'Gudang Utama', 'Gudang A', 24, 6, 165000, AssetCondition::BelumDicek, 'SGI'],
            ['Sekop', 'PL', 'Gudang Utama', 'Gudang A', 10, 3, 88000, AssetCondition::BelumDicek, 'SGI'],
            ['Gerobak Dorong', 'PL', 'Gudang Utama', 'Gudang A', 4, 1, 985000, AssetCondition::BelumDicek, 'SGI'],
            ['Sarung Tangan Safety', 'KK', 'Gudang Utama', 'Rak Konsumabel', 60, 12, 35000, AssetCondition::Baik, 'SGI'],
            ['Sapu Lidi', 'KK', 'Kantor Pusat', 'Lobi', 8, 2, 25000, AssetCondition::Baik, 'GA'],
            ['Laptop Lenovo ThinkPad', 'PD', 'Kantor Pusat', 'Ruang IT', 5, 1, 14500000, AssetCondition::Baik, 'IT'],
            ['Kursi Kerja Ergonomis', 'F&I', 'Kantor Pusat', 'Ruang Meeting', 10, 2, 1250000, AssetCondition::Baik, 'GA'],
            ['Proyektor Epson EB-X51', 'PD', 'Kantor Pusat', 'Ruang Meeting', 3, 1, 6750000, AssetCondition::RusakRingan, 'IT'],
            ['Meja Kerja 120cm', 'F&I', 'Kantor Pusat', 'Area Staf', 14, 3, 1450000, AssetCondition::Baik, 'GA'],
            ['Lemari Arsip Besi', 'F&I', 'Kantor Pusat', 'Ruang Arsip', 6, 2, 2350000, AssetCondition::Baik, 'GA'],
            ['Mesin Bor Bosch GSB 550', 'PE', 'Workshop', 'Rak Perkakas', 4, 1, 785000, AssetCondition::Baik, 'SGI'],
            ['Gerinda Tangan Makita', 'PE', 'Workshop', 'Rak Perkakas', 3, 1, 920000, AssetCondition::RusakRingan, 'SGI'],
            ['Kabel Roll 50m', 'PE', 'Workshop', 'Rak Perkakas', 6, 2, 640000, AssetCondition::Baik, 'SGI'],
            ['Tabung APAR 3kg', 'KK', 'Gudang Utama', 'Koridor', 12, 4, 385000, AssetCondition::Baik, 'K3'],
            ['Lampu Emergensi LED', 'PE', 'Gudang Utama', 'Koridor', 9, 3, 215000, AssetCondition::BelumDicek, 'K3'],
            ['Genset Portable 2000W', 'PE', 'Workshop', 'Area Parkir', 2, 1, 4250000, AssetCondition::Baik, 'SGI'],
            ['Hand Pallet 2.5 Ton', 'PL', 'Gudang Utama', 'Gudang B', 2, 1, 3650000, AssetCondition::Rusak, 'SGI'],
            ['Tang Kombinasi 8"', 'PL', 'Workshop', 'Rak Perkakas', 20, 5, 62000, AssetCondition::Baik, 'SGI'],
            ['Set Kunci Pas 12pcs', 'PL', 'Workshop', 'Rak Perkakas', 7, 2, 340000, AssetCondition::Baik, 'SGI'],
            ['Printer Canon LBP6030', 'PD', 'Kantor Pusat', 'Ruang Administrasi', 4, 1, 1890000, AssetCondition::Baik, 'ADM'],
            ['Dispenser Air Galon', 'F&I', 'Kantor Pusat', 'Pantry', 3, 1, 1150000, AssetCondition::Baik, 'GA'],
            ['Whiteboard 120x240', 'F&I', 'Kantor Pusat', 'Ruang Training', 2, 1, 985000, AssetCondition::Baik, 'GA'],
            ['Troli Barang 3 Tingkat', 'PL', 'Gudang Utama', 'Gudang B', 3, 1, 1450000, AssetCondition::BelumDicek, 'SGI'],
            ['Kertas HVS A4 (rim)', 'KK', 'Kantor Pusat', 'Ruang Administrasi', 2, 6, 58000, AssetCondition::Baik, 'ADM'],
            ['Tinta Printer Canon 810', 'KK', 'Kantor Pusat', 'Ruang Administrasi', 1, 4, 135000, AssetCondition::Baik, 'ADM'],
            ['Masker Medis (box)', 'KK', 'Gudang Utama', 'Rak Konsumabel', 3, 10, 45000, AssetCondition::Baik, 'K3'],
            ['Baterai AA (pack)', 'KK', 'Gudang Utama', 'Rak Konsumabel', 4, 8, 32000, AssetCondition::Baik, 'SGI'],
        ];

        $assets = [];
        $purchaseYears = [2023, 2024, 2025, 2026];

        foreach ($rows as $index => [$name, $categoryCode, $locationName, $room, $quantity, $minimum, $price, $condition, $ownerUnit]) {
            $category = $categories[$categoryCode];
            $location = $locations[$locationName];

            $purchaseDate = now()
                ->subYears($purchaseYears[$index % 4] === 2026 ? 0 : 2026 - $purchaseYears[$index % 4])
                ->subDays(($index * 7) % 300)
                ->startOfMonth()
                ->addDays(($index * 3) % 25);

            /** @var Asset $asset */
            $asset = Asset::query()->updateOrCreate(
                ['code' => sprintf('%s/%03d', Asset::codePrefix($category, $location), $index + 1)],
                [
                    'name' => $name,
                    'serial_number' => sprintf('SN-%s-%05d', strtoupper($category->code), 1000 + $index * 7),
                    'brand' => null,
                    'category_id' => $category->getKey(),
                    'location_id' => $location->getKey(),
                    'room' => $room,
                    'owner_unit' => $ownerUnit,
                    'quantity' => $quantity,
                    'minimum_stock' => $minimum,
                    'condition' => $condition,
                    'purchase_date' => $purchaseDate,
                    'purchase_price' => $price,
                    'description' => "Aset {$name} terdaftar pada unit {$ownerUnit}.",
                    'last_checked_at' => $condition === AssetCondition::BelumDicek ? null : now()->subDays($index % 30),
                ],
            );

            $asset->syncStatus()->save();

            AssetActivity::query()->create([
                'asset_id' => $asset->getKey(),
                'user_id' => User::query()->where('email', 'logistik@purifikasi.co.id')->value('id'),
                'type' => 'created',
                'subject' => $asset->code,
                'description' => "Aset {$asset->name} ({$asset->code}) didaftarkan ke inventaris.",
            ]);

            $assets[] = $asset;
        }

        return $assets;
    }

    /** @param  array<int, Asset>  $assets */
    private function seedLoans(array $assets): void
    {
        $borrowers = User::query()->whereIn('email', ['pegawai@purifikasi.co.id', 'dewi@purifikasi.co.id', 'logistik@purifikasi.co.id'])->get();
        $handler = User::query()->where('email', 'logistik@purifikasi.co.id')->first();

        $plans = [
            [8, 1, 'Pemeriksaan jaringan distribusi', 6, 0],
            [10, 1, 'Rapat koordinasi bulanan', 3, 1],
            [15, 2, 'Pekerjaan fabrikasi workshop', 12, 2],
            [6, 1, 'Pemindahan material gudang', 2, 3],
        ];

        foreach ($plans as [$assetIndex, $quantity, $purpose, $daysAgo, $borrowerIndex]) {
            $asset = $assets[$assetIndex];
            $borrower = $borrowers[$borrowerIndex % $borrowers->count()];

            /** @var AssetLoan $loan */
            $loan = AssetLoan::query()->create([
                'code' => sprintf('PJM.%s.%04d', now()->format('Y.m'), $assetIndex + 1),
                'asset_id' => $asset->getKey(),
                'borrower_id' => $borrower->getKey(),
                'handled_by' => $handler?->getKey(),
                'quantity' => min($quantity, $asset->availableQuantity()),
                'purpose' => $purpose,
                'room' => $asset->room,
                'borrowed_at' => now()->subDays($daysAgo),
                'due_at' => now()->subDays($daysAgo)->addDays(7)->toDateString(),
                'status' => LoanStatus::Dipinjam,
            ]);

            $asset->borrowed_quantity += $loan->quantity;
            $asset->syncStatus()->save();

            AssetActivity::query()->create([
                'asset_id' => $asset->getKey(),
                'user_id' => $handler?->getKey(),
                'type' => 'loan',
                'subject' => $loan->code,
                'description' => "{$borrower->name} meminjam {$loan->quantity} unit {$asset->name} ({$loan->code}).",
            ]);
        }

        // Satu riwayat peminjaman yang sudah selesai.
        $returned = $assets[13];
        /** @var AssetLoan $oldLoan */
        $oldLoan = AssetLoan::query()->create([
            'code' => sprintf('PJM.%s.%04d', now()->subMonth()->format('Y.m'), 99),
            'asset_id' => $returned->getKey(),
            'borrower_id' => $borrowers[1]->getKey(),
            'handled_by' => $handler?->getKey(),
            'quantity' => 1,
            'purpose' => 'Perbaikan rangka kanopi',
            'room' => $returned->room,
            'borrowed_at' => now()->subDays(21),
            'due_at' => now()->subDays(14)->toDateString(),
            'returned_at' => now()->subDays(15),
            'status' => LoanStatus::Dikembalikan,
            'condition_on_return' => AssetCondition::Baik,
            'note' => 'Dikembalikan lengkap dengan kotak penyimpanan.',
        ]);

        AssetActivity::query()->create([
            'asset_id' => $returned->getKey(),
            'user_id' => $handler?->getKey(),
            'type' => 'return',
            'subject' => $oldLoan->code,
            'description' => "{$oldLoan->code} dikembalikan (1 unit {$returned->name}). Kondisi: Baik.",
        ]);
    }
}