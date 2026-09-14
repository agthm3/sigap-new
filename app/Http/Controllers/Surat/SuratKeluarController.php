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
            'tanggal'         => 'required|date',
            'nomor_berkas'    => 'required|string|max:50',
            'alamat_penerima' => 'required|string',
            'perihal'         => 'required|string',
            'slot_id'         => 'nullable|exists:surat_keluars,id',
            'file_surat'      => 'nullable|mimes:pdf|max:10240',
        ]);

        return DB::transaction(function () use ($request) {
            $user = auth()->user();

            if ($request->filled('slot_id')) {
                $target = SuratKeluar::lockForUpdate()->find($request->slot_id);
            } else {
                $this->suratService->alokasikanBlokJikaBaru($request->tanggal);

                $target = SuratKeluar::whereDate('tanggal', $request->tanggal)
                    ->where('status', 'slot_kosong')
                    ->orderBy('nomor_urut', 'asc')
                    ->lockForUpdate()
                    ->first();

                // Jika 10 slot awal sudah habis dipakai semua di tanggal ini, buka 5 slot tambahan
                if (!$target) {
                    $tahun = Carbon::parse($request->tanggal)->year;
                    $last = SuratKeluar::where('tahun', $tahun)->max('nomor_urut');
                    $target = SuratKeluar::create([
                        'tahun' => $tahun,
                        'tanggal' => $request->tanggal,
                        'nomor_urut' => $last + 1,
                        'status' => 'slot_kosong',
                    ]);
                }
            }

            $nomorLengkap = $this->suratService->generateFormatLengkap(
                $request->nomor_berkas,
                $target->nomor_urut,
                $request->tanggal
            );

            $filePath = null;
            if ($request->hasFile('file_surat')) {
                $filePath = $request->file('file_surat')->store('surat_keluar', 'public');
            }

            $target->update([
                'nomor_berkas'        => $request->nomor_berkas,
                'nomor_surat_lengkap' => $nomorLengkap,
                'alamat_penerima'     => $request->alamat_penerima,
                'perihal'             => $request->perihal,
                'created_by'          => $user->id,
                'status'              => 'terbit',
                'file_surat'          => $filePath ?? $target->file_surat,
            ]);

            return redirect()->route('sigap-surat.keluar.index')
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
        $surat = SuratKeluar::findOrFail($id);

        $request->validate([
            'nomor_berkas'    => 'required|string|max:50',
            'alamat_penerima' => 'required|string',
            'perihal'         => 'required|string',
            'file_surat'      => 'nullable|mimes:pdf|max:10240',
        ]);

        $nomorLengkap = $this->suratService->generateFormatLengkap(
            $request->nomor_berkas,
            $surat->nomor_urut,
            $surat->tanggal->toDateString()
        );

        $data = [
            'nomor_berkas'        => $request->nomor_berkas,
            'nomor_surat_lengkap' => $nomorLengkap,
            'alamat_penerima'     => $request->alamat_penerima,
            'perihal'             => $request->perihal,
            'status'              => 'terbit',
        ];

        // Jika slot kosong diisi pertama kali
        if (!$surat->created_by) {
            $data['created_by'] = auth()->id();
        }

        if ($request->hasFile('file_surat')) {
            if ($surat->file_surat) {
                Storage::disk('public')->delete($surat->file_surat);
            }
            $data['file_surat'] = $request->file('file_surat')->store('surat_keluar', 'public');
        }

        $surat->update($data);

        return redirect()->route('sigap-surat.keluar.index')
            ->with('success', "Data surat no. urut {$surat->nomor_urut} berhasil diperbarui.");
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