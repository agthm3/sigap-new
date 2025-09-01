<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SigapDokumenController;
use App\Http\Controllers\SigapPegawaiController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';


//dashboard route
Route::get('/dashboard', [DashboardController::class, 'index'])->name('home.index');


Route::get('/sigap-pegawai', [SigapPegawaiController::class, 'index'])->name('sigap-pegawai.index');
Route::get('/sigap-pegawai/create', [SigapPegawaiController::class, 'create'])->name('sigap-pegawai.create');
Route::post('/sigap-pegawai', [SigapPegawaiController::class, 'store'])->name('sigap-pegawai.store');
Route::get('/sigap-pegawai/{id}', [SigapPegawaiController::class, 'show'])->name('sigap-pegawai.show');
Route::get('/sigap-pegawai/{id}/edit', [SigapPegawaiController::class, 'edit'])->name('sigap-pegawai.edit');
Route::put('/sigap-pegawai/{id}', [SigapPegawaiController::class, 'update'])->name('sigap-pegawai.update');
Route::delete('/sigap-pegawai/{id}', [SigapPegawaiController::class, 'destroy'])->name('sigap-pegawai.destroy');

Route::get('/sigap-dokumen', [SigapDokumenController::class, 'index'])->name('sigap-dokumen.index');
Route::post('/sigap-dokumen', [SigapDokumenController::class, 'store'])->name('sigap-dokumen.store');
Route::get('/sigap-dokumen/{id}', [SigapDokumenController::class, 'show'])->name('sigap-dokumen.show');
Route::get('/sigap-dokumen/{id}/download', [SigapDokumenController::class, 'download'])->name('sigap-dokumen.download');
Route::delete('/sigap-dokumen/{id}', [SigapDokumenController::class, 'destroy'])->name('sigap-dokumen.destroy');
Route::get('/sigap-dokumen/{id}/edit', [SigapDokumenController::class, 'edit'])->name('sigap-dokumen.edit');
Route::put('/sigap-dokumen/{id}', [SigapDokumenController::class, 'update'])->name('sigap-dokumen.update');