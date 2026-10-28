<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\ConsignmentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReturnController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\SettlementController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/login', [LoginController::class, 'create'])->name('login');
Route::post('/login', [LoginController::class, 'store']);
Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

Route::get('/register', [RegisterController::class, 'create'])->name('register');
Route::post('/register', [RegisterController::class, 'store']);

Route::middleware(['role:umkm,store,admin'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

Route::middleware(['role:umkm,admin'])->group(function () {
    Route::resource('products', ProductController::class);
    Route::post('/consignments', [ConsignmentController::class, 'store'])->name('consignments.store');
    Route::get('/consignments/create', [ConsignmentController::class, 'create'])->name('consignments.create');
});

Route::middleware(['role:store,admin'])->group(function () {
    Route::post('/sales', [SalesController::class, 'store'])->name('sales.store');
    Route::get('/sales/create', [SalesController::class, 'create'])->name('sales.create');
    Route::delete('/sales/{sale}', [SalesController::class, 'destroy'])->name('sales.destroy');
    
    Route::post('/returns', [ReturnController::class, 'store'])->name('returns.store');
    Route::get('/returns/create', [ReturnController::class, 'create'])->name('returns.create');
    Route::delete('/returns/{return}', [ReturnController::class, 'destroy'])->name('returns.destroy');
});

Route::middleware(['role:umkm,store,admin'])->group(function () {
    Route::get('/consignments', [ConsignmentController::class, 'index'])->name('consignments.index');
    Route::get('/consignments/{consignment}', [ConsignmentController::class, 'show'])->name('consignments.show');
    Route::get('/consignments/{consignment}/edit', [ConsignmentController::class, 'edit'])->name('consignments.edit');
    Route::put('/consignments/{consignment}', [ConsignmentController::class, 'update'])->name('consignments.update');
    Route::delete('/consignments/{consignment}', [ConsignmentController::class, 'destroy'])->name('consignments.destroy');
    
    Route::get('/sales', [SalesController::class, 'index'])->name('sales.index');
    Route::get('/sales/{sale}', [SalesController::class, 'show'])->name('sales.show');
    
    Route::get('/returns', [ReturnController::class, 'index'])->name('returns.index');
    Route::get('/returns/{return}', [ReturnController::class, 'show'])->name('returns.show');
});

Route::middleware(['role:admin'])->group(function () {
    Route::resource('settlements', SettlementController::class);
    Route::get('/reports', function() {
        return view('reports.index');
    })->name('reports.index');
});
