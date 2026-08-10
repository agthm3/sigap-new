<?php

namespace App\Http\Controllers;

use App\Models\MagangBatch;
use App\Models\MagangLogbook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Storage;

class MagangController extends Controller
{
    // Dashboard / Tabel Utama Batch Magang
    public function index()
    {
        $batches = MagangBatch::withCount('peserta')
            ->latest()
            ->paginate(10);

        $totalMahasiswa = DB::table('magang_peserta')->count();

        return view('dashboard.magang.index', compact('batches', 'totalMahasiswa'));
    }

    

    public function showBatch($id)
    {
        $batch = MagangBatch::with('peserta')->findOrFail($id);

        // Ambil ID peserta yang sudah terdaftar di batch ini
        $existingPesertaIds = $batch->peserta->pluck('id')->toArray();

        // Ambil user yang memiliki role Spatie 'magang' dan BELUM terdaftar di batch ini
        $users = \App\Models\User::role('magang')
            ->whereNotIn('id', $existingPesertaIds)
            ->orderBy('name')
            ->get();

        return view('dashboard.magang.batch-show', compact('batch', 'users'));
    }

    // Simpan Batch Baru dari Modal
    public function storeBatch(Request $request)
    {
        $request->validate([
            'nama_batch'      => 'required|string|max:255',
            'deskripsi'       => 'nullable|string',
            'tanggal_mulai'   => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'kuota'           => 'required|integer|min:1',
            'status'          => 'required|in:mendatang,aktif,selesai',
        ]);

        MagangBatch::create($request->all());

        return redirect()->route('magang.index')
            ->with('success', 'Batch magang berhasil ditambahkan.');
    }

    // Update Batch
    public function updateBatch(Request $request, $id)
    {
        $batch = MagangBatch::findOrFail($id);

        $request->validate([
            'nama_batch'      => 'required|string|max:255',
            'deskripsi'       => 'nullable|string',
            'tanggal_mulai'   => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'kuota'           => 'required|integer|min:1',
            'status'          => 'required|in:mendatang,aktif,selesai',
        ]);

        $batch->update($request->all());

        return redirect()->route('magang.index')
            ->with('success', 'Batch magang berhasil diperbarui.');
    }

    // Hapus Batch
    public function destroyBatch($id)
    {
        $batch = MagangBatch::findOrFail($id);
        $batch->delete();

        return redirect()->route('magang.index')
            ->with('success', 'Batch magang berhasil dihapus.');
    }

    // Aksi Peserta Join Batch
    public function joinBatch(Request $request, $id)
    {
        $batch = MagangBatch::findOrFail($id);
        $userId = Auth::id();

        if ($batch->peserta()->where('user_id', $userId)->exists()) {
            return back()->with('error', 'Anda sudah terdaftar pada batch ini.');
        }

        if ($batch->peserta()->count() >= $batch->kuota) {
            return back()->with('error', 'Kuota batch magang ini sudah penuh.');
        }

        $batch->peserta()->attach($userId, [
            'instansi_asal' => $request->input('instansi_asal'),
            'jurusan'       => $request->input('jurusan'),
            'status'        => 'diterima',
        ]);

        return back()->with('success', 'Berhasil bergabung dengan batch magang.');
    }


    // Tambah Peserta Mahasiswa Manual oleh Admin / Verif Magang
    public function addPeserta(Request $request, $id)
    {
        $batch = MagangBatch::findOrFail($id);

        $request->validate([
            'user_id'       => 'required|exists:users,id',
            'instansi_asal' => 'required|string|max:255',
            'jurusan'       => 'required|string|max:255',
        ]);

        if ($batch->peserta()->where('user_id', $request->user_id)->exists()) {
            return back()->with('error', 'Mahasiswa tersebut sudah terdaftar pada batch ini.');
        }

        if ($batch->peserta()->count() >= $batch->kuota) {
            return back()->with('error', 'Kuota batch magang ini sudah penuh.');
        }

        $batch->peserta()->attach($request->user_id, [
            'instansi_asal' => $request->instansi_asal,
            'jurusan'       => $request->jurusan,
            'status'        => 'diterima',
        ]);

        return back()->with('success', 'Berhasil menambahkan mahasiswa ke batch magang.');
    }

