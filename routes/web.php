<?php

use App\Http\Controllers\Api\UserSearchController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

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
use App\Http\Controllers\InovasiReviewController;
use App\Http\Controllers\page\PegawaiPublicController as PagePegawaiPublicController;
use App\Http\Controllers\PegawaiProfilController;
use App\Http\Controllers\PegawaiProfileController;
use App\Http\Controllers\PegawaiPublicController;
use App\Http\Controllers\PersonalDocumentController;
use App\Http\Controllers\ProfileOrganisasiController;
use App\Http\Controllers\RewardController;
use App\Http\Controllers\RisetController;
use App\Http\Controllers\SertifikatController;
use App\Http\Controllers\SigapAbsensiController;
use App\Http\Controllers\SigapAgendaController;
use App\Http\Controllers\SigapAutoController;
use App\Http\Controllers\SigapFormatController;
use App\Http\Controllers\SigapKinerjaController;
use App\Http\Controllers\SigapRisetController;
use App\Http\Controllers\SigapInkubatormaController;
use App\Http\Controllers\SigapPpdController;
use App\Http\Controllers\SigapSertifikatController;

// --- Public
Route::get('/',      [HomeController::class, 'index'])->name('home');
Route::get('/hasil', [HomeController::class, 'show'])->name('home.show');

Route::get('/sigap-riset', [SigapRisetController::class, 'index'])->name('sigap-riset.index');
Route::get('/sigap-riset/{riset}', [SigapRisetController::class, 'show'])->name('sigap-riset.show');
Route::get('/sigap-riset/{riset}/download', [SigapRisetController::class, 'download'])->name('sigap-riset.download');

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
//Export user
Route::get('/sigap-pegawai/export', [SigapPegawaiController::class, 'export'])
    ->name('sigap-pegawai.export');

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
   
    // === PEDOMAN EVIDENCE (GLOBAL) ===
    Route::get('/pedoman-evidence',
        [SigapInovasiController::class, 'pedomanEvidence']
    )->name('evidence.pedoman');

    Route::post('/pedoman-evidence',
        [SigapInovasiController::class, 'pedomanEvidenceSave']
    )->middleware('role:admin')
    ->name('evidence.pedoman.save');

    Route::delete(
        '/pedoman-evidence/{guide}',
        [SigapInovasiController::class, 'pedomanEvidenceDelete']
    )->middleware('role:admin')
    ->name('evidence.pedoman.delete');

    Route::post('{id}/review/evidence', [InovasiReviewController::class, 'storeEvidenceReview'])
     ->name('inovasi.review.evidence.store');

    Route::post('sigap-inovasi/pedoman-metadata', [SigapInovasiController::class, 'pedomanMetaSave'])
     ->name('evidence.pedoman.meta.save');

});


