# Sistem Inventaris Kantor PURIFIKASI

Aplikasi inventaris kantor berbasis **Laravel 13**, **Tailwind CSS 4**, **Alpine.js**, dan **Filament 5** untuk panel admin.
Desain mengikuti identitas visual Purifikasi: hijau hutan `#0D4C1E`, hijau daun `#1E6B38`, biru lembut `#6495ED`, zaitun/emas `#8B8540`, latar `#F8F9FA`.

## Fitur

- **Dashboard** — kartu Total Aset, Barang Dipinjam, Stok Kritis, Aktivitas Terbaru; grafik unit per kategori, nilai perolehan per tahun, dan donat kondisi aset.
- **Aset & Barang** — CRUD lengkap (nama, serial number, kategori, lokasi, foto, tanggal beli, harga), kode aset otomatis (`KOTA.KATEGORI/TAHUN/SGI.URUT`), pencarian & filter (kategori, status, lokasi, kondisi, unit pemilik), tabel desktop dan grid kartu mobile.
- **Label QR / Barcode** — QR berisi URL detail aset (`/aset/kode/{kode}`) dan barcode Code-128 berisi kode aset; halaman cetak A4 (QR / barcode / keduanya) dengan pratinjau.
- **Scanner** — pemindaian lewat kamera HP (`html5-qrcode`) atau input manual kode/URL; kode aset, serial number, maupun URL label dikenali.
- **Peminjaman** — pencatatan pinjam, stok & status otomatis menyesuaikan, pengembalian dengan kondisi barang, deteksi keterlambatan, riwayat dengan jejak aktivitas.
- **Laporan** — rekap aset & peminjaman, ekspor **PDF** (DomPDF) dan **Excel XLSX** (PhpSpreadsheet) dengan filter tanggal/kategori/lokasi/status.
- **Hak akses** — Admin dan Karyawan (middleware `role` + policy aset/peminjaman).
- **Masuk & Pendaftaran** — masuk memakai **username atau email** (tanpa membedakan huruf besar/kecil); pendaftaran mandiri di `/daftar` otomatis memberi peran **Karyawan**, sedangkan peran **Admin** hanya dapat diberikan Admin lain dari dashboard Pengaturan Pengguna.
- **Panel admin Filament** di `/admin` dengan sidebar bertema hijau hutan, resource Aset, Peminjaman (aksi "Kembalikan"), Kategori, Lokasi, Pengguna, serta widget statistik, donut kondisi, dan tabel stok kritis.
- **Mobile-first** — drawer hamburger, bottom navigation (Dashboard, Scan, Riwayat, Profil), FAB "Tambah Barang" warna zaitun.

## Kebutuhan

- PHP **8.3+** dengan ekstensi `pdo_sqlite`, `gd`, `intl`, `zip`, `fileinfo`, `mbstring`, `openssl`
- Composer 2
- Node.js 20+ dan npm

## Instalasi

```bash
composer install
cp .env.example .env
php artisan key:generate

# Database default: SQLite
touch database/database.sqlite
php artisan migrate --seed

php artisan storage:link

npm install
npm run build
```

Jalankan server:

```bash
php artisan serve
```

Buka `http://localhost:8000` (aplikasi staf) dan `http://localhost:8000/admin` (panel admin).

### Menggunakan MySQL (opsional)

Ubah `.env` lalu jalankan ulang migrasi:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=purifikasi_inventaris
DB_USERNAME=root
DB_PASSWORD=
```

## Akun demo (hasil seeder)

| Peran | Username | Email | Kata sandi |
|---|---|---|---|
| Admin | `andi.prasetyo` | `admin@purifikasi.co.id` | `password` |
| Admin | `sri.wahyuni` | `logistik@purifikasi.co.id` | `password` |
| Karyawan | `bagus.nugroho` | `pegawai@purifikasi.co.id` | `password` |
| Karyawan | `dewi.lestari` | `dewi@purifikasi.co.id` | `password` |

Masuk dapat memakai username maupun email pada kolom yang sama.

Seeder mengisi 30 aset (276+ unit), 6 kategori, 3 lokasi, peminjaman aktif, dan riwayat pengembalian agar dashboard, grafik, serta ekspor langsung berisi data.

## Struktur penting

```
app/
  Enums/                 AssetStatus, AssetCondition, LoanStatus, UserRole
  Http/Controllers/      Dashboard, Asset, Loan, Scan/Label, Report, User, Profile
  Http/Middleware/       EnsureUserRole (alias: role)
  Models/                Asset, AssetLoan, AssetActivity, Category, Location, Setting, User
  Policies/              AssetPolicy, AssetLoanPolicy
  Services/              LoanService (transaksi pinjam/kembali), InventoryStats, LabelService,
                         PdfReportService, ExcelReportService
  Filament/              Resource panel admin + Widget
  View/Components/       AppLayout (menu sidebar + bottom nav), StatusBadge
resources/views/
  layouts/app.blade.php          shell staf (sidebar, topbar, FAB, bottom nav)
  dashboard.blade.php            dashboard utama
  assets/, loans/, labels/, reports/, settings/, profile/, notifications/, scan/
  components/                    stat-card, donut-chart, bar-chart, asset-card, asset-thumb, ui-icon
resources/css/app.css            token palet + komponen (card, btn-*, badge-*, data-table, field)
resources/css/filament/admin/    tema panel admin (sidebar hutan, menu aktif zaitun)
```

## Logika stok & status

- `assets.borrowed_quantity` menyimpan jumlah unit yang sedang dipinjam.
- Status diturunkan dari stok: `quantity < 1` atau `availableQuantity() < 1` → **Habis**; ada yang dipinjam → **Dipinjam**; selain itu **Tersedia**.
- `Asset::syncStatus()` dipanggil pada setiap perubahan aset, peminjaman, dan pengembalian.
- `LoanService` membungkus aksi dalam transaksi + `lockForUpdate()` sehingga peminjaman bersamaan tidak dapat melebihi stok.
- Stok kritis: `availableQuantity() <= minimum_stock` (ditampilkan pada notifikasi, kartu dashboard, dan filter panel admin).

## Perintah berguna

```bash
php artisan migrate:fresh --seed     # reset + isi data demo
php artisan test                     # 29 test (28 feature + 1 unit: alur pinjam/kembali, hak akses, masuk & daftar, ekspor)
npm run build                        # build aset Vite
php artisan filament:optimize        # cache komponen Filament (produksi)
```

## Catatan produksi

- Set `APP_ENV=production`, `APP_DEBUG=false`, dan `APP_URL` sesuai domain; skema HTTPS dipaksa otomatis pada environment produksi.
- Jalankan `php artisan config:cache route:cache view:cache` setelah build aset.
- URL QR memakai `APP_URL`, jadi tetapkan domain final sebelum mencetak label agar hasil scan mengarah ke host yang benar.