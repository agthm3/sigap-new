<?php

namespace App\Http\Controllers\Surat;

use App\Http\Controllers\Controller;
use App\Models\SuratKeluar;
use App\Services\SuratKeluarService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SuratKeluarController extends Controller
{
    protected $suratService;

    public function __construct(SuratKeluarService $suratService)
    {
        $this->suratService = $suratService;
    }

    public function index(Request $request)
    {
        $tahunSekarang = $request->get('tahun', date('Y'));

        $query = SuratKeluar::with('creator')
            ->where('tahun', $tahunSekarang)
            ->orderBy('nomor_urut', 'asc');

        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal', $request->tanggal);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('nomor_surat_lengkap', 'like', "%{$q}%")
                    ->orWhere('perihal', 'like', "%{$q}%")
                    ->orWhere('alamat_penerima', 'like', "%{$q}%")
                    ->orWhere('nomor_berkas', 'like', "%{$q}%");
            });
        }

        $surats = $query->paginate(25)->withQueryString();

        $totalSemua = SuratKeluar::where('tahun', $tahunSekarang)->count();
        $totalTerbit = SuratKeluar::where('tahun', $tahunSekarang)->where('status', 'terbit')->count();
        $totalSlot = SuratKeluar::where('tahun', $tahunSekarang)->where('status', 'slot_kosong')->count();
        $totalBatal = SuratKeluar::where('tahun', $tahunSekarang)->where('status', 'batal')->count();

        return view('dashboard.surat.keluar.index', compact(
            'surats', 'totalSemua', 'totalTerbit', 'totalSlot', 'totalBatal', 'tahunSekarang'
        ));
    }

    /**
     * Endpoint Ajax untuk mendeteksi slot cadangan saat user mengganti tanggal di form
     */
    public function checkSlots(Request $request)
    {
        $request->validate(['tanggal' => 'required|date']);

        $slots = SuratKeluar::whereDate('tanggal', $request->tanggal)
            ->where('status', 'slot_kosong')
            ->orderBy('nomor_urut', 'asc')
            ->get(['id', 'nomor_urut', 'tanggal']);

        return response()->json([
            'ada_blok' => SuratKeluar::whereDate('tanggal', $request->tanggal)->exists(),
            'slots' => $slots
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal'           => 'required|date',
            'nomor_berkas'      => 'required|string|max:50',
            'alamat_penerima'   => 'required|string',
            'perihal'           => 'required|string',
            'mode_penomoran'    => 'required|in:otomatis,manual',
            'nomor_urut_manual' => 'nullable|integer|min:1',
            'slot_id'           => 'nullable|exists:surat_keluars,id',
            'file_surat'        => 'nullable|mimes:pdf|max:10240',
        ]);

        return DB::transaction(function () use ($request) {
            $user  = auth()->user();
            $tahun = Carbon::parse($request->tanggal)->year;

            // -------------------------------------------------------------
            // KONDISI 1: ADA NOMOR URUT MANUAL (DARI MODE MANUAL ATAU CUSTOM OTOMATIS)
            // -------------------------------------------------------------
            if ($request->mode_penomoran === 'manual' || $request->filled('nomor_urut_manual')) {
                $nomorUrut = (int) $request->nomor_urut_manual;

                $target = SuratKeluar::where('tahun', $tahun)
                    ->where('nomor_urut', $nomorUrut)
                    ->lockForUpdate()
                    ->first();

                if ($target) {
                    if ($target->status !== 'slot_kosong') {
                        return back()->withInput()->withErrors([
                            'nomor_urut_manual' => "Nomor urut {$nomorUrut} pada tahun {$tahun} sudah terbit dan digunakan!"
                        ]);
                    }
                } else {
                    // Buat baris baru khusus nomor urut yang diinput (tanpa alokasi 10 baris)
                    $target = SuratKeluar::create([
                        'tahun'      => $tahun,
                        'tanggal'    => $request->tanggal,
                        'nomor_urut' => $nomorUrut,
                        'status'     => 'slot_kosong',
                    ]);
                }
            } 
            // -------------------------------------------------------------
            // KONDISI 2: MODE MURNI OTOMATIS (ALOKASI BLOK 10 BARIS CADANGAN)
            // -------------------------------------------------------------
            else {
                if ($request->filled('slot_id')) {
                    $target = SuratKeluar::lockForUpdate()->find($request->slot_id);
                } else {
                    // Panggil service untuk alokasikan 10 nomor urut baru jika tanggal ini belum ada blok
                    $this->suratService->alokasikanBlokJikaBaru($request->tanggal);

                    // Ambil slot cadangan paling awal yang masih kosong di tanggal tersebut
                    $target = SuratKeluar::whereDate('tanggal', $request->tanggal)
                        ->where('status', 'slot_kosong')
                        ->orderBy('nomor_urut', 'asc')
                        ->lockForUpdate()
                        ->first();

                    // Jika 10 slot awal di tanggal ini sudah terpakai semua, buat 5 slot tambahan
                    if (!$target) {
                        $last = SuratKeluar::where('tahun', $tahun)->lockForUpdate()->max('nomor_urut') ?? 0;
                        for ($i = 1; $i <= 5; $i++) {
                            $slotBaru = SuratKeluar::create([
                                'tahun'      => $tahun,
                                'tanggal'    => $request->tanggal,
                                'nomor_urut' => $last + $i,
                                'status'     => 'slot_kosong',
                            ]);
                            if ($i === 1) {
                                $target = $slotBaru;
                            }
                        }
                    }
                }
            }

            // Generate format lengkap nomor surat
            $nomorLengkap = $this->suratService->generateFormatLengkap(
                $request->nomor_berkas,
                $target->nomor_urut,
                $request->tanggal
            );

            // Upload PDF jika ada
            $filePath = null;
            if ($request->hasFile('file_surat')) {
                $filePath = $request->file('file_surat')->store('surat_keluar', 'public');
            }

            $target->update([
                'tanggal'             => $request->tanggal,
                'nomor_berkas'        => $request->nomor_berkas,
                'nomor_surat_lengkap' => $nomorLengkap,
                'alamat_penerima'     => $request->alamat_penerima,
                'perihal'             => $request->perihal,
                'created_by'          => $user->id,
                'status'              => 'terbit',
                'file_surat'          => $filePath ?? $target->file_surat,
            ]);

            return redirect()->route('sigap-surat.keluar.index', ['tahun' => $tahun])
                ->with('success', "Nomor surat berhasil diterbitkan: {$nomorLengkap}");
        });
    }

    public function show($id)
    {
        $surat = SuratKeluar::with('creator')->findOrFail($id);
        return view('dashboard.surat.keluar.show', compact('surat'));
    }

    public function create()
    {
        $daftarKlasifikasi = SuratKeluarService::getDaftarKlasifikasi();
        return view('dashboard.surat.keluar.create', compact('daftarKlasifikasi'));
    }

    public function edit($id)
    {
        $surat = SuratKeluar::findOrFail($id);
        $daftarKlasifikasi = SuratKeluarService::getDaftarKlasifikasi();
        return view('dashboard.surat.keluar.edit', compact('surat', 'daftarKlasifikasi'));
    }

    public function update(Request $request, $id)
{
    abort_if(!auth()->user()->hasAnyRole(['admin', 'verif_surat']), 403, 'Akses ditolak.');

    $surat = SuratKeluar::findOrFail($id);

    $request->validate([
        'tanggal'         => 'required|date',
        'nomor_berkas'    => 'required|string|max:50',
        'perihal'         => 'required|string',
        'alamat_penerima' => 'required|string|max:255',
    ]);

    // Format ulang nomor surat lengkap
    $bulanRomawi = SuratKeluarService::getRomawi((int) date('n', strtotime($request->tanggal)));
    $nomorLengkap = "{$request->nomor_berkas}/{$surat->nomor_urut}/BRIDA/{$bulanRomawi}/{$surat->tahun}";

    // HANYA ambil kolom yang benar-benar ada di tabel surat_keluars
    $surat->update([
        'tanggal'             => $request->tanggal,
        'nomor_berkas'        => $request->nomor_berkas,
        'nomor_surat_lengkap' => $nomorLengkap,
        'perihal'             => $request->perihal,
        'alamat_penerima'     => $request->alamat_penerima,
        'status'              => 'terbit',
    ]);

    return redirect()->route('sigap-surat.keluar.index')
        ->with('success', "Data Surat Keluar nomor urut {$surat->nomor_urut} berhasil diperbarui.");
}

    public function voidNomor(Request $request, $id)
    {
        $surat = SuratKeluar::findOrFail($id);
        $request->validate(['catatan' => 'required|string']);

        $surat->update([
            'status' => 'batal',
            'catatan' => $request->catatan,
        ]);

        return back()->with('success', "Nomor urut {$surat->nomor_urut} berhasil dibatalkan (void).");
    }

    /**
     * Tampilan Mode Buku Register Fisik Tahunan (Siap Cetak / Save PDF)
     */
    public function bukuAgenda(Request $request)
    {
        $tahun = $request->get('tahun', date('Y'));

        // Ambil seluruh nomor urut pada tahun tersebut secara kronologis
        $surats = SuratKeluar::with('creator')
            ->where('tahun', $tahun)
            ->orderBy('nomor_urut', 'asc')
            ->get();

        // Rekap statistik buku
        $totalSemua = $surats->count();
        $totalTerbit = $surats->where('status', 'terbit')->count();
        $totalBatal = $surats->where('status', 'batal')->count();
        $totalSlot = $surats->where('status', 'slot_kosong')->count();

        return view('dashboard.surat.keluar.buku-agenda', compact(
            'surats', 'tahun', 'totalSemua', 'totalTerbit', 'totalBatal', 'totalSlot'
        ));
    }
}