<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\page\HomeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;

use App\Http\Controllers\SigapDokumenController;
use App\Http\Controllers\SigapInovasiController;
use App\Http\Controllers\EvidenceController;
use App\Http\Controllers\Dashboard\EvidenceConfigController;

use App\Http\Controllers\SigapPegawaiController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\PegawaiProfilController;
use App\Http\Controllers\PegawaiProfileController;

// --- Public
Route::get('/',      [HomeController::class, 'index'])->name('home');
Route::get('/hasil', [HomeController::class, 'show'])->name('home.show');

Route::get('/pegawai', [HomeController::class, 'indexPegawai'])->name('home.pegawai');

require __DIR__.'/auth.php';

// --- Profile (auth)
Route::middleware('auth')->group(function () {
    Route::get   ('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch ('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// --- Dashboard (pakai middleware milikmu)
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'role:admin'])
    ->name('home.index');

// --- SIGAP Pegawai (tanpa resource, middleware milikmu tetap)
Route::get   ('/sigap-pegawai',          [SigapPegawaiController::class, 'index'])->middleware('auth', 'role:admin')->name('sigap-pegawai.index');
Route::get   ('/sigap-pegawai/create',   [SigapPegawaiController::class, 'create'])->name('sigap-pegawai.create');
Route::post  ('/sigap-pegawai',          [SigapPegawaiController::class, 'store'])->name('sigap-pegawai.store');

// admin create user (UBAH path supaya tidak bentrok)
Route::post  ('/sigap-pegawai/users',    [RegisteredUserController::class, 'adminStore'])->name('sigap-pegawai.users.store');

// edit / update / delete
Route::get   ('/sigap-pegawai/{user}/edit', [SigapPegawaiController::class, 'edit'])->name('sigap-pegawai.edit');
Route::put   ('/sigap-pegawai/{user}',      [SigapPegawaiController::class, 'update'])->name('sigap-pegawai.update');
Route::delete('/sigap-pegawai/{user}',      [SigapPegawaiController::class, 'destroy'])->name('sigap-pegawai.destroy');

// hapus avatar (middleware milikmu tetap)
Route::delete('/sigap-pegawai/{user}/avatar', [SigapPegawaiController::class,'destroyAvatar'])
    ->middleware(['auth','permission:pegawai.manage'])
    ->name('sigap-pegawai.avatar.destroy');

// --- SIGAP Dokumen (tanpa resource)
Route::prefix('sigap-dokumen')->middleware('auth', 'role:employee|admin')->name('sigap-dokumen.')->group(function () {
    Route::get('/',           [SigapDokumenController::class, 'index'])->name('index');
    Route::post('/',          [SigapDokumenController::class, 'store'])->name('store');
    Route::get('/{id}',       [SigapDokumenController::class, 'show'])->name('show');
    Route::get('/{id}/download', [SigapDokumenController::class, 'download'])->name('download');
    Route::middleware('role:admin')->group(function(){
        Route::get('/{id}/edit',  [SigapDokumenController::class, 'edit'])->name('edit');
        Route::put('/{id}',       [SigapDokumenController::class, 'update'])->name('update');
        Route::delete('/{id}',    [SigapDokumenController::class, 'destroy'])->name('destroy');
    });
});


// --- SIGAP Inovasi (middleware milikmu untuk index tetap)
Route::get('/inovasi', [SigapInovasiController::class, 'home'])
    ->name('sigap-inovasi.home'); // Landing / non-dashboard
        Route::get('/sigap-inovasi', [SigapInovasiController::class, 'index'])->name('sigap-inovasi.index');
    // Route::get ('/sigap-inovasi/konfigurasi', [SigapInovasiController::class, 'konfigurasi'])->name('sigap-inovasi.konfigurasi');
    // Route::get ('/sigap-inovasi/dashboard',   [SigapInovasiController::class, 'dashboard'])->name('sigap-inovasi.dashboard');
    // Route::post('/sigap-inovasi',             [SigapInovasiController::class, 'store'])->name('sigap-inovasi.store');

Route::prefix('/sigap-inovasi')->middleware('auth', 'role:inovator|admin')->group(function () {
    Route::get('/home', [SigapInovasiController::class, 'index'])->name('sigap-inovasi.index');
    Route::get ('/konfigurasi', [SigapInovasiController::class, 'konfigurasi'])->name('sigap-inovasi.konfigurasi');
    Route::get ('/dashboard',   [SigapInovasiController::class, 'dashboard'])->name('sigap-inovasi.dashboard');
    Route::post('/sigap-inovasi',             [SigapInovasiController::class, 'store'])->name('sigap-inovasi.store');
    // Evidence routes (taruh sebelum {id} agar tidak ketimpa)
    Route::get   ('/sigap-inovasi/{inovasi}/evidence',              [EvidenceController::class,'index'])->name('evidence.index');
    Route::post  ('/sigap-inovasi/{inovasi}/evidence',              [EvidenceController::class,'store'])->name('evidence.store');
    Route::delete('/sigap-inovasi/{inovasi}/evidence/{no}/file',    [EvidenceController::class,'destroyFile'])->name('evidence.file.destroy');
    Route::get   ('/sigap-inovasi/{inovasi}/evidence/form',         [SigapInovasiController::class,'evidenceForm'])->name('evidence.form');
    Route::post  ('/sigap-inovasi/{inovasi}/evidence/save',         [SigapInovasiController::class,'evidenceSave'])->name('evidence.save');

    // Asistensi (admin/verifikator) — kalau ada role 'verifikator', tambahkan di sini
    Route::post  ('/{id}/asistensi', [SigapInovasiController::class, 'asistensiUpdate'])
            ->middleware('role:admin')  // atau ->middleware('role:admin|verifikator')
            ->name('sigap-inovasi.asistensi');
    // CRUD Inovasi by id
    Route::get   ('/sigap-inovasi/{id}',       [SigapInovasiController::class, 'show'])->name('sigap-inovasi.show');
    Route::get   ('/sigap-inovasi/{id}/edit',  [SigapInovasiController::class, 'edit'])->name('sigap-inovasi.edit');
    Route::put   ('/sigap-inovasi/{id}',       [SigapInovasiController::class, 'update'])->name('sigap-inovasi.update');
    Route::delete('/sigap-inovasi/{id}',       [SigapInovasiController::class, 'destroy'])->name('sigap-inovasi.destroy');

});


// --- Evidence Config (tetap auth saja, tidak sentuh middleware khususmu)
Route::middleware('auth')
    ->prefix('admin/evidence-config')
    ->name('evidence-config.')
    ->group(function(){
        Route::get   ('/',                       [EvidenceConfigController::class,'index'])->name('index');
        Route::get   ('/indicators',             [EvidenceConfigController::class,'listIndicators'])->name('indicators.index');
        Route::post  ('/indicators',             [EvidenceConfigController::class,'storeIndicator'])->name('indicators.store');
        Route::put   ('/indicators/{id}',        [EvidenceConfigController::class,'updateIndicator'])->name('indicators.update');
        Route::delete('/indicators/{id}',        [EvidenceConfigController::class,'destroyIndicator'])->name('indicators.destroy');
        Route::post  ('/indicators/reorder',     [EvidenceConfigController::class,'reorder'])->name('indicators.reorder');

        Route::post  ('/indicators/{indicator}/params', [EvidenceConfigController::class,'storeParam'])->name('params.store');
        Route::put   ('/params/{id}',                   [EvidenceConfigController::class,'updateParam'])->name('params.update');
        Route::delete('/params/{id}',                   [EvidenceConfigController::class,'destroyParam'])->name('params.destroy');
        Route::post  ('/indicators/{indicator}/params/copy', [EvidenceConfigController::class,'copyParams'])->name('params.copy');
    });

Route::get('/pegawai-profil', [PegawaiProfilController::class, 'show'])
    ->middleware('auth')->name('pegawai.profil');

Route::get('/pegawai-profil/edit', [PegawaiProfilController::class, 'edit'])
    ->middleware('auth')->name('pegawai.profil.edit');

Route::put('/pegawai-profil', [PegawaiProfilController::class, 'update'])
    ->middleware('auth')->name('pegawai.profil.update');

Route::delete('/pegawai-profil/avatar', [PegawaiProfilController::class, 'destroyAvatar'])
    ->middleware('auth')->name('pegawai.profil.avatar.destroy');