   // 1. Halaman Logbook Saya
   public function indexLogbook()
    {
        $user = Auth::user();

        // 1. Ambil Batch Magang Aktif
        $activeBatch = $user->batches()
            ->where('magang_batches.status', 'aktif')
            ->first();

        if (!$activeBatch) {
            return view('dashboard.magang.logbook', [
                'activeBatch' => null,
            ]);
        }

        // 2. Ambil Data Pivot Peserta (WPM, File PDF, Status)
        $pesertaPivot = DB::table('magang_peserta')
            ->where('magang_batch_id', $activeBatch->id)
            ->where('user_id', $user->id)
            ->first();

        // 3. Generate Daftar Hari Kerja (Senin - Jumat) dalam Rentang Batch
        $startDate = \Carbon\Carbon::parse($activeBatch->tanggal_mulai);
        $endDate   = \Carbon\Carbon::parse($activeBatch->tanggal_selesai);
        $today     = \Carbon\Carbon::today();

        $scheduleDays = [];
        $curr = $startDate->copy();

        while ($curr->lte($endDate)) {
            if ($curr->isWeekday()) {
                $dateStr = $curr->format('Y-m-d');
                $scheduleDays[] = [
                    'date'           => $dateStr,
                    'formatted_date' => $curr->isoFormat('D MMMM Y (dddd)'),
                    'is_today'       => $curr->isToday(),
                    'is_past'        => $curr->lt($today),
                ];
            }
            $curr->addDay();
        }

        // 4. Ambil Semua Logbook User
        $logs = MagangLogbook::where('user_id', $user->id)
            ->where('magang_batch_id', $activeBatch->id)
            ->get();

        // Grouping Logbook Reguler per Tanggal
        $filledLogs = $logs->whereIn('kategori', ['reguler', null])
            ->groupBy(function ($item) {
                return \Carbon\Carbon::parse($item->tanggal)->format('Y-m-d');
            });

        // Ambil Special Logs (Penerimaan & Penutupan) Menggunakan keyBy AGAR MENGEMBALIKAN SINGLE INSTANCE
        $specialLogs = $logs->whereIn('kategori', ['penerimaan', 'penutupan'])->keyBy('kategori');

        $penerimaanLog = $specialLogs->get('penerimaan');
        $penutupanLog  = $specialLogs->get('penutupan');

        // 5. Ambil Tanggal Izin Susulan dari Admin/Verifikator
        $extraTimeAllowedDates = DB::table('magang_izin_susulan')
            ->where('user_id', $user->id)
            ->where('magang_batch_id', $activeBatch->id)
            ->get()
            ->map(function ($row) {
                return \Carbon\Carbon::parse($row->tanggal)->format('Y-m-d');
            })
            ->toArray();

        $todayLogbook   = $filledLogs->get($today->format('Y-m-d'));
        $isTodayWorkday = $today->isWeekday() && $today->between($startDate, $endDate);
        $filledCount    = $filledLogs->count();

        // 6. Evaluasi Syarat Kelulusan
        $hasPenerimaan = $penerimaanLog !== null;
        $hasTypingPass = ($pesertaPivot->typing_wpm ?? 0) >= 40;
        $hasPenutupan  = $penutupanLog !== null && !empty($pesertaPivot->file_laporan_pdf);

        return view('dashboard.magang.logbook', compact(
            'activeBatch',
            'pesertaPivot',
            'scheduleDays',
            'filledLogs',
            'specialLogs',
            'extraTimeAllowedDates',
            'todayLogbook',
            'isTodayWorkday',
            'filledCount',
            'hasPenerimaan',
            'hasTypingPass',
            'hasPenutupan'
        ));
    }
    // 2. Simpan Logbook & Upload PDF Laporan Akhir
    public function storeLogbook(Request $request)
    {
        $request->validate([
            'magang_batch_id' => 'required|exists:magang_batches,id',
            'tanggal'         => 'required|date',
            'kategori'        => 'required|in:reguler,penerimaan,penutupan',
            'items'           => 'required|array|min:1|max:5',
            'items.*.kegiatan' => 'required|string',
            'laporan_pdf'     => 'nullable|file|mimes:pdf|max:10240', // Max 10MB PDF
        ]);

        $userId = Auth::id();
        $targetDate = \Carbon\Carbon::parse($request->tanggal)->format('Y-m-d');

        // Validasi penutupan magang: Wajib sudah isi penerimaan & lulus 40 WPM
        if ($request->kategori === 'penutupan') {
            $hasPenerimaan = MagangLogbook::where('magang_batch_id', $request->magang_batch_id)
                ->where('user_id', $userId)
                ->where('kategori', 'penerimaan')
                ->exists();

            $peserta = DB::table('magang_peserta')
                ->where('magang_batch_id', $request->magang_batch_id)
                ->where('user_id', $userId)
                ->first();

            if (!$hasPenerimaan) {
                return back()->with('error', 'Gagal: Anda harus melengkapi Laporan Penerimaan Magang terlebih dahulu.');
            }

            if (($peserta->typing_wpm ?? 0) < 40) {
                return back()->with('error', 'Gagal: Anda belum lulus Tes Ketik 10 Jari (Minimal 40 WPM).');
            }

            // Upload File Laporan PDF jika ada
            if ($request->hasFile('laporan_pdf')) {
                $pdfFile = $request->file('laporan_pdf');
                $pdfPath = $pdfFile->storeAs(
                    'magang/laporan_pdf',
                    'Laporan_Akhir_' . $userId . '_' . time() . '.pdf',
                    'public'
                );

                // Update file PDF di tabel pivot & set status selesai jika semua terpenuhi
                DB::table('magang_peserta')
                    ->where('magang_batch_id', $request->magang_batch_id)
                    ->where('user_id', $userId)
                    ->update([
                        'file_laporan_pdf' => $pdfPath,
                        'status'           => 'selesai',
                        'updated_at'       => now(),
                    ]);
            }
        }

        // Olah kegiatan & foto terkompresi
        $oldLogs = MagangLogbook::where('magang_batch_id', $request->magang_batch_id)
            ->where('user_id', $userId)
            ->where('tanggal', $targetDate)
            ->where('kategori', $request->kategori)
            ->get();

        MagangLogbook::where('magang_batch_id', $request->magang_batch_id)
            ->where('user_id', $userId)
            ->where('tanggal', $targetDate)
            ->where('kategori', $request->kategori)
            ->delete();

        foreach ($request->items as $index => $item) {
            $filePath = null;

            if (!empty($item['compressed_image'])) {
                $base64Image = $item['compressed_image'];
                if (preg_match('/^data:image\/(\w+);base64,/', $base64Image, $type)) {
                    $data = substr($base64Image, strpos($base64Image, ',') + 1);
                    $type = strtolower($type[1]);
                    $data = base64_decode($data);

                    if ($data !== false) {
                        $fileName = 'logbook_' . $userId . '_' . time() . '_' . ($index + 1) . '.' . $type;
                        $filePath = 'magang/logbooks/' . $fileName;
                        \Storage::disk('public')->put($filePath, $data);
                    }
                }
            } else {
                if (isset($oldLogs[$index]) && $oldLogs[$index]->file_lampiran) {
                    $filePath = $oldLogs[$index]->file_lampiran;
                }
            }

            MagangLogbook::create([
                'magang_batch_id' => $request->magang_batch_id,
                'user_id'         => $userId,
                'tanggal'         => $targetDate,
                'kategori'        => $request->kategori,
                'kegiatan'        => $item['kegiatan'],
                'file_lampiran'   => $filePath,
            ]);
        }

        return back()->with('success', 'Laporan berhasil disimpan.');
    }

