<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\ConsignmentController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\ReturnController;
use App\Http\Controllers\SettlementController;


// ==================================================
// HALAMAN UTAMA
// ==================================================

Route::get('/', function () {
    return redirect()->route('dashboard');
});


// ==================================================
// AUTHENTICATION
// ==================================================

Route::middleware('guest')->group(function () {

    Route::get('/login', [LoginController::class, 'create'])
        ->name('login');

    Route::post('/login', [LoginController::class, 'store']);

    Route::get('/register', [RegisterController::class, 'create'])
        ->name('register');

    Route::post('/register', [RegisterController::class, 'store']);

});

Route::post('/logout', [LoginController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');


// ==================================================
// DASHBOARD
// ==================================================

Route::middleware(['auth', 'role:umkm,store,admin'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

});


// ==================================================
// PRODUK
// ==================================================

Route::middleware(['auth', 'role:umkm,admin'])->group(function () {

    Route::resource('products', ProductController::class);

});


// ==================================================
// TOKO MITRA
// KHUSUS ADMIN
// ==================================================

Route::middleware(['auth', 'role:admin'])->group(function () {

    Route::resource('stores', StoreController::class);

});


// ==================================================
// TRANSAKSI KONSINYASI
// ==================================================

Route::middleware(['auth', 'role:umkm,admin'])->group(function () {

    Route::get('/consignments/create', [ConsignmentController::class, 'create'])
        ->name('consignments.create');

    Route::post('/consignments', [ConsignmentController::class, 'store'])
        ->name('consignments.store');

});

Route::middleware(['auth', 'role:umkm,store,admin'])->group(function () {

    Route::get('/consignments', [ConsignmentController::class, 'index'])
        ->name('consignments.index');

    Route::get('/consignments/{consignment}', [ConsignmentController::class, 'show'])
        ->name('consignments.show');

    Route::get('/consignments/{consignment}/edit', [ConsignmentController::class, 'edit'])
        ->name('consignments.edit');

    Route::put('/consignments/{consignment}', [ConsignmentController::class, 'update'])
        ->name('consignments.update');

    Route::delete('/consignments/{consignment}', [ConsignmentController::class, 'destroy'])
        ->name('consignments.destroy');

});


// ==================================================
// PENJUALAN
// ==================================================

Route::middleware(['auth', 'role:store,admin'])->group(function () {

    Route::get('/sales/create', [SalesController::class, 'create'])
        ->name('sales.create');

    Route::post('/sales', [SalesController::class, 'store'])
        ->name('sales.store');

    Route::delete('/sales/{sale}', [SalesController::class, 'destroy'])
        ->name('sales.destroy');

});

Route::middleware(['auth', 'role:umkm,store,admin'])->group(function () {

    Route::get('/sales', [SalesController::class, 'index'])
        ->name('sales.index');

    Route::get('/sales/{sale}', [SalesController::class, 'show'])
        ->name('sales.show');

});


// ==================================================
// RETUR BARANG
// ==================================================

Route::middleware(['auth', 'role:store,admin'])->group(function () {

    Route::get('/returns/create', [ReturnController::class, 'create'])
        ->name('returns.create');

    Route::post('/returns', [ReturnController::class, 'store'])
        ->name('returns.store');

    Route::delete('/returns/{return}', [ReturnController::class, 'destroy'])
        ->name('returns.destroy');

});

Route::middleware(['auth', 'role:umkm,store,admin'])->group(function () {

    Route::get('/returns', [ReturnController::class, 'index'])
        ->name('returns.index');

    Route::get('/returns/{return}', [ReturnController::class, 'show'])
        ->name('returns.show');

});


// ==================================================
// SETTLEMENT / SETORAN
// ==================================================

Route::middleware(['auth', 'role:admin'])->group(function () {

    Route::resource('settlements', SettlementController::class);

});


// ==================================================
// LAPORAN
// ==================================================

Route::middleware(['auth', 'role:admin,umkm,store'])->group(function () {

    Route::get('/reports', function () {
        return view('reports.index');
    })->name('reports.index');

});
