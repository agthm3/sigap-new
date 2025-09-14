<?php

use App\Http\Controllers\Dashboard\EvidenceConfigController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\page\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SigapDokumenController;
use App\Http\Controllers\SigapInovasiController;
use App\Http\Controllers\SigapPegawaiController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EvidenceController;
use App\Http\Controllers\Auth\RegisteredUserController;
// Route::get('/', function () {
//     return view('welcome');
// });
Route::get('/', [App\Http\Controllers\page\HomeController::class, 'index'])->name('home');
Route::get('/hasil', [HomeController::class, 'show'])->name('home.show');
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
Route::resource('sigap-pegawai', \App\Http\Controllers\SigapPegawaiController::class)
    ->parameters(['sigap-pegawai' => 'user'])
    ->middleware(['auth','permission:pegawai.manage']);
  Route::post('/sigap-pegawai', [RegisteredUserController::class, 'adminStore'])
        ->name('sigap-pegawai.users.store');
Route::delete('/sigap-pegawai/{user}/avatar', [SigapPegawaiController::class,'destroyAvatar'])
    ->middleware(['auth','permission:pegawai.manage'])
    ->name('sigap-pegawai.avatar.destroy');

Route::get('/sigap-dokumen', [SigapDokumenController::class, 'index'])->name('sigap-dokumen.index');
Route::post('/sigap-dokumen', [SigapDokumenController::class, 'store'])->name('sigap-dokumen.store');
Route::get('/sigap-dokumen/{id}', [SigapDokumenController::class, 'show'])->name('sigap-dokumen.show');
Route::get('/sigap-dokumen/{id}/download', [SigapDokumenController::class, 'download'])->name('sigap-dokumen.download');
Route::delete('/sigap-dokumen/{id}', [SigapDokumenController::class, 'destroy'])->name('sigap-dokumen.destroy');
Route::get('/sigap-dokumen/{id}/edit', [SigapDokumenController::class, 'edit'])->name('sigap-dokumen.edit');
Route::put('/sigap-dokumen/{id}', [SigapDokumenController::class, 'update'])->name('sigap-dokumen.update');

// Route::get('/sigap-inovasi', [SigapInovasiController::class, 'index'])->name('sigap-inovasi.index');
Route::get('/sigap-inovasi', [SigapInovasiController::class, 'index'])
    ->middleware(['auth', 'role:admin|inovator'])  // wajib login + role admin ATAU inovator
    ->name('sigap-inovasi.index');
Route::get('/sigap-inovasi/konfigurasi', [SigapInovasiController::class, 'konfigurasi'])->name('sigap-inovasi.konfigurasi');
Route::get('/sigap-inovasi/dashboard', [SigapInovasiController::class, 'dashboard'])->name('sigap-inovasi.dashboard');
Route::post('/sigap-inovasi', [SigapInovasiController::class, 'store'])->name('sigap-inovasi.store');
Route::get('/sigap-inovasi/{id}', [SigapInovasiController::class, 'show'])->name('sigap-inovasi.show');
Route::get('/sigap-inovasi/{id}/edit', [SigapInovasiController::class, 'edit'])->name('sigap-inovasi.edit');
Route::put('/sigap-inovasi/{id}', [SigapInovasiController::class, 'update'])->name('sigap-inovasi.update');
Route::delete('/sigap-inovasi/{id}', [SigapInovasiController::class, 'destroy'])->name('sigap-inovasi.destroy');
Route::get('/sigap-inovasi/{inovasi}/evidence/form', [SigapInovasiController::class,'evidenceForm'])
  ->name('evidence.form');
// Route::get('/sigap-inovasi/{inovasi}/evidence/form', [SigapInovasiController::class,'evidenceForm'])
//   ->name('evidence.form')
//   ->middleware('auth');

Route::prefix('admin/evidence-config')->name('evidence-config.')->middleware(['auth'])->group(function(){
  Route::get('/', [EvidenceConfigController::class,'index'])->name('index');
  Route::get('/indicators', [EvidenceConfigController::class,'listIndicators'])->name('indicators.index');
  Route::post('/indicators', [EvidenceConfigController::class,'storeIndicator'])->name('indicators.store');
  Route::put('/indicators/{id}', [EvidenceConfigController::class,'updateIndicator'])->name('indicators.update');
  Route::delete('/indicators/{id}', [EvidenceConfigController::class,'destroyIndicator'])->name('indicators.destroy');
  Route::post('/indicators/reorder', [EvidenceConfigController::class,'reorder'])->name('indicators.reorder');

  Route::post('/indicators/{indicator}/params', [EvidenceConfigController::class,'storeParam'])->name('params.store');
  Route::put('/params/{id}', [EvidenceConfigController::class,'updateParam'])->name('params.update');
  Route::delete('/params/{id}', [EvidenceConfigController::class,'destroyParam'])->name('params.destroy');
  Route::post('/indicators/{indicator}/params/copy', [EvidenceConfigController::class,'copyParams'])->name('params.copy');
});


Route::get   ('/sigap-inovasi/{inovasi}/evidence',        [EvidenceController::class,'index'])->name('evidence.index');
Route::post  ('/sigap-inovasi/{inovasi}/evidence',        [EvidenceController::class,'store'])->name('evidence.store');
Route::delete('/sigap-inovasi/{inovasi}/evidence/{no}/file', [EvidenceController::class,'destroyFile'])->name('evidence.file.destroy');

// routes/web.php
Route::get('/sigap-inovasi/{inovasi}/evidence/form', [SigapInovasiController::class,'evidenceForm'])
  ->name('evidence.form');

Route::post('/sigap-inovasi/{inovasi}/evidence/save', [SigapInovasiController::class,'evidenceSave'])
  ->name('evidence.save');