    // Halaman Monitoring Logbook (Khusus Admin & Verif Magang)
    public function monitoringLogbook(Request $request)
    {
        $batches = MagangBatch::orderBy('created_at', 'desc')->get();
        $selectedBatchId = $request->get('batch_id', $batches->where('status', 'aktif')->first()->id ?? $batches->first()->id ?? null);
        $selectedDate = $request->get('tanggal', now()->format('Y-m-d'));

        $batch = $selectedBatchId ? MagangBatch::with('peserta')->find($selectedBatchId) : null;

        $pesertaList = collect();
        $logbooksMap = collect();
        $pendingApprovals = collect();
        $weeklyReportsMap = collect();

        if ($batch) {
            $pesertaList = $batch->peserta;

            // Ambil semua logbook pada batch ini
            $allLogbooks = MagangLogbook::where('magang_batch_id', $batch->id)->get();
            $logbooksMap = $allLogbooks->groupBy('user_id');

            // Ambil data izin susulan yang aktif
            $approvedSusulan = DB::table('magang_izin_susulan')
                ->where('magang_batch_id', $batch->id)
                ->get()
                ->groupBy('user_id');

            // 1. GENERATE DAFTAR HARI TERLEWAT YANG MEMBUTUHKAN KONFIRMASI
            $startDate = \Carbon\Carbon::parse($batch->tanggal_mulai);
            $endDate   = \Carbon\Carbon::parse($batch->tanggal_selesai);
            $today     = \Carbon\Carbon::today();

            foreach ($pesertaList as $p) {
                $userLogs = $logbooksMap->get($p->id, collect())->keyBy(function($item) {
                    return \Carbon\Carbon::parse($item->tanggal)->format('Y-m-d');
                });

                $userSusulan = $approvedSusulan->get($p->id, collect())->keyBy(function($item) {
                    return \Carbon\Carbon::parse($item->tanggal)->format('Y-m-d');
                });

                $curr = $startDate->copy();
                while ($curr->lt($today) && $curr->lte($endDate)) {
                    if ($curr->isWeekday()) {
                        $dateStr = $curr->format('Y-m-d');
                        
                        // Jika tidak mengisi logbook reguler & belum diberi izin susulan
                        if (!$userLogs->has($dateStr) && !$userSusulan->has($dateStr)) {
                            $pendingApprovals->push([
                                'user_id'        => $p->id,
                                'user_name'      => $p->name,
                                'instansi'       => $p->pivot->instansi_asal,
                                'tanggal'        => $dateStr,
                                'formatted_date' => $curr->isoFormat('D MMMM Y (dddd)'),
                                'hari_ke'        => $startDate->diffInDaysFiltered(function($d) { return $d->isWeekday(); }, $curr) + 1,
                            ]);
                        }
                    }
                    $curr->addDay();
                }
            }

            // 2. GENERATE LAPORAN MINGGU INI (Senin - Jumat)
            $startOfWeek = now()->startOfWeek(); // Senin
            $endOfWeek   = now()->endOfWeek()->subDays(2); // Jumat

            foreach ($pesertaList as $p) {
                $weeklyLogs = $allLogbooks->where('user_id', $p->id)
                    ->whereBetween('tanggal', [$startOfWeek->format('Y-m-d'), $endOfWeek->format('Y-m-d')])
                    ->sortBy('tanggal');

                $weeklyReportsMap->put($p->id, $weeklyLogs);
            }
        }

        // Statistik
        $totalPeserta = $pesertaList->count();
        $terisiHariIni = 0;
        if ($batch) {
            $terisiHariIni = MagangLogbook::where('magang_batch_id', $batch->id)
                ->where('tanggal', $selectedDate)
                ->where('kategori', 'reguler')
                ->count();
        }
        $belumIsiHariIni = max(0, $totalPeserta - $terisiHariIni);

        return view('dashboard.magang.monitoring-logbook', compact(
            'batches',
            'batch',
            'selectedBatchId',
            'selectedDate',
            'pesertaList',
            'logbooksMap',
            'pendingApprovals',
            'weeklyReportsMap',
            'totalPeserta',
            'terisiHariIni',
            'belumIsiHariIni'
        ));
    }

