<?php

use App\Http\Controllers\AssetController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ScanController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

// TEMPORARY — delete after migration runs
Route::get('/_mig_puri9x2k', function () {
    \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
    return '<pre style="font-family:monospace;padding:20px">' . \Illuminate\Support\Facades\Artisan::output() . '</pre>';
});

Route::middleware('guest')->group(function () {
    Route::get('masuk', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('masuk', [AuthenticatedSessionController::class, 'store'])->name('login.attempt');
    Route::get('daftar', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('daftar', [RegisteredUserController::class, 'store'])->name('register.store');
});

Route::middleware(['auth', 'role'])->group(function () {
    Route::post('keluar', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    Route::get('dashboard', DashboardController::class)->name('dashboard');

    // Aset & Barang
    Route::get('aset', [AssetController::class, 'index'])->name('assets.index');
    Route::get('aset/tambah', [AssetController::class, 'create'])->middleware('role:admin')->name('assets.create');
    Route::post('aset', [AssetController::class, 'store'])->middleware('role:admin')->name('assets.store');
    Route::get('aset/{asset}', [AssetController::class, 'show'])->name('assets.show');
    Route::get('aset/{asset}/ubah', [AssetController::class, 'edit'])->middleware('role:admin')->name('assets.edit');
    Route::put('aset/{asset}', [AssetController::class, 'update'])->middleware('role:admin')->name('assets.update');
    Route::delete('aset/{asset}', [AssetController::class, 'destroy'])->middleware('role:admin')->name('assets.destroy');
    Route::post('aset/{asset}/cek', [AssetController::class, 'check'])->middleware('role:admin')->name('assets.check');
    Route::post('aset/{asset}/unit', [AssetController::class, 'addItem'])->middleware('role:admin')->name('assets.items.store');
    Route::post('aset/{asset}/unit/{item}', [AssetController::class, 'updateItem'])->name('assets.items.update');
    Route::delete('aset/{asset}/unit/{item}', [AssetController::class, 'destroyItem'])->middleware('role:admin')->name('assets.items.destroy');

    // Label & QR
    Route::get('label', [ScanController::class, 'labels'])->middleware('role:admin')->name('labels.index');
    Route::get('label/cetak', [ScanController::class, 'print'])->middleware('role:admin')->name('labels.print');
    Route::get('scan', [ScanController::class, 'index'])->name('scan.index');
    Route::post('scan', [ScanController::class, 'resolve'])->name('scan.resolve');
    Route::get('aset/kode/{code}', [ScanController::class, 'show'])->where('code', '.*')->name('assets.scan');

    // Kategori & Lokasi
    Route::get('kategori', [CategoryController::class, 'index'])->middleware('role:admin')->name('categories.index');
    Route::post('kategori', [CategoryController::class, 'store'])->middleware('role:admin')->name('categories.store');
    Route::put('kategori/{category}', [CategoryController::class, 'update'])->middleware('role:admin')->name('categories.update');
    Route::delete('kategori/{category}', [CategoryController::class, 'destroy'])->middleware('role:admin')->name('categories.destroy');

    Route::get('lokasi', [LocationController::class, 'index'])->middleware('role:admin')->name('locations.index');
    Route::post('lokasi', [LocationController::class, 'store'])->middleware('role:admin')->name('locations.store');
    Route::put('lokasi/{location}', [LocationController::class, 'update'])->middleware('role:admin')->name('locations.update');
    Route::delete('lokasi/{location}', [LocationController::class, 'destroy'])->middleware('role:admin')->name('locations.destroy');

    // Peminjaman
    Route::get('peminjaman/aktif', [LoanController::class, 'active'])->name('loans.active');
    Route::get('peminjaman/riwayat', [LoanController::class, 'history'])->name('loans.history');
    Route::get('peminjaman/baru', [LoanController::class, 'create'])->middleware('role:admin')->name('loans.create');
    Route::post('peminjaman', [LoanController::class, 'store'])->middleware('role:admin')->name('loans.store');
    Route::get('peminjaman/{loan}', [LoanController::class, 'show'])->name('loans.show');
    Route::get('peminjaman/{loan}/kembali', [LoanController::class, 'returnForm'])->middleware('role:admin')->name('loans.return.form');
    Route::post('peminjaman/{loan}/kembali', [LoanController::class, 'returnBack'])->middleware('role:admin')->name('loans.return');

    // Service & Pemeliharaan Aset Kantor
    Route::get('service/formulir', [\App\Http\Controllers\AssetMaintenanceController::class, 'create'])->name('services.create');
    Route::post('service', [\App\Http\Controllers\AssetMaintenanceController::class, 'store'])->name('services.store');
    Route::get('service', [\App\Http\Controllers\AssetMaintenanceController::class, 'index'])->name('services.index');
    Route::get('service/ekspor/pdf', [\App\Http\Controllers\AssetMaintenanceController::class, 'exportPdf'])->name('services.export.pdf');
    Route::get('service/ekspor/excel', [\App\Http\Controllers\AssetMaintenanceController::class, 'exportExcel'])->name('services.export.excel');
    Route::get('service/{maintenance}', [\App\Http\Controllers\AssetMaintenanceController::class, 'show'])->name('services.show');

    // Realisasi Pengadaan & Aset Kantor
    Route::get('pengadaan/formulir', [\App\Http\Controllers\AssetProcurementController::class, 'create'])->name('procurements.create');
    Route::post('pengadaan', [\App\Http\Controllers\AssetProcurementController::class, 'store'])->name('procurements.store');
    Route::get('pengadaan', [\App\Http\Controllers\AssetProcurementController::class, 'index'])->name('procurements.index');
    Route::get('pengadaan/ekspor/pdf', [\App\Http\Controllers\AssetProcurementController::class, 'exportPdf'])->name('procurements.export.pdf');
    Route::get('pengadaan/ekspor/excel', [\App\Http\Controllers\AssetProcurementController::class, 'exportExcel'])->name('procurements.export.excel');
    Route::get('pengadaan/{procurement}', [\App\Http\Controllers\AssetProcurementController::class, 'show'])->name('procurements.show');
    Route::get('pengadaan/{procurement}/unduh', [\App\Http\Controllers\AssetProcurementController::class, 'download'])->name('procurements.download');

    // Izin / Cuti / WFA Karyawan & Magang
    Route::get('izin/formulir', [\App\Http\Controllers\EmployeeLeaveController::class, 'create'])->name('leaves.create');
    Route::post('izin', [\App\Http\Controllers\EmployeeLeaveController::class, 'store'])->name('leaves.store');
    Route::get('izin', [\App\Http\Controllers\EmployeeLeaveController::class, 'index'])->name('leaves.index');
    Route::get('izin/ekspor/pdf', [\App\Http\Controllers\EmployeeLeaveController::class, 'exportPdf'])->name('leaves.export.pdf');
    Route::get('izin/ekspor/excel', [\App\Http\Controllers\EmployeeLeaveController::class, 'exportExcel'])->name('leaves.export.excel');
    Route::get('izin/{leave}', [\App\Http\Controllers\EmployeeLeaveController::class, 'show'])->name('leaves.show');
    Route::get('izin/{leave}/unduh', [\App\Http\Controllers\EmployeeLeaveController::class, 'download'])->name('leaves.download');

    // Formulir Peminjaman Kendaraan
    Route::get('peminjaman-kendaraan/formulir', [\App\Http\Controllers\VehicleLoanController::class, 'create'])->name('vehicle-loans.create');
    Route::post('peminjaman-kendaraan', [\App\Http\Controllers\VehicleLoanController::class, 'store'])->name('vehicle-loans.store');
    Route::get('peminjaman-kendaraan', [\App\Http\Controllers\VehicleLoanController::class, 'index'])->name('vehicle-loans.index');
    Route::get('peminjaman-kendaraan/ekspor/pdf', [\App\Http\Controllers\VehicleLoanController::class, 'exportPdf'])->name('vehicle-loans.export.pdf');
    Route::get('peminjaman-kendaraan/ekspor/excel', [\App\Http\Controllers\VehicleLoanController::class, 'exportExcel'])->name('vehicle-loans.export.excel');
    Route::get('peminjaman-kendaraan/{vehicleLoan}', [\App\Http\Controllers\VehicleLoanController::class, 'show'])->name('vehicle-loans.show');

    // Daftar Hadir
    Route::get('daftar-hadir/formulir', [\App\Http\Controllers\MeetingAttendanceController::class, 'create'])->name('attendances.create');
    Route::post('daftar-hadir', [\App\Http\Controllers\MeetingAttendanceController::class, 'store'])->name('attendances.store');
    Route::get('daftar-hadir', [\App\Http\Controllers\MeetingAttendanceController::class, 'index'])->name('attendances.index');
    Route::get('daftar-hadir/ekspor/pdf', [\App\Http\Controllers\MeetingAttendanceController::class, 'exportPdf'])->name('attendances.export.pdf');
    Route::get('daftar-hadir/ekspor/excel', [\App\Http\Controllers\MeetingAttendanceController::class, 'exportExcel'])->name('attendances.export.excel');
    Route::get('daftar-hadir/{attendance}', [\App\Http\Controllers\MeetingAttendanceController::class, 'show'])->name('attendances.show');

    // Penilaian Fatik Individual
    Route::get('penilaian-fatik/formulir', [\App\Http\Controllers\FatigueAssessmentController::class, 'create'])->name('fatigue.create');
    Route::post('penilaian-fatik', [\App\Http\Controllers\FatigueAssessmentController::class, 'store'])->name('fatigue.store');
    Route::get('penilaian-fatik', [\App\Http\Controllers\FatigueAssessmentController::class, 'index'])->name('fatigue.index');
    Route::get('penilaian-fatik/{fatigue}', [\App\Http\Controllers\FatigueAssessmentController::class, 'show'])->name('fatigue.show');

    // Rekapitulasi Kontrak / SPK
    Route::get('kontrak/formulir', [\App\Http\Controllers\ProjectContractController::class, 'create'])->name('contracts.create');
    Route::post('kontrak', [\App\Http\Controllers\ProjectContractController::class, 'store'])->name('contracts.store');
    Route::get('kontrak', [\App\Http\Controllers\ProjectContractController::class, 'index'])->name('contracts.index');
    Route::get('kontrak/ekspor/pdf', [\App\Http\Controllers\ProjectContractController::class, 'exportPdf'])->name('contracts.export.pdf');
    Route::get('kontrak/ekspor/excel', [\App\Http\Controllers\ProjectContractController::class, 'exportExcel'])->name('contracts.export.excel');
    Route::get('kontrak/{contract}', [\App\Http\Controllers\ProjectContractController::class, 'show'])->name('contracts.show');
    Route::get('kontrak/{contract}/ubah', [\App\Http\Controllers\ProjectContractController::class, 'edit'])->name('contracts.edit');
    Route::put('kontrak/{contract}', [\App\Http\Controllers\ProjectContractController::class, 'update'])->name('contracts.update');
    Route::delete('kontrak/{contract}', [\App\Http\Controllers\ProjectContractController::class, 'destroy'])->name('contracts.destroy');
    Route::get('kontrak/{contract}/unduh', [\App\Http\Controllers\ProjectContractController::class, 'download'])->name('contracts.download');

    // Laporan
    Route::get('laporan', [ReportController::class, 'index'])->middleware('role:admin')->name('reports.index');
    Route::get('laporan/ekspor/{type}/pdf', [ReportController::class, 'exportPdf'])->middleware('role:admin')->name('reports.pdf');
    Route::get('laporan/ekspor/{type}/excel', [ReportController::class, 'exportExcel'])->middleware('role:admin')->name('reports.excel');

    // Pengaturan pengguna
    Route::get('pengguna', [UserController::class, 'index'])->middleware('role:admin')->name('users.index');
    Route::post('pengguna', [UserController::class, 'store'])->middleware('role:admin')->name('users.store');
    Route::put('pengguna/{user}', [UserController::class, 'update'])->middleware('role:admin')->name('users.update');
    Route::delete('pengguna/{user}', [UserController::class, 'destroy'])->middleware('role:admin')->name('users.destroy');

    // Profil & notifikasi
    Route::get('profil', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('profil', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('profil/sandi', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::get('notifikasi', [ProfileController::class, 'notifications'])->name('notifications');
});