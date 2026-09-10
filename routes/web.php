<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\BarangKeluarController;
use App\Http\Controllers\BarangMasukController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\JenisController;
use App\Http\Controllers\KeranjangController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\LokasiController;
use App\Http\Controllers\OpnameController;
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\RakController;
use App\Http\Controllers\SatuanController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => redirect()->route('login'));

Route::middleware('guest')->group(function () {
    Route::get('login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AuthController::class, 'login'])->name('login.post');
});

Route::post('logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Barang — Kepala Gudang tidak boleh akses (1:1 content.php)
    Route::middleware('role_not:Kepala Gudang')->group(function () {
    // AJAX harus didefinisikan sebelum route show {id}
    Route::get('barang/keranjang', [BarangController::class, 'getKeranjang'])->name('barang.getKeranjang');
    Route::match(['get', 'post'], 'barang/ajax', [BarangController::class, 'getBarangAjax'])->name('barang.ajax');
    Route::get('barang', [BarangController::class, 'index'])->name('barang.index');
    Route::get('barang/create', [BarangController::class, 'create'])->name('barang.create');
    Route::post('barang', [BarangController::class, 'store'])->name('barang.store');
    Route::get('barang/{id}', [BarangController::class, 'show'])->name('barang.show');
    Route::get('barang/{id}/edit', [BarangController::class, 'edit'])->name('barang.edit');
    Route::put('barang/{id}', [BarangController::class, 'update'])->name('barang.update');
    Route::delete('barang/{id}', [BarangController::class, 'destroy'])->name('barang.destroy');
    });

    // Barang Masuk (port _legacy/modules/barang-masuk) — Kepala Gudang tidak boleh akses
    Route::middleware('role_not:Kepala Gudang')->group(function () {
        // AJAX harus didefinisikan sebelum route {id}
        Route::get('barang-masuk/ajax-barang', [BarangMasukController::class, 'ajaxBarang'])->name('barang-masuk.ajax-barang');
        Route::get('barang-masuk/ajax-location', [BarangMasukController::class, 'ajaxLocation'])->name('barang-masuk.ajax-location');
        Route::get('barang-masuk', [BarangMasukController::class, 'index'])->name('barang-masuk.index');
        Route::get('barang-masuk/create', [BarangMasukController::class, 'create'])->name('barang-masuk.create');
        Route::post('barang-masuk', [BarangMasukController::class, 'store'])->name('barang-masuk.store');
        Route::delete('barang-masuk/{id}', [BarangMasukController::class, 'destroy'])->name('barang-masuk.destroy');
    });

    // Barang Keluar (port _legacy/modules/barang-keluar) — Kepala Gudang tidak boleh akses list/entri
    // AJAX + status + pengembalian + cetak + detail didefinisikan sebelum route show {id}
    Route::get('barang-keluar/ajax-barang', [BarangKeluarController::class, 'ajaxBarang'])->name('barang-keluar.ajax-barang');
    Route::get('barang-keluar/ajax-serials', [BarangKeluarController::class, 'ajaxSerials'])->name('barang-keluar.ajax-serials');
    Route::get('barang-keluar/ajax-location', [BarangKeluarController::class, 'ajaxLocation'])->name('barang-keluar.ajax-location');
    Route::match(['get', 'post'], 'barang-keluar/status', [BarangKeluarController::class, 'updateStatus'])->name('barang-keluar.status');
    Route::get('barang-keluar/pengembalian/{id?}', [BarangKeluarController::class, 'pengembalianForm'])->name('barang-keluar.pengembalian.form');
    Route::post('barang-keluar/pengembalian/{id?}', [BarangKeluarController::class, 'prosesPengembalian'])->name('barang-keluar.pengembalian.store');
    Route::middleware('role_not:Kepala Gudang')->group(function () {
        Route::get('barang-keluar', [BarangKeluarController::class, 'index'])->name('barang-keluar.index');
        Route::get('barang-keluar/create', [BarangKeluarController::class, 'create'])->name('barang-keluar.create');
        Route::post('barang-keluar', [BarangKeluarController::class, 'store'])->name('barang-keluar.store');
        Route::delete('barang-keluar/{id}', [BarangKeluarController::class, 'destroy'])->name('barang-keluar.destroy');
    });
    Route::get('barang-keluar/{id}/cetak', [BarangKeluarController::class, 'cetak'])->name('barang-keluar.cetak');
    Route::get('barang-keluar/{id}', [BarangKeluarController::class, 'show'])->name('barang-keluar.show');

    // MASTER Jenis / Satuan / Rak / Keranjang / Lokasi (port _legacy/modules/...) — Kepala Gudang tidak boleh akses
    Route::middleware('role_not:Kepala Gudang')->group(function () {
        // Jenis Barang (export didefinisikan sebelum route {id})
        Route::get('jenis/export', [JenisController::class, 'export'])->name('jenis.export');
        Route::get('jenis', [JenisController::class, 'index'])->name('jenis.index');
        Route::get('jenis/create', [JenisController::class, 'create'])->name('jenis.create');
        Route::post('jenis', [JenisController::class, 'store'])->name('jenis.store');
        Route::get('jenis/{id}/edit', [JenisController::class, 'edit'])->name('jenis.edit');
        Route::put('jenis/{id}', [JenisController::class, 'update'])->name('jenis.update');
        Route::delete('jenis/{id}', [JenisController::class, 'destroy'])->name('jenis.destroy');

        // Satuan (export didefinisikan sebelum route {id})
        Route::get('satuan/export', [SatuanController::class, 'export'])->name('satuan.export');
        Route::get('satuan', [SatuanController::class, 'index'])->name('satuan.index');
        Route::get('satuan/create', [SatuanController::class, 'create'])->name('satuan.create');
        Route::post('satuan', [SatuanController::class, 'store'])->name('satuan.store');
        Route::get('satuan/{id}/edit', [SatuanController::class, 'edit'])->name('satuan.edit');
        Route::put('satuan/{id}', [SatuanController::class, 'update'])->name('satuan.update');
        Route::delete('satuan/{id}', [SatuanController::class, 'destroy'])->name('satuan.destroy');

        // Rak
        Route::get('rak', [RakController::class, 'index'])->name('rak.index');
        Route::get('rak/create', [RakController::class, 'create'])->name('rak.create');
        Route::post('rak', [RakController::class, 'store'])->name('rak.store');
        Route::get('rak/{id}/edit', [RakController::class, 'edit'])->name('rak.edit');
        Route::put('rak/{id}', [RakController::class, 'update'])->name('rak.update');
        Route::delete('rak/{id}', [RakController::class, 'destroy'])->name('rak.destroy');

        // Keranjang
        Route::get('keranjang', [KeranjangController::class, 'index'])->name('keranjang.index');
        Route::get('keranjang/create', [KeranjangController::class, 'create'])->name('keranjang.create');
        Route::post('keranjang', [KeranjangController::class, 'store'])->name('keranjang.store');
        Route::get('keranjang/{id}/edit', [KeranjangController::class, 'edit'])->name('keranjang.edit');
        Route::put('keranjang/{id}', [KeranjangController::class, 'update'])->name('keranjang.update');
        Route::delete('keranjang/{id}', [KeranjangController::class, 'destroy'])->name('keranjang.destroy');

        // Lokasi Rak (hub overview, hanya index)
        Route::get('lokasi', [LokasiController::class, 'index'])->name('lokasi.index');
    });

    // USER (port _legacy/modules/user) — hanya Administrator
    Route::middleware('role:Administrator')->group(function () {
        Route::get('user', [UserController::class, 'index'])->name('user.index');
        Route::get('user/create', [UserController::class, 'create'])->name('user.create');
        Route::post('user', [UserController::class, 'store'])->name('user.store');
        Route::get('user/{id}/edit', [UserController::class, 'edit'])->name('user.edit');
        Route::put('user/{id}', [UserController::class, 'update'])->name('user.update');
        Route::delete('user/{id}', [UserController::class, 'destroy'])->name('user.destroy');
    });

    // PROFIL (port _legacy/modules/profil) — semua role yang login
    Route::get('profil', [ProfilController::class, 'edit'])->name('profil.edit');
    Route::put('profil', [ProfilController::class, 'update'])->name('profil.update');

    // PASSWORD (port _legacy/modules/password) — semua role yang login
    Route::get('password', [PasswordController::class, 'edit'])->name('password.edit');
    Route::put('password', [PasswordController::class, 'update'])->name('password.update');

    // LAPORAN (port _legacy/modules/laporan-*) — semua role yang login
    Route::get('laporan/stok', [LaporanController::class, 'stok'])->name('laporan.stok');
    Route::get('laporan/stok/cetak', [LaporanController::class, 'stokCetak'])->name('laporan.stok-cetak');
    Route::get('laporan/stok/export', [LaporanController::class, 'stokExport'])->name('laporan.stok-export');
    Route::get('laporan/masuk', [LaporanController::class, 'masuk'])->name('laporan.masuk');
    Route::get('laporan/masuk/cetak', [LaporanController::class, 'masukCetak'])->name('laporan.masuk-cetak');
    Route::get('laporan/masuk/export', [LaporanController::class, 'masukExport'])->name('laporan.masuk-export');
    Route::get('laporan/keluar', [LaporanController::class, 'keluar'])->name('laporan.keluar');
    Route::get('laporan/keluar/cetak', [LaporanController::class, 'keluarCetak'])->name('laporan.keluar-cetak');
    Route::get('laporan/keluar/export', [LaporanController::class, 'keluarExport'])->name('laporan.keluar-export');

    // STOCK OPNAME (port _legacy/modules/stock-opname) — Administrator & Admin Gudang
    Route::middleware('role:Administrator,Admin Gudang')->group(function () {
        Route::get('opname/get-stok', [OpnameController::class, 'getStok'])->name('opname.get-stok');
        Route::get('opname/export-pdf', [OpnameController::class, 'exportPdf'])->name('opname.export-pdf');
        Route::get('opname/export-csv', [OpnameController::class, 'exportCsv'])->name('opname.export-csv');
        Route::get('opname', [OpnameController::class, 'index'])->name('opname.index');
        Route::post('opname', [OpnameController::class, 'store'])->name('opname.store');
        Route::delete('opname/{id}', [OpnameController::class, 'destroy'])->name('opname.destroy');
    });
});