    // Beri Izin Waktu Tambahan (Susulan)
    public function storeIzinSusulan(Request $request)
    {
        $request->validate([
            'magang_batch_id' => 'required|exists:magang_batches,id',
            'user_id'         => 'required|exists:users,id',
            'tanggal'         => 'required|date',
        ]);

        DB::table('magang_izin_susulan')->updateOrInsert(
            [
                'magang_batch_id' => $request->magang_batch_id,
                'user_id'         => $request->user_id,
                'tanggal'         => $request->tanggal,
            ],
            [
                'given_by'   => Auth::id(),
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        return back()->with('success', 'Izin pengisian susulan berhasil diberikan.');
    }

    // Hapus Izin Susulan
    public function revokeIzinSusulan(Request $request)
    {
        $request->validate([
            'magang_batch_id' => 'required|exists:magang_batches,id',
            'user_id'         => 'required|exists:users,id',
            'tanggal'         => 'required|date',
        ]);

        DB::table('magang_izin_susulan')
            ->where('magang_batch_id', $request->magang_batch_id)
            ->where('user_id', $request->user_id)
            ->where('tanggal', $request->tanggal)
            ->delete();

        return back()->with('success', 'Izin pengisian susulan berhasil dicabut.');
    }


    public function typingGame()
    {
        return view('dashboard.magang.typing-game');
    }

    public function saveTypingScore(Request $request)
    {
        $request->validate(['wpm' => 'required|integer']);
        
        $user = Auth::user();
        $batch = $user->batches()->where('magang_batches.status', 'aktif')->first();

        if ($batch) {
            DB::table('magang_peserta')
                ->where('magang_batch_id', $batch->id)
                ->where('user_id', $user->id)
                ->update([
                    'typing_wpm' => $request->wpm,
                    'typing_passed_at' => $request->wpm >= 40 ? now() : null,
                    'updated_at' => now(),
                ]);
        }

        return response()->json(['success' => true, 'passed' => $request->wpm >= 40]);
    }

    public function riwayatIndex()
    {
        // Ambil semua batch yang memiliki peserta
        $completedBatches = MagangBatch::withCount('peserta')
            ->whereHas('peserta') // Memastikan batch memiliki peserta
            ->get()
            ->filter(function ($batch) {
                // Cek apakah seluruh peserta pada batch ini statusnya 'selesai'
                $totalPeserta = $batch->peserta_count;
                
                $completedPesertaCount = DB::table('magang_peserta')
                    ->where('magang_batch_id', $batch->id)
                    ->where('status', 'selesai')
                    ->count();

                // Hanya lolos jika total peserta > 0 DAN semua pesertanya sudah 'selesai'
                return $totalPeserta > 0 && $totalPeserta === $completedPesertaCount;
            });

        return view('dashboard.magang.riwayat.index', compact('completedBatches'));
    }

    // 1. Halaman Daftar Mahasiswa Lulus pada Batch Tertentu
    public function riwayatShowBatch($batchId)
    {
        $batch = MagangBatch::with(['peserta'])->findOrFail($batchId);

        // Keamanan: Pastikan seluruh peserta pada batch ini memang sudah status 'selesai'
        $totalPeserta = $batch->peserta->count();
        $completedCount = $batch->peserta->where('pivot.status', 'selesai')->count();

        if ($totalPeserta === 0 || $totalPeserta !== $completedCount) {
            return redirect()->route('magang.riwayat.index')
                ->with('error', 'Batch ini belum bisa diakses di menu Riwayat karena masih ada mahasiswa yang belum menyelesaikan magang.');
        }

        return view('dashboard.magang.riwayat.show-batch', compact('batch'));
    }

    // 2. Halaman Detail Logbook Lengkap & Laporan Akhir Mahasiswa
    public function riwayatShowPeserta($batchId, $userId)
    {
        $batch = MagangBatch::findOrFail($batchId);
        
        $peserta = User::whereHas('batches', function ($query) use ($batchId) {
            $query->where('magang_batch_id', $batchId);
        })->findOrFail($userId);

        $pesertaPivot = DB::table('magang_peserta')
            ->where('magang_batch_id', $batchId)
            ->where('user_id', $userId)
            ->first();

        // Ambil seluruh logbook mahasiswa pada batch ini
        $logs = MagangLogbook::where('magang_batch_id', $batchId)
            ->where('user_id', $userId)
            ->orderBy('tanggal', 'asc')
            ->get();

        // Dapatkan logbook khusus
        $penerimaanLog = $logs->where('kategori', 'penerimaan')->first();
        $penutupanLog  = $logs->where('kategori', 'penutupan')->first();

        // Grouping logbook reguler harian
        $regulerLogs = $logs->whereIn('kategori', ['reguler', null])
            ->groupBy(function ($item) {
                return \Carbon\Carbon::parse($item->tanggal)->format('Y-m-d');
            });

        return view('dashboard.magang.riwayat.show-peserta', compact(
            'batch',
            'peserta',
            'pesertaPivot',
            'penerimaanLog',
            'penutupanLog',
            'regulerLogs'
        ));
    }

    public function removePeserta($batchId, $userId)
    {
        $batch = MagangBatch::findOrFail($batchId);

        // Lepaskan relasi peserta di tabel pivot magang_peserta
        $batch->peserta()->detach($userId);

        // Opsional: Hapus logbook peserta di batch ini agar data tetap bersih
        MagangLogbook::where('magang_batch_id', $batchId)
            ->where('user_id', $userId)
            ->delete();

        DB::table('magang_izin_susulan')
            ->where('magang_batch_id', $batchId)
            ->where('user_id', $userId)
            ->delete();

        return back()->with('success', 'Berhasil mengeluarkan mahasiswa dari batch magang.');
    }
    public function updatePeserta(Request $request, $batchId, $userId)
    {
        $batch = MagangBatch::findOrFail($batchId);

        $request->validate([
            'instansi_asal' => 'required|string|max:255',
            'jurusan'       => 'required|string|max:255',
        ]);

        // Pastikan peserta terdaftar di batch ini
        if (!$batch->peserta()->where('user_id', $userId)->exists()) {
            return back()->with('error', 'Mahasiswa tidak ditemukan pada batch ini.');
        }

        // Update data pada tabel pivot magang_peserta
        $batch->peserta()->updateExistingPivot($userId, [
            'instansi_asal' => $request->instansi_asal,
            'jurusan'       => $request->jurusan,
            'updated_at'    => now(),
        ]);

        return back()->with('success', 'Berhasil memperbarui data peserta magang.');
    }
}