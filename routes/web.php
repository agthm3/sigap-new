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
use App\Http\Controllers\FormatController;
use App\Http\Controllers\page\PegawaiPublicController as PagePegawaiPublicController;
use App\Http\Controllers\PegawaiProfilController;
use App\Http\Controllers\PegawaiProfileController;
use App\Http\Controllers\PegawaiPublicController;
use App\Http\Controllers\PersonalDocumentController;
use App\Http\Controllers\RewardController;
use App\Http\Controllers\RisetController;
use App\Http\Controllers\SigapAutoController;
use App\Http\Controllers\SigapFormatController;
use App\Http\Controllers\SigapKinerjaController;
use App\Http\Controllers\SigapRisetController;

// --- Public
Route::get('/',      [HomeController::class, 'index'])->name('home');
Route::get('/hasil', [HomeController::class, 'show'])->name('home.show');

Route::get('/sigap-riset', [SigapRisetController::class, 'index'])->name('sigap-riset.index');
Route::get('/sigap-riset/{riset}', [SigapRisetController::class, 'show'])->name('sigap-riset.show');

// Route::get('/pegawai', [HomeController::class, 'indexPegawai'])->name('home.pegawai');
Route::prefix('pegawai')->middleware(['auth'])->group(function () {
    Route::get('/',                [\App\Http\Controllers\page\HomeController::class, 'indexPegawai'])->name('home.pegawai');

    // hasil pencarian pegawai (list)
    Route::get('/hasil',           [PagePegawaiPublicController::class,'search'])->name('public.pegawai.search');

    // detail pegawai + daftar dokumen
    Route::get('/{user}',          [PagePegawaiPublicController::class,'show'])->name('public.pegawai.show');

    // verifikasi kode akses (modal submit)
    Route::post('/docs/{doc}/verify', [PagePegawaiPublicController::class,'verify'])->name('public.pegawai.verify');

    // stream preview & download (hanya jika sudah diverifikasi / pemilik / admin)
    Route::get('/docs/{doc}/view',    [PagePegawaiPublicController::class,'view'])->name('public.pegawai.view');
    Route::get('/docs/{doc}/download',[PagePegawaiPublicController::class,'download'])->name('public.pegawai.download');
});

require __DIR__.'/auth.php';

// --- Profile (auth)
Route::middleware('auth')->group(function () {
    Route::get   ('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch ('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// self-serve upload dari halaman index/profil
Route::post('/pegawai/profil/dokumen', [PersonalDocumentController::class,'storeSelf'])->middleware('auth')
    ->name('pegawai.docs.storeSelf');

// download (pakai policy + log)
Route::get('/pegawai/dokumen/{doc}/download', [PersonalDocumentController::class,'download'])
    ->name('pegawai.docs.download');
   // atur kode akses (pemilik/admin)
Route::post('/pegawai/dokumen/{doc}/access-code', [PersonalDocumentController::class,'setAccessCode'])
    ->name('pegawai.docs.access.set');
Route::delete('/pegawai/dokumen/{doc}/access-code', [PersonalDocumentController::class,'clearAccessCode'])
    ->name('pegawai.docs.access.clear');
Route::get('/pegawai/dokumen/{doc}', [PersonalDocumentController::class,'show'])
    ->name('pegawai.docs.show');
Route::post('/pegawai/dokumen/{doc}/reveal', [PersonalDocumentController::class,'reveal'])
    ->name('pegawai.docs.reveal');
Route::get('/pegawai/dokumen/{doc}/preview', [PersonalDocumentController::class,'preview'])
    ->name('pegawai.docs.preview');

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


// // --- SIGAP Inovasi (middleware milikmu untuk index tetap)
Route::get('/inovasi', [SigapInovasiController::class, 'home'])
    ->name('sigap-inovasi.home'); // Landing / non-dashboard
//         Route::get('/sigap-inovasi', [SigapInovasiController::class, 'index'])->name('sigap-inovasi.index');

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

//SIGAP KINERJA
Route::get('/sigap-kinerja', [SigapKinerjaController::class, 'index'])->name('sigap-kinerja.index');
Route::post('/sigap-kinerja', [SigapKinerjaController::class, 'store'])->name('sigap-kinerja.store');

Route::get('/sigap-kinerja/p/{id}', [SigapKinerjaController::class, 'publicShow'])->name('sigap-kinerja.public');

Route::get('/kinerja/y/{year}', [SigapKinerjaController::class, 'annualPublic'])->name('sigap-kinerja.annual-public');


Route::get('/sigap-format', [FormatController::class, 'index'])->name('sigap-format.index');
Route::post('/sigap-format/{id}/unlock', [FormatController::class, 'unlock'])->name('sigap-format.unlock'); // NEW (kode akses)
Route::get('/sigap-format/{id}', [FormatController::class, 'show'])->name('sigap-format.show');
Route::get('/sigap-format/preview/{id}', [FormatController::class, 'preview'])->name('sigap-format.preview');
Route::post('/sigap-format/{id}/download', [FormatController::class, 'download'])->name('sigap-format.download');

Route::get('/riset', [RisetController::class, 'index'])->name('riset.index');
Route::get('/riset/dashboard', [RisetController::class, 'dashboard'])->name('riset.dashboard');
Route::get('/riset/create', [RisetController::class, 'show'])->name('riset.create');
Route::post('/riset/store', [RisetController::class, 'store'])->name('riset.store');

Route::get('/riset/{id}/edit', [RisetController::class, 'edit'])->name('riset.edit');
Route::put('/riset/{id}',       [RisetController::class, 'update'])->name('riset.update');

Route::get('/about', [HomeController::class, 'about'])->name('about');


// Dashboard SIGAP FORMAT (login required via controller)
Route::get('/format',               [SigapFormatController::class, 'index'])->name('format.index');

// hanya admin
Route::post('/format',              [SigapFormatController::class, 'store'])->middleware('role:admin')->name('format.store');
Route::get('/format/{id}/edit',     [SigapFormatController::class, 'edit'])->middleware('role:admin')->name('format.edit');
Route::put('/format/{id}',          [SigapFormatController::class, 'update'])->middleware('role:admin')->name('format.update');
Route::delete('/format/{id}',       [SigapFormatController::class, 'destroy'])->middleware('role:admin')->name('format.destroy');
Route::post('/format/{id}/unlock-download', [SigapFormatController::class, 'unlockAndDownload'])
    ->name('format.unlock');

// download (dashboard)
Route::get('/format/{id}/download', [SigapFormatController::class, 'download'])->name('format.download');

Route::get('/reward', [RewardController::class, 'index'])->name('reward.index');


Route::get('/sigap-auto', [SigapAutoController::class, 'index'])->name('sigap-auto.index');