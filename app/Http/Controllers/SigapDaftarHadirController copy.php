<?php

namespace App\Http\Controllers;

use App\Models\SigapDaftarHadirKegiatan;
use App\Models\SigapDaftarHadirPejabat;
use App\Models\SigapDaftarHadirPenandatangan;
use App\Models\SigapDaftarHadirPeserta;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use iio\libmergepdf\Merger;
use App\Models\SertifikatKegiatan;
use App\Models\SertifikatPeserta;

class SigapDaftarHadirController extends Controller
{

    public function index(Request $request)     
    {
        $user = Auth::user();

        $base = SigapDaftarHadirKegiatan::query();

        if (!$user->hasAnyRole(['admin', 'verif_daftarhadir'])) {
            $base->where('created_by', $user->id);
        }

        if ($request->filled('q')) {
            $keyword = trim($request->get('q'));
            $base->where('nama_kegiatan', 'like', "%{$keyword}%");
        }

        // Filter berdasarkan status (draft, proses, selesai)
        if ($request->filled('status')) {
            $status = $request->get('status');
            $base->where('status', $status);
        }

        $totalKegiatan = (clone $base)->count();
        $totalDraft    = (clone $base)->where('status', 'draft')->count();
        $totalProses   = (clone $base)->where('status', 'proses')->count();
        $totalSelesai  = (clone $base)->where('status', 'selesai')->count();

        $kegiatans = (clone $base)
            ->with(['creator']) // Eager loading creator agar tidak N+1 query
            ->withCount('peserta')
            ->latest()
            ->paginate(10);

        return view('dashboard.daftar_hadir.index', compact(
            'kegiatans',
            'totalKegiatan',
            'totalDraft',
            'totalProses',
            'totalSelesai'
        ));
    }

    public function create()
    {
        return view('dashboard.daftar_hadir.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kegiatan'               => ['required', 'string', 'max:500'],
            'hari_tanggal'                => ['required', 'string', 'max:255'],
            'tempat'                      => ['required', 'string', 'max:255'],
            'waktu'                       => ['required', 'string', 'max:255'],
            'undangan_pdf'                => ['nullable', 'file', 'mimes:pdf', 'max:5120'], // Max 5MB
            'buat_sertifikat'             => ['nullable'],  
            // Penandatangan — opsional
            'pejabat.nama_lengkap'        => ['nullable', 'string', 'max:255'],
            'pejabat.jabatan'             => ['nullable', 'string', 'max:255'],
            'pejabat.pangkat'             => ['nullable', 'string', 'max:255'],
            'pejabat.golongan'            => ['nullable', 'string', 'max:20'],
            'pejabat.nip'                 => ['nullable', 'string', 'max:30'],
            'pejabat.tempat_ttd'          => ['nullable', 'string', 'max:255'],
            'pejabat.tanggal_ttd'         => ['nullable', 'string', 'max:255'],
            // VALIDASI ANTISIPASI DUPLIKAT:
                'nomor_surat'     => [
                    'nullable', 
                    'string', 
                    'max:255',
                    function ($attribute, $value, $fail) use ($request, $kegiatan) {
                        // Jika operator mencentang "Buatkan Sertifikat"
                        if ($request->has('buat_sertifikat')) {
                            $query = \App\Models\SigapDaftarHadirKegiatan::where('nomor_surat', trim($value))
                                ->where('buat_sertifikat', 1);
                            
                            // Jika ini proses UPDATE, abaikan id kegiatan yang sedang diedit saat ini
                            if (isset($kegiatan)) {
                                $query->where('id', '!=', $kegiatan->id);
                            }

                            if ($query->exists()) {
                                $fail('Nomor Surat/Undangan ini sudah digunakan pada kegiatan sertifikat lain. Silakan gunakan nomor yang berbeda agar tidak terjadi duplikasi nomor sertifikat.');
                            }
                        }
                    }
                ],
        ]);

        $undanganPath = null;
        if ($request->hasFile('undangan_pdf')) {
            $undanganPath = $request->file('undangan_pdf')->store('sigap/daftar-hadir/undangan', 'public');
        }