//Route Invasi Review
Route::middleware(['auth'])->group(function () {

    Route::get('/inovasi/{id}/review', [InovasiReviewController::class, 'form'])
        ->name('inovasi.review');

    Route::post('/inovasi/{id}/review', [InovasiReviewController::class, 'store'])
        ->name('inovasi.review.store');
    Route::get('/inovasi/{id}/review-result', [InovasiReviewController::class, 'reviewResult'])
    ->name('inovasi.review.result');
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
Route::post('/pegawai/profil/avatar', [PegawaiProfilController::class, 'updateAvatar'])
    ->name('pegawai.profil.avatar');

//SIGAP KINERJA
Route::get('/sigap-kinerja', [SigapKinerjaController::class, 'index'])->name('sigap-kinerja.index');
Route::post('/sigap-kinerja', [SigapKinerjaController::class, 'store'])->name('sigap-kinerja.store');

Route::get('/sigap-kinerja/p/{id}', [SigapKinerjaController::class, 'publicShow'])->name('sigap-kinerja.public');

Route::get('/kinerja/y/{year}', [SigapKinerjaController::class, 'annualPublic'])->name('sigap-kinerja.annual-public');
Route::get('/sigap-kinerja/p/{id}/download-images', [SigapKinerjaController::class, 'downloadImages'])
    ->name('sigap-kinerja.download-images');
Route::delete('/sigap-kinerja/{id}', [SigapKinerjaController::class, 'destroy'])->middleware('role:admin')
    ->name('sigap-kinerja.destroy');


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



Route::get('/agenda', [SigapAgendaController::class, 'publicIndex'])->name('agenda.public-index');

Route::middleware('auth','role:user|admin|verificator')->group(function () {
    Route::get('/sigap-agenda',            [SigapAgendaController::class, 'index'])->name('sigap-agenda.index');
    Route::get('/sigap-agenda/create',     [SigapAgendaController::class, 'create'])->name('sigap-agenda.create');
    Route::post('/sigap-agenda/store',     [SigapAgendaController::class, 'store'])->name('sigap-agenda.store');

    Route::get('/sigap-agenda/edit',       [SigapAgendaController::class, 'edit'])->name('sigap-agenda.edit');      // ?id=123
    Route::post('/sigap-agenda/update',    [SigapAgendaController::class, 'update'])->name('sigap-agenda.update');  // POST
    Route::post('/sigap-agenda/delete', [SigapAgendaController::class, 'delete'])->name('sigap-agenda.delete');

});

Route::middleware('auth')
    ->get('/api/users/search', [UserSearchController::class, 'search'])
    ->name('api.users.search');

// (opsional) jika masih butuh JSON show:
Route::get('/sigap-agenda/show',       [SigapAgendaController::class, 'show'])->name('sigap-agenda.show');     

Route::get('/sigap-inkubatorma', [SigapInkubatormaController::class, 'index'])
    ->name('sigap-inkubatorma.index'); // landing publik

Route::post('/sigap-inkubatorma/store', [SigapInkubatormaController::class, 'store'])
    ->name('sigap-inkubatorma.store');

Route::prefix('/sigap-inkubatorma')->group(function () {

    // ========== PUBLIK ==========
    Route::get('/', [SigapInkubatormaController::class, 'index'])
        ->name('sigap-inkubatorma.index');

    // ========== WAJIB LOGIN UNTUK SUBMIT ==========
    // Route::post('/store', [SigapInkubatormaController::class, 'store'])
    //     ->middleware('auth')
    //     ->name('sigap-inkubatorma.store');

    // ========== AUTH AREA ==========
    Route::middleware('auth')->group(function () {

        Route::get('/dashboard', [SigapInkubatormaController::class, 'dashboard'])
            ->name('sigap-inkubatorma.dashboard');

            Route::get('/dashboard/print', [SigapInkubatormaController::class, 'printLaporan'])
            ->middleware('role:admin|verifikator_inkubatorma|user')
            ->name('sigap-inkubatorma.dashboard.print');

        Route::get('/{id}/detail', [SigapInkubatormaController::class, 'detail'])
            ->whereNumber('id')
            ->name('sigap-inkubatorma.detail');

        Route::get('/employees/search', [SigapInkubatormaController::class, 'employeesSearch'])
            ->middleware('role:admin|verifikator_inkubatorma|employee')
            ->name('sigap-inkubatorma.employees.search');

        Route::middleware('role:admin|verifikator_inkubatorma|employee')->group(function () {

            Route::get('/{id}/verifikasi', [SigapInkubatormaController::class, 'verifikasi'])
                ->whereNumber('id')
                ->name('sigap-inkubatorma.verifikasi');

            Route::put('/{id}/verifikasi', [SigapInkubatormaController::class, 'verifikasiUpdate'])
                ->whereNumber('id')
                ->name('sigap-inkubatorma.verifikasi.update');
        });

        Route::middleware('role:admin|user')->group(function () {

            Route::get('/{id}/edit', [SigapInkubatormaController::class, 'edit'])
                ->whereNumber('id')
                ->name('sigap-inkubatorma.edit');

            Route::put('/{id}', [SigapInkubatormaController::class, 'update'])
                ->whereNumber('id')
                ->name('sigap-inkubatorma.update');

            Route::delete('/{id}', [SigapInkubatormaController::class, 'destroy'])
                ->whereNumber('id')
                ->name('sigap-inkubatorma.destroy');
        });

        Route::get('/{id}/records', [SigapInkubatormaController::class, 'records'])
            ->whereNumber('id')
            ->name('sigap-inkubatorma.records');

        Route::post('/{id}/records', [SigapInkubatormaController::class, 'storeRecord'])
            ->whereNumber('id')
            ->name('sigap-inkubatorma.records.store');

        Route::put('/{id}/records/{recordId}', [SigapInkubatormaController::class, 'updateRecord'])
            ->whereNumber('id')
            ->whereNumber('recordId')
            ->name('sigap-inkubatorma.records.update');

        Route::post('/{id}/records/{recordId}/upload-revision', [SigapInkubatormaController::class, 'uploadRecordRevision'])
            ->whereNumber('id')
            ->whereNumber('recordId')
            ->name('sigap-inkubatorma.records.upload-revision');

        Route::post('/{id}/records/confirm-finish', [SigapInkubatormaController::class, 'confirmRecordFinish'])
            ->whereNumber('id')
            ->name('sigap-inkubatorma.records.confirm-finish');
    });
});

Route::get('/profil-struktur', [ProfileOrganisasiController::class, 'struktur'])->name('profil.struktur');
Route::get('/profil-visi-misi', [ProfileOrganisasiController::class, 'visiMisi'])->name('profil.visimisi');
Route::get('/profil-berita', [ProfileOrganisasiController::class, 'berita'])->name('profil.berita');
Route::get('/profil-tentang', [ProfileOrganisasiController::class, 'tentang'])->name('profil.tentang');
Route::get('/profil-kontak', [ProfileOrganisasiController::class, 'kontak'])->name('profil.kontak');


Route::get('/dashboard-sertifikat', [SertifikatController::class, 'index'])
    ->middleware('auth')
    ->name('sigap-sertifikat.dashboard');
    Route::prefix('sertifikat-kegiatan')
->middleware('auth')
->group(function () {
Route::post('/store', [SertifikatController::class, 'store'])
        ->name('sertifikat-kegiatan.store');

});
Route::middleware('auth')->group(function(){

Route::get('/sertifikat-kegiatan/{id}',
[SertifikatController::class,'show'])
->name('sertifikat.show');

Route::post('/sertifikat/store',
[SertifikatController::class,'storeSertifikat'])
->name('sertifikat.store');

});

Route::get('/sertifikat', [SigapSertifikatController::class, 'index'])->name('sigap-sertifikat.index');
Route::post('/sertifikat/verifikasi', [SigapSertifikatController::class,'verifikasi'])
->name('sigap-sertifikat.verifikasi');
Route::post('/sertifikat/import',
[SertifikatController::class,'importExcel'])
->name('sertifikat.import');
Route::get('/sertifikat/template',
[SertifikatController::class,'downloadTemplate'])
->name('sertifikat.template');
Route::get('/sertifikat/view/{id}',
[SigapSertifikatController::class,'view'])
->name('sigap-sertifikat.view');


Route::get('/sigap-absensi', function () {
    return view('SigapAbsensi.home.index');
})->name('sigap-absensi.home');

Route::middleware(['auth'])->group(function () {
    Route::prefix('sigap-absensi')->name('sigap-absensi.')->group(function () {

        Route::middleware('role:employee|admin')->group(function () {
            Route::get('/masuk', [SigapAbsensiController::class, 'index'])->name('index');
            Route::post('/store', [SigapAbsensiController::class, 'store'])->name('store');
        });

        Route::middleware('role:admin|verificator_absensi')->group(function () {
            Route::get('/dashboard', [SigapAbsensiController::class, 'dashboard'])->name('dashboard');
            Route::get('/rekap-harian', [SigapAbsensiController::class, 'rekapHarian'])->name('rekap-harian');
            Route::get('/rekap-mingguan', [SigapAbsensiController::class, 'rekapMingguan'])->name('rekap-mingguan');
            Route::get('/rekap-bulanan', [SigapAbsensiController::class, 'rekapBulanan'])->name('rekap-bulanan');

            Route::get('/{absensi}/edit', [SigapAbsensiController::class, 'edit'])->name('edit');
            Route::put('/{absensi}', [SigapAbsensiController::class, 'update'])->name('update');
        });
    });

    Route::get('/sigap-absensi/rekap-harian/export-pdf', [SigapAbsensiController::class, 'exportRekapHarianPdf'])
        ->middleware('role:admin|verificator_absensi')
        ->name('sigap-absensi.rekap-harian.pdf');
});


Route::get('/sigap-ppd', [SigapPpdController::class, 'publicIndex'])
    ->name('sigap-ppd.public');
Route::middleware('auth')->group(function () {
    Route::prefix('sigap-ppd/dashboard')->name('sigap-ppd.')->group(function () {
        Route::get('/', [SigapPpdController::class, 'index'])->name('index');
        Route::get('/create', [SigapPpdController::class, 'create'])->name('create')->middleware('role:admin|verif_ppd');
        Route::post('/', [SigapPpdController::class, 'store'])->name('store')->middleware('role:admin|verif_ppd');

        Route::get('/{kegiatan}', [SigapPpdController::class, 'show'])->name('show');
        Route::get('/{kegiatan}/export-pdf/{user?}', [SigapPpdController::class, 'exportPdf'])->name('export-pdf');

        Route::post('/lembar/{lembar}', [SigapPpdController::class, 'storeLembar'])->name('lembar.store');
        Route::post('/{kegiatan}/status', [SigapPpdController::class, 'updateStatus'])->name('status')->middleware('role:admin|verif_ppd');
        Route::delete('/{kegiatan}', [SigapPpdController::class, 'destroy'])->name('destroy')->middleware('role:admin|verif_ppd');
    });
});