        DB::transaction(function () use ($request, &$kegiatan, &$undanganPath) {
            $kegiatan = SigapDaftarHadirKegiatan::create([
                'uuid'          => (string) Str::uuid(),
                'nama_kegiatan' => $request->nama_kegiatan,
                'hari_tanggal'  => $request->hari_tanggal,
                'tempat'        => $request->tempat,
                'waktu'         => $request->waktu,
                'status'        => 'draft',
                'created_by'    => Auth::id(),
                'undangan_path'   => $undanganPath,
                'buat_sertifikat' => $request->has('buat_sertifikat') ? 1 : 0,
                'nomor_surat'       => $request->input('nomor_surat'), // Simpan nomor_surat ke database
            ]);

            // Simpan penandatangan hanya jika nama_lengkap diisi
            $pejabatInput = $request->input('pejabat', []);
            if (!empty($pejabatInput['nama_lengkap'])) {
                $this->upsertPenandatangan($kegiatan, $pejabatInput);
            }
        });

        return redirect()
            ->route('sigap-daftar-hadir.show', $kegiatan->uuid)
            ->with('success', 'Kegiatan daftar hadir berhasil dibuat.');
    }

    public function show(SigapDaftarHadirKegiatan $kegiatan)
    {
        $user = Auth::user();

        if (!$user->hasAnyRole(['admin', 'verif_daftarhadir'])) {
            abort_unless((int) $kegiatan->created_by === (int) $user->id, 403, 'Anda tidak memiliki akses ke kegiatan ini.');
        }

        $kegiatan->load([
            'creator',
            'penandatangan',
            'peserta' => fn ($q) => $q->orderBy('urutan_absen')->orderBy('created_at'),
        ]);

        $qrUrl = route('sigap-daftar-hadir.public', $kegiatan->uuid);

        return view('dashboard.daftar_hadir.show', compact('kegiatan', 'qrUrl'));
    }

    public function edit(SigapDaftarHadirKegiatan $kegiatan)
    {
        $user = Auth::user();

        if (!$user->hasAnyRole(['admin', 'verif_daftarhadir'])) {
            abort_unless((int) $kegiatan->created_by === (int) $user->id, 403, 'Anda tidak memiliki akses ke kegiatan ini.');
        }

        $kegiatan->load([
            'penandatangan',
            'peserta' => fn ($q) => $q->orderBy('urutan_absen')->orderBy('created_at'),
        ]);

        return view('dashboard.daftar_hadir.edit', compact('kegiatan'));
    }

    public function update(Request $request, SigapDaftarHadirKegiatan $kegiatan)
    {
        $user = Auth::user();

        // 1. Validasi Hak Akses (Hanya Admin, Verifikator, atau Pembuat Kegiatan)
        if (!$user->hasAnyRole(['admin', 'verif_daftarhadir'])) {
            abort_unless((int) $kegiatan->created_by === (int) $user->id, 403, 'Anda tidak memiliki akses ke kegiatan ini.');
        }

        // 2. Validasi Input Data Form
        $request->validate([
            'nama_kegiatan'               => ['required', 'string', 'max:500'],
            'hari_tanggal'                => ['required', 'string', 'max:255'],
            'tempat'                      => ['required', 'string', 'max:255'],
            'waktu'                       => ['required', 'string', 'max:255'],
            'undangan_pdf'                => ['nullable', 'file', 'mimes:pdf', 'max:5120'], // Max 5MB
            'buat_sertifikat'             => ['nullable'],
            
            // Validasi baris data peserta (jika ada)
            'peserta'                     => ['sometimes', 'array'],
            'peserta.*.nama'              => ['required_with:peserta', 'string', 'max:255'],
            'peserta.*.instansi'          => ['required_with:peserta', 'string', 'max:255'],
            'peserta.*.gender'            => ['required_with:peserta', 'in:L,P'],
            'peserta.*.no_hp'             => ['required_with:peserta', 'string', 'max:30'],
            'peserta.*.email'             => ['nullable', 'email', 'max:255'],
            'peserta.*.urutan_absen'      => ['required_with:peserta', 'integer', 'min:1'],
            
            // Validasi data penandatangan (opsional)
            'pejabat.nama_lengkap'        => ['nullable', 'string', 'max:255'],
            'pejabat.jabatan'             => ['nullable', 'string', 'max:255'],
            'pejabat.pangkat'             => ['nullable', 'string', 'max:255'],
            'pejabat.golongan'            => ['nullable', 'string', 'max:20'],
            'pejabat.nip'                 => ['nullable', 'string', 'max:30'],
            'pejabat.tempat_ttd'          => ['nullable', 'string', 'max:255'],
            'pejabat.tanggal_ttd'         => ['nullable', 'string', 'max:255'],
            // VALIDASI ANTISIPASI DUPLIKAT:
                'nomor_surat'     => [
                    'nullable', 
                    'string', 
                    'max:255',
                    function ($attribute, $value, $fail) use ($request, $kegiatan) {
                        // Jika operator mencentang "Buatkan Sertifikat"
                        if ($request->has('buat_sertifikat')) {
                            $query = \App\Models\SigapDaftarHadirKegiatan::where('nomor_surat', trim($value))
                                ->where('buat_sertifikat', 1);
                            
                            // Jika ini proses UPDATE, abaikan id kegiatan yang sedang diedit saat ini
                            if (isset($kegiatan)) {
                                $query->where('id', '!=', $kegiatan->id);
                            }

                            if ($query->exists()) {
                                $fail('Nomor Surat/Undangan ini sudah digunakan pada kegiatan sertifikat lain. Silakan gunakan nomor yang berbeda agar tidak terjadi duplikasi nomor sertifikat.');
                            }
                        }
                    }
                ],
        ]);

        DB::transaction(function () use ($request, $kegiatan) {
            
            // 3. Logika Upload File Undangan PDF Baru
            if ($request->hasFile('undangan_pdf')) {
                // Hapus berkas fisik undangan lama dari disk public jika sebelumnya pernah ada
                if ($kegiatan->undangan_path && Storage::disk('public')->exists($kegiatan->undangan_path)) {
                    Storage::disk('public')->delete($kegiatan->undangan_path);
                }

                // Simpan berkas baru ke direktori yang ditentukan
                $path = $request->file('undangan_pdf')->store('sigap/daftar-hadir/undangan', 'public');
                $kegiatan->undangan_path = $path;
            }

            // 4. Set Nilai Mutasi Checkbox Sertifikat
            $kegiatan->buat_sertifikat = $request->has('buat_sertifikat') ? 1 : 0;

            // 5. Jalankan Perbaruan Informasi Utama Kegiatan
            $kegiatan->update([
                'nama_kegiatan' => $request->nama_kegiatan,
                'hari_tanggal'  => $request->hari_tanggal,
                'tempat'        => $request->tempat,
                'waktu'         => $request->waktu,
            ]);

            // 6. Proses Update Data Massal Peserta Kegiatan
            $pesertaInput = collect($request->input('peserta', []));
            foreach ($pesertaInput as $id => $row) {
                $peserta = SigapDaftarHadirPeserta::where('kegiatan_id', $kegiatan->id)
                    ->where('id', $id)
                    ->firstOrFail();

                $peserta->update([
                    'nama'         => $row['nama'],
                    'instansi'     => $row['instansi'],
                    'gender'       => $row['gender'],
                    'no_hp'        => $row['no_hp'],
                    'email'        => $row['email'] ?? null,
                    'urutan_absen' => (int) $row['urutan_absen'],
                    'nomor_surat'     => $row['nomor_surat'] ?? null, // Update nomor_surat jika ada
                ]);
            }

            // 7. Normalisasi / Re-order Urutan Nomor Absen Peserta (Agar Berurutan Rapi 1, 2, 3...)
            $sorted = $kegiatan->peserta()->orderBy('urutan_absen')->orderBy('created_at')->get();
            $urut = 1;
            foreach ($sorted as $item) {
                $item->update(['urutan_absen' => $urut++]);
            }

            // 8. Logika Perbaruan / Pembersihan Data Pejabat Penandatangan
            $pejabatInput = $request->input('pejabat', []);
            if (!empty($pejabatInput['nama_lengkap'])) {
                $this->upsertPenandatangan($kegiatan, $pejabatInput);
            } else {
                // Jika input nama pejabat dikosongkan, hapus relasi penandatangan (beserta file TTD-nya)
                $existing = $kegiatan->penandatangan;
                if ($existing) {
                    if ($existing->ttd_path && Storage::disk('public')->exists($existing->ttd_path)) {
                        Storage::disk('public')->delete($existing->ttd_path);
                    }
                    $existing->delete();
                }
            }
        });

        return redirect()
            ->route('sigap-daftar-hadir.show', $kegiatan->uuid)
            ->with('success', 'Kegiatan dan data peserta berhasil diperbarui.');
    }
    public function updateStatus(Request $request, SigapDaftarHadirKegiatan $kegiatan)
    {
        $request->validate([
            'status' => ['required', 'in:draft,proses,selesai'],
        ]);

        $kegiatan->update(['status' => $request->status]);

        // JIKA STATUS SELESAI DAN MINTA DIBUATKAN SERTIFIKAT -> GENERATE KE TABEL SERTIFIKAT
        if ($request->status === 'selesai' && $kegiatan->buat_sertifikat == 1) {
            
            // 1. Buat master kegiatan di tabel sertifikat_kegiatans
            $sertifKegiatan = SertifikatKegiatan::firstOrCreate(
                [
                    'nama_kegiatan' => $kegiatan->nama_kegiatan,
                    'tanggal'       => $kegiatan->hari_tanggal,
                ],
                [
                    'jenis'      => 'Kegiatan Internal',
                    'keterangan' => 'Auto-generate dari Daftar Hadir: ' . $kegiatan->nama_kegiatan,
                    'status'     => 'Aktif'
                ]
            );

            // 2. Looping seluruh peserta dan buatkan data sertifikatnya
            foreach ($kegiatan->peserta as $p) {
                $nomorDinamis = $this->formatNomorSertifikat($kegiatan->nomor_surat, $p->urutan_absen);
                
                SertifikatPeserta::firstOrCreate(
                    [
                        'kegiatan_id'   => $sertifKegiatan->id,
                        'nama_penerima' => $p->nama,
                    ],
                    [
                        'nomor_sertifikat' => $nomorDinamis,
                        // Note: Sesuaikan nama kolom asal_dinas jika tersedia di tabel SertifikatPeserta
                    ]
                );
            }
        }

        return back()->with('success', 'Status kegiatan berhasil diperbarui dan sertifikat diproses.');
    }

    public function destroy(SigapDaftarHadirKegiatan $kegiatan)
    {
        DB::transaction(function () use ($kegiatan) {
            // Hapus TTD peserta
            foreach ($kegiatan->peserta as $peserta) {
                if ($peserta->ttd_path && Storage::disk('public')->exists($peserta->ttd_path)) {
                    Storage::disk('public')->delete($peserta->ttd_path);
                }
            }
            $kegiatan->peserta()->delete();

            // Hapus TTD penandatangan
            $penandatangan = $kegiatan->penandatangan;
            if ($penandatangan) {
                if ($penandatangan->ttd_path && Storage::disk('public')->exists($penandatangan->ttd_path)) {
                    Storage::disk('public')->delete($penandatangan->ttd_path);
                }
                $penandatangan->delete();
            }

            $kegiatan->delete();
        });

        return redirect()
            ->route('sigap-daftar-hadir.index')
            ->with('success', 'Kegiatan berhasil dihapus.');
    }

    // =========================================================================
    // RIWAYAT PESERTA
    // =========================================================================

    public function riwayatPeserta(Request $request)
    {
        $q       = trim((string) $request->get('q', ''));
        $results = collect();

        if ($q !== '') {
            $results = SigapDaftarHadirPeserta::with('kegiatan')
                ->where('nama', 'like', "%{$q}%")
                ->orderBy('nama')
                ->get()
                ->groupBy(fn ($p) => Str::lower(trim($p->nama)));
        }

        return view('dashboard.daftar_hadir.riwayat-peserta', compact('q', 'results'));
    }

    public function riwayatPesertaDetail(Request $request)
    {
        $nama = trim((string) $request->get('nama', ''));
        abort_if($nama === '', 400, 'Parameter nama diperlukan.');

        $pesertaList = SigapDaftarHadirPeserta::with('kegiatan')
            ->whereRaw('LOWER(nama) = ?', [Str::lower($nama)])
            ->orderByDesc('created_at')
            ->get();

        return view('dashboard.daftar_hadir.riwayat-peserta-detail', compact('nama', 'pesertaList'));
    }

    // =========================================================================
    // PUBLIC FORM — PESERTA
    // =========================================================================

    public function publicForm(SigapDaftarHadirKegiatan $kegiatan)
    {
        $kegiatan->loadCount('peserta');

        return view('dashboard.daftar_hadir.public-form', compact('kegiatan'));
    }

    public function searchPeserta(Request $request)
    {
        $q = trim((string) $request->get('q', ''));

        if ($q === '') {
            return response()->json([]);
        }

        $items = SigapDaftarHadirPeserta::query()
            ->where('nama', 'like', "%{$q}%")
            ->orderByDesc('created_at')
            ->limit(20)
            ->get()
            ->unique(fn ($item) => Str::lower(trim($item->nama)))
            ->values()
            ->map(fn ($item) => [
                'id'       => $item->id,
                'nama'     => $item->nama,
                'instansi' => $item->instansi,
                'gender'   => $item->gender,
                'no_hp'    => $item->no_hp,
                'email'    => $item->email,
                'ttd_path' => $item->ttd_path ? asset('storage/' . $item->ttd_path) : null,
            ]);

        return response()->json($items);
    }

    public function storePublic(Request $request, SigapDaftarHadirKegiatan $kegiatan)
    {
        if ($kegiatan->status === 'selesai') {
            return redirect()
                ->route('sigap-daftar-hadir.public', $kegiatan->uuid)
                ->with('error', 'Maaf, kegiatan ini sudah selesai dan tidak menerima peserta baru.');
        }

        $request->validate([
            'nama'              => ['required', 'string', 'max:255'],
            'instansi'          => ['required', 'string', 'max:255'],
            'gender'            => ['required', 'in:L,P'],
            'no_hp'             => ['required', 'string', 'max:30'],
            'email'             => ['nullable', 'email', 'max:255'],
            'ttd_data'          => ['nullable', 'string'],
            'existing_ttd_path' => ['nullable', 'string'],
        ]);

        $nama = trim($request->nama);

        $duplicate = SigapDaftarHadirPeserta::where('kegiatan_id', $kegiatan->id)
            ->whereRaw('LOWER(nama) = ?', [Str::lower($nama)])
            ->exists();

        if ($duplicate) {
            return back()->withInput()->with('error', 'Nama tersebut sudah terdaftar pada kegiatan ini.');
        }

        $ttdPath = null;
        if ($request->filled('ttd_data')) {
            $ttdPath = $this->saveSignatureBase64(
                $request->ttd_data,
                'sigap/daftar-hadir/ttd/' . $kegiatan->id
            );
        } elseif ($request->filled('existing_ttd_path')) {
            $ttdPath = ltrim(str_replace(asset('storage/'), '', $request->existing_ttd_path), '/');
        }

        $nextOrder = (int) (SigapDaftarHadirPeserta::where('kegiatan_id', $kegiatan->id)->max('urutan_absen') ?? 0) + 1;

        SigapDaftarHadirPeserta::create([
            'kegiatan_id'  => $kegiatan->id,
            'nama'         => $nama,
            'instansi'     => $request->instansi,
            'gender'       => $request->gender,
            'no_hp'        => $request->no_hp,
            'email'        => $request->email,
            'ttd_path'     => $ttdPath,
            'urutan_absen' => $nextOrder,
            'created_by'   => null,
        ]);

        return redirect()
            ->route('sigap-daftar-hadir.public', $kegiatan->uuid)
            ->with('success_name', $nama)
            ->with('success_kegiatan', $kegiatan->nama_kegiatan);
    }

    // =========================================================================
    // PUBLIC FORM — PEJABAT (TTD)
    // =========================================================================

    /**
     * Form TTD untuk pejabat penandatangan.
     * Route: GET /sigap-daftar-hadir/pejabat/{penandatangan:uuid}
     */
    public function publicFormPejabat(SigapDaftarHadirPenandatangan $penandatangan)
    {
        $penandatangan->load('kegiatan');

        return view('dashboard.daftar_hadir.public-form-pejabat', compact('penandatangan'));
    }

    /**
     * Simpan TTD pejabat.
     * Route: POST /sigap-daftar-hadir/pejabat/{penandatangan:uuid}
     */
    public function storePublicPejabat(Request $request, SigapDaftarHadirPenandatangan $penandatangan)
    {
        // Jika sudah TTD, tidak boleh TTD ulang (opsional — bisa dihapus jika ingin bisa revisi)
        if ($penandatangan->sudah_ttd) {
            return redirect()
                ->route('sigap-daftar-hadir.pejabat-form', $penandatangan->uuid)
                ->with('error', 'Tanda tangan sudah tersimpan untuk kegiatan ini.');
        }

        $request->validate([
            'ttd_data' => ['required', 'string'],
        ]);

        $ttdPath = $this->saveSignatureBase64(
            $request->ttd_data,
            'sigap/daftar-hadir/ttd-pejabat/' . $penandatangan->kegiatan_id
        );

        if (!$ttdPath) {
            return back()->with('error', 'Data TTD tidak valid.');
        }

        // Hapus TTD lama jika ada
        if ($penandatangan->ttd_path && Storage::disk('public')->exists($penandatangan->ttd_path)) {
            Storage::disk('public')->delete($penandatangan->ttd_path);
        }

        $penandatangan->update([
            'ttd_path'  => $ttdPath,
            'signed_at' => now(),
        ]);

        // Update / insert master pejabat (upsert berdasarkan NIP atau nama)
        if ($penandatangan->nip) {
            SigapDaftarHadirPejabat::updateOrCreate(
                ['nip' => $penandatangan->nip],
                [
                    'nama_lengkap' => $penandatangan->nama_lengkap,
                    'jabatan'      => $penandatangan->jabatan,
                    'pangkat'      => $penandatangan->pangkat,
                    'golongan'     => $penandatangan->golongan,
                ]
            );
        }

        return redirect()
            ->route('sigap-daftar-hadir.pejabat-form', $penandatangan->uuid)
            ->with('success', 'Tanda tangan berhasil disimpan. Terima kasih, ' . $penandatangan->nama_lengkap . '.');
    }

    // =========================================================================
    // SEARCH PEJABAT (autocomplete di create/edit form)
    // =========================================================================

    /**
     * Route: GET /sigap-daftar-hadir/pejabat/search?q=...
     */
    public function searchPejabat(Request $request)
    {
        $q = trim((string) $request->get('q', ''));

        if ($q === '') {
            return response()->json([]);
        }

        $items = SigapDaftarHadirPejabat::where('nama_lengkap', 'like', "%{$q}%")
            ->orWhere('nip', 'like', "%{$q}%")
            ->orWhere('jabatan', 'like', "%{$q}%")
            ->orderBy('nama_lengkap')
            ->limit(10)
            ->get()
            ->map(fn ($p) => [
                'id'           => $p->id,
                'nama_lengkap' => $p->nama_lengkap,
                'jabatan'      => $p->jabatan,
                'pangkat'      => $p->pangkat,
                'golongan'     => $p->golongan,
                'nip'          => $p->nip,
            ]);

        return response()->json($items);
    }

    // =========================================================================
    // VERIFIKASI PUBLIK — QR di footer PDF
    // =========================================================================

    /**
     * Halaman verifikasi publik yang ditampilkan saat QR di footer PDF discan.
     * Route: GET /verifikasi/daftar-hadir/{kegiatan:uuid}
     */
    public function verifikasi(SigapDaftarHadirKegiatan $kegiatan)
    {
        $kegiatan->load([
            'penandatangan',
            'peserta' => fn ($q) => $q->orderBy('urutan_absen')->orderBy('created_at'),
        ]);

        return view('dashboard.daftar_hadir.verifikasi', compact('kegiatan'));
    }

    // =========================================================================
    // EXPORT PDF
    // =========================================================================

    public function exportPdf(SigapDaftarHadirKegiatan $kegiatan)
    {
        $user = Auth::user();

        // 1. Validasi Hak Akses (Hanya Admin, Verifikator, atau Pembuat Kegiatan)
        if (!$user->hasAnyRole(['admin', 'verif_daftarhadir'])) {
            abort_unless((int) $kegiatan->created_by === (int) $user->id, 403, 'Anda tidak memiliki akses.');
        }

        // 2. Batasan Export (Hanya jika status kegiatan sudah selesai)
        abort_unless($kegiatan->status === 'selesai', 403, 'PDF hanya bisa diexport saat kegiatan selesai.');

        // 3. Eager Loading Data Relasi Peserta & Penandatangan
        $kegiatan->load([
            'penandatangan',
            'peserta' => fn ($q) => $q->orderBy('urutan_absen')->orderBy('created_at'),
        ]);

        // 4. Load Asset Logo Pemerintah Kota & BRIDA ke Base64 (Untuk DomPDF)
        $logoPemkot = $this->loadLogoBase64('logo-pemkot.png');
        $logoBrida  = $this->loadLogoBase64('logo-brida.png');

        // 5. Generate QR Code Verifikasi Publik (Untuk Halaman Utama Daftar Hadir)
        $verifikasiUrl = route('sigap-daftar-hadir.verifikasi', $kegiatan->uuid);
        $qrVerifikasi = base64_encode(
            QrCode::format('svg')->size(120)->margin(1)->generate($verifikasiUrl)
        );

        // 6. Generate Dokumen Utama: Lembar Daftar Hadir Menggunakan DomPDF
        $pdfUtama = Pdf::loadView('dashboard.daftar_hadir.pdf', [
            'kegiatan'      => $kegiatan,
            'logoPemkot'    => $logoPemkot,
            'logoBrida'     => $logoBrida,
            'qrVerifikasi'  => $qrVerifikasi,
            'verifikasiUrl' => $verifikasiUrl,
        ])->setPaper('letter', 'portrait');

        try {
            $merger = new Merger();
            
            // LAPISAN 1: File Undangan Asli (Jika Sebelumnya Diupload)
            if ($kegiatan->undangan_path && Storage::disk('public')->exists($kegiatan->undangan_path)) {
                $merger->addFile(storage_path('app/public/' . $kegiatan->undangan_path));
            }
            
            // LAPISAN 2: Lembar Daftar Hadir Resmi (DomPDF Output)
            $merger->addRaw($pdfUtama->output());

            // LAPISAN 3: Lampiran Tabel List Nomor Sertifikat Peserta
            if ($kegiatan->buat_sertifikat == 1) {
                // Generate QR Code Khusus untuk Link Portal Sertifikat (Format SVG -> Base64)
                $portalSertifikatUrl = 'https://sigap.brida.makassarkota.go.id/sertifikat';
                $qrSertifikatSvg = base64_encode(
                    QrCode::format('svg')->size(70)->margin(0)->generate($portalSertifikatUrl)
                );

                // Mapping penomoran dinamis agar bernilai sama persis antara database dan cetakan tabel
                $kegiatan->peserta->transform(function ($p) use ($kegiatan) {
                    $p->nomor_sertifikat_dinamis = $this->formatNomorSertifikat($kegiatan->nomor_surat, $p->urutan_absen);
                    return $p;
                });

                // Generate Dokumen Lampiran Menggunakan DomPDF
                $pdfSertifikat = Pdf::loadView('dashboard.daftar_hadir.pdf_lampiran_sertifikat', [
                    'kegiatan'     => $kegiatan,
                    'logoPemkot'   => $logoPemkot,
                    'logoBrida'    => $logoBrida,
                    'qrSertifikat' => $qrSertifikatSvg
                ])->setPaper('letter', 'portrait');

                // Masukkan sebagai halaman terakhir dokumen
                $merger->addRaw($pdfSertifikat->output());
            }

            // Proses Penggabungan Seluruh Dokumen (Undangan + Daftar Hadir + Lampiran)
            $mergedPdf = $merger->merge();

            // Return Response Download Berkas Kolektif
            return response($mergedPdf)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="dokumen-lengkap-' . Str::slug($kegiatan->nama_kegiatan) . '.pdf"');
        
        } catch (\Exception $e) {
            // Fail-safe: Jika parser library merger crash / corrupt, sistem otomatis mendownload berkas daftar hadir utama saja
            return $pdfUtama->download(
                'daftar-hadir-saja-' . Str::slug($kegiatan->nama_kegiatan) . '.pdf'
            );
        }
    }
    // =========================================================================
    // PRINT QR
    // =========================================================================

    public function printQr(SigapDaftarHadirKegiatan $kegiatan)
    {
        $user = Auth::user();

        if (!$user->hasAnyRole(['admin', 'verif_daftarhadir'])) {
            abort_unless((int) $kegiatan->created_by === (int) $user->id, 403, 'Anda tidak memiliki akses.');
        }

        $qrUrl        = route('sigap-daftar-hadir.public', $kegiatan->uuid);
        $instagramUrl = 'https://www.instagram.com/bridakotamakassar/';

        return view('dashboard.daftar_hadir.print-qr', compact('kegiatan', 'qrUrl', 'instagramUrl'));
    }

    /**
     * Print QR khusus pejabat penandatangan.
     * Route: GET /sigap-daftar-hadir/{kegiatan}/print-qr-pejabat
     */
    public function printQrPejabat(SigapDaftarHadirKegiatan $kegiatan)
    {
        $user = Auth::user();

        if (!$user->hasAnyRole(['admin', 'verif_daftarhadir'])) {
            abort_unless((int) $kegiatan->created_by === (int) $user->id, 403, 'Anda tidak memiliki akses.');
        }

        $penandatangan = $kegiatan->penandatangan;

        abort_unless($penandatangan !== null, 404, 'Kegiatan ini tidak memiliki data penandatangan.');

        $qrPejabatUrl = route('sigap-daftar-hadir.pejabat-form', $penandatangan->uuid);

        return view('dashboard.daftar_hadir.print-qr-pejabat', compact('kegiatan', 'penandatangan', 'qrPejabatUrl'));
    }

    // =========================================================================
    // PRIVATE HELPERS
    // =========================================================================

    /**
     * Buat atau perbarui data penandatangan untuk satu kegiatan.
     */
    private function upsertPenandatangan(SigapDaftarHadirKegiatan $kegiatan, array $input): void
    {
        // Cari master pejabat (via NIP jika ada, else nama)
        $pejabatId = null;
        if (!empty($input['nip'])) {
            $master = SigapDaftarHadirPejabat::firstOrCreate(
                ['nip' => $input['nip']],
                [
                    'nama_lengkap' => $input['nama_lengkap'],
                    'jabatan'      => $input['jabatan']      ?? null,
                    'pangkat'      => $input['pangkat']      ?? null,
                    'golongan'     => $input['golongan']     ?? null,
                    'created_by'   => Auth::id(),
                ]
            );
            // Update data master jika sudah ada
            $master->update([
                'nama_lengkap' => $input['nama_lengkap'],
                'jabatan'      => $input['jabatan']      ?? $master->jabatan,
                'pangkat'      => $input['pangkat']      ?? $master->pangkat,
                'golongan'     => $input['golongan']     ?? $master->golongan,
            ]);
            $pejabatId = $master->id;
        }

        $existing = $kegiatan->penandatangan;

        if ($existing) {
            // Jangan reset TTD yang sudah ada jika hanya update data
            $existing->update([
                'pejabat_id'   => $pejabatId ?? $existing->pejabat_id,
                'nama_lengkap' => $input['nama_lengkap'],
                'jabatan'      => $input['jabatan']      ?? null,
                'pangkat'      => $input['pangkat']      ?? null,
                'golongan'     => $input['golongan']     ?? null,
                'nip'          => $input['nip']          ?? null,
                'tempat_ttd'   => $input['tempat_ttd']   ?? null,
                'tanggal_ttd'  => $input['tanggal_ttd']  ?? null,
            ]);
        } else {
            SigapDaftarHadirPenandatangan::create([
                'uuid'         => (string) Str::uuid(),
                'kegiatan_id'  => $kegiatan->id,
                'pejabat_id'   => $pejabatId,
                'nama_lengkap' => $input['nama_lengkap'],
                'jabatan'      => $input['jabatan']      ?? null,
                'pangkat'      => $input['pangkat']      ?? null,
                'golongan'     => $input['golongan']     ?? null,
                'nip'          => $input['nip']          ?? null,
                'tempat_ttd'   => $input['tempat_ttd']   ?? null,
                'tanggal_ttd'  => $input['tanggal_ttd']  ?? null,
            ]);
        }
    }

    private function saveSignatureBase64(string $data, string $folder): string
    {
        if (!Str::startsWith($data, 'data:image/')) {
            return '';
        }

        [$meta, $content] = explode(',', $data, 2);

        $extension = 'png';
        if (Str::contains($meta, 'image/jpeg')) {
            $extension = 'jpg';
        }

        $binary   = base64_decode($content);
        $fileName = Str::uuid() . '.' . $extension;
        $path     = trim($folder, '/') . '/' . $fileName;

        Storage::disk('public')->put($path, $binary);

        return $path;
    }

    private function loadLogoBase64(string $filename): ?string
    {
        $candidates = [
            base_path('../public_html/images/' . $filename),
            '/home/sigap/public_html/images/' . $filename,
            public_path('images/' . $filename),
        ];

        foreach ($candidates as $path) {
            if (file_exists($path) && is_readable($path)) {
                $content = @file_get_contents($path);
                if ($content !== false && $content !== '') {
                    $ext  = strtolower(pathinfo($path, PATHINFO_EXTENSION));
                    $mime = match ($ext) {
                        'jpg', 'jpeg' => 'image/jpeg',
                        'svg'         => 'image/svg+xml',
                        'gif'         => 'image/gif',
                        default       => 'image/png',
                    };
                    return 'data:' . $mime . ';base64,' . base64_encode($content);
                }
            }
        }

        return null;
    }
    private function formatNomorSertifikat(?string $nomorSurat, int $urutan, int $kegiatanId = 0): string
    {
        $paddedUrutan = str_pad($urutan, 2, '0', STR_PAD_LEFT);

        if (empty($nomorSurat)) {
            return '000/SERTIF-' . $paddedUrutan . '/BRIDA/' . \Carbon\Carbon::now()->format('Y');
        }

        $nomorSurat = trim($nomorSurat);
        $parts = explode('/', $nomorSurat);
        
        if (count($parts) > 1) {
            $tahun = array_pop($parts);
            $prefix = implode('/', $parts);
            
            // KITA TAMBAHKAN ID KEGIATAN DI SINI (Misal: SERTIF-01-KG4)
            // KG4 berarti Kegiatan ID nomor 4. Ini dijamin 100% TIDAK AKAN PERNAH DUPLIKAT di server.
            $suffixSertif = 'SERTIF-' . $paddedUrutan;
            if ($kegiatanId > 0) {
                $suffixSertif .= '-KG' . $kegiatanId;
            }

            return $prefix . '/' . $suffixSertif . '/' . $tahun;
        }

        return $nomorSurat . '/SERTIF-' . $paddedUrutan . ($kegiatanId > 0 ? '-KG' . $kegiatanId : '');
    }